<?php 

class Santander {

    static public function ctrAdquisicionTradicional(){
        /**
         * Pagos Fijos
         */

        $data = array();
        $encabezados = ['Periodo', 'Tasa de Interés Anual Ordinaria', 'Intereses', 'Amortización', 'Pago de Crédito', 'Seguro de Vida', 'Seguro de Daños', 'Comision por autorización de crédito diferido', 'Iva de Intereses', 'Pago Total', 'Aportaciones Patronales', 'Saldo al Final del Periodo', 'Pagos Anticipados'];

        $anios = 20;
        $tasaInput = 10.65;
        $prestamo = 2250000;
        $valorVivienda = 2500000;

        $comisionDiferida = 290;
        $factorSeguroVida = .000618;
        $factorSeguroDanios = .00037;
        $valorDestructible = $valorVivienda*.7;

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

        $periodos = $anios*12;
        $tasa = $tasaInput/100;
        $fecha_de_pago = $fecha_inicial;

        $tasaCuota = $tasaInput/12;
        $pagoTotalSimulado = $prestamo*(pow(1+$tasaCuota/100, $periodos)*$tasaCuota/100)/(pow(1+$tasaCuota/100, $periodos)-1);

        for ($i=1; $i <= $periodos; $i++) { 

            $saldoInicial = $saldo;
            
            if ($i == 1) {
                $pagoInteres = $saldo*($tasaCuota)/100;
                // print_r($pagoInteres);
                $strFecha_de_pago = date('Y-m-d', strtotime($fecha_de_pago->format('Y-m-d')));
                
                $pagoCapital = $pagoTotalSimulado-($tasaCuota)/100*$saldo;
                
                $pagoSeguroVida = $factorSeguroVida*$saldoInicial;
                
                $pagoSeguroDanios = $factorSeguroDanios*$valorDestructible;
                
                $pagoDeCredito = $pagoCapital + $pagoInteres;
                $pagoMensual = $pagoCapital+$pagoInteres+$pagoSeguroVida+$pagoSeguroDanios+$comisionDiferida;
                
                $saldo = $saldo - $pagoCapital;
                
                // Concatenacion
                array_push($data, ['periodo'=>$i,'saldoInicial'=>$saldoInicial, 'pagoCapital'=>$pagoCapital, 'pagoIntereses'=>$pagoInteres, 'seguroVida'=>$pagoSeguroVida, 'seguroDanios'=>$pagoSeguroDanios, 'comisionDiferida'=>$comisionDiferida, 'iva'=>0, 'pagoMensual'=>$pagoMensual,'saldoFinal'=>$saldo, 'aportacionCapital'=>0, 'aportacionPatronal'=>0, 'tasa'=>$tasaInput, 'pagoCredito'=>$pagoDeCredito]);
                
            }else{
                $fecha_de_pago = $fecha_de_pago->modify('+1 month');
                $strFecha_de_pago = date('Y-m-d', strtotime($fecha_de_pago->format('Y-m-d')));
                
                $pagoInteres = $saldo*($tasaCuota)/100;

                $pagoCapital = $pagoTotalSimulado-($tasaCuota)/100*$saldo;

                $pagoSeguroVida = $factorSeguroVida*$saldoInicial;

                $pagoSeguroDanios = $factorSeguroDanios*$valorDestructible;

                $pagoMensual = $pagoCapital+$pagoInteres+$pagoSeguroVida+$pagoSeguroDanios+$comisionDiferida;

                $saldo = $saldo - $pagoCapital;

                if ($saldo<0) {
                    $saldo = 0;
                }

                // Concatenacion
                array_push($data, ['periodo'=>$i,'saldoInicial'=>$saldoInicial, 'pagoCapital'=>$pagoCapital, 'pagoIntereses'=>$pagoInteres, 'seguroVida'=>$pagoSeguroVida, 'seguroDanios'=>$pagoSeguroDanios, 'comisionDiferida'=>$comisionDiferida, 'iva'=>0, 'pagoMensual'=>$pagoMensual,'saldoFinal'=>$saldo, 'aportacionCapital'=>0, 'aportacionPatronal'=>0, 'tasa'=>$tasaInput, ]);

            }      

        }

        $export = array('encabezados'=>$encabezados,'data'=>$data);
        return $export;

    }


}


?>