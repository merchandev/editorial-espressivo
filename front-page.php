<?php
/**
 * Portada personalizada estilo editorial (WaPo Style).
 *
 * @package Pro
 */

get_header();
?>

<main id="primary" class="site-main container">

    <!-- HERO POST -->
    <?php
    $hero_args = array(
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'ignore_sticky_posts' => 1
    );
    $hero_query = new WP_Query( $hero_args );

    if ( $hero_query->have_posts() ) : ?>
        <div class="home-layout-grid">
            <?php
            while ( $hero_query->have_posts() ) : $hero_query->the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('hero-post'); ?>>
                    <a href="<?php the_permalink(); ?>" class="post-thumbnail" aria-hidden="true" tabindex="-1">
                        <?php the_post_thumbnail( 'hero-thumbnail', array( 'loading' => 'eager' ) ); ?>
                    </a>
                    <div class="hero-content">
                        <?php pro_post_categories(); ?>
                        <h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
                        <div class="entry-summary">
                            <?php the_excerpt(); ?>
                        </div>
                        <div class="post-meta-footer">
                            <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
                        </div>
                    </div>
                </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    <?php endif; ?>

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
