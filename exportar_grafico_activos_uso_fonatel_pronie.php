<?php

ini_set('display_errors', true); 
require 'vendor/autoload.php';
require_once 'sql/conexion.php';

use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend as ChartLegend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

try {
    $pdo = new \PDO(DB_Str, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    $consultaSQL = "SELECT 
                        SUM(CASE WHEN t_placa.enuso = 1 THEN 1 ELSE 0 END) AS total_en_uso,
                        SUM(CASE WHEN t_placa.enuso = 0 THEN 1 ELSE 0 END) AS total_no_en_uso
                    FROM 
                        t_placa
                    WHERE 
                    t_placa.id_fondos IN (2,7) AND activo=1 AND prestar=1
                    AND (placa <> '' and serial <> '')";
    $sql = $pdo->query($consultaSQL);
    $rs = [];
    $rs_Export = array(array('', ''));
    $count = 0;
    while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
        $rs = array($row['total_en_uso'],$row['total_no_en_uso']);
        array_push($rs_Export,$rs);
        $count=$count+1;
    }    
} catch (\Throwable $th) {
    throw $th;
}

date_default_timezone_set('America/Costa_Rica');
$fecha = date_create('now')->format('m-d-Y');
$nombreDelDocumento = "Activos para donar-".$fecha.".xlsx";

$spreadsheet = new Spreadsheet();
$spreadsheet->getProperties()
            ->setCreator("TecnoPresta.mep.go.cr")
            ->setLastModifiedBy("Mauricio Bermudez Vargas")
            ->setTitle("Graficos Inventario Nacional")
            ->setSubject("Inventario Nacional")
            ->setDescription("Documento generado desde tecnopresta.mep.go.cr")
            ->setKeywords("tecnopresta.mep.go.cr")
            ->setCategory("Graficos");


$worksheet = $spreadsheet->getActiveSheet();
$worksheet->fromArray($rs_Export);

$dataSeriesLabels = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$1', null, 1) // Total
];

$xAxisTickValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$2:$A$'.$count+1, null, $count) // MEP to PNTM
];

$dataSeriesValues = [
    new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$2:$B$'.$count+1, null, $count)
];

// Build the dataseries
$series = new DataSeries(
    DataSeries::TYPE_BARCHART, // plotType
    DataSeries::GROUPING_CLUSTERED, // plotGrouping
    range(0, count($dataSeriesValues) - 1), // plotOrder
    $dataSeriesLabels, // plotLabel
    $xAxisTickValues, // plotCategory
    $dataSeriesValues          // plotValues
);

$series->setPlotDirection(DataSeries::DIRECTION_COL);

// Set the series in the plot area
$plotArea = new PlotArea(null, [$series]);
// Set the chart legend
$legend = new ChartLegend(ChartLegend::POSITION_RIGHT, null, false);

$title = new Title('Activos en uso FONATEL y PRONIE');
$yAxisLabel = new Title('Cantidad de Artículos');

// Create the chart
$chart = new Chart(
    'chart1', // name
    $title, // title
    $legend, // legend
    $plotArea, // plotArea
    true, // plotVisibleOnly
    DataSeries::EMPTY_AS_GAP, // displayBlanksAs
    null, // xAxisLabel
    $yAxisLabel  // yAxisLabel
);

// Set the position where the chart should appear in the worksheet
$chart->setTopLeftPosition('A'.$count+2);
$chart->setBottomRightPosition('H20');

// Add the chart to the worksheet
$worksheet->addChart($chart);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'. $nombreDelDocumento . '"');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->setIncludeCharts(true);
$writer->save('php://output');
$spreadsheet->disconnectWorksheets();
unset($spreadsheet);
exit;

?>