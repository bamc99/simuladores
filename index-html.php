<?php 
require_once('Banorte.controlador.php');
require_once('Hsbc.controlador.php');

$data = Hsbc::ctrAdquisicionTradicional();

// print_r($data);
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Simulador</title>

  </head>

    <body id="page-top">
      <table>
        <thead>
          <tr>
            <th>Periodo</th>
            <th>Fecha</th>
            <th>Dias</th>
            <th>Tasa</th>
            <th>S Inicial</th>
            <th>P Mensual</th>
            <th>P Interes</th>
            <th>P Capital</th>
            <th>IVA</th>
            <th>Seguro de Vida</th>
            <th>Daños</th>
            <th>Comision</th>
            <th>Aportacion Patronal</th>
            <th>Apor Capital</th>
            <th>S. Final</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          foreach ($data as $key => $periodo) {
          ?>
          <tr>
            <td><?= $periodo[0] ?></td>
            <td><?= $periodo[1] ?></td>
            <td><?= $periodo[2] ?></td>
            <td><?= $periodo[3] ?></td>
            <td><?= $periodo[4] ?></td>
            <td><?= $periodo[5] ?></td>
            <td><?= $periodo[6] ?></td>
            <td><?= $periodo[7] ?></td>
            <td><?= $periodo[8] ?></td>
            <td><?= $periodo[9] ?></td>
            <td><?= $periodo[10] ?></td>
            <td><?= $periodo[11] ?></td>
            <td><?= $periodo[12] ?></td>
            <td><?= $periodo[13] ?></td>
            <td><?= $periodo[14] ?></td>
          </tr>
          <?php
          }
          ?>
        </tbody>
      </table>
    </body>
</html>