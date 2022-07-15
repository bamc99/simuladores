<?php 

class Banorte {

    static public function ctrAdquisicionTradicional(){

        $data = array();
        $encabezados = ['Periodo','Fecha','Dias','Tasa','Saldo Inicial', 'Capital', 'Interes', 'Iva de interés', 'Seguro de Vida', 'Seguro de daños y contenidos', 'Comision Directa', 'Aportacion Patronal', 'Aportacion Capital', 'Pago Mensual', 'Saldo Final'];

        // Aportacion patronal y capital afectan directamente al pago capital
        // ISSUE: Valor prestamo lo toma con notacion cientifica, evitar.

        $anios = 20;
        $tasaInput = 9;
        $prestamo = 2250000;
        $valorVivienda = 2500000;

        $comisionDiferida = 399;
        $factorSeguroVida = .0006;
        $factorSeguroDanios = .0003;
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
        $dif_dias = $dif_dias->days+1;

        $periodos = $anios*12;
        $tasa = $tasaInput/100;
        $fecha_de_pago = $fecha_inicial;

        $tasaCuota = $tasaInput/360*$dias;
        $pagoTotalSimulado = $prestamo*(pow(1+$tasaCuota/100, $periodos)*$tasaCuota/100)/(pow(1+$tasaCuota/100, $periodos)-1);

        for ($i=1; $i <= $periodos; $i++) { 

            $saldoInicial = $saldo;

            if ($i == 1) {
                $pagoInteres = $saldo*($tasaInput/360*$dif_dias)/100;
                // print_r($pagoInteres);
                $strFecha_de_pago = date('Y-m-d', strtotime($fecha_de_pago->format('Y-m-d')));

                $pagoCapital = $pagoTotalSimulado-($tasaInput/360*$dias)/100*$saldo;

                $pagoSeguroVida = (( $factorSeguroVida/30)*$dif_dias)*intval(strval($prestamo));

                $pagoSeguroDanios = (($factorSeguroDanios/30)*$dif_dias)*$valorDestructible;

                $pagoMensual = $pagoCapital+$pagoInteres+$pagoSeguroVida+$pagoSeguroDanios+$comisionDiferida;

                $saldo = $saldo - $pagoCapital;

                // Concatenacion
                array_push($data, ['periodo'=>$i, 'fechaPago'=>$strFecha_de_pago, 'Dias'=>$dif_dias, 'tasa'=>$tasaInput, 'saldoInicial'=>$saldoInicial, 'pagoCapital'=>$pagoCapital, 'pagoIntereses'=>$pagoInteres, 'iva'=>0,'seguroVida'=>$pagoSeguroVida, 'seguroDanios'=>$pagoSeguroDanios, 'comisionDiferida'=>$comisionDiferida, 'aportacionPatronal'=>0, 'aportacionCapital'=>0, 'pagoMensual'=>$pagoMensual,  'saldoFinal'=>$saldo]);

            }else{
                
                $fecha_de_pago = $fecha_de_pago->modify('+1 month');
                $strFecha_de_pago = date('Y-m-d', strtotime($fecha_de_pago->format('Y-m-d')));

                $pagoInteres = $saldo*($tasaInput/360*$dias)/100 ;

                $pagoCapital = $pagoTotalSimulado-($tasaInput/360*$dias)/100*$saldo;

                $pagoSeguroVida = $factorSeguroVida*intval(strval($saldoInicial));

                $pagoSeguroDanios = (($factorSeguroDanios)/30*$dias)*$valorDestructible;

                $pagoMensual = $pagoCapital+$pagoInteres+$pagoSeguroVida+$pagoSeguroDanios+$comisionDiferida;

                $saldo = $saldo - $pagoCapital;

                // Concatenacion
                array_push($data, ['periodo'=>$i, 'fechaPago'=>$strFecha_de_pago, 'Dias'=>$dias, 'tasa'=>$tasaInput, 'saldoInicial'=>$saldoInicial, 'pagoCapital'=>$pagoCapital, 'pagoIntereses'=>$pagoInteres, 'iva'=>0,'seguroVida'=>$pagoSeguroVida, 'seguroDanios'=>$pagoSeguroDanios, 'comisionDiferida'=>$comisionDiferida, 'aportacionPatronal'=>0, 'aportacionCapital'=>0, 'pagoMensual'=>$pagoMensual,  'saldoFinal'=>$saldo]);

            }      

        }

        $export = array('encabezados'=>$encabezados,'data'=>$data);
        return $export;

    }


}


?>