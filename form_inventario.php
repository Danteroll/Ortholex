<?php
include("conexion.php");
?>

<div class="inventario-container">

  <!-- Encabezado del módulo -->
  <div class="inventario-header">
    <h2>Inventario actual</h2>
    <button id="btnModificar" class="btn-modificar">Modificar inventario</button>
  </div>

  <!-- Selección de acción -->
  <div id="accionesInventario" class="acciones-box" style="display:none;">
    <h3>Seleccione una acción:</h3>
    <div class="acciones-buttons">
      <button id="btnAgregar" class="btn-accion agregar">Agregar artículo</button>
      <button id="btnEliminar" class="btn-accion eliminar">Eliminar artículo</button>
      <button id="btnCancelarAccion" class="btn-cancelar">Cancelar</button>
    </div>
  </div>

  <!-- Formulario AGREGAR -->
  <div id="formAgregar" class="form-box" style="display:none;">
    <h3>Agregar artículo al inventario</h3>
    <form action="funciones/guardar_inventario.php" method="post"
          autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">

      <div class="input-group">
        <label for="nombre">Nombre del artículo:</label>
        <input type="text" id="nombre" name="nombre" required>
      </div>

      <div class="input-group">
        <label for="cantidad">Cantidad:</label>
        <input type="number" id="cantidad" name="cantidad" required min="1">
      </div>

      <div class="input-group">
        <label for="descripcion">Descripción (opcional):</label>
        <input type="text" id="descripcion" name="descripcion">
      </div>

      <div class="input-group">
        <label for="fecha">Fecha de modificación:</label>
        <input type="date" id="fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>">
      </div>

      <div class="buttons">
        <button type="submit" class="btn-guardar">Guardar</button>
        <button type="button" id="btnCancelarAgregar" class="btn-cancelar">Cancelar</button>
      </div>
    </form>
  </div>

  <!-- Formulario ELIMINAR -->
  <div id="formEliminar" class="form-box" style="display:none;">
    <h3>Eliminar artículo del inventario</h3>
    <form action="funciones/eliminar_inventario.php" method="post"
          autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">

      <div class="input-group">
        <label for="idEliminar">ID o nombre del artículo:</label>
        <input type="text" id="idEliminar" name="idEliminar" required
               placeholder="Ej. 12 o Guantes">
      </div>

      <div class="input-group">
        <label for="cantidadEliminar">Cantidad a eliminar:</label>
        <input type="number" id="cantidadEliminar" name="cantidadEliminar"
               required min="1" placeholder="Ej. 5">
      </div>

      <div class="buttons">
        <button type="submit" class="btn-eliminar">Eliminar</button>
        <button type="button" id="btnCancelarEliminar" class="btn-cancelar">Cancelar</button>
      </div>
    </form>
  </div>

  <!-- Tabla de inventario -->
  <div class="tabla-inventario">
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
        if ($res->num_rows > 0) {
          while ($fila = $res->fetch_assoc()) {
            echo "<tr>
                    <td>{$fila['id_objeto']}</td>
                    <td>{$fila['nombre_objeto']}</td>
                    <td>{$fila['descripcion']}</td>
                    <td>{$fila['cantidad']}</td>
                    <td>{$fila['fecha_modificacion']}</td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan='5'>No hay artículos registrados en el inventario</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<script>
  // Referencias
  const btnModificar = document.getElementById("btnModificar");
  const accionesBox = document.getElementById("accionesInventario");
  const btnAgregar = document.getElementById("btnAgregar");
  const btnEliminar = document.getElementById("btnEliminar");
  const formAgregar = document.getElementById("formAgregar");
  const formEliminar = document.getElementById("formEliminar");
  const btnCancelarAccion = document.getElementById("btnCancelarAccion");
  const btnCancelarAgregar = document.getElementById("btnCancelarAgregar");
  const btnCancelarEliminar = document.getElementById("btnCancelarEliminar");

  // Mostrar opciones
  btnModificar.addEventListener("click", () => {
    accionesBox.style.display = "block";
    btnModificar.style.display = "none";
  });

  // Agregar
  btnAgregar.addEventListener("click", () => {
    accionesBox.style.display = "none";
    formAgregar.style.display = "block";
  });

  // Eliminar
  btnEliminar.addEventListener("click", () => {
    accionesBox.style.display = "none";
    formEliminar.style.display = "block";
  });

  // Cancelar acción
  btnCancelarAccion.addEventListener("click", () => {
    accionesBox.style.display = "none";
    btnModificar.style.display = "inline-block";
  });

  // Cancelar agregar
  btnCancelarAgregar.addEventListener("click", () => {
    formAgregar.style.display = "none";
    btnModificar.style.display = "inline-block";
  });

  // Cancelar eliminar
  btnCancelarEliminar.addEventListener("click", () => {
    formEliminar.style.display = "none";
    btnModificar.style.display = "inline-block";
  });
</script>
