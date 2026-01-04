<?php
require '../../config/config.php';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Drawing\Drawing;

// Fetch all medical records
$sql = "SELECT mr.*, i.first_name, i.last_name, i.inmate_id
        FROM medical_records mr
        JOIN inmates i ON mr.inmate_id = i.inmate_id
        ORDER BY i.last_name, i.first_name, mr.record_date DESC";
$result = $conn->query($sql);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Add logo
$logoPath = dirname(dirname(dirname(__DIR__))) . '/assets/img/logo.png';
if (file_exists($logoPath)) {
    $drawing = new Drawing();
    $drawing->setName('Logo');
    $drawing->setDescription('BJMP Logo');
    $drawing->setPath($logoPath);
    $drawing->setHeight(60);
    $drawing->setWidth(60);
    $drawing->setCoordinates('A1');
    $drawing->setOffsetX(10);
    $drawing->setOffsetY(10);
    $sheet->addDrawing($drawing);
}

// Set up professional header starting from column B (to make room for logo)
$sheet->setCellValue('B1', 'REPUBLIC OF THE PHILIPPINES');
$sheet->mergeCells('B1:J1');
$sheet->getStyle('B1')->getFont()->setBold(true)->setSize(12);
$sheet->getStyle('B1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getRowDimension(1)->setRowHeight(20);

$sheet->setCellValue('B2', 'Department of the Interior and Local Government');
$sheet->mergeCells('B2:J2');
$sheet->getStyle('B2')->getFont()->setSize(10);
$sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getRowDimension(2)->setRowHeight(18);

$sheet->setCellValue('B3', 'Bureau of Jail Management and Penology Regional Office VII');
$sheet->mergeCells('B3:J3');
$sheet->getStyle('B3')->getFont()->setSize(10);
$sheet->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getRowDimension(3)->setRowHeight(18);

$sheet->setCellValue('B4', 'Medical Report');
$sheet->mergeCells('B4:J4');
$sheet->getStyle('B4')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getRowDimension(4)->setRowHeight(22);

$sheet->setCellValue('B5', 'Generated: ' . date('F j, Y \a\t g:i A'));
$sheet->mergeCells('B5:J5');
$sheet->getStyle('B5')->getFont()->setItalic(true)->setSize(9);
$sheet->getStyle('B5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getRowDimension(5)->setRowHeight(16);

// Add empty row for spacing
$sheet->setCellValue('A6', '');

// Set column headers starting at row 7
$sheet->setCellValue('A7', 'Inmate ID');
$sheet->setCellValue('B7', 'Inmate Name');
$sheet->setCellValue('C7', 'Record Date');
$sheet->setCellValue('D7', 'Visit Type');
$sheet->setCellValue('E7', 'Diagnosis');
$sheet->setCellValue('F7', 'Treatment');
$sheet->setCellValue('G7', 'Medication');
$sheet->setCellValue('H7', 'Blood Pressure');
$sheet->setCellValue('I7', 'Temperature (°C)');
$sheet->setCellValue('J7', 'Pulse Rate (bpm)');

// Style header row
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '366092']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
    'border' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
];

for ($col = 'A'; $col <= 'J'; $col++) {
    $sheet->getStyle($col . '7')->applyFromArray($headerStyle);
}

// Add data rows starting at row 8
$row = 8;
while($data = $result->fetch_assoc()) {
    $sheet->setCellValue('A'.$row, $data['inmate_id']);
    $sheet->setCellValue('B'.$row, $data['first_name'] . ' ' . $data['last_name']);
    $sheet->setCellValue('C'.$row, $data['record_date']);
    $sheet->setCellValue('D'.$row, $data['visit_type']);
    $sheet->setCellValue('E'.$row, $data['diagnosis']);
    $sheet->setCellValue('F'.$row, $data['treatment']);
    $sheet->setCellValue('G'.$row, $data['medication']);
    $sheet->setCellValue('H'.$row, $data['blood_pressure'] ?? '');
    $sheet->setCellValue('I'.$row, $data['temperature_c'] ?? '');
    $sheet->setCellValue('J'.$row, $data['pulse_rate'] ?? '');
    
    // Add borders to data rows
    $sheet->getStyle('A'.$row.':J'.$row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    
    $row++;
}

// Auto-size columns
$sheet->getColumnDimension('A')->setWidth(12);
$sheet->getColumnDimension('B')->setWidth(20);
$sheet->getColumnDimension('C')->setWidth(15);
$sheet->getColumnDimension('D')->setWidth(15);
$sheet->getColumnDimension('E')->setWidth(20);
$sheet->getColumnDimension('F')->setWidth(20);
$sheet->getColumnDimension('G')->setWidth(20);
$sheet->getColumnDimension('H')->setWidth(15);
$sheet->getColumnDimension('I')->setWidth(15);
$sheet->getColumnDimension('J')->setWidth(15);

$writer = new Xlsx($spreadsheet);
$filename = 'Medical_Report_' . date('Y-m-d') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'. $filename .'"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
$conn->close();
?>
