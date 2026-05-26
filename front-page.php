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

    <!-- PORTADA DEL DÍA (EDICIÓN IMPRESA) -->
    <?php
    $portada_img = get_option( 'pro_portada_actual' );
    $is_demo = false;
    if ( ! $portada_img ) {
        $portada_img = get_template_directory_uri() . '/screenshot.png';
        $is_demo = true;
    }
    ?>
    <div class="home-portada-banner card-portada" data-full-url="<?php echo esc_url($portada_img); ?>">
        <div class="home-portada-wrapper">
            <div class="home-portada-thumbnail">
                <img src="<?php echo esc_url($portada_img); ?>" alt="Portada del Día" loading="eager">
                <div class="portada-overlay">
                    <span class="material-symbols-outlined portada-icon">zoom_in</span>
                    <span>Ampliar Portada</span>
                </div>
            </div>
            <div class="home-portada-info">
                <span class="edition-badge" style="<?php echo $is_demo ? 'background: rgba(239, 68, 68, 0.15); color: #f87171; border-color: rgba(239, 68, 68, 0.25);' : ''; ?>">
                    <?php echo $is_demo ? 'Vista Previa (Demostración)' : 'Edición Impresa de Hoy'; ?>
                </span>
                <h2 class="edition-title"><?php echo esc_html( get_bloginfo('name') ); ?> — Portada Oficial</h2>
                <p class="edition-desc">
                    <?php if ( $is_demo ) : ?>
                        Esta es una demostración de la portada del día. Puedes reemplazar esta imagen y configurar la portada oficial de tu periódico o revista (así como programar la del día siguiente a las 05:00 AM) directamente desde el menú <strong>Portadas > Portada del Día</strong> en tu panel de control.
                    <?php else : ?>
                        Explora la portada oficial del día de hoy en alta resolución. Haz clic en la imagen para abrir el visor interactivo donde podrás hacer zoom, mover la imagen y examinar todos los detalles con total claridad, o descárgala directamente en tu dispositivo.
                    <?php endif; ?>
                </p>
                <div class="edition-buttons">
                    <button type="button" class="btn-edition-open">
                        <span class="material-symbols-outlined">zoom_in</span> Ver Portada
                    </button>
                    <a href="<?php echo esc_url($portada_img); ?>" download class="btn-edition-download">
                        <span class="material-symbols-outlined">download</span> Descargar Portada
                    </a>
                </div>
                
                <?php if ( $is_demo && current_user_can( 'edit_posts' ) ) : ?>
                    <div style="margin-top: 20px; padding: 10px 15px; background: rgba(59, 130, 246, 0.1); border-left: 4px solid #3b82f6; border-radius: 4px; font-size: 0.85rem; color: #93c5fd;">
                        💡 <strong>Nota del Editor:</strong> Esta portada de prueba solo se muestra porque aún no has subido una imagen en tu panel. Entra a <strong>Portadas > Portada del Día</strong> para personalizarla.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

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

        <!-- BANNER CARTELES Y EDICTOS -->
        <?php get_template_part('template-parts/home/carteles'); ?>

    </div>

</main><!-- #primary -->

<?php
get_footer();
