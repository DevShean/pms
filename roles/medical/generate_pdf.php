<?php
require '../../config/config.php';
require '../../vendor/fpdf186/fpdf.php';

// Fetch all medical records
$sql = "SELECT mr.*, i.first_name, i.last_name, i.inmate_id
        FROM medical_records mr
        JOIN inmates i ON mr.inmate_id = i.inmate_id
        ORDER BY i.last_name, i.first_name, mr.record_date DESC";
$result = $conn->query($sql);

class PDF extends FPDF
{
    // Page header
    function Header()
    {
        // Add logo
        $logoPath = dirname(dirname(dirname(__DIR__))) . '/assets/img/logo.png';
        if (file_exists($logoPath)) {
            $this->Image($logoPath, 15, 10, 20);
        }
        
        $this->SetFont('Arial','B',11);
        $this->SetXY(40, 12);
        $this->Cell(0,6,'REPUBLIC OF THE PHILIPPINES',0,1,'C');
        
        $this->SetFont('Arial','',10);
        $this->SetX(40);
        $this->Cell(0,5,'Department of the Interior and Local Government',0,1,'C');
        
        $this->SetFont('Arial','',10);
        $this->SetX(40);
        $this->Cell(0,5,'Bureau of Jail Management and Penology Regional Office VII',0,1,'C');
        
        $this->SetFont('Arial','B',12);
        $this->SetX(40);
        $this->Cell(0,8,'Medical Report',0,1,'C');
        
        $this->SetFont('Arial','',9);
        $this->SetX(40);
        $this->Cell(0,5,'Generated: ' . date('F j, Y'),0,1,'C');
        
        $this->Ln(20);
    }

    // Page footer
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',10);

$current_inmate = '';
while($row = $result->fetch_assoc()) {
    $inmate_name = $row['first_name'] . ' ' . $row['last_name'];
    if ($current_inmate != $inmate_name) {
        if ($current_inmate != '') {
            $pdf->Ln(8);
        }
        $current_inmate = $inmate_name;
        $pdf->SetFont('Arial','B',11);
        $pdf->SetFillColor(200, 220, 255);
        $pdf->Cell(0,8,$current_inmate . ' (ID: ' . $row['inmate_id'] . ')',0,1,'L',true);
        $pdf->SetFont('Arial','',9);
        $pdf->SetFillColor(255, 255, 255);
    }

    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(40,6,'Record Date:',0,0);
    $pdf->SetFont('Arial','',9);
    $pdf->Cell(0,6,date('M j, Y', strtotime($row['record_date'])),0,1);
    
    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(40,6,'Visit Type:',0,0);
    $pdf->SetFont('Arial','',9);
    $pdf->Cell(0,6,$row['visit_type'],0,1);
    
    if (!empty($row['diagnosis'])) {
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(40,6,'Diagnosis:',0,0);
        $pdf->SetFont('Arial','',9);
        $pdf->MultiCell(0,6,$row['diagnosis']);
    }
    
    if (!empty($row['treatment'])) {
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(40,6,'Treatment:',0,0);
        $pdf->SetFont('Arial','',9);
        $pdf->MultiCell(0,6,$row['treatment']);
    }
    
    if (!empty($row['medication'])) {
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(40,6,'Medication:',0,0);
        $pdf->SetFont('Arial','',9);
        $pdf->MultiCell(0,6,$row['medication']);
    }
    
    if (!empty($row['blood_pressure']) || !empty($row['temperature_c']) || !empty($row['pulse_rate'])) {
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(40,6,'Vital Signs:',0,0);
        $pdf->SetFont('Arial','',9);
        $vitals = array();
        if (!empty($row['blood_pressure'])) $vitals[] = 'BP: ' . $row['blood_pressure'];
        if (!empty($row['temperature_c'])) $vitals[] = 'Temp: ' . $row['temperature_c'] . '°C';
        if (!empty($row['pulse_rate'])) $vitals[] = 'Pulse: ' . $row['pulse_rate'] . ' bpm';
        $pdf->Cell(0,6,implode(' | ', $vitals),0,1);
    }
    
    $pdf->Ln(3);
}

$conn->close();
$pdf->Output('D', 'Medical_Report_' . date('Y-m-d') . '.pdf');
?>
