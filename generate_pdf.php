<?php
require_once("dbcontroller.php");
$db_handle = new DBController();

$result = $db_handle->runQuery("SELECT tbl_request.date AS date, tbl_request.validate, tbl_users.userName AS userN FROM tbl_request right JOIN tbl_users ON tbl_request.userid = tbl_users.userId ORDER By tbl_request.validate ASC");

require('fpdf.php');


class PDF extends FPDF
{
// Page header
function Header()
{
    // Logo
    $this->Image('./images/logo-accenture.png',10,6,30);
    // Arial bold 15
    $this->SetFont('Arial','I',10);
    // Move to the right
    $this->Cell(57);
		$tDate=$tDate = date("F j, Y, G:i");
    // Date
    $this->Cell(0, 10, utf8_decode('eTélétravail rapport toutes les demandes: ').$tDate, 0, false, 'C', 0, '', 0, false, 'T', 'M');
    // Line break
    $this->Ln(10);
}

// Page footer
function Footer()
{
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);

    // Page number
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial','B',12);
$pdf->Cell(60,12,'Date', 1);
$pdf->Cell(60,12,utf8_decode('Réponse'), 1);
$pdf->Cell(60,12,'Name', 1);
foreach($result as $row) {
	if (!empty($row['date'])){
		$pdf->SetFont('Arial','',12);
		$pdf->Ln();
		foreach($row as $column)
			$pdf->Cell(60,12,$column,1);
	}

}
$pdf->Output();
?>
