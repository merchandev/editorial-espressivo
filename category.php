<?php
/**
 * Plantilla para mostrar los archivos de Categoría.
 *
 * @package Pro
 */

get_header();
?>

<main id="primary" class="site-main container archive-container">

    <?php if ( have_posts() ) : ?>

        <header class="page-header">
            <?php
            the_archive_title( '<h1 class="page-title">', '</h1>' );
            the_archive_description( '<div class="archive-description">', '</div>' );
            ?>
        </header><!-- .page-header -->

        <div class="category-grid">
            <?php
            /* Iniciar el Loop */
            while ( have_posts() ) :
                the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('card-post'); ?>>
                    <a href="<?php the_permalink(); ?>" class="post-thumbnail" aria-hidden="true" tabindex="-1">
                        <?php the_post_thumbnail( 'card-thumbnail', array( 'loading' => 'lazy' ) ); ?>
                    </a>
                    <div class="card-content">
                        <div class="post-meta">
                            <?php pro_post_categories(); ?>
                            <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date(); ?></time>
                        </div>
                        <h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
                        <div class="entry-excerpt">
                            <?php echo wp_trim_words( get_the_excerpt(), 20, '...' ); ?>
                        </div>
                    </div>
                </article>
                <?php
            endwhile;
            ?>
        </div>

        <?php global $wp_query; if ( $wp_query->max_num_pages > 1 ) : ?>
            <div class="load-more-container text-center">
                <button id="load-more-btn" class="btn-primary">Cargar más noticias</button>
            </div>
        <?php endif; ?>

    <?php else : ?>
        <section class="no-results not-found">
            <header class="page-header">
                <h1 class="page-title"><?php esc_html_e( 'No hay noticias en esta sección', 'pro' ); ?></h1>
            </header>
            <div class="page-content">
                <p><?php esc_html_e( 'Parece que no podemos encontrar lo que buscas. Tal vez una búsqueda ayude.', 'pro' ); ?></p>
                <?php get_search_form(); ?>
            </div>
        </section>
    <?php endif; ?>

</main><!-- #primary -->

<?php
get_footer();
