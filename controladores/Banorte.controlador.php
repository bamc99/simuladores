<?php 

class Banorte {
    
    static public function calculoTir($montantes){

        $ret = -1000000000.0;
        $juros_inicial = -1.0;
        $juros_medio = 0.0;
        $juros_final = 1.0;
        $vpl_inicial = 0.0;
        $vpl_final = 0.0;
        $vf = 0.0;
        $erro = 1e-5;

        for ($i = 0; $i < 100; $i++) {
            $vpl_inicial = Self::vpl($juros_inicial, $montantes);
            $vpl_final = Self::vpl($juros_final, $montantes);
            if (Self::sinal($vpl_inicial) != Self::sinal($vpl_final)){
                break;
            }
            $juros_inicial -= 1.0;
            $juros_final += 1.0;
        };
        $count = 0;
        for (; ;) {
            // Busca por Bisseção
            $juros_medio = ($juros_inicial + $juros_final) / 2.0;
            $vpl_medio = Self::vpl($juros_medio, $montantes);

            if(abs($vpl_medio) <= $erro) {
                // Resultado foi encontrado
                return $juros_medio * 100.0;
            };
            if (Self::sinal($vpl_inicial) == Self::sinal($vpl_medio)) {
                $juros_inicial = $juros_medio;
                $vpl_inicial =Self::vpl($juros_medio, $montantes);
            } else {
                $juros_final = $juros_medio;
                $vpl_final = Self::vpl($juros_medio, $montantes);
            };
            if (++$count > 10000){
                throw "looping inválido";
            }
        };
        return $ret;
    }

    static public function vpl($taxa, $montantes) {
        print_r('||||||||||||||||||||||||||||||');
        $ret = $montantes[0];
        print_r($montantes[0]);
        for ($i = 1; $i < count($montantes); $i++){
            $ret += $montantes[$i] / pow((1.0 + $taxa), $i);
        }
        return $ret;
    }

    static public function sinal($x) {
        return (($x < 0.0) ? -1 : 1 );
    }

    static public function ctrAdquisicionTradicional(){

        /**
         * Introducir (Setters):
         * 
         * 
         * 
         * $gastos infonavit
         * comision Apertura = montoCredito * 1%;
         * avalúo = -------------
         * gastos notariales estimados ----------
         * comision investigacion --------------
         * enganche ------------
         * 
         * 
         */
        $data = array();
        $encabezados = ['Periodo','Fecha','Dias','Tasa','Saldo Inicial', 'Capital', 'Interes', 'Iva de interés', 'Seguro de Vida', 'Seguro de daños y contenidos', 'Comision Directa', 'Aportacion Patronal', 'Aportacion Capital', 'Pago Mensual', 'Saldo Final'];

        // Aportacion patronal y capital afectan directamente al pago capital
        // ISSUE: Valor prestamo lo toma con notacion cientifica, evitar.

        $anios = 20;
        $tasaInput = 9;
        $prestamo = 2250000;
        $valorVivienda = 2500000;
        $porcentajeGastosNotariales = 4;
        $programa = '';
        $enganche = $valorVivienda-$prestamo;


        $comisionDiferida = 399;
        $factorSeguroVida = .0006;
        $factorSeguroDanios = .0003;
        $valorDestructible = $valorVivienda*.7;


        // Avaluo
        $avaluo = 0;
        switch ($valorVivienda) {
            case $valorVivienda<1000001:
                $tarifa = 3;
                $minimo = 1200;
                break;
            case $valorVivienda<5000001:
                $tarifa = 2.5;
                $minimo = 3000;
                break;
            case $valorVivienda<10000001:
                $tarifa = 2;
                $minimo = 12500;
                break;
            case $valorVivienda<20000000:
                $tarifa = 1.5;
                $minimo = 20000;
                break;
            case $valorVivienda>20000000:
                $tarifa = 1;
                $minimo = 30000;
                break;
            default:
                $tarifa = 0;
                $minimo = 0;
                break;
        }
        
        $avaluo = ($tarifa/1000)*$valorVivienda;
        $avaluoConIva = $avaluo*1.16;
        // Termina Avaluo

        // GastosNotariales
        $gastosNotariales = ($valorVivienda)*($porcentajeGastosNotariales/100);

        // Comision Investigacion
        switch ($programa) {
            case 'liquidez':
                $comisionInvestigacion = 500;
                break;
            case 'terrenos':
                $comisionInvestigacion = 500;
                break;
            default:
                $comisionInvestigacion = 750;
                break;
        }

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
        $montantes = array();
        
        $cat0 = -$prestamo+($prestamo*.01)+$avaluo+($comisionInvestigacion/1.16);
        $totalPagosTir = 0;


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

                // $pagoParaTir = $pagoTotalSimulado+$pagoSeguroVida+($pagoSeguroDanios/1.16)+$comisionDiferida;
              


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
            

            $seguroDeVidaTir = $factorSeguroVida*$saldoInicial;
            $seguroDeDaniosTir = $valorDestructible*$factorSeguroDanios;

            
            // print_r('-----------------------------');
            // print_r($seguroDeDaniosTir);
            // print_r('-----------------------------');
            $pagoParaTir = $pagoTotalSimulado+$seguroDeVidaTir+($seguroDeDaniosTir/1.16)+$comisionDiferida;

            array_push($montantes, $totalPagosTir+$cat0);
            print_r($pagoParaTir);
            $totalPagosTir = $totalPagosTir+$pagoParaTir;
            echo('<hr>');
            
            // Todos pagos tir - inversion


        }

        print_r($totalPagosTir+$cat0);


        $export = array('encabezados'=>$encabezados,'data'=>$data);
        print_r(Banorte::calculoTir($montantes));
        // return $export;

    }


}


?>