<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$this->load->library("PHPExcel");
//require_once('./assets/qrcode/qrcode.php');
//$this->load->view('Excel/PHPExcel');
$objPHPExcel = new PHPExcel();
$sheet = $objPHPExcel->getActiveSheet();
//$sheet->setCellValue('A1', 'ID');
//$sheet->mergeCells('A1:A2');
//$sheet->setCellValue('B1', 'Name');
//$sheet->setCellValue('C1', 'Date');
//$sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);


//MEWARNAI HEADER UTAMA LALU MERGE DITENGAH-TENGAH
//$sheet->mergeCells('A1:G1');
//$sheet->setCellValue('A1','DATA PHPEXCEL DARI MYSQL');
//$sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


/*
$default_border = array(
    'style' => PHPExcel_Style_Border::BORDER_THIN,
    'color' => array('rgb'=>'1006A3')
);
$style_main_header = array(
    'borders' => array(
        'bottom' => $default_border,
        'left' => $default_border,
        'top' => $default_border,
        'right' => $default_border,
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb'=>'1100FF'),
    ),
    'font' => array(
        'bold' => true,
    )
);

$style_header = array(
    'borders' => array(
        'bottom' => $default_border,
        'left' => $default_border,
        'top' => $default_border,
        'right' => $default_border,
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb'=>'47AF5C'),
    ),
    'font' => array(
        'bold' => true,
    )
);

$sheet->getStyle('A1')->applyFromArray( $style_main_header );
$sheet->getStyle('A2')->applyFromArray( $style_header );
$sheet->getStyle('B2')->applyFromArray( $style_header );
$sheet->getStyle('C2')->applyFromArray( $style_header );
*/

$sheet->setCellValue('A1', 'NO_ENTRY');
$sheet->setCellValue('B1', 'KODE_BRG');
$sheet->setCellValue('C1', 'SHIFTS');
$sheet->setCellValue('D1', 'LINES');
$sheet->setCellValue('E1', 'MESIN');
$sheet->setCellValue('F1', 'NOPO');
$sheet->setCellValue('G1', 'NOLOT');
$sheet->setCellValue('H1', 'QTYBOX');
$sheet->setCellValue('I1', 'QTYPCS');
$sheet->setCellValue('J1', 'QTYBERAT');

$bar = 2;
$kol = 0;

foreach($rows->result() as $data) {
    
    //$sheet->setCellValueByColumnAndRow($kol,   $bar, 'entry');
    $sheet->setCellValueExplicitByColumnAndRow($kol+1, $bar, $data->t_po_detail_item, PHPExcel_Cell_DataType::TYPE_STRING);
    $sheet->setCellValueExplicitByColumnAndRow($kol+2, $bar, $data->t_process_shif, PHPExcel_Cell_DataType::TYPE_STRING);
    $sheet->setCellValueExplicitByColumnAndRow($kol+3, $bar, $data->m_machine_lines, PHPExcel_Cell_DataType::TYPE_STRING);
    $sheet->setCellValueExplicitByColumnAndRow($kol+4, $bar, $data->m_machine_mac, PHPExcel_Cell_DataType::TYPE_STRING);
    $sheet->setCellValueExplicitByColumnAndRow($kol+5, $bar, $data->t_po_detail_no, PHPExcel_Cell_DataType::TYPE_STRING);
    $sheet->setCellValueExplicitByColumnAndRow($kol+6, $bar, $data->nolot, PHPExcel_Cell_DataType::TYPE_STRING);
    $sheet->setCellValueByColumnAndRow($kol+7, $bar, $data->qtybox);
    $sheet->setCellValueByColumnAndRow($kol+8, $bar, $data->qtypcs);
    $sheet->setCellValueByColumnAndRow($kol+9, $bar, $data->qtyberat);
    
    $bar++;
}

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Data.xls"');
header('Cache-Control: max-age=0');

// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;

?>
