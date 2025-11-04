<?php
require '../../config/config.php';
require '../../vendor/fpdf186/fpdf.php';

// Fetch all medical records
$sql = "SELECT mr.*, i.first_name, i.last_name 
        FROM medical_records mr
        JOIN inmates i ON mr.inmate_id = i.id
        ORDER BY i.last_name, i.first_name, mr.visit_date DESC";
$result = $conn->query($sql);

class PDF extends FPDF
{
    // Page header
    function Header()
    {
        $this->SetFont('Arial','B',12);
        $this->Cell(0,10,'Inmate Medical Records',0,1,'C');
        $this->Ln(10);
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
            $pdf->Ln(10);
        }
        $current_inmate = $inmate_name;
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0,10,$current_inmate,0,1);
        $pdf->SetFont('Arial','',10);
    }

    $pdf->Cell(30,7,'Visit Date:',0,0);
    $pdf->Cell(0,7,$row['visit_date'],0,1);
    $pdf->Cell(30,7,'Visit Type:',0,0);
    $pdf->Cell(0,7,$row['visit_type'],0,1);
    $pdf->Cell(30,7,'Diagnosis:',0,0);
    $pdf->MultiCell(0,7,$row['diagnosis'],0,1);
    $pdf->Cell(30,7,'Treatment:',0,0);
    $pdf->MultiCell(0,7,$row['treatment'],0,1);
    $pdf->Cell(30,7,'Medication:',0,0);
    $pdf->MultiCell(0,7,$row['medication'],0,1);
    $pdf->Ln(5);
}

$conn->close();
$pdf->Output();
?>
