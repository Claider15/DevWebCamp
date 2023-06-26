<main class="auth">
    <h2 class="auth__heading"><?php echo $titulo; ?></h2>
    <p class="auth__texto">
        Coloca tu nuevo password</p>
        
    <?php require_once __DIR__ . '/../templates/alertas.php'; ?>

    <?php if ($token_valido) { ?>
        <form method="POST" class="formulario">
            <div class="formulario__campo">
                <label class="formulario__label" for="password">Nuevo Password</label>
                <input type="password" class="formulario__input" placeholder="Tu Nuevo Password" id="password" name="password">
            </div>
    
            <input type="submit" class="formulario__submit" value="Guardar Password">
        </form>
    
        <?php }  ?>
        
        <div class="acciones">
            <a class="acciones__enlace" href="/login">¿Ya tienes cuenta? Iniciar Sesión</a>
            <a class="acciones__enlace" href="/registro">¿Aún no tienes cuenta? Obtener una</a>
        </div>

</main>