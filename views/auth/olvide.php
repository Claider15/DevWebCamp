<main class="auth">
    <h2 class="auth__heading"><?php echo $titulo; ?></h2>
    <p class="auth__texto">Recupera tu acceso DevWebcamp</p>

    <?php require_once __DIR__ . '/../templates/alertas.php'; ?>

    <form method="POST" action="/olvide" class="formulario">
        <div class="formulario__campo">
            <label class="formulario__label" for="email">Email</label>
            <input type="email" class="formulario__input" placeholder="Tu Email" id="email" name="email">
        </div>

        <input type="submit" class="formulario__submit" value="Enviar Instrucciones">
    </form>

    <div class="acciones">
        <a class="acciones__enlace" href="/login">¿Ya tienes cuenta? Iniciar Sesión</a>
        <a class="acciones__enlace" href="/registro">¿Aún no tienes cuenta? Obtener una</a>
    </div>
</main>