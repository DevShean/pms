<?php
require '../../config/config.php';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Fetch all medical records
$sql = "SELECT mr.*, i.first_name, i.last_name 
        FROM medical_records mr
        JOIN inmates i ON mr.inmate_id = i.inmate_id
        ORDER BY i.last_name, i.first_name, mr.record_date DESC";
$result = $conn->query($sql);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'Inmate');
$sheet->setCellValue('B1', 'Visit Date');
$sheet->setCellValue('C1', 'Visit Type');
$sheet->setCellValue('D1', 'Diagnosis');
$sheet->setCellValue('E1', 'Treatment');
$sheet->setCellValue('F1', 'Medication');

$row = 2;
while($data = $result->fetch_assoc()) {
    $sheet->setCellValue('A'.$row, $data['first_name'] . ' ' . $data['last_name']);
    $sheet->setCellValue('B'.$row, $data['record_date']);
    $sheet->setCellValue('C'.$row, $data['visit_type']);
    $sheet->setCellValue('D'.$row, $data['diagnosis']);
    $sheet->setCellValue('E'.$row, $data['treatment']);
    $sheet->setCellValue('F'.$row, $data['medication']);
    $row++;
}

$writer = new Xlsx($spreadsheet);
$filename = 'medical_records.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'. $filename .'"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
$conn->close();
?>
