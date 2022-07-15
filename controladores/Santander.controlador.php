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

        $saldo = $prestamo;

        $periodos = $anios*12;

        $tasaCuota = $tasaInput/12;
        $pagoTotalSimulado = $prestamo*(pow(1+$tasaCuota/100, $periodos)*$tasaCuota/100)/(pow(1+$tasaCuota/100, $periodos)-1);

        for ($i=1; $i <= $periodos; $i++) { 

            $saldoInicial = $saldo;
            
            $pagoInteres = $saldo*($tasaCuota)/100;
    
            
            $pagoCapital = $pagoTotalSimulado-($tasaCuota)/100*$saldo;
            
            $pagoSeguroVida = $factorSeguroVida*$saldoInicial;
            
            $pagoSeguroDanios = $factorSeguroDanios*$valorDestructible;
            
            $pagoDeCredito = $pagoCapital + $pagoInteres;

            $pagoMensual = $pagoCapital+$pagoInteres+$pagoSeguroVida+$pagoSeguroDanios+$comisionDiferida;
            
            $saldo = $saldo - $pagoCapital;

            if ($saldo<0) {
                $saldo = 0;
            }
            
            // Concatenacion
            array_push($data, ['periodo'=>$i, 'tasa'=>$tasaInput, 'pagoIntereses'=>$pagoInteres, 'pagoCapital'=>$pagoCapital, 'pagoCredito'=>$pagoDeCredito,'seguroVida'=>$pagoSeguroVida, 'seguroDanios'=>$pagoSeguroDanios, 'comisionDiferida'=>$comisionDiferida, 'iva'=>0, 'pagoMensual'=>$pagoMensual, 'aportacionPatronal'=>0, 'saldoFinal'=>$saldo, 'aportacionCapital'=>0]);

        }

        $export = array('encabezados'=>$encabezados,'data'=>$data);
        return $export;

    }


}


?>