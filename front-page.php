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
                        <?php 
                        $categories = get_the_category();
                        if ( ! empty( $categories ) ) {
                            foreach( $categories as $category ) {
                                echo '<span class="cat-label cat-' . esc_attr( $category->slug ) . '"><a href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . esc_html( $category->name ) . '</a></span> ';
                            }
                        }
                        ?>
                        <h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
                        <div class="entry-summary">
                            <?php the_excerpt(); ?>
                        </div>
                        <div class="post-meta-footer">
                            Por <span class="author"><?php echo esc_html( get_the_author() ); ?></span> &bull; 
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
        // Definir categorías destacadas
        $featured_cats = array(
            'nacional'      => 'Nacional',
            'internacional' => 'Internacional',
            'deportes'      => 'Deportes',
            'economia'      => 'Economía'
        );

        foreach ( $featured_cats as $cat_slug => $cat_name ) :
            $cat_args = array(
                'category_name'  => $cat_slug,
                'posts_per_page' => 4,
                'post_status'    => 'publish'
            );
            $cat_query = new WP_Query( $cat_args );
            
            ?>
            <section class="wapo-category-section">
                <h2 class="wapo-section-title">
                    <span><?php echo esc_html( $cat_name ); ?></span>
                </h2>
                <div class="wapo-grid">
                    <?php
                    if ( $cat_query->have_posts() ) {
                        $count = 0;
                        while ( $cat_query->have_posts() ) : $cat_query->the_post();
                            if ( $count === 0 ) :
                                // Noticia Principal de la Categoría
                                ?>
                                <article class="wapo-main-article">
                                    <a href="<?php the_permalink(); ?>" class="post-thumbnail">
                                        <?php 
                                        if ( has_post_thumbnail() ) {
                                            the_post_thumbnail( 'card-thumbnail', array( 'loading' => 'lazy' ) );
                                        } else {
                                            echo '<div class="placeholder-image"><span>Foto Destacada</span></div>';
                                        }
                                        ?>
                                    </a>
                                    <div class="wapo-main-content">
                                        <h3 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                        <div class="entry-summary"><?php the_excerpt(); ?></div>
                                        <div class="post-meta-mini">
                                            Por <?php echo esc_html( get_the_author() ); ?> &bull; <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
                                        </div>
                                    </div>
                                </article>
                                <div class="wapo-side-articles">
                                <?php
                            else :
                                // Noticias Secundarias (Lista Densa)
                                ?>
                                <article class="wapo-list-item">
                                    <h4 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                    <div class="post-meta-mini"><time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time></div>
                                </article>
                                <?php
                            endif;
                            $count++;
                        endwhile;
                        
                        if ( $count > 1 ) {
                            echo '</div><!-- .wapo-side-articles -->';
                        } elseif ( $count === 1 ) {
                            // En caso de que solo haya 1 post, rellenamos el side articles vacío por layout
                            echo '<div class="wapo-side-articles empty-side"></div>';
                        }
                    } else {
                        // ESQUEMA VISUAL / PLACEHOLDER SI NO HAY NOTICIAS
                        ?>
                        <article class="wapo-main-article placeholder-mode">
                            <div class="post-thumbnail placeholder-image">
                                <span>Espacio para Foto (16:9)</span>
                            </div>
                            <div class="wapo-main-content">
                                <h3 class="entry-title placeholder-text">Título de Noticia Principal (Ejemplo)</h3>
                                <div class="entry-summary placeholder-text-small">
                                    Este es un texto de relleno que muestra cómo se verá el extracto de la noticia una vez que publiques contenido en esta categoría. Ocupará alrededor de tres líneas.
                                </div>
                                <div class="post-meta-mini">Por Autor &bull; Fecha</div>
                            </div>
                        </article>
                        <div class="wapo-side-articles placeholder-mode">
                            <?php for($i=1; $i<=3; $i++) : ?>
                            <article class="wapo-list-item">
                                <h4 class="entry-title placeholder-text">Titular secundario de noticia <?php echo $i; ?> para llenar la columna</h4>
                                <div class="post-meta-mini">Fecha de publicación</div>
                            </article>
                            <?php endfor; ?>
                        </div>
                        <?php
                    }
                    ?>
                </div><!-- .wapo-grid -->
            </section>
            <?php
            wp_reset_postdata();
        endforeach;
        ?>
    </div>

</main><!-- #primary -->

<?php
get_footer();
