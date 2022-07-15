<?php 

class Scotiabank {

    static public function ctrAdquisicionTradicional(){

        /**
         * Pagos Crecientes
         */

        $data = array();
        $encabezados = ['Periodo', 'Saldo Inicial', 'Erogación', 'Interes', 'Amortización', 'Prepagos a Capital', 'Comision por prepago', 'Aportacion Bimestral (Apoyo Inf)', 'Tasa Aplicada', 'Saldo Final', 'Seguro de Vida', 'Seguro de Daños', 'Pago Mensual', 'Inc. Pago'];

        $anios = 20;
        $tasaInput = 10;
        $prestamo = 2250000;
        $valorVivienda = 2368421.053;

        $comisionDiferida = 0;
        $factorSeguroVida = .000556;
        $factorSeguroDanios = .0003016;

        $tasaTope = 9.5;

        /**
         * Variables de relleno
         */
        $prepagosCapital = 0;
        $comisionPrepagos = 0;
        $aportacionBimestral = 0;
        
        //Verificar Formula 
        $valorDestructible = $valorVivienda*.7;

        // Dia - No Relevante
        $hoy = date("Y-m-d H:i:s");
        $hoyDateTime = new DateTime(date('Y-m-d'));
        $mes_actual_int = date('n');
        $dia_hoy_int = date('j');
        $mes_inicial = '';

        $dia_pago = 3;
        $dias = 30.4;
        $saldo = $prestamo;

        if ($dia_hoy_int>=$dia_pago) {
            $mes_inicial = $mes_actual_int+1;
        }else{
            $mes_inicial = $mes_actual_int;
        }

        $fecha_inicial = new DateTime('2022-'.$mes_inicial.'-3');
        $dif_dias = $hoyDateTime->diff($fecha_inicial);
        $dif_dias = $dif_dias->days+2;

        // Termina Día - No relevante
        /**
         * --------------------------------
         */

        $periodos = $anios*12;
        // 
        $tasa = $tasaInput/100;
        $fecha_de_pago = $fecha_inicial;

        // $tasaCuota = $tasaInput/12;
        // $pagoTotalSimulado = $prestamo*(pow(1+$tasaCuota/100, $periodos)*$tasaCuota/100)/(pow(1+$tasaCuota/100, $periodos)-1);

        // Factor - Cambia por tipo de tasa y tiempo del prestamo, Modificar son Switch o If
        $factorPpm = 8.64;
        $incrementoAnual = 2.60;
        $erogacion = 0;

        $hayIncremento = 0;

        $saldoInicial = $saldo;
        $erogacion = ($saldoInicial/1000*$factorPpm);

        for ($i=1; $i <= $periodos; $i++) { 
            // Calcula Días del mes
            $strMes = date('m', strtotime($fecha_de_pago->format('Y-m-d')));
            $strAnio = date('Y', strtotime($fecha_de_pago->format('Y-m-d')));
            $diasDelMes = cal_days_in_month(CAL_GREGORIAN, intval($strMes), intval($strAnio));

            // Calcular Pago Interes
            $pagoInteres = floatval($tasa/360*$diasDelMes*$saldoInicial);
            
            // Calcular Pago Capital
            $pagoCapital = $erogacion-$pagoInteres;
            if ($erogacion>$saldoInicial) {
                $pagoCapital = $saldoInicial;
                $erogacion = $pagoCapital+$pagoInteres;
            }
            // Calcular pago Seguro de Vida
            $pagoSeguroVida = $saldoInicial*$factorSeguroVida;
            // Calcular Pago Seguro Daños
            $pagoSeguroDanios = $valorVivienda*$factorSeguroDanios;

            // Saldo Final
            $saldoFinal = $saldoInicial-$pagoCapital;

            /**
             * Pago del mes
             */
            $pagoMensual = $pagoCapital+$pagoInteres+$pagoSeguroVida+$pagoSeguroDanios;

            // Add To Array 
            array_push($data, ['periodo'=>$i ,'saldoInicial'=>$saldoInicial, 'erogacion'=>$erogacion, 'pagoInteres' => $pagoInteres, 'pagoCapital'=>$pagoCapital, 'prepagosCapital'=>$prepagosCapital, 'comisionPrepagos'=> $comisionPrepagos, 'aportacionBimestral'=>$aportacionBimestral,'tasa'=>$tasa*100, 'saldoFinal'=>$saldoFinal,'seguroVida'=>$pagoSeguroVida, 'seguroDanios'=>$pagoSeguroDanios, 'pagoMensual'=>$pagoMensual, 'incrementoPago'=>$hayIncremento] );

            // if (condition) {
            //     # code...
            // }

            if ($tasa*100>9.5) {
                if ($i==36) {
                    if ($tasa*100>9.5) {
                        $tasa = ($tasa*100-.25)/100;
                    }
                }elseif ($i>36) {
                    if (($i-36)%12 == 0) {
                        $tasa = (($tasa*100)-.25)/100;
                    }
                }
            }

            if ($i%12==0) {
                $erogacion = $erogacion+(($erogacion)*($incrementoAnual/100));
                $hayIncremento = $incrementoAnual;
            }else{
                $hayIncremento = 0;
            }

            $saldoInicial = $saldoFinal;
            $fecha_de_pago = $fecha_de_pago->modify('+1 month');

            if ($saldoFinal==0) {
                break;
            }

        }


        $export = array('encabezados'=>$encabezados,'data'=>$data);
        return $export;

    }


}


?>