<?php

namespace App\Models;

use Core\Model;
use Core\Formity;

class Financiero extends Model
{
    protected $connection = 'financiero';
    protected $table = 'financiero.movimiento';
    protected $fillable = ['id','rotulo','categoria'];


    public static function tablefyMovimientos() {
      return db('financiero')->tablefy("
  SELECT
    DATE(MOV.fecha) as fecha_corta,
    MOV.*,
    array_to_string(MOV.tags, ',') tags,
    C.nombre as cuenta,
    M.nombre as moneda,
    T.nombre as tipo,
    B.nombre as banco,
    S.nombre as sujeto,
    B.color as banco_color,
    CAT.nombre as categoria,
    M2.nombre as moneda2
  FROM financiero.movimiento MOV
  JOIN financiero.cuenta C ON C.id = MOV.cuenta_id
  LEFT JOIN financiero.sujeto S ON S.id = MOV.sujeto_id
  LEFT JOIN financiero.moneda M2 ON M2.id = MOV.moneda_id
  JOIN financiero.moneda M ON M.id = C.moneda_id
  LEFT JOIN financiero.tipo T ON T.id = C.tipo_id
  LEFT JOIN financiero.categoria CAT ON CAT.id = MOV.categoria_id
  LEFT JOIN financiero.banco B ON B.id = C.banco_id
  WHERE MOV.tenant_id = :tenant AND MOV.fecha::date <= :visto_last AND MOV.eliminado IS NULL AND MOV.efectuado IS TRUE
  ORDER BY MOV.fecha ASC, MOV.id ASC", [
        'tenant' => 1,
        'visto_last' => '2023-09-15',
  ]);
    }
    public static function tablefyMovimientosPendientes() {
      return db('financiero')->tablefy("
  SELECT
    DATE(MOV.fecha) as fecha_corta,
    MOV.*,
    array_to_string(MOV.tags, ',') tags,
    C.nombre as cuenta,
    M.nombre as moneda,
    T.nombre as tipo,
    B.nombre as banco,
    S.nombre as sujeto,
    B.color as banco_color,
    CAT.nombre as categoria,
    M2.nombre as moneda2
  FROM financiero.movimiento MOV
  JOIN financiero.cuenta C ON C.id = MOV.cuenta_id
  LEFT JOIN financiero.sujeto S ON S.id = MOV.sujeto_id
  LEFT JOIN financiero.moneda M2 ON M2.id = MOV.moneda_id
  JOIN financiero.moneda M ON M.id = C.moneda_id
  LEFT JOIN financiero.tipo T ON T.id = C.tipo_id
  LEFT JOIN financiero.categoria CAT ON CAT.id = MOV.categoria_id
  LEFT JOIN financiero.banco B ON B.id = C.banco_id
  WHERE MOV.tenant_id = :tenant AND MOV.fecha::date <= :visto_last AND MOV.eliminado IS NULL AND MOV.efectuado IS FALSE
  ORDER BY MOV.fecha ASC, MOV.id ASC", [
        'tenant' => 1,
        'visto_last' => '2023-09-15',
  ]);
    }
    static function reporteAnual() {
      return [];
    }
    static function tipoCambio($fecha = null) {
      return db('financiero')->first("
  SELECT
    TC.*,
    M1.nombre as desde,
    M2.nombre as hasta
  FROM financiero.tipo_cambio TC
  JOIN financiero.moneda M1 ON M1.id = TC.desde_id
  JOIN financiero.moneda M2 ON M2.id = TC.hasta_id
  ORDER BY TC.fecha DESC
  LIMIT 1");
    }

    static function cuentasDebito() {
      return db('financiero')->tablefy("SELECT * FROM financiero.obtener_cuentas_debitos(NOW()::timestamp)");
    }

    static function cuentasCredito() {
      return db('financiero')->tablefy("SELECT * FROM financiero.obtener_cuentas_creditos(NOW()::timestamp)");
    }

    static function formMovimiento() {
      $cuentas = [];
      $form = Formity::getInstance('movimiento');
$form->setUniqueId('nuevo');
$form->setTitle('Transacción');
$form->addField('cuenta_id:Cuenta', 'select')->setOptions($cuentas);
$form->addField('tags:Etiquetas', 'input:text');
$form->addField('fecha', 'input:datetime-local')->setValue(date('Y-m-d H:i:s'));
$form->addField('descripcion', 'textarea:autocomplete')->setOptions(function($form, $field, $term) {
  $term = '%' . $term . '%';
  return db('financiero')->get("
    SELECT
      DISTINCT CONCAT(descripcion, ' x ', monto) as label,
      descripcion as id,
      monto,
      categoria_id,
      moneda_id,
      cuenta_id
    FROM financiero.movimiento
    WHERE LOWER(descripcion) LIKE ?
    AND categoria_id <> 29
    ORDER BY fecha DESC
    LIMIT 10", false, false, array(
    $term
  ));
});
$form->addField('monto', 'decimal')->setMin(-9999999)->setMax(9999999)->setStep(0.0000000001);
$form->addField('moneda_id?:Moneda', 'select')->setOptions(['' => 'Auto']);
$form->addField('efectuado', 'boolean')->setValue(1);
#$form->addField('bloqueado', 'boolean')->setValue(0);
#$form->addField('bloqueado_id?:Desbloqueo', 'select')->setOptions(['' => 'No desbloqueado'] + $bloqueados);
$form->addField('disponible', 'boolean')->setValue(1);
      return $form;
    }
}
