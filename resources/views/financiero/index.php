@extends('layouts.modern')
@section('content')
<?php function money($a, $b, $c) { return $a; } ?>
<div>
  <ul class="nav justify-content-center nav-pills">
    <li class="nav-item"><a class="nav-link" href="/financiero/reporte">REPORTES</a></li>
    <li class="nav-item"><a class="nav-link" href="?salir">SALIR</a></li>
  </ul>
</div>
<div>
<?php if(!empty($tc)) { ?>
  <b>{{ $tc->fecha }}</b> => <?= $tc->desde ?> => <?= $tc->hasta ?>: <?= $tc->multiplicar ?>/<?= $tc->dividir ?></br>
<?php } ?><br />
</div>

<div class="row is-multiline">
  <div class="column col-4">
    <div class="card mb-3">
      <div class="card-body">
        <div>
        <nexus:tablefy :route="repository.cuentas_debito" height="400px" size="small">
        </nexus:tablefy>
        </div>
      </div>
    </div>
  </div>
  <div class="column col-4">
    <div class="card mb-3">
      <div class="card-body">
        <div>
        <nexus:tablefy :route="repository.cuentas_credito" height="400px" size="small">
        </nexus:tablefy>
        </div>
      </div>
    </div>
  </div>
  <div class="column col-4">
    <div class="card mb-3">
      <div class="card-body">
        <div class="tablefy">
          <table class="table">
<?php foreach($db->get("SELECT * FROM financiero.obtener_cuadro_de_liquidez(NOW()::timestamp)") as $c) { $c = (array) $c; ?>
            <tr>
              <th colspan="4" style="text-align:center;vertical-align:middle;"><?= $c['moneda'] ?></th>
            </tr>
            <tr>
              <th style="text-align:right;">CONTABLE</th>
              <th style="text-align:right;">DISPONIBLE</th>
              <th style="text-align:right;">PROYECTADO</th>
              <th style="text-align:right;">CREDITOS</th>
            </tr>
            <tr>
              <td><?= money($c['contable'], $c['moneda'], 'CONTABLE ' . $c['moneda']) ?></td>
              <td>
                <?= money($c['disponible'], $c['moneda'], 'DISPONIBLE ' . $c['moneda']) ?>
              </td>
              <td>
                <?= money($c['proyectado_disponible'] + $c['disponible'], $c['moneda'], 'PROYECTADO ' . $c['moneda']) ?>
              </td>
              <td>
                <?= money($c['credito_monto'], $c['moneda'], 'CREDITO TOTAL ' . $c['moneda']) ?>
                <?= money($c['credito_consumo'], $c['moneda'], 'CREDITO CONSUMO ' . $c['moneda']) ?>
                <?= money($c['credito_proyectado'], $c['moneda'], 'CREDITO PROYECTADO ' . $c['moneda']) ?>
              </td>
            </tr>
<?php } ?>
          </table>
        </div>
        </div>
        </div>
    <div class="card mb-3">
      <div class="card-body">
        <div class="tablefy">
          <table class="table">
<?php foreach($db->get("SELECT * FROM financiero.obtener_cuadro_de_flujo(NOW()::timestamp)") as $c) { $c = (array) $c; ?>
            <tr>
              <th colspan="5" style="text-align:center;vertical-align:middle;"><?= $c['moneda'] ?></th>
            </tr>
            <tr>
              <th></th>
              <th style="text-align:right">INGRESOS</th>
              <th style="text-align:right">GASTOS</th>
              <th style="text-align:right">FLUJO</th>
            </tr>
            <tr>
              <th>EFECTUADO</th>
              <td><?= money($c['ingreso'], $c['moneda'], 'INGRESO EFECTUADO') ?></td>
              <td><?= money($c['gasto'], $c['moneda'], 'GASTO EFECTUADO') ?></td>
              <td><?= money($c['ingreso'] + $c['gasto'], $c['moneda'], 'FLUJO DEL MES EFECTUADO') ?></td>
            </tr>
            <tr>
              <th>PENDIENTE</th>
              <td><?= money($c['ingreso_pendiente'], $c['moneda'], 'INGRESO PENDIENTE') ?></td>
              <td><?= money($c['gasto_pendiente'], $c['moneda'], 'GASTO PENDIENTE') ?></td>
              <td><?= money($c['ingreso_pendiente'] + $c['gasto_pendiente'], $c['moneda'], 'FLUJO PENDIENTE') ?></td>
            </tr>
            <tr>
              <th>PROYECTADO</th>
              <td><?= money($c['ingreso_proyectado'], $c['moneda'], 'INGRESO PROYECTADO A FIN DE MES') ?></td>
              <td><?= money($c['gasto_proyectado'], $c['moneda'], 'GASTO PROYECTADO A FIN DE MES') ?></td>
              <td><?= money($c['ingreso_proyectado'] + $c['gasto_proyectado'], $c['moneda'], 'FLUJO PROYECTADO A FIN DE MES') ?></td>
            </tr>
<?php } ?>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="column col-12 text-center">
            <a class="btn btn-danger mb-2" href="{{ route('financiero.create_movimiento') }}" data-popup>Registrar nuevo Movimiento</a>
  </div>
  <div class="column col-6">
    <div class="card mb-3">
      <div class="card-body">
        <div>
          <nexus:tablefy :route="financiero.movimientos_pendientes_tablefy" height="1200px" headers:json="[{name:'FECHA',width:80},{name:'MOVIMIENTO',width:300},{name:'MONTO'}]">
          </nexus:tablefy>
        </div>
      </div>
    </div>
  </div>
  <div class="column col-6">
    <div class="card mb-3">
      <div class="card-body">
    <div>
          <nexus:tablefy :route="financiero.movimientos_tablefy" height="1200px" headers:json="[{name:'FECHA',width:80},{name:'MOVIMIENTO',width:300},{name:'MONTO'}]">
          </nexus:tablefy>
    </div>
    </div>
    </div>
  </div>
</div>
</div>
@endsection
