<?php
/**
 * Portada personalizada estilo editorial (WaPo Style).
 *
 * @package Pro
 */

get_header();
?>

<main id="primary" class="site-main container">

    <!-- SECCIÓN UNIFICADA: 3 NOTICIAS RELEVANTES (60%) y PORTADA (40%) -->
    <?php
    $portada_img = get_option( 'pro_portada_actual' );
    if ( ! $portada_img ) {
        $portada_img = get_template_directory_uri() . '/screenshot.png';
    }

    $relevant_query = new WP_Query( array(
        'category_name'       => 'relevantes',
        'posts_per_page'      => 3,
        'post_status'         => 'publish',
        'ignore_sticky_posts' => 1
    ) );
    ?>
    <div class="home-hero-portada-grid">
        <!-- Columna de Noticias (60%) -->
        <div class="hero-news-column">
            <?php 
            if ( $relevant_query->have_posts() ) :
                while ( $relevant_query->have_posts() ) : $relevant_query->the_post();
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('hero-sub-post'); ?>>
                        <div class="hero-content">
                            <?php if ( function_exists('pro_post_categories') ) : ?>
                                <?php pro_post_categories(null, 'relevantes'); ?>
                            <?php else : ?>
                                <span class="cat-label cat-relevantes">
                                    <a href="<?php echo esc_url( get_category_link( get_cat_ID('Relevantes') ) ); ?>">Relevantes</a>
                                </span>
                            <?php endif; ?>
                            <h3 class="entry-title">
                                <a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
                            </h3>
                            <div class="post-meta-footer">
                                <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
                            </div>
                        </div>
                    </article>
                    <?php
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>

        <!-- Columna de Portada (40%) -->
        <div class="hero-portada-column">
            <div class="home-portada-card card-portada" data-full-url="<?php echo esc_url($portada_img); ?>">
                <div class="home-portada-thumbnail">
                    <img src="<?php echo esc_url($portada_img); ?>" alt="Portada del Día" loading="eager">
                    <div class="portada-overlay">
                        <span class="material-symbols-outlined portada-icon">zoom_in</span>
                        <span>Ampliar Portada</span>
                    </div>
                </div>
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
