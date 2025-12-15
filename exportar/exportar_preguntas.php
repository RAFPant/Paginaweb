<?php
require('../fpdf/fpdf.php');
include('../config/conexion.php');

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,utf8_decode('Listado de Preguntas (sin respuestas)'),0,1,'C');
        $this->Ln(5);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',12);

$dimensiones = $conn->query("SELECT * FROM dimensiones");
while($d = $dimensiones->fetch_assoc()) {
    $pdf->SetFont('Arial','B',12);
    $pdf->SetFillColor(230,230,230);
    $pdf->Cell(0,8,utf8_decode($d['nombre']),0,1,'L',true);
    $pdf->Ln(2);

    $preguntas = $conn->query("SELECT * FROM preguntas WHERE id_dimension=".$d['id']);
    if($preguntas->num_rows > 0){
        while($p = $preguntas->fetch_assoc()){
            $pdf->SetFont('Arial','',11);
            $pdf->MultiCell(0,6,utf8_decode("- ".$p['texto']." (".$p['tipo'].")"));

            if($p['tipo'] == 'cerrada'){
                $opciones = $conn->query("SELECT texto FROM opciones_respuesta WHERE id_pregunta=".$p['id']);
                while($o = $opciones->fetch_assoc()){
                    $pdf->SetFont('Arial','I',10);
                    $pdf->Cell(10);
                    $pdf->Cell(0,6,utf8_decode("• ".$o['texto']),0,1);
                }
            }
            $pdf->Ln(2);
        }
    } else {
        $pdf->SetFont('Arial','I',10);
        $pdf->Cell(0,6,utf8_decode("No hay preguntas registradas en esta dimensión."),0,1);
    }
    $pdf->Ln(4);
}

$pdf->Output('D', 'preguntas_sin_respuestas.pdf');
?>
