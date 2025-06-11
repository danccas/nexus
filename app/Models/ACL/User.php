<?php

namespace App\Models\ACL;

#use Illuminate\Contracts\Auth\MustVerifyEmail;
#use Illuminate\Foundation\Auth\User as Authenticatable;
use Core\DB;
use Core\Model;
use App\Auth;
use Core\Identify;

class User extends Identify {
  protected $connection = 'interno';
  protected $table = 'public.usuario';
    protected $primaryKey = 'id';

  const UPDATED_AT = null;
    const CREATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','displayName','mail', 'usuario', 'clave','tenant_id','last_sesion','habilitado','rotulo','permisos'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
      ];

//    public function getAuthIdentifier() {
//        return $this->getKey();
    //    }
    public function permisos() {
        return db()->get("
            SELECT
                UG.grupo_id
            FROM public.acl_usuario_grupo UG
            WHERE UG.usuario_id = " . $this->id . " AND UG.eliminado = 0");
    }
    public function refreshLastSesion() {
      $this->update([
        'last_sesion' => db()->raw('now()')
      ]);
    }
    public function getAuthPassword() {
      return $this->clave;
    }
    public function username() {
      return '@' . $this->usuario;
    }
    public function tenants() {
      return $this->hasMany('App\Models\Empresa', 'tenant_id', 'tenant_id')->get();
    }
    public function allow($tipo, $externo = null) {
      return (collect(db()->get("SELECT osce.fn_usuario_permiso(:id, :tipo, :externo) estado", [
        'id'      => Auth::user()->id,
        'tipo'    => $tipo,
        'externo' => $externo
      ]))->first())->estado;
    }
    public function byId($id) {
      return (collect(db()->get("SELECT osce.fn_usuario_rotulo(:id) user", [
        'id' => $id,
      ]))->first())->user;
    }
    public static function empresas() {
      return collect(db()->get("
        SELECT id, razon_social
        FROM osce.empresa E
        WHERE E.tenant_id = :tenant
        ORDER BY 2 ASC
      ", [
        'tenant' => Auth::user()->tenant_id
      ]));
    }
    public static function perfiles($empresa_id = null, $id_usuario = null, $correos = null) {
      return collect(db()->get("SELECT * FROM osce.fn_usuario_perfiles(:tenant, :id, :empresa, :correos)", [
        'tenant'  => Auth::user()->tenant_id,
        'id'      => $id_usuario == null ? Auth::user()->id :  $id_usuario ,
        'empresa' => $empresa_id,
        'correos' => $correos,
      ]));
    }
    public static function space() {
      return ceil(((disk_total_space('/') - disk_free_space('/')) * 100) / disk_total_space('/'));
    }
    public static function perfil($id, $correo_id = null) {
      return collect(db()->get("
        SELECT
          UE.linea, UE.anexo, UE.celular, UE.cargo, UE.correo,
        	E.color_primario, E.logo_head logo,
          C.ex_user_id, C.ex_access_token,
          (SELECT CC.cid FROM osce.correo CC WHERE CC.id = :cid LIMIT 1) correo_cid,
          (SELECT CC.asunto FROM osce.correo CC WHERE CC.id = :cid LIMIT 1) correo_asunto
        FROM public.usuario_empresa UE
        LEFT JOIN osce.empresa E ON E.id = UE.empresa_id
        LEFT JOIN osce.credencial C ON C.id = UE.credencial_id
        WHERE UE.id = :id AND UE.tenant_id = :tenant AND (:user = ANY(UE.usuario_id) OR UE.usuario_id IS NULL)", [
        'id'      => $id,
        'tenant'  => Auth::user()->tenant_id,
        'user'    => Auth::user()->id,
        'cid'     => $correo_id,
      ]))->first();
    }
    public static function facturas_visibles($out) {
      $rp  = db()->collect("
        SELECT *, (COALESCE(F.monto_pagado, 0) = F.monto) es_pagado
        FROM public.factura F
        WHERE F.tenant_id = :tenant AND (F.saldo_a_favor < 0 OR F.fecha_vencimiento <= NOW())
        	AND (F.fecha_pago >= NOW() - INTERVAL '5' DAY OR F.fecha_pago IS NULL)
        ORDER BY F.fecha_emision ASC
        LIMIT 4
      ", [
        'tenant' => Auth::user()->tenant_id,
      ]);
      $out = $rp->execute;
      return static::hydrate($rp->toArray());
    }
    public static function estadisticas($id = null) {
      $id = $id ?? Auth::user()->id;
      return collect(db()->get("
        SELECT
          SUM((CASE WHEN P.id IS NOT NULL THEN C.monto ELSE 0 END)) monto,
          SUM((CASE WHEN D.finalizado_el IS NOT NULL AND D.revisado_status IS TRUE AND C.elaborado_por = :user THEN 1 ELSE 0 END)) elaborados,
          SUM((CASE WHEN C.propuesta_el IS NOT NULL AND C.propuesta_por = :user THEN 1 ELSE 0 END)) enviados,
          SUM((CASE WHEN P.id IS NOT NULL AND C.elaborado_por = :user THEN 1 ELSE 0 END)) ganados
      	FROM public.acl_tenant T
        LEFT JOIN osce.cotizacion C ON C.tenant_id = T.id AND C.elaborado_por IS NOT NULL
          AND C.propuesta_el >= DATE_TRUNC('month', NOW())
        LEFT JOIN osce.documento D ON D.id = C.documento_id AND D.finalizado_el IS NOT NULL
        LEFT JOIN osce.proyecto P ON P.cotizacion_id = C.id
        WHERE T.id = :tenant
        GROUP BY T.id
        LIMIT 100", [
        'tenant' => Auth::user()->tenant_id,
        'user'   => $id,
      ]))->first();
    }
    public static function search($term ) {
      $term = strtolower(trim($term));
      return  static::where( function ($query ) use( $term ) {
        $query->WhereRaw('LOWER(usuario) LIKE ?', ["%{$term}%"]);
      })->where('habilitado', true);
    }
    static function permitidos() {
      return db()->get("SELECT * FROM public.usuario WHERE tenant_id = :tid", [
        'tid' => Auth::user()->tenant_id,
      ]);
      return static::where('tenant_id', Auth::user()->tenant_id)->orderBy('usuario','ASC')->get();
    }
    static function habilitados() {
      return static::where('habilitado', true)->orderBy('usuario','ASC')->get();
    }
    public function metricas_resumen() {
      return db()->expire(1)->first("
SELECT
	percentile_cont(0.9) within group (order by P.cantidad asc) as percentil,
	CEIL(AVG(P.cantidad)) promedio,
	AVG(P.duracion_promedio) duracion_promedio,
	percentile_cont(0.9) within group (order by P.duracion_promedio asc) as duracion_percentil,
	SUM(P.cantidad) total
FROM (
SELECT fecha::date fecha, COUNT(D.id) cantidad,
	AVG(D.elaborado_hasta - D.elaborado_desde) duracion_promedio,
	percentile_cont(0.9) within group (order by (D.elaborado_hasta - D.elaborado_desde) asc) as duracion_percentil
FROM generate_series((NOW() - INTERVAL '30' DAY)::date, NOW()::date, '1 day'::interval) fecha
LEFT JOIN osce.documento D ON D.finalizado_el IS NOT NULL AND D.finalizado_por = :user AND D.finalizado_el::date = fecha::date AND D.tenant_id = :tid
GROUP BY fecha::date
ORDER BY 1 ASC) P
      ", ['user' => $this->id, 'tid' => user()->tenant_id]);
    }
    public function metricas_aprobados() {
      return db()->expire(1)->get("
SELECT fff::date fecha, COUNT(C.id) cantidad
FROM generate_series((NOW() - INTERVAL '30' DAY)::date, NOW()::date, '1 day'::interval) fff
LEFT JOIN osce.oportunidad C ON C.aprobado_el IS NOT NULL AND C.aprobado_el::date = fff::date AND C.aprobado_por = :user AND C.tenant_id = :tid
GROUP BY fff::date
ORDER BY 1 ASC
", ['user' => $this->id, 'tid' => user()->tenant_id]);
    }
    public function metricas_rechazados() {
      return db()->expire(1)->get("
SELECT fff::date fecha, COUNT(C.id) cantidad
FROM generate_series((NOW() - INTERVAL '30' DAY)::date, NOW()::date, '1 day'::interval) fff
LEFT JOIN osce.oportunidad C ON C.rechazado_el IS NOT NULL AND C.rechazado_el::date = fff::date AND C.rechazado_por = :user AND C.tenant_id = :tid
GROUP BY fff::date
ORDER BY 1 ASC
", ['user' => $this->id, 'tid' => user()->tenant_id]);
    }
    public function metricas_participaciones() {
      return db()->expire(1)->get("
SELECT fff::date fecha, COUNT(C.id) cantidad
FROM generate_series((NOW() - INTERVAL '30' DAY)::date, NOW()::date, '1 day'::interval) fff
LEFT JOIN osce.cotizacion C ON C.participacion_el IS NOT NULL AND C.participacion_el::date = fff::date AND C.participacion_por = :user AND C.tenant_id = :tid
GROUP BY fff::date
ORDER BY 1 ASC
", ['user' => $this->id, 'tid' => user()->tenant_id]);
    }
    public function metricas_elaborados() {
      return db()->expire(1)->get("
SELECT fecha::date fecha, COUNT(D.id) cantidad
FROM generate_series((NOW() - INTERVAL '30' DAY)::date, NOW()::date, '1 day'::interval) fecha
LEFT JOIN osce.documento D ON D.finalizado_el IS NOT NULL AND D.finalizado_por = :user AND D.finalizado_el::date = fecha::date AND D.tenant_id = :tid
GROUP BY fecha::date
ORDER BY 1 ASC
", ['user' => $this->id, 'tid' => user()->tenant_id]);
    
    }
    public function metricas_precios() {
      return db()->expire(1)->get("
    SELECT fff::date fecha, COUNT(C.id) cantidad
FROM generate_series((NOW() - INTERVAL '30' DAY)::date, NOW()::date, '1 day'::interval) fff
LEFT JOIN osce.cotizacion C ON C.precio_por IS NOT NULL AND C.monto_el::date = fff::date AND C.precio_por = :user AND C.tenant_id = :tid
GROUP BY fff::date
ORDER BY 1 ASC
", ['user' => $this->id, 'tid' => user()->tenant_id]);
    }
    public function metricas_propuestas() {
      return db()->expire(1)->get("
SELECT fff::date fecha, COUNT(C.id) cantidad
FROM generate_series((NOW() - INTERVAL '30' DAY)::date, NOW()::date, '1 day'::interval) fff
LEFT JOIN osce.cotizacion C ON C.propuesta_el IS NOT NULL AND C.propuesta_el::date = fff::date AND C.propuesta_por = :user AND C.tenant_id = :tid
GROUP BY fff::date
ORDER BY 1 ASC
", ['user' => $this->id, 'tid' => user()->tenant_id]);
    }

    public function metricas_perdidos() {
      return db()->expire(1)->get("
SELECT
	F.estado,
	COUNT(F.estado) cantidad
FROM (
SELECT O.codigo, (CASE WHEN P.id IS NOT NULL THEN 'GANADO' ELSE COALESCE(C.perdido_con, 'POR REVISAR') END) estado
FROM osce.oportunidad O
JOIN osce.cotizacion C ON C.oportunidad_id = O.id
JOIN osce.documento D ON D.id = C.documento_id AND D.finalizado_por = :user
LEFT JOIN osce.proyecto P ON P.cotizacion_id = C.id
WHERE O.tenant_id = :tid
ORDER BY D.id DESC) F
GROUP BY F.estado
ORDER BY 1 ASC
", ['user' => $this->id, 'tid' => user()->tenant_id]);
    }
}
