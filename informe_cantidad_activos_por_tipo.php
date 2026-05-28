<?php

    include "data.php";

?>

<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8">

    <script src="https://www.google.com/jsapi"></script>

    <style>

        .pie-chart {

            width: 600px;

            height: 400px;

            margin: 0 auto;

        }

        .text-center{

            text-align: center;

        }

    </style>

</head>

<body>

<h2 class="text-center">Clasificaci&oacute;n Global de Activos del Entorno TecnoPresta</h2>

<div id="chartDiv" class="pie-chart"></div>

<div class="text-center">

    <h2>tecnopresta.mep.go.cr</h2>

</div>

<script type="text/javascript">

    window.onload = function() {

        google.load("visualization", "1.1", {

            packages: ["corechart"],

            callback: 'drawChart'

        });

    };

    function drawChart() {

        var data = new google.visualization.arrayToDataTable([

            ['Language', 'Rating'],

            <?php

                while($row = mysqli_fetch_assoc($chartQueryRecords)){

                    echo "['".$row['clase']."', ".$row['n']."],";

                }

            ?>

        ]);


        var options = {

            title: 'Cantidad de Modelos Registrados por Tipo',

        };

  

        var chart = new google.visualization.PieChart(document.getElementById('chartDiv'));

        chart.draw(data, options);

    }

</script>

</body>

</html>
