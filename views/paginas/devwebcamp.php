<main class="devwebcamp">
    <h2 class="devwebcamp__heading"><?php echo $titulo; ?></h2>
    <p class="devwebcamp__descripcion">Conoce sobre la conferencia más importante de Latinoamérica</p>

    <div <?php aos_animacion(); ?> class="devwebcamp__grid">
        <div class="devwebcamp__imagen">
            <picture>
                <source srcset="build/img/sobre_devwebcamp.avif" type="image/avif">
                <source srcset="build/img/sobre_devwebcamp.webp" type="image/webp">
                <img loading="lazy" width="200" height="300" src="build/img/sobre_devwebcamp.jpg" alt="Imagen DevWebcamp">
            </picture>
        </div>

        <div <?php aos_animacion(); ?> class="devwebcamp__contenido">
            <p class="devwebcamp__texto">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Non, assumenda voluptatem? Delectus consequatur maiores, adipisci nihil consequuntur quisquam sapiente expedita libero laboriosam impedit, inventore id voluptatem. Beatae illo corporis deserunt?</p>
            <p class="devwebcamp__texto">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Non, assumenda voluptatem? Delectus consequatur maiores, adipisci nihil consequuntur quisquam sapiente expedita libero laboriosam impedit, inventore id voluptatem. Beatae illo corporis deserunt?</p>
        </div>
    </div>
</main>