<?php

require('fpdf/fpdf.php');
class PdfController
{
    public function DescargarPDF()
    {
        $pdf = new FPDF();
        $pdf->AddPage();

        $rutaLogo = 'Logo/logo.png';

        
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 50, 'Logo de la Empresa', 0, 1, 'C');

        if (file_exists($rutaLogo)) {
            $pdf->Image($rutaLogo, 80, 40, 50);
        } else {
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, 'Logo no disponible', 0, 1, 'C');
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="ReporteEmpresa.pdf"');

        $pdf->Output('I');
        exit;
    }
}