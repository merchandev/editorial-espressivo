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
            <?php
            $premium_cats = array('nacional' => 'Nacional', 'internacional' => 'Internacional', 'economia' => 'Economía', 'sucesos' => 'Sucesos');
            foreach ( $premium_cats as $cat_slug => $cat_name ) :
                $cat_args = array('category_name' => $cat_slug, 'posts_per_page' => 4, 'post_status' => 'publish');
                $cat_query = new WP_Query( $cat_args );
                ?>
                <section class="wapo-category-section">
                    <h2 class="wapo-section-title"><span><?php echo esc_html( $cat_name ); ?></span></h2>
                    <div class="wapo-grid">
                        <?php
                        if ( $cat_query->have_posts() ) {
                            $count = 0;
                            while ( $cat_query->have_posts() ) : $cat_query->the_post();
                                if ( $count === 0 ) :
                                    ?>
                                    <article class="wapo-main-article">
                                        <a href="<?php the_permalink(); ?>" class="post-thumbnail">
                                            <?php 
                                            if ( has_post_thumbnail() ) { the_post_thumbnail( 'card-thumbnail', array( 'loading' => 'lazy' ) ); } 
                                            else { echo '<div class="placeholder-image"><span>Foto</span></div>'; }
                                            ?>
                                        </a>
                                        <div class="wapo-main-content">
                                            <h3 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                            <div class="entry-summary"><?php the_excerpt(); ?></div>
                                        </div>
                                    </article>
                                    <div class="wapo-side-articles">
                                    <?php
                                else :
                                    ?>
                                    <article class="wapo-list-item">
                                        <h4 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                    </article>
                                    <?php
                                endif;
                                $count++;
                            endwhile;
                            if ( $count > 1 ) { echo '</div>'; } elseif ( $count === 1 ) { echo '<div class="wapo-side-articles empty-side"></div>'; }
                        } else {
                            pro_render_placeholder_main("Noticia de " . $cat_name);
                            pro_render_placeholder_side(3);
                        }
                        ?>
                    </div>
                </section>
                <?php wp_reset_postdata(); ?>
            <?php endforeach; ?>
        </div>

        <!-- ZONA LOCAL (Maturín y Monagas) -->
        <section class="wapo-category-section local-zone">
            <h2 class="wapo-section-title"><span>Noticias Locales</span></h2>
            <div class="local-news-block">
                <?php
                $local_cats = array('maturin' => 'Maturín', 'monagas' => 'Monagas');
                foreach($local_cats as $slug => $name) :
                    $local_q = new WP_Query(array('category_name' => $slug, 'posts_per_page' => 1));
                    ?>
                    <div class="local-news-card">
                        <div class="cat-label" style="background:var(--color-primary); color:#fff; display:inline-block; padding:2px 10px; border-radius:20px; font-size:0.8rem; margin-bottom:10px; font-weight:bold;"><?php echo $name; ?></div>
                        <?php if($local_q->have_posts()): while($local_q->have_posts()): $local_q->the_post(); ?>
                            <h3 class="entry-title" style="font-size:1.5rem; margin-bottom:10px;"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <div class="entry-summary" style="font-size:0.95rem; color:var(--color-text-muted);"><?php echo wp_trim_words(get_the_excerpt(), 15); ?></div>
                        <?php endwhile; else: ?>
                            <h3 class="entry-title placeholder-text">Titular de <?php echo $name; ?></h3>
                            <div class="entry-summary placeholder-text-small">Aún no hay noticias publicadas en esta sección local.</div>
                        <?php endif; wp_reset_postdata(); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- ZONA COLUMNAS (Secciones Secundarias) -->
        <section class="wapo-category-section secondary-zone">
            <h2 class="wapo-section-title"><span>Más Secciones</span></h2>
            <div class="news-grid-3col">
                <?php
                $sec_cats = array(
                    'deportes' => 'Deportes', 'cultura-y-entretenimiento' => 'Cultura', 
                    'ciencia-y-tecnologia' => 'Ciencia', 'opinion' => 'Opinión', 
                    'salud' => 'Salud', 'educacion' => 'Educación', 
                    'servicios-publicos' => 'Servicios', 'comunidad' => 'Comunidad'
                );
                foreach($sec_cats as $slug => $name) :
                    $sec_q = new WP_Query(array('category_name' => $slug, 'posts_per_page' => 3));
                    ?>
                    <div class="sec-column-block">
                        <h3 style="font-family:var(--font-heading); font-size:1.3rem; border-bottom:1px solid var(--color-border); padding-bottom:5px; margin-bottom:15px;"><?php echo $name; ?></h3>
                        <?php if($sec_q->have_posts()): while($sec_q->have_posts()): $sec_q->the_post(); ?>
                            <article class="wapo-list-item" style="padding:0; margin-bottom:15px; border:none;">
                                <h4 class="entry-title" style="font-size:1rem; margin-bottom:0;"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                            </article>
                        <?php endwhile; else: ?>
                            <article class="wapo-list-item" style="padding:0; margin-bottom:15px; border:none;">
                                <h4 class="entry-title placeholder-text" style="font-size:1rem;">Noticia de <?php echo $name; ?></h4>
                            </article>
                        <?php endif; wp_reset_postdata(); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- ZONA MUNICIPIOS -->
        <section class="wapo-category-section municipios-zone">
            <h2 class="wapo-section-title"><span>Municipios</span></h2>
            <div class="municipios-grid">
                <?php
                $municipios = array('Acosta', 'Aguasay', 'Bolívar', 'Caripe', 'Cedeño', 'Ezequiel Zamora', 'Libertador', 'Piar', 'Punceres', 'Santa Bárbara', 'Sotillo', 'Uracoa');
                foreach($municipios as $muni) :
                    $m_slug = sanitize_title($muni);
                    $m_q = new WP_Query(array('category_name' => $m_slug, 'posts_per_page' => 1));
                    ?>
                    <div class="municipio-card">
                        <h4><a href="<?php echo esc_url(get_category_link(get_cat_ID($muni))); ?>"><?php echo $muni; ?></a></h4>
                        <?php if($m_q->have_posts()): while($m_q->have_posts()): $m_q->the_post(); ?>
                            <div style="font-size:0.85rem; color:var(--color-text-muted);"><a href="<?php the_permalink(); ?>" style="color:inherit;"><?php echo wp_trim_words(get_the_title(), 8); ?></a></div>
                        <?php endwhile; else: ?>
                            <div style="font-size:0.85rem; color:var(--color-text-muted); opacity:0.6;">Sin actualizaciones.</div>
                        <?php endif; wp_reset_postdata(); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

    </div>

</main><!-- #primary -->

<?php
get_footer();
