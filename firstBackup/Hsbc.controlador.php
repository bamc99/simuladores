<?php 

class Hsbc {

    static public function ctrAdquisicionTradicional(){

        $data = array();
        $encabezados = ['Mes', 'Saldo Inicial', 'Pago a capital', 'Intereses', 'Seguro de Vida', 'Seguro de Daños', 'Comisión por Administración', 'Iva de intereses', 'Mensualidad Total', 'Saldo Final', 'Prepago', 'Aportacion Infonavit', 'Tasa de Interes'];

        $anios = 20;
        $tasaInput = 9.99;
        $prestamo = 2250000;
        $valorVivienda = 2500000;

        $comisionDiferida = 250;
        $factorSeguroVida = .00042;
        $factorSeguroDanios = .0003174;

        $saldo = $prestamo;

        $periodos = $anios*12;

        $tasaCuota = $tasaInput/12;
        $pagoTotalSimulado = $prestamo*(pow(1+$tasaCuota/100, $periodos)*$tasaCuota/100)/(pow(1+$tasaCuota/100, $periodos)-1);

        for ($i=1; $i <= $periodos; $i++) { 

            $saldoInicial = $saldo;
            
            
            
            if ($i == 1) {
                $pagoInteres = $saldo*($tasaCuota)/100;
                
                $pagoCapital = $pagoTotalSimulado-($tasaCuota)/100*$saldo;
                
                $pagoSeguroVida = $factorSeguroVida*$saldoInicial;
                
                $pagoSeguroDanios = $factorSeguroDanios*$saldoInicial;
                
                $pagoMensual = $pagoCapital+$pagoInteres+$pagoSeguroVida+$pagoSeguroDanios+$comisionDiferida;
                
                $saldo = $saldo - $pagoCapital;
                
                // Concatenacion
                array_push($data, ['periodo'=>$i,'saldoInicial'=>$saldoInicial, 'pagoCapital'=>$pagoCapital, 'pagoIntereses'=>$pagoInteres, 'seguroVida'=>$pagoSeguroVida, 'seguroDanios'=>$pagoSeguroDanios, 'comisionDiferida'=>$comisionDiferida, 'iva'=>0, 'pagoMensual'=>$pagoMensual,'saldoFinal'=>$saldo, 'aportacionCapital'=>0, 'aportacionPatronal'=>0, 'tasa'=>$tasaInput, ]);
                
            }else{

                
                $pagoInteres = $saldo*($tasaCuota)/100;

                $pagoCapital = $pagoTotalSimulado-($tasaCuota)/100*$saldo;

                $pagoSeguroVida = $factorSeguroVida*$saldoInicial;

                $pagoSeguroDanios = $factorSeguroDanios*$saldoInicial;

                $pagoMensual = $pagoCapital+$pagoInteres+$pagoSeguroVida+$pagoSeguroDanios+$comisionDiferida;

                $saldo = $saldo - $pagoCapital;

                // Concatenacion
                array_push($data, ['periodo'=>$i,'saldoInicial'=>$saldoInicial, 'pagoCapital'=>$pagoCapital, 'pagoIntereses'=>$pagoInteres, 'seguroVida'=>$pagoSeguroVida, 'seguroDanios'=>$pagoSeguroDanios, 'comisionDiferida'=>$comisionDiferida, 'iva'=>0, 'pagoMensual'=>$pagoMensual,'saldoFinal'=>$saldo, 'aportacionCapital'=>0, 'aportacionPatronal'=>0, 'tasa'=>$tasaInput, ]);

            }      

        }

        $export = array('encabezados'=>$encabezados,'data'=>$data);
        return $export;

    }


}


?>