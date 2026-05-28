<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../vendor/autoload.php';
require_once("select.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;

try {
    $id_visita = $_GET['id_visita'];
	$db = new sql();		
    $rsIngenieros_asignados = $db->conlista_ingenieros_asignados($id_visita);
    $rsVisitas_sitio = $db->conVisita_id($id_visita);
    $rshoja_trabajo_procedimientos = $db->conlista_hoja_trabajo_procedimientos($id_visita);
    $rshoja_trabajo_activos = $db->conlista_hoja_trabajo_activos($id_visita);
    $rshoja_trabajo = $db->convisitas_sitio_hoja_trabajo_id_visita($id_visita);

    // Crear el objeto Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
    $drawing->setName('Mep');
    $drawing->setDescription('Mep');
    $drawing->setPath('logo-mep 2025.png');
    $drawing->setCoordinates('A1');    
    $drawing->setWorksheet($spreadsheet->getActiveSheet());
    $sheet->getRowDimension(1)->setRowHeight(30);
    $sheet->getStyle('A1:D1')->getAlignment()->setWrapText(true); 
    $sheet->mergeCells('A1:D1');
    $sheet->getStyle('A1:D1')->getAlignment()->setHorizontal('center');
   
    $sheet->setCellValueByColumnAndRow(1, 2, "PROGRAMA NACIONAL DE FORMACIÓN TECNOLÓGICA");
    $sheet->getStyle('A2')->getFont()->setBold(true);
    $sheet->getRowDimension(2)->setRowHeight(25);
    $sheet->getStyle('A2:D2')->getAlignment()->setWrapText(true); 
    $sheet->mergeCells('A2:D2');
    $sheet->getStyle('A2:D2')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14)->setName('Times New Roman');

    $sheet->setCellValueByColumnAndRow(1, 3, " Dirección de Recursos Tecnológicos en Educación");
    $sheet->getStyle('A3')->getFont()->setBold(true);
    $sheet->getRowDimension(3)->setRowHeight(25);
    $sheet->getStyle('A3:D3')->getAlignment()->setWrapText(true); 
    $sheet->mergeCells('A3:D3');
    $sheet->getStyle('A3:D3')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(14)->setName('Times New Roman')->setItalic(true);

    $sheet->setCellValueByColumnAndRow(1, 5, "Informe de Visita Técnica");
    $sheet->getRowDimension(5)->setRowHeight(25);
    $sheet->getStyle('A5:D5')->getAlignment()->setWrapText(true); 
    $sheet->mergeCells('A5:D5');
    $sheet->getStyle('A5:D5')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('A5:D5')->getAlignment()->setVertical('center');
    $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(14)->setName('Times New Roman')->setItalic(true);
    $sheet->getStyle('A5:D5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('286ce6');
    $sheet->getStyle('A5:D5')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);    

    $sheet->setCellValueByColumnAndRow(1, 7, "Nombre del Centro Educativo:");
    $sheet->getRowDimension(7)->setRowHeight(20);
    $sheet->getStyle('A7')->getFont()->setSize(12)->setName('Times New Roman');
    $nombre_institucion = array_column($rsVisitas_sitio, "nombre_institucion");
    $sheet->setCellValueByColumnAndRow(2, 7, implode($nombre_institucion));

    $sheet->setCellValueByColumnAndRow(1, 8, "Fecha de la Visita:");
    $sheet->getRowDimension(8)->setRowHeight(20);
    $sheet->getStyle('A8')->getFont()->setSize(12)->setName('Times New Roman');
    $fecha_visita = array_column($rsVisitas_sitio, "fecha_visita");

    date_default_timezone_set('America/Costa_Rica');		
    $solicitud_Fecha_format = date_create(implode($fecha_visita))->format('d-m-Y');
    $sheet->setCellValueByColumnAndRow(2, 8, $solicitud_Fecha_format);
    
    $sheet->setCellValueByColumnAndRow(1, 9, "Ingenieros Asignados:");    
    $sheet->getStyle('A9')->getFont()->setSize(12)->setName('Times New Roman');

    $txtNombres = "";
    if (!empty($rsIngenieros_asignados)) {
        $nombre = array_column($rsIngenieros_asignados, "nombre");
        $txtNombres = implode(", ", $nombre);
    }

    $sheet->setCellValueByColumnAndRow(2, 9, $txtNombres);
    $sheet->getStyle('B9')->getFont()->setSize(12)->setName('Times New Roman');
 
    $sheet->setCellValueByColumnAndRow(1, 10, "Persona(s) de contacto:");
    $sheet->getRowDimension(10)->setRowHeight(20);
    $sheet->getStyle('A10')->getFont()->setSize(12)->setName('Times New Roman');
    $persona_contacto = array_column($rsVisitas_sitio, "persona_contacto");
    $sheet->setCellValueByColumnAndRow(2, 10, implode($persona_contacto));

    $sheet->setCellValueByColumnAndRow(1, 12, "Motivo de la Visita");
    $sheet->getRowDimension(12)->setRowHeight(25);
    $sheet->getStyle('A12:D12')->getAlignment()->setWrapText(true); 
    $sheet->mergeCells('A12:D12');
    $sheet->getStyle('A12:D12')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('A12:D12')->getAlignment()->setVertical('center');
    $sheet->getStyle('A12')->getFont()->setBold(true)->setSize(14)->setName('Times New Roman')->setItalic(true);
    $sheet->getStyle('A12:D12')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('286ce6');
    $sheet->getStyle('A12:D12')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);    

    $sheet->getStyle('A14')->getFont()->setSize(12)->setName('Times New Roman');    
    $sheet->getStyle('A14:D14')->getAlignment()->setWrapText(true); 
    $sheet->mergeCells('A14:D14');
    $titulo_problema = array_map(fn($item) => trim($item["titulo_problema"]), $rsVisitas_sitio);
    $sheet->setCellValueByColumnAndRow(1, 14, implode($titulo_problema));

    $texEncabezado = "Descripción del Motivo de la Visita:";
    $sheet->setCellValueByColumnAndRow(1, 16, $texEncabezado);   
    $sheet->getStyle('A16:D16')->getAlignment()->setWrapText(true); 
    $sheet->mergeCells('A16:D16');
    $sheet->getStyle('A16')->getFont()->setSize(12)->setName('Times New Roman')->setItalic(true);

    $sheet->getStyle('A18')->getFont()->setSize(12)->setName('Times New Roman');    
    $sheet->getStyle('A18:D18')->getAlignment()->setWrapText(true); 
    $sheet->mergeCells('A18:D18');
    $descripcion_problema = array_map(fn($item) => trim($item["descripcion_problema"]), $rsVisitas_sitio);
    $sheet->setCellValueByColumnAndRow(1, 18, implode($descripcion_problema));

    $texEncabezado = "Resultado Final de los Procedimientos Realizados:";
    $sheet->setCellValueByColumnAndRow(1, 20, $texEncabezado);   
    $sheet->getStyle('A20:D20')->getAlignment()->setWrapText(true); 
    $sheet->mergeCells('A20:D20');
    $sheet->getStyle('A20')->getFont()->setSize(12)->setName('Times New Roman')->setItalic(true);    

    $sheet->getStyle('A22')->getFont()->setSize(12)->setName('Times New Roman');
    $sheet->getRowDimension(22)->setRowHeight(35);
    $sheet->getStyle('A22:D22')->getAlignment()->setWrapText(true); 
    $sheet->mergeCells('A22:D22');
    $visitas_sitio_hoja_trabajo_resultado = array_map(fn($item) => trim($item["visitas_sitio_hoja_trabajo_resultado"]), $rshoja_trabajo);
    $sheet->setCellValueByColumnAndRow(1, 22, implode($visitas_sitio_hoja_trabajo_resultado));

    $sheet->setCellValueByColumnAndRow(1, 24, "Procedimientos Realizados");
    $sheet->getStyle('A24:D24')->getAlignment()->setWrapText(true); 
    $sheet->mergeCells('A24:D24');
    $sheet->getStyle('A24:D24')->getAlignment()->setHorizontal('center');
    $sheet->getStyle('A24:D24')->getAlignment()->setVertical('center');
    $sheet->getStyle('A24')->getFont()->setBold(true)->setSize(12)->setName('Times New Roman')->setItalic(true);

    $contFila = 26;

    if (!empty($rshoja_trabajo_procedimientos)) {
        foreach($rshoja_trabajo_procedimientos as $key => $item_nombreprocedimiento) {
            $sheet->getStyle('A' . $contFila)->getFont()->setSize(12)->setName('Times New Roman');
            $sheet->getStyle('A' . $contFila. ':D' . $contFila)->getAlignment()->setWrapText(true); 
            $sheet->mergeCells('A' . $contFila. ':D' . $contFila);            
            $sheet->setCellValueByColumnAndRow(1, $contFila, $item_nombreprocedimiento["visitas_procedimiento_descripcion"]);
            $contFila = $contFila+1;
        }          
    }

    $contFila = $contFila+1;

    $sheet->setCellValueByColumnAndRow(1, $contFila, "Equipos Atendidos");
    $sheet->getStyle('A' . $contFila. ':D' . $contFila)->getAlignment()->setWrapText(true); 
    $sheet->mergeCells('A' . $contFila. ':D' . $contFila);
    $sheet->getStyle('A' . $contFila. ':D' . $contFila)->getAlignment()->setHorizontal('center');
    $sheet->getStyle('A' . $contFila. ':D' . $contFila)->getAlignment()->setVertical('center');
    $sheet->getStyle('A' . $contFila)->getFont()->setBold(true)->setSize(12)->setName('Times New Roman')->setItalic(true);

    $contFila = $contFila+2;

    if (!empty($rshoja_trabajo_activos)) {
        foreach($rshoja_trabajo_activos as $key => $item_activos) {
            $sheet->getStyle('A' . $contFila)->getFont()->setSize(12)->setName('Times New Roman');
            $sheet->getStyle('A' . $contFila. ':D' . $contFila)->getAlignment()->setWrapText(true); 
            $sheet->mergeCells('A' . $contFila. ':D' . $contFila);            
            $sheet->setCellValueByColumnAndRow(1, $contFila, 
                    $item_activos["clase"] . " ".
                    $item_activos["modelo"] . " ". 
                    $item_activos["marca"] . " ".
                    $item_activos["serial"] . " ". 
                    $item_activos["placa"]);
            $contFila = $contFila+1;
        }          
    }

    $contFila = $contFila+2;

    $textIndicaciones = "Indicaciones Adicionales y/o Recomendaciones:";
    $sheet->getStyle('A' . $contFila)->getFont()->setSize(12)->setName('Times New Roman')->setItalic(true);
    $sheet->getRowDimension($contFila)->setRowHeight(35);
    $sheet->getStyle('A' . $contFila. ':D' . $contFila)->getAlignment()->setWrapText(true); 
    $sheet->mergeCells('A' . $contFila. ':D' . $contFila);
    $sheet->setCellValueByColumnAndRow(1, $contFila, $textIndicaciones);
    
    $contFila = $contFila+2;

    $sheet->getStyle('A' . $contFila)->getFont()->setSize(12)->setName('Times New Roman');
    $sheet->getRowDimension($contFila)->setRowHeight(35);
    $sheet->getStyle('A' . $contFila. ':D' . $contFila)->getAlignment()->setWrapText(true); 
    $sheet->mergeCells('A' . $contFila. ':D' . $contFila);
    $visitas_sitio_hoja_trabajo_indicaciones = array_map(fn($item) => trim($item["visitas_sitio_hoja_trabajo_indicaciones"]), $rshoja_trabajo);
    $sheet->setCellValueByColumnAndRow(1, $contFila, implode($visitas_sitio_hoja_trabajo_indicaciones));

    $contFila = $contFila+5;
    $sheet->setCellValueByColumnAndRow(1, $contFila, "__________________________");
    $contFila = $contFila+1;
    $sheet->setCellValueByColumnAndRow(1, $contFila, "Director(a) Centro Educativo");
    $sheet->getStyle('A' . $contFila)->getFont()->setSize(10)->setName('Times New Roman')->setItalic(true);

    $contFila = $contFila-1;

    if (!empty($rsIngenieros_asignados)) {
        foreach($rsIngenieros_asignados as $key => $item_nombre) {
            $sheet->setCellValueByColumnAndRow(2, $contFila, "__________________________");
            $contFila = $contFila+1;
            $sheet->getStyle('B' . $contFila)->getFont()->setSize(10)->setName('Times New Roman')->setItalic(true);            
            $sheet->setCellValueByColumnAndRow(2, $contFila, $item_nombre["nombre"]);
            $contFila = $contFila+1;
            $sheet->getStyle('B' . $contFila)->getFont()->setSize(8)->setName('Times New Roman')->setItalic(true);
            $sheet->setCellValueByColumnAndRow(2, $contFila, "Ingeniero del Programa de Formación Tecnológica");
            $contFila = $contFila+5;
        }          
    }

    //ancho de columnas
    $sheet->getColumnDimension('A')->setWidth(30);
    $sheet->getColumnDimension('B')->setWidth(40);

    // Exportar a PDF con Mpdf
    $writer = new Mpdf($spreadsheet);
    $nombreDelDocumento = "testPDF.pdf";

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment;filename="'.$nombreDelDocumento.'"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');

    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}