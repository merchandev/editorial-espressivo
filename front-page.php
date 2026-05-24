<?php
/**
 * Portada personalizada estilo editorial (WaPo Style).
 *
 * @package Pro
 */

get_header();
?>

<main id="primary" class="site-main container">

    <!-- HERO SECTION -->
    <?php get_template_part('template-parts/home/hero'); ?>

    <!-- PUBLICIDAD BELOW HERO -->
    <?php get_template_part('template-parts/ads/in-feed', null, array('location' => 'below-hero')); ?>

    <!-- WAPO CATEGORY SECTIONS -->
    <div class="wapo-sections-container">
        
        <?php
        // Función Helper interna para evitar repetir el código de placeholders
        if ( ! function_exists( 'pro_render_placeholder_main' ) ) {
            function pro_render_placeholder_main( $title = "Título de Noticia Principal" ) {
                ?>
                <article class="wapo-main-article placeholder-mode">
                    <div class="post-thumbnail placeholder-image">
                        <span>Espacio Foto</span>
                    </div>
                    <div class="wapo-main-content">
                        <h3 class="entry-title placeholder-text"><?php echo esc_html($title); ?></h3>
                        <div class="entry-summary placeholder-text-small">
                            Este es un texto de relleno que muestra cómo se verá el extracto de la noticia.
                        </div>
                    </div>
                </article>
                <?php
            }
        }
        if ( ! function_exists( 'pro_render_placeholder_side' ) ) {
            function pro_render_placeholder_side($count = 3) {
                echo '<div class="wapo-side-articles placeholder-mode">';
                for($i=1; $i<=$count; $i++) {
                    echo '<article class="wapo-list-item">';
                    echo '<h4 class="entry-title placeholder-text">Titular secundario de noticia ' . $i . '</h4>';
                    echo '</article>';
                }
                echo '</div>';
            }
        }
        ?>

        <!-- ZONA PREMIUM (Diseño Principal) -->
        <div class="zone-premium">
            <?php get_template_part('template-parts/home/premium'); ?>
        </div>

        <!-- PUBLICIDAD IN-FEED 1 -->
        <?php get_template_part('template-parts/ads/in-feed', null, array('location' => 'in-feed-1')); ?>

        <!-- ZONA LOCAL (Maturín y Monagas) -->
        <?php get_template_part('template-parts/home/local'); ?>

        <!-- PUBLICIDAD IN-FEED 2 -->
        <?php get_template_part('template-parts/ads/in-feed', null, array('location' => 'in-feed-2')); ?>

        <!-- ZONA COLUMNAS (Secciones Secundarias) -->
        <?php get_template_part('template-parts/home/secondary'); ?>

    </div>

</main><!-- #primary -->

<?php
get_footer();
