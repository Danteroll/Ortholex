<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ortholex - Iniciar Sesión</title>
  <link rel="stylesheet" href="css/index.css">
</head>
<body>
  <!-- Barra superior -->
  <div class="topbar">
    <img src="imagenes/logo" alt="Logo" class="topbar-logo">
  </div>

  <!-- Contenedor principal -->
  <div class="login-container">
    <div class="login-box">
      <h2>Bienvenida, Doctora</h2>

      <!-- Formulario de inicio de sesión -->
      <form id="loginForm">
        <div class="input-group">
          <label for="password">Contraseña</label>
          <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" required>
          <p id="error-message" class="error-message"></p>
        </div>

        <button type="submit" class="btn-login">Entrar</button>
      </form>

      <!-- Enlace para recuperar/cambiar contraseña -->
      <p class="login-footer">
        ¿Olvidaste tu contraseña? <a href="#" id="showChangePassword">Cambiala aquí</a>
      </p>

      <!-- Formulario oculto de cambio de contraseña -->
      <div id="changePasswordBox" class="change-box" style="display: none;">
        <h3>Cambiar contraseña</h3>
        <form id="changePasswordForm">
          <div class="input-group">
            <label for="securityAnswer">¿Cuál es el nombre de tu hija?</label>
            <!-- 🔒 Campo oculto tipo contraseña -->
            <input type="password" id="securityAnswer" name="securityAnswer" placeholder="Escribe tu respuesta" required>
          </div>

          <div class="input-group">
            <label for="newPass">Nueva contraseña</label>
            <input type="password" id="newPass" name="newPass" placeholder="Ingresa una nueva contraseña" required>
          </div>

          <div class="buttons">
            <button type="submit" class="btn-login">Guardar nueva contraseña</button>
            <!-- 🔙 Nuevo botón de cancelar -->
            <button type="button" class="btn-login" id="cancelChange">Cancelar</button>
          </div>

          <p id="change-message" class="change-message"></p>
        </form>
      </div>
    </div>
  </div>
  <script>
    // Bloquear botones del navegador
    history.pushState(null, document.title, location.href);
    window.addEventListener('popstate', function () {
      history.pushState(null, document.title, location.href);
    });

    // Contraseña y respuesta de seguridad
    let passwordCorrecta = localStorage.getItem("passwordOrtholex") || "1234";
    const respuestaSeguridad = "lexie";

    // Mostrar formulario de cambio de contraseña
    document.getElementById("showChangePassword").addEventListener("click", function(e) {
      e.preventDefault();
      document.getElementById("changePasswordBox").style.display = "block";
      document.getElementById("loginForm").style.display = "none";
      document.querySelector(".login-footer").style.display = "none";
    });

    // Iniciar sesión
    document.getElementById('loginForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const password = document.getElementById('password').value.trim();
      const errorMessage = document.getElementById('error-message');

      if (password === passwordCorrecta) {
        sessionStorage.setItem("logueado", "true");
        window.location.href = "inicio.php";
      } else {
        errorMessage.textContent = "Contraseña incorrecta. Intenta nuevamente.";
        document.getElementById('password').value = "";
        document.getElementById('password').focus();
      }
    });

    // Cambiar contraseña usando pregunta de seguridad
    document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const respuesta = document.getElementById('securityAnswer').value.trim().toLowerCase();
      const nueva = document.getElementById('newPass').value.trim();
      const msg = document.getElementById('change-message');

      if (respuesta === respuestaSeguridad) {
        localStorage.setItem("passwordOrtholex", nueva);
        passwordCorrecta = nueva;
        msg.textContent = "Contraseña actualizada correctamente.";
        msg.style.color = "#2ecc71";

        setTimeout(() => {
          document.getElementById("changePasswordBox").style.display = "none";
          document.getElementById("loginForm").style.display = "block";
          document.querySelector(".login-footer").style.display = "block";
          msg.textContent = "";
        }, 2000);
      } else {
        msg.textContent = "Respuesta incorrecta. Intenta nuevamente.";
        msg.style.color = "#c0392b";
      }

      document.getElementById('securityAnswer').value = "";
      document.getElementById('newPass').value = "";
    });

    // 🔙 Cancelar cambio de contraseña y volver al login
    document.getElementById('cancelChange').addEventListener('click', function() {
      document.getElementById("changePasswordBox").style.display = "none";
      document.getElementById("loginForm").style.display = "block";
      document.querySelector(".login-footer").style.display = "block";
      document.getElementById('change-message').textContent = "";
    });
  </script>
</body>
</html>