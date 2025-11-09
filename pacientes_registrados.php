<?php
include("conexion.php");

$res = $conexion->query("SELECT * FROM pacientes ORDER BY fecha_registro DESC");
?>

<div class="inventario-container">
  <div class="inventario-header">
    <h2>Pacientes Registrados</h2>
    <a href="form_paciente.php">
      <button class="btn-modificar">+ Nuevo paciente</button>
    </a>
  </div>

  <div class="tabla-inventario">
    <table>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Edad</th>
        <th>Celular</th>
        <th>Profesión</th>
        <th>Historial</th>
      </tr>

      <?php if ($res && $res->num_rows > 0): ?>
        <?php while ($row = $res->fetch_assoc()): ?>
          <tr>
            <td><?php echo $row['id_paciente']; ?></td>
            <td><?php echo $row['nombre']; ?></td>
            <td><?php echo $row['edad']; ?></td>
            <td><?php echo $row['celular']; ?></td>
            <td><?php echo $row['profesion']; ?></td>
            <td>
              <a href="historial_paciente.php?id=<?php echo $row['id_paciente']; ?>">
                <button class="btn-modificar">Ver historial</button>
              </a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="6" style="text-align:center;padding:20px;color:#555;">
            No se encontraron pacientes.
          </td>
        </tr>
      <?php endif; ?>
    </table>
  </div>
</div>

<?php
$conexion->close();
?>


