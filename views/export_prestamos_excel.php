<?php
require_once '../model/MYSQL.php';

$db = new MYSQL();
$db->conectar();
$conn = $db->getConexion();

$sql = "SELECT 
            u.nombre AS usuario,
            l.titulo AS libro,
            p.fecha_prestamo,
            p.fecha_devolucion,
            p.dias,
            p.estado
        FROM prestamo p
        JOIN usuarios u ON p.id_usuario = u.id
        JOIN libro l ON p.id_libro = l.id
        ORDER BY p.fecha_prestamo DESC";
$result = $conn->query($sql);

header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="Reporte_Prestamos.xls"');
header('Cache-Control: max-age=0');

echo '<?xml version="1.0" encoding="UTF-8"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">

 <Styles>
  <Style ss:ID="Header">
    <Font ss:Bold="1" ss:Color="#FFFFFF"/>
    <Interior ss:Color="#0070C0" ss:Pattern="Solid"/>
    <Alignment ss:Horizontal="Center"/>
    <Borders>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
      <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
      <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
      <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    </Borders>
  </Style>

  <Style ss:ID="Cell">
    <Borders>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
      <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
      <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
      <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    </Borders>
  </Style>
 </Styles>

 <Worksheet ss:Name="Préstamos">
  <Table ss:DefaultColumnWidth="130">
   <Row>
     <Cell ss:MergeAcross="5"><Data ss:Type="String">📘 Reporte de Préstamos</Data></Cell>
   </Row>
   <Row ss:StyleID="Header">
     <Cell><Data ss:Type="String">Usuario</Data></Cell>
     <Cell><Data ss:Type="String">Libro</Data></Cell>
     <Cell><Data ss:Type="String">Fecha Préstamo</Data></Cell>
     <Cell><Data ss:Type="String">Fecha Devolución</Data></Cell>
     <Cell><Data ss:Type="String">Días</Data></Cell>
     <Cell><Data ss:Type="String">Estado</Data></Cell>
   </Row>';

if ($result && $result->num_rows > 0) {
    while ($r = $result->fetch_assoc()) {
        echo '<Row ss:StyleID="Cell">
                <Cell><Data ss:Type="String">'.htmlspecialchars($r['usuario']).'</Data></Cell>
                <Cell><Data ss:Type="String">'.htmlspecialchars($r['libro']).'</Data></Cell>
                <Cell><Data ss:Type="String">'.htmlspecialchars($r['fecha_prestamo']).'</Data></Cell>
                <Cell><Data ss:Type="String">'.htmlspecialchars($r['fecha_devolucion']).'</Data></Cell>
                <Cell><Data ss:Type="Number">'.htmlspecialchars($r['dias']).'</Data></Cell>
                <Cell><Data ss:Type="String">'.htmlspecialchars(ucfirst($r['estado'])).'</Data></Cell>
              </Row>';
    }
} else {
    echo '<Row><Cell ss:MergeAcross="5"><Data ss:Type="String">No hay préstamos registrados</Data></Cell></Row>';
}

echo '
  </Table>
 </Worksheet>
</Workbook>';

$db->desconectar();
exit;
?>
