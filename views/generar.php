<?php
// Configurar datos en el PDF
$pdf = $this->pdf;
$pdf->datosEmpresa = $datosEmpresa;
$pdf->datosCliente = $datosCliente;
$pdf->datosFecha = $fechaVenta;
$pdf->detallesVenta = $detallesVenta;

// Función de ayuda para codificación
function pdfText($text) {
    return mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
}

// Encabezado de los detalles del cliente
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, pdfText('Datos del Cliente'), 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, pdfText('Identificacion: ' . $datosCliente['identificacion']), 0, 1, 'L');
$pdf->Cell(0, 5, pdfText('Nombre: ' . $datosCliente['nombre']), 0, 1, 'L');
$pdf->Cell(0, 5, pdfText('Apellido: ' . $datosCliente['apellido']), 0, 1, 'L');
$pdf->Cell(0, 5, pdfText('Teléfono: ' . $datosCliente['telefono']), 0, 1, 'L');
$pdf->Cell(0, 5, pdfText('Dirección: ' . $datosCliente['direccion']), 0, 1, 'L');
$pdf->Ln(10);

// Mostrar el tipo de pago
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, pdfText('Tipo de Pago'), 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, pdfText('Método de pago: ' . $tipoPago), 0, 1, 'L');
$pdf->Ln(10);

// Mostrar fecha de venta
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, pdfText('Fecha de venta'), 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, pdfText('Fecha: ' . $fechaVenta), 0, 1, 'L');
$pdf->Ln(10);

// Encabezado de la tabla de productos
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, pdfText('Detalle de la Venta'), 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(59, 89, 152);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(10, 10, pdfText('N°'), 1, 0, 'C', true);
$pdf->Cell(100, 10, pdfText('Descripción'), 1, 0, 'L', true);
$pdf->Cell(25, 10, pdfText('Cantidad'), 1, 0, 'C', true);
$pdf->Cell(25, 10, pdfText('Precio'), 1, 0, 'C', true);
$pdf->Cell(30, 10, pdfText('Sub Total'), 1, 1, 'C', true);

// Detalles de los productos
$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(0, 0, 0);
$contador = 1;

foreach ($detallesVenta as $row) {
    $pdf->Cell(10, 10, $contador, 1, 0, 'C');
    $pdf->Cell(100, 10, pdfText($row['descripcion']), 1, 0, 'L');
    $pdf->Cell(25, 10, $row['cantidad'], 1, 0, 'C');
    $pdf->Cell(25, 10, '$' . number_format($row['precio'], 2, '.', ','), 1, 0, 'C');
    $pdf->Cell(30, 10, '$' . number_format($row['cantidad'] * $row['precio'], 2, '.', ','), 1, 1, 'C');
    $contador++;
}

// Total de la venta
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(160, 10, pdfText('Total de la Venta:'), 1, 0, 'R');
$pdf->Cell(30, 10, '$' . number_format($totalVenta, 2, '.', ','), 1, 1, 'C');

// Pie de página
$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 10, pdfText('Gracias por su compra.'), 0, 1, 'C');
?>