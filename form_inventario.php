<?php
include("conexion.php");
date_default_timezone_set('America/Mexico_City');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Ortholex — Inventario</title>
  <link rel="stylesheet" href="css/inicio.css">
</head>
<body> 
  <!-- Barra superior -->
  <div class="topbar">
    <img src="imagenes/logo" alt="Logo" class="topbar-logo">
  </div>

  <!-- Sidebar -->
  <div class="sidebar">
    <ul class="menu">
      <li><a href="form_cita.php">Citas</a></li>
      <li><a href="pacientes.php">Pacientes</a></li>
      <li><a href="form_expediente.php">Expedientes</a></li>
      <li><a href="form_inventario.php" class="active">Inventario</a></li>
      <li><a href="form_pago.php">Pagos</a></li>
      <li><a href="tratamientos.php">Tratamientos</a></li>
    </ul>
  </div>

  <!-- Contenido principal -->
  <div class="main">
    <div class="content">

      <div class="inventario-header">
        <h2>Inventario actual</h2>
        <button id="btnModificar" class="btn-modificar">Modificar inventario</button>
      </div>

      <!-- 📋 Tabla normal (solo lectura) -->
      <div class="tabla-inventario" id="tablaNormal">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre del artículo</th>
              <th>Descripción</th>
              <th>Cantidad</th>
              <th>Última modificación</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $res = $conexion->query("SELECT * FROM inventario ORDER BY id_objeto ASC");
            if ($res->num_rows > 0):
              while ($fila = $res->fetch_assoc()):
            ?>
              <tr>
                <td><?= $fila['id_objeto'] ?></td>
                <td><?= htmlspecialchars($fila['nombre_objeto']) ?></td>
                <td><?= htmlspecialchars($fila['descripcion']) ?></td>
                <td><?= $fila['cantidad'] ?></td>
                <td><?= date('d-m-Y H:i', strtotime($fila['fecha_modificacion'])) ?> hrs</td>
              </tr>
            <?php endwhile; else: ?>
              <tr><td colspan="5" style="text-align:center;">No hay artículos registrados</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- ✏️ Sección de edición -->
      <div id="seccionEditable" style="display:none; margin-top:25px;">

        <!-- 🔧 Botones y formularios -->
        <div id="accionesInventario">
          <h3 style="text-align:center;">Seleccione una acción:</h3>
          <div style="display:flex; justify-content:center; gap:10px; margin-bottom:20px;">
            <button id="btnAgregar" class="btn-modificar">Agregar artículo</button>
            <button id="btnEliminar" class="btn-eliminar">Eliminar artículo</button>
          </div>

          <!-- 🆕 Formulario agregar -->
          <div id="formAgregar" class="form-box" style="display:none;">
            <form action="funciones/guardar_inventario.php" method="post" autocomplete="off">
              <h3>Agregar nuevo artículo</h3>
              <div class="input-group">
                <label>Nombre del artículo:</label>
                <input type="text" name="nombre" required>
              </div>
              <div class="input-group">
                <label>Cantidad:</label>
                <input type="number" name="cantidad" required min="1">
              </div>
              <div class="input-group">
                <label>Descripción:</label>
                <input type="text" name="descripcion" placeholder="Opcional...">
              </div>
              <div class="input-group">
                <label>Fecha:</label>
                <input type="text" name="fecha" value="<?= date('d-m-Y H:i') ?> hrs" readonly>
              </div>
              <div class="buttons">
                <button type="submit" class="btn-guardar">Guardar</button>
                <button type="button" id="btnCancelarAgregar" class="btn-cancelar">Cancelar</button>
              </div>
            </form>
          </div>

          <!-- 🗑️ Formulario eliminar -->
          <div id="formEliminar" class="form-box" style="display:none;">
            <form action="funciones/eliminar_inventario.php" method="post" autocomplete="off">
              <h3>Eliminar artículo</h3>
              <div class="input-group">
                <label>Seleccione el artículo:</label>
                <select name="idEliminar" required>
                  <option value="">Seleccione...</option>
                  <?php
                  $articulos = $conexion->query("SELECT id_objeto, nombre_objeto, cantidad FROM inventario ORDER BY nombre_objeto ASC");
                  if ($articulos->num_rows > 0) {
                    while ($a = $articulos->fetch_assoc()) {
                      echo "<option value='{$a['id_objeto']}'>{$a['nombre_objeto']} (Existencias: {$a['cantidad']})</option>";
                    }
                  } else {
                    echo "<option value=''>No hay artículos registrados</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="input-group">
                <label>Cantidad a eliminar:</label>
                <input type="number" name="cantidadEliminar" required min="1" placeholder="Ej. 3">
              </div>
              <div class="buttons">
                <button type="submit" class="btn-eliminar">Eliminar</button>
                <button type="button" id="btnCancelarEliminar" class="btn-cancelar">Cancelar</button>
              </div>
            </form>
          </div>
        </div>

        <!-- 📋 Tabla editable -->
        <div class="tabla-inventario" id="tablaEditable">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Nombre del artículo</th>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Última modificación</th>
                <th>Acción</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $res2 = $conexion->query("SELECT * FROM inventario ORDER BY id_objeto ASC");
              if ($res2->num_rows > 0):
                while ($fila = $res2->fetch_assoc()):
              ?>
                <tr>
                  <form method="POST" action="funciones/editar_inventario.php">
                    <td><?= $fila['id_objeto'] ?></td>
                    <td><input type="text" name="nombre_objeto" value="<?= htmlspecialchars($fila['nombre_objeto']) ?>" required></td>
                    <td><input type="text" name="descripcion" value="<?= htmlspecialchars($fila['descripcion']) ?>"></td>
                    <td><input type="number" name="cantidad" value="<?= $fila['cantidad'] ?>" min="0" required></td>
                    <td><?= date('d-m-Y H:i', strtotime($fila['fecha_modificacion'])) ?> hrs</td>
                    <td>
                      <input type="hidden" name="id_objeto" value="<?= $fila['id_objeto'] ?>">
                      <button type="submit" class="btn-modificar">Actualizar</button>
                    </td>
                  </form>
                </tr>
              <?php endwhile; else: ?>
                <tr><td colspan="6" style="text-align:center;">No hay artículos registrados</td></tr>
              <?php endif; ?>
            </tbody>
          </table>

          <div style="margin-top:15px;text-align:center;">
            <button id="btnCancelarEdicion" class="btn-cancelar">Cancelar</button>
          </div>
        </div>
      </div>

    </div>
  </div>

  <script>
    const btnModificar = document.getElementById("btnModificar");
    const tablaNormal = document.getElementById("tablaNormal");
    const seccionEditable = document.getElementById("seccionEditable");
    const btnCancelarEdicion = document.getElementById("btnCancelarEdicion");

    const btnAgregar = document.getElementById("btnAgregar");
    const btnEliminar = document.getElementById("btnEliminar");
    const formAgregar = document.getElementById("formAgregar");
    const formEliminar = document.getElementById("formEliminar");
    const btnCancelarAgregar = document.getElementById("btnCancelarAgregar");
    const btnCancelarEliminar = document.getElementById("btnCancelarEliminar");

    btnModificar.addEventListener("click", () => {
      tablaNormal.style.display = "none";
      seccionEditable.style.display = "block";
      btnModificar.style.display = "none";
    });

    btnCancelarEdicion.addEventListener("click", () => {
      seccionEditable.style.display = "none";
      tablaNormal.style.display = "block";
      btnModificar.style.display = "inline-block";
      formAgregar.style.display = "none";
      formEliminar.style.display = "none";
    });

    btnAgregar.addEventListener("click", () => {
      formAgregar.style.display = "block";
      formEliminar.style.display = "none";
    });
    btnCancelarAgregar.addEventListener("click", () => formAgregar.style.display = "none");

    btnEliminar.addEventListener("click", () => {
      formEliminar.style.display = "block";
      formAgregar.style.display = "none";
    });
    btnCancelarEliminar.addEventListener("click", () => formEliminar.style.display = "none");
  </script>

  <style>
    /* 🔹 Inputs de formularios */
    .input-group input,
    .input-group select {
      width: 100%;
      background: #fff;
      border: 1px solid #ccc;
      border-radius: 8px;
      padding: 8px;
      font-family: 'Segoe UI', sans-serif;
      font-size: 15px;
      color: #333;
      transition: border-color 0.3s, box-shadow 0.3s;
    }

    .input-group input:focus,
    .input-group select:focus {
      border-color: #a16976;
      box-shadow: 0 0 4px rgba(161,105,118,0.4);
      outline: none;
    }

    /* 🔹 Inputs dentro de la tabla editable */
    table input {
      width: 85%; /* más compactos */
      background: #fff;
      border: 1px solid #ccc;
      border-radius: 6px;
      padding: 4px 6px;
      text-align: center;
      font-size: 14px;
      transition: border-color 0.3s;
    }

    table input:focus {
      border-color: #a16976;
      box-shadow: 0 0 3px rgba(161,105,118,0.4);
      outline: none;
    }

    table td {
      vertical-align: middle;
    }
  </style>

<?php $conexion->close(); ?>
</body>
</html>



