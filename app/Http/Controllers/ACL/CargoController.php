<?php

namespace App\Http\Controllers\ACL;

use App\Models\ACL\Rol as ACLRol;
use App\Models\ACL\Grupo as ACLGrupo;
use App\Models\ACL\Controlador as ACLControlador;
use App\Models\ACL\GrupoPermiso as ACLGrupoPermiso;
use App\Models\ACL\Usuario as ACLUsuario;
use App\Models\ACL\UsuarioGrupo as ACLUsuarioGrupo;
use App\Models\ACL\Permission;
use Core\Request;
use Core\DB;
#use Illuminate\Support\Facades\Session;
use Redirect;
use stdClass;
use Core\Controller;
use Core\Formity;

class PermissionController extends Controller
{
    public function __construct()
    {
      $form = Formity::instance('rol');
      $form->addField('rotulo', 'input:text');

      $form = Formity::instance('grupo');
      $form->addField('nombre', 'input:text');
      $form->addField('descripcion', 'input:text');

      $form = Formity::instance('controlador');
      $form->addField('controlador_padre_id?', 'input:autocomplete');
      $form->addField('rotulo', 'input:text');
      $form->addField('link', 'input:text');
      $form->addField('permisos', 'input:text');
      $form->addField('visible', 'boolean');

      $form->getField('controlador_padre_id')->setOptions(function($field, $form, $q) {
        $listado = ACLControlador::busqueda($q);
        return $listado->map(function($n) {
          return [
            'value' => $n->rotulo,
            'id'    => $n->id,
            'name'  => $n->rotulo,
          ];
        });
      });
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \App\lain  $lain
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $usuarios = Permission::usuarios();
      $roles = Permission::roles();
        $modulos  = Permission::modulos();
        $grupos  = Permission::grupos();
        return view('acl.permissions.index', compact('usuarios', 'roles','modulos', 'grupos'));
    }
    public function UsuarioPermisos(Request $request, ACLUsuario $aclusuario)
    {
        if ($request->isMethod('post')) {
            $accion = $request->input('accion');
            ACLUsuarioGrupo::where('usuario_id', $aclusuario->id)->update(['eliminado' => 1]);
            if(!empty($accion)) {
                foreach ($accion as $grupo_id => $acciones) {
                    ACLUsuarioGrupo::updateOrCreate(
                        [
                            'usuario_id' => $aclusuario->id,
                            'grupo_id'   => $grupo_id
                        ],
                        [
                            'eliminado' => 0
                        ]
                    );
                }
            }
            return response()->json([
                'status'  => 'success',
                'message' => 'Se ha realizado registro con éxito.',
                'data' => [
                ],
            ]);
        } else {
            $permisos = $aclusuario->permisos();
            $p2 = array_map(function($n) {
                return $n->grupo_id;
            }, $permisos->toArray());
            $listado = Permission::grupos();
            $listado = array_map(function ($grupo) use ($p2) {
                $grupo->checked = in_array($grupo->id, $p2);
                return $grupo;
            }, $listado->toArray());
            return view('acl.permissions.usuario_permisos', compact('aclusuario', 'listado'));
        }
    }
    public function RolPermisos(Request $request, ACLRol $rol)
    {
        if ($request->isMethod('post')) {
          $accion = $request->input('accion');

          $rol->update([
            'grupo_ids' => '{' . implode(',', array_keys($accion)) . '}',
          ]);
          return response()->json([
                'status'  => 'success',
                'message' => 'Se ha realizado registro con éxito.',
                'data' => [
                ],
            ]);
        } else {
          $permisos = $rol->permisos();
          $listado = $permisos->toArray();
          return view('acl.permissions.rol_permisos', compact('rol', 'listado'));
        }
    }
    public function GrupoPermisos(Request $request, ACLGrupo $aclgrupo)
    {
        if ($request->isMethod('post')) {
            $accion = $request->input('accion');
            ACLGrupoPermiso::where('grupo_id', $aclgrupo->id)->update(['eliminado' => 1]);
            if(!empty($accion)) {
                foreach ($accion as $controlador_id => $acciones) {
                    $acciones = array_keys($acciones);
                    ACLGrupoPermiso::updateOrCreate(
                        [
                            'grupo_id' => $aclgrupo->id,
                            'controlador_id' => $controlador_id
                        ],
                        [
                            'permisos'  => '{"' . implode('","', $acciones) . '"}',
                            'eliminado' => 0
                        ]
                    );
                }
            }
            return response()->json([
                'status'  => 'success',
                'message' => 'Se ha realizado registro con éxito.',
                'data' => [
                ],
            ]);
        } else {
          $permisos = $aclgrupo->permisos();
          $p2 = [];
            foreach ($permisos as $p) {
                $p2[$p->controlador_id] = explode(',', $p->permisos);
            }
            $listado = Permission::modulos();
            $listado = array_map(function ($controlador) use ($p2) {
                $controlador->permisos = explode(',', $controlador->permisos);
                $controlador->permisos = array_map(function ($accion) use ($controlador, $p2) {
                    $checked = false;
                    if (isset($p2[$controlador->id])) {
                        if (in_array($accion, $p2[$controlador->id])) {
                            $checked = true;
                        }
                    }
                    return (object) [
                        'checked' => $checked,
                        'accion'  => $accion,
                    ];
                }, $controlador->permisos);
                return $controlador;
            }, $listado->toArray());
            return view('acl.permissions.grupo_permisos', compact('aclgrupo', 'listado'));
        }
    }
    public function crearRol(Request $request)
    {
      $form = Formity::instance('rol');
      if ($request->isMethod('post')) {
        if($form->valid()) {
          $data = $form->data();
            $e = new ACLRol;
            $e->rotulo = $data->rotulo;
            $e->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'Con exito',
                'refresh' => true,
                'data'    => [
                    'id'    => $e->id,
                    'value' => $data->rotulo,
                ],
            ]);
        } else {
          return response()->back();
        }
        } else {
            return view('acl.permissions.rol', compact('form'));
        }
    }
    public function RolEdit(Request $request, ACLRol $rol)
    {
      $form = Formity::instance('rol');
      if ($request->isMethod('post')) {
        if($form->valid()) {
          $data = $form->data();
            $rol->update([
                'rotulo' => $data->rotulo,
            ]);
            return response()->json([
                'status'  => 'success',
                'message' => 'Con exito',
                'refresh' => true,
                'data'    => [
                ],
            ]);
        } else {
          return response()->back();
        }
      } else {
        $form->setPreData($rol->toArray());
            return view('acl.permissions.rol', compact('rol','form'));
        }
    }
    public function crearGrupo(Request $request)
    {
      $form = Formity::instance('grupo');
      if ($request->isMethod('post')) {
        if($form->valid()) {
          $data = $form->data();
            $e = new ACLGrupo;
            $e->nombre      = $data->nombre;
            $e->descripcion = $data->descripcion;
            $e->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'Con exito',
                'refresh' => true,
                'data'    => [
                    'id'    => $e->id,
                    'value' => $data->nombre,
                ],
            ]);
        } else {
          return response()->back();
        }
        } else {
            return view('acl.permissions.grupo', compact('form'));
        }
    }
    public function GrupoEdit(Request $request, ACLGrupo $grupo)
    {
      $form = Formity::instance('grupo');
      if ($request->isMethod('post')) {
        if($form->valid()) {
          $data = $form->data();
            $grupo->update([
                'nombre'      => $data->nombre,
                'descripcion' => $data->descripcion,
            ]);
            return response()->json([
                'status'  => 'success',
                'message' => 'Con exito',
                'refresh' => true,
                'data'    => [
                ],
            ]);
        } else {
          return response()->back();
        }
      } else {
        $form->setPreData($grupo->toArray());
            return view('acl.permissions.grupo', compact('grupo','form'));
        }
    }
    public function ControladorEdit(Request $request, ACLControlador $controlador)
    {
      $form = Formity::instance('controlador');
      if ($request->isMethod('post')) {
        if($form->valid()) {
          $data = $form->data();
            $controlador->update([
                'rotulo' => $data->rotulo,
                'link'   => $data->link,
                'permisos' => '{"' . $data->permisos . '"}',
                'controlador_padre_id' => $data->controlador_padre_id,
                'visible'              => $data->visible,
            ]);
            return response()->json([
                'status'  => 'success',
                'message' => 'Con exito',
                'refresh' => true,
                'data'    => [
                ],
            ]);
        } else {
          return resposne()->back();
        }
        } else {
            $controlador->permisos = preg_replace('/[\}\{]/', "", $controlador->permisos);
            $form->setPreData($controlador->toArray());
            return view('acl.permissions.controlador', compact('controlador', 'form'));
        }
    }
    public function crearControlador(Request $request)
    {
            $form = Formity::instance('controlador');
            if ($request->isMethod('post')) {
              if($form->valid()) {
                $data = $form->data();
                $permisos = explode(',', $data->permisos);
            $permisos = array_map(function ($n) {
                return strtolower(trim($n));
            }, $permisos);

            $e = new ACLControlador;
            $e->rotulo   = $data->rotulo;
            $e->link     = $data->link;
            $e->controlador_padre_id = $data->controlador_padre_id;
            $e->permisos = '{"' . implode('","', $permisos) . '"}';
            $e->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'Con exito',
                'refresh' => true,
                'data'    => [
                    'id'    => $e->id,
                    'value' => $data->rotulo,
                ],
            ]);
              } else {
                return response()->back();
              }
        } else {
            return view('acl.permissions.controlador', compact('form'));
        }
    }
}

