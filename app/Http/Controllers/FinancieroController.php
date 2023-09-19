<?php

namespace App\Http\Controllers;

use Core\Controller;
use Core\Tablefy;
use Core\Request;
use App\Models\Financiero;

class FinancieroController extends Controller
{
    public function index()
    {
      $db = db('financiero');
      $tc = Financiero::tipoCambio();
		  return view('financiero.index', compact('db', 'tc'));
    }
    public function cuentas_debito_tablefy(Request $request) {
      $res = Financiero::cuentasDebito()
        ->appends(request()->input())
        ->map(function($n) {
          $n = (array) $n;
          return array(
    '<div><a href="?cid=' . $n['id'] . '">#' . $n['id'] . ':' . $n['banco'] . ':' . $n['moneda'] . ':' . $n['cuenta'] . '</a></div><small>' . $n['numero'] . ' - Ajustado: ' . ($n['ajustes']) . '</small>',
    implode(',', [$n['contable'], $n['moneda'], 'CONTABLE ACTUAL']),
    implode(',', [$n['disponible'], $n['moneda'], 'DISPONIBLE ACTUAL']),
    implode(',', [$n['proyectado_disponible'] + $n['disponible'], $n['moneda'], 'PROYECTADO DISPONIBLE ACTUAL']),
          );
        })
        ->get();
      return response()->json($res);
    }
    public function cuentas_credito_tablefy(Request $request) {
      $res = Financiero::cuentasCredito()
        ->appends(request()->input())
        ->map(function($n) {
          $n = (array) $n;
          return array(
    '#' . $n['id'] . ':' . $n['banco'] . ' ' . $n['moneda'] . ':' . $n['cuenta'],
    implode(',', [$n['credito_monto'], $n['moneda'], 'CREDITO DE CUENTA']),
    implode(',', [$n['credito_consumo'], $n['moneda'], 'CONSUMO ACTUAL']),
    implode(',', [$n['credito_proyectado'] + $n['credito_consumo'], $n['moneda'], 'CONSUMO PROYECTADO']),
          );
        })
        ->get();
      return response()->json($res);
    }
    public function movimientos_pendientes_tablefy(Request $request) {
      $res = Financiero::tablefyMovimientosPendientes()
        ->appends(request()->input())
        ->map(function($n) {
          $n = (array) $n;
          $tr = [];
          $tr[] = date('d/m/Y', strtotime($n['fecha']));
          $tr[] = '<div>#' . $n['id'] . ': ' . date('h:i A', strtotime($n['fecha'])) . '</div>' .
            '<b>#' . $n['cuenta_id'] . ':' .  $n['banco'] . ':' . $n['moneda'] . ':' . $n['cuenta'] . ': </b> ' . $n['descripcion'];
          $tr[] = $n['monto'] . ' en ' .$n['moneda2'];
          return $tr;
        })
        ->get();
      return response()->json($res);
    }
    public function movimientos_tablefy(Request $request) {
      $res = Financiero::tablefyMovimientos()
        ->appends(request()->input())
        ->map(function($n) {
          $n = (array) $n;
          $tr = [];
          $tr[] = date('d/m/Y', strtotime($n['fecha']));
          $tr[] = '<div>#' . $n['id'] . ': ' . date('h:i A', strtotime($n['fecha'])) . '</div>' .
            '<b>#' . $n['cuenta_id'] . ':' .  $n['banco'] . ':' . $n['moneda'] . ':' . $n['cuenta'] . ': </b> ' . $n['descripcion'];
          $tr[] = $n['monto'] . ' en ' .$n['moneda2'];
          return $tr;
        })
        ->get();
      return response()->json($res);
    }
    public function create_movimiento(Request $request)  {
      $form = Financiero::formMovimiento();
      return view('financiero.create_movimiento', compact('form'));
    }
    public function create_movimiento_store(Request $request) {
      $form = Financiero::formMovimiento();
      if(!$form->isValid()) {
        return response()->back();
      }
    }
}
