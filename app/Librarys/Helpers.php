<?php

namespace App\Librarys;

class Helpers
{
    static function Function_Placa($p = "")
    {
        if (empty($p)) {
            return "";
        }
        $rest = substr($p, 3);
        $rest1 = substr($p, 0, 3);
        return $rest1 . "-" . $rest;
    }


    static function Clase_Vehicular2($N)
    {
        if ($N == 1) $r = "Transporte interprovincial (M3)";
        elseif ($N == 2) $r = "Transporte interprovincial (M3)"; //ANARANJADO
        elseif ($N == 3) $r = "Taxis y colectivos (M1)";
        elseif ($N == 4) $r = "Transporte Urbano/Interurbano (M2)";
        elseif ($N == 5) $r = "Taxis y colectivos (M1)";
        elseif ($N == 6) $r = "Transporte Mercancías Remolques (01, 02, 03, 04)";
        elseif ($N == 7) $r = "Transporte mercancia (N1, N2, N3)"; // AMARILLO
        else $r = "Transporte interprovincial (M3)";
        return $r;
    }

    static function Orden_Clase_V()
    {
        $SERVICIO_PASAJERO = array(
            "PNR" => "SERVICIO REGULAR", "PNT" => "SERVICIO TURÍSTICO", "PDR" => "SERVICIO DEPARTAMENTAL", "PIR" => "SERVICIO INTERNACIONAL", "PNW" => "SERVICIO DE TRABAJADORES", "PNC" => "SERVICIO COMUNAL", "PNS" => "SERVICIO SOCIAL", "PNP" => "SERVICIO PRIVADO"
        );

        $SERVICIO_CARGA = array(
            "CNG" => "CARGA DE MERCANCÍAS", "MRP" => "CARGA DE MATERIALES DE RESÍDUOS PELIGROSOS", "CIR" => "CARGA INTERNACIONAL REGULAR"
        );

        $todo = array("Servicio de Pasajero" => $SERVICIO_PASAJERO, "Servicio de Carga" => $SERVICIO_CARGA);
        return $todo;
    }

    static function Clase_Vehicular($N)
    {
        $N = trim($N);
        $SERVICIO_PASAJERO = array(
            "PNR", "PNT", "PDR", "PIR", "PNW", "PNC", "PNS", "PNP"
        );
        $SERVICIO_CARGA = array(
            "CNG", "MRP", "CIR"
        );



        if (in_array($N, $SERVICIO_CARGA, true)) $r = "Transporte mercancia (N1, N2, N3)"; // AMARILLO
        elseif (in_array($N, $SERVICIO_PASAJERO)) $r = "Transporte interprovincial (M3)"; //ANARANJADO

        else $r = "";
        return $r;
    }



    static function setPlaca($nombre, $tipo)
    {
        $class_placa = "";
        $header = "placa-header";
        $serie = "placa-serie";

        switch ($tipo) {
            case "Taxis y colectivos (M1)":
                $class_placa = "placa-tc";
                break;
            case "Transporte Urbano/Interurbano (M2)":
                $class_placa = "placa-tu-i";
                break;
            case "Transporte interprovincial (M3)":
                $class_placa = "placa-tip";
                break;
            case "Vehiculos particulares (M1)":
                $class_placa = "placa-vp";
                break;
            case "Transporte mercancia (N1, N2, N3)":
                $class_placa = "placa-tran-m";
                break;
            case "Placa Gubernamental":
                $class_placa = "placa-gub";
                break;
            case "Transporte Mercancías Remolques (01, 02, 03, 04)":
                $class_placa = "placa-tran-mr";
                break;
            case "Placa Policial":
                $class_placa = "placa-pol";
                break;
            case "Placa de Emergencia":
                $class_placa = "placa-eme";
                break;
            case "Placa de Exhibición":
                $class_placa = "placa-exh";
                break;
            case "Placa Rotativa":
                $class_placa = "placa-rot";
                break;
            case "Placa Temporal":
                $class_placa = "placa-temp";
                break;
            case "Placa de Gracia":
                $class_placa = "placa-gra";
                break;
            case "Moto lineal (L1, L2, L3, L4)":
                $class_placa = "placa-ml";
                break;
            case "Mototaxi (L5)":
                $class_placa = "placa-mtaxi";
                break;
            case "Placa Gubernamental (Vehículos Menores)":
                $class_placa = "placa-mgub";
                break;
        }

        return " <div class='placaxx " . $class_placa . "'><div class='" . $header . "'>
        <div>Peru</div></div> <div class='" . $serie . "'> " . $nombre . "</div></div>";
    }

    static function Frecuencia_veces($N)
    {
        if (empty($N)) return 0;
        elseif ($N == 1) $N = "$N vez";
        else $N = "$N veces";
        return $N;
    }
    static function random_color_part()
    {
        return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
    }

    static function random_color()
    {
        return random_color_part() . random_color_part() . random_color_part();
    }
    static function reiniciar_colores()
    {
        global $colores;
        $colores = ['#285FD3', '#FF8B26', '#FFC533', '#D8F8BC', 'red', 'green', 'purple', 'blue', '#285cc3', '#FddB26', '#FFbb33', '#D83cBC'];
    }
    static function dame_un_color()
    {
        global $colores;

        return !empty($colores) ? array_shift($colores) : random_color();
    }

    static function FormtearFechaTimestamp($FECHA  =  "")
    {
        return empty($FECHA)  ?  ""  :  date("d/m/Y H:i:s",  strtotime(substr($FECHA,  0,  17)));
    }

    static function Alerta_theme($FECHA = "")
    {

        $FECHA = str_replace("/", "-", $FECHA);
        $A['ALERT_color'] = "bg-theme-12";
        $A['ALERT_icon'] = "clock";
        $A['ALERT_MSN'] = "Sin procesar";
        if (!empty($FECHA)) {
            $time = strtotime($FECHA);
            $fecha_sumada_1_year =  strtotime("+1 year");
            if ($time > $fecha_sumada_1_year) {
                $A['ALERT_MSN'] = "Vehículo nuevo";
                $A['ALERT_icon'] = "sun";
                $A['ALERT_color'] = "bg-theme-1";
            } elseif ($time > time()) {
                $A['ALERT_color'] = "bg-theme-9";
                $A['ALERT_icon'] = "award";
                $A['ALERT_MSN'] = "Vigente";
            } elseif ($FECHA == "1987-01-01") {
                $A['ALERT_MSN'] = "No encontrado";
                $A['ALERT_icon'] = "alert-triangle";
                $A['ALERT_color'] = "bg-gray-700";
            } else {
                $A['ALERT_color'] = "bg-theme-6";
                $A['ALERT_MSN'] = "Vencido";
            }
        }
        return $A;
    }



    static function Formateo_datos_vehiculo($detalle, $MSJ_PESAJE = "")
    {

        if (!$detalle) return [];

        $FECHA_F_SOAT = empty($detalle['FECHAFINSOAT']) ? "" : $detalle['FECHAFINSOAT'];
        $FECHA_F_CITV = empty($detalle['FECHAFINCITV']) ? "" : $detalle['FECHAFINCITV'];
        $SOAT = Alerta_theme($FECHA_F_SOAT);
        $CITV = Alerta_theme($FECHA_F_CITV);

        $FECHA_F_SOAT = ($SOAT['ALERT_MSN'] == "No encontrado") ? "" : $FECHA_F_SOAT;
        $FECHA_F_CITV = ($CITV['ALERT_MSN'] == "No encontrado") ? "" : $FECHA_F_CITV;


        $DATA['FECHA'] = fecha($detalle['FECHA'], true);
        $D23 = explode(" ", $DATA['FECHA']);
        $DATA['PLACA_DIA'] = $D23[0];
        $DATA['PLACA_HORA'] = $D23[1] . " " . $D23[2];

        $DATA['FRECUENCIA'] = empty($detalle['FRECUENCIA']) ? 1 : $detalle['FRECUENCIA'];
        $DATA['FRECUENCIA'] = Frecuencia_veces($DATA['FRECUENCIA']);
        $DATA['FIABILIDAD'] = empty($detalle['FIABILIDAD']) ? 0 : round($detalle['FIABILIDAD']) . " %";

        $DATA['BTN_SOAT'] = ' <div class="rounded-md flex items-center px-4 py-1 mb-2 ' . $SOAT['ALERT_color'] . ' text-white"> 
    <i data-feather="' . $SOAT['ALERT_icon'] . '" class="w-6 h-6 mr-2"></i> ' . $SOAT['ALERT_MSN'] . '<br>' . $FECHA_F_SOAT . '</div>';

        $DATA['BTN_CITV'] = ' <div class="rounded-md flex items-center px-4 py-1 mb-2 ' . $CITV['ALERT_color'] . ' text-white">
    <i data-feather="' . $CITV['ALERT_icon'] . '" class="w-6 h-6 mr-2"></i> 
    ' . $CITV['ALERT_MSN'] . '<br>' . $FECHA_F_CITV . '</div>';
        if (strtoupper($MSJ_PESAJE) == "PESAJE") {
            $DATA['VELOCIDAD'] = 'Promedio de pesaje';
        } else {
            $KM = Colores_por_Velocidad($detalle['VELOCIDAD']);
            $DATA['VELOCIDAD'] = '<div class="w-full h-5 bg-gray-400 dark:bg-dark-1 rounded mt-3"><div class="w-' . $KM['NIVEL'] .
                '/4 h-full rounded text-center text-xs text-white" style="background:#' . $KM['color']  . '">' . $detalle['VELOCIDAD'] . ' km</div> </div>';
        }

        return $DATA;
    }

    static function Colores_por_Velocidad($n)
    {
        $OK['color'] = "000";
        $OK['NIVEL'] = 4;
        if ($n <= 10) {
            $OK['NIVEL'] = 2;
            $OK['color'] = "1E41AA";
        } elseif ($n <= 50) {
            $OK['NIVEL'] = 2;
            $OK['color'] = "00B050";
        } else if ($n <= 71) {
            $OK['NIVEL'] = 3;
            $OK['color'] = "FFC100";
        } else if ($n <= 100) {
            $OK['NIVEL'] = 4;
            $OK['color'] = "FD0100";
        }
        return $OK;
    }

    static function Colores_por_Velocidad2($n)
    {
        $color = "000";
        if ($n == '0-40') {
            $color = "00B050";
        } else if ($n == '41-70') {
            $color = "FFC100";
        } else if ($n == '71-100') {
            $color = "FD0100";
        }
        return $color;
    }





    static function Tiempo_EN_Letras($TIEMPO_ANTES, $TIEMPO_DESPUES)
    {
        $etime = $TIEMPO_DESPUES - $TIEMPO_ANTES;
        if ($etime < 1) return '0 segundos';

        $a = array(
            365 * 24 * 60 * 60  =>  'año',
            30 * 24 * 60 * 60  =>  'mes',
            24 * 60 * 60  =>  'dia',
            60 * 60  =>  'hora',
            60  =>  'minuto',
            1  =>  'segundo'
        );
        $a_plural = array(
            'año'   => 'años',
            'mes'  => 'meses',
            'dia'    => 'dias',
            'hora'   => 'horas',
            'minuto' => 'minutos',
            'segundo' => 'segundos'
        );

        foreach ($a as $secs => $str) {
            $d = $etime / $secs;
            if ($d >= 1) {
                $r = round($d);
                $r2 = empty($r) ? 0 : $r;
                $r = ($r > 1) ? $a_plural[$str] : $str;
                return $r2 . ' ' . $r;
            }
        }
    }



    static function Semana($N = "", $Array = "")
    {
        $N_S[] = "Domingo";
        $N_S[] = "Lunes";
        $N_S[] = "Martes";
        $N_S[] = "Miercoles";
        $N_S[] = "Jueves";
        $N_S[] = "Viernes";
        $N_S[] = "Sabado";
        if ($Array == 1) {
            return $N_S;
        }
        return empty($N_S[$N]) ? "" : $N_S[$N];
    }

    static function Mes($N = "", $Array = "")
    {
        $N_M[] = "Enero";
        $N_M[] = "Febrero";
        $N_M[] = "Marzo";
        $N_M[] = "Abril";
        $N_M[] = "Mayo";
        $N_M[] = "Junio";
        $N_M[] = "Julio";
        $N_M[] = "Agosto";
        $N_M[] = "Setiembre";
        $N_M[] = "Octubre";
        $N_M[] = "Noviembre";
        $N_M[] = "Diciembre";
        if ($Array == 1) {
            return $N_M;
        }
        return empty($N_M[$N]) ? "" : $N_M[$N];
    }
    static function getLimiteTexto($T, $N = 147)
    {
        if (empty($T))
            return $T;
        $T = trim(preg_replace('/\s\s+/', ' ', $T));
        if (strlen($T) >= $N) {
            $T = substr($T, 0, $N);
            $T = $T . "...";
        }
        return $T;
    }




    static function Formateo_fecha($fecha = "")
    { /* 01/02/2018 */
        return   empty($fecha) ? "" : date("Y-m-d", strtotime(str_replace("/", "-", $fecha)));
    }


    static function SumarFecha_VIGENTE($fecha = "")
    {
        //Sumarle 1 año a la fecha insertada
        if (empty($fecha)) return "";
        else $fecha = Formateo_fecha($fecha);
        $fecha_actual_SY = strtotime(date("Y-m-d"));
        $fecha_sumada_1 =  strtotime($fecha . " +1 year");
        //verificar si es menor al tiempo actual
        return date("Y-m-d", $fecha_sumada_1);
        /*if ($fecha_sumada_1 <= $fecha_actual_SY) {
            return date("Y-d-m", $fecha_sumada_1);
        }*/
        return "";
    }
    static function ALERTA_VIGENCIA($FECHA = "")
    {
        $ALERTA = 0;
        if (!empty($FECHA)) {
            //Si la fecha es mayor que la tiempo actual.
            if (time() > strtotime($FECHA)) {
                //GENERA EL ESTADO DE LA ALERTA
                $ALERTA = 1;
            }
        }
        return  $ALERTA;
    }

    static function getMetaDescription($T, $N = "")
    {
        if (!$N) {
            $N = "147";
        }
        if (empty($T))
            return $T;
        $T = trim(preg_replace('/\s\s+/', ' ', strip_tags(html_entity_decode($T))));
        if (strlen($T) >= $N) {
            $T = substr($T, 0, $N);
            $T = $T . "...";
        }
        return $T;
    }
    /**
     * @param $n
     * @return string
     * Use to convert large positive numbers in to short form like 1K+, 100K+, 199K+, 1M+, 10M+, 1B+ etc
     */
    static function number_format_short($n)
    {
        if ($n >= 0 && $n < 1000) {
            // 1 - 999
            $n_format = floor($n);
            $suffix = '';
        } else if ($n >= 1000 && $n < 1000000) {
            // 1k-999k
            $n_format = floor($n / 1000);
            $suffix = 'K+';
        } else if ($n >= 1000000 && $n < 1000000000) {
            // 1m-999m
            $n_format = floor($n / 1000000);
            $suffix = 'M+';
        } else if ($n >= 1000000000 && $n < 1000000000000) {
            // 1b-999b
            $n_format = floor($n / 1000000000);
            $suffix = 'B+';
        } else if ($n >= 1000000000000) {
            // 1t+
            $n_format = floor($n / 1000000000000);
            $suffix = 'T+';
        }

        return !empty($n_format . $suffix) ? $n_format . $suffix : 0;
    }


    static function gs_cp($from, $to)
    {
        echo "\nCopiando...\n";
        $cmd = "gsutil cp \"" . $from . "\" \"" . $to . "\"";
        exec($cmd);
        return true;
    }
    static function gs_mv($from, $to)
    {
        $cmd = "gsutil mv \"" . $from . "\" \"" . $to . "\"";
        exec($cmd);
        return true;
    }

    static function gs_rm($file)
    {
        $cmd = "gsutil rm \"" . $file . "\"";
        exec($cmd);
        return true;
    }

    static function generatePassword($length)
    {
        $key = "";
        $pattern = "1234567890abcdefghijklmnopqrstuvwxyz";
        $max = strlen($pattern) - 1;
        for ($i = 0; $i < $length; $i++) {
            $key .= substr($pattern, mt_rand(0, $max), 1);
        }
        return $key;
    }


    static function fcRumbo($RUMBO)
    {
        $rpta = '';
        if ($RUMBO == 0 || $RUMBO == 360) $rpta =  '(NORTE)';
        elseif ($RUMBO > 0 && $RUMBO < 90) $rpta =  '(NORESTE)';
        elseif ($RUMBO == 90) $rpta =  '(ESTE)';
        elseif ($RUMBO > 90 && $RUMBO < 180) $rpta =  '(SURESTE)';
        elseif ($RUMBO == 180) $rpta =  '(SUR)';
        elseif ($RUMBO > 180 && $RUMBO < 270) $rpta =  '(SUROESTE)';
        elseif ($RUMBO == 270) $rpta =  '(OESTE)';
        elseif ($RUMBO > 270) $rpta =  '(NOROESTE)';
        if ($rpta) $rpta = $RUMBO . '=' . $rpta;

        return $rpta;
    }
}
