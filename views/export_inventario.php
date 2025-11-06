<?php
require_once '../model/MYSQL.php';

// Conexión a base de datos
$db = new MYSQL();
$db->conectar();
$conn = $db->getConexion();

// Consulta solo de libros
$sql = "SELECT titulo, autor, categoria, cantidad, disponibilidad FROM libro ORDER BY titulo ASC";
$result = $conn->query($sql);

// Cabeceras para exportar como Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Reporte_Inventario.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "<table border='1'>";
echo "<tr style='background-color:#007bff; color:white;'>
        <th>Título</th>
        <th>Autor</th>
        <th>Categoría</th>
        <th>Cantidad</th>
        <th>Disponibilidad</th>
      </tr>";

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['titulo']) . "</td>
                <td>" . htmlspecialchars($row['autor']) . "</td>
                <td>" . htmlspecialchars($row['categoria']) . "</td>
                <td>" . htmlspecialchars($row['cantidad']) . "</td>
                <td>" . htmlspecialchars(ucfirst($row['disponibilidad'])) . "</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='5'>No hay libros registrados</td></tr>";
}
echo "</table>";

$db->desconectar();
?>
