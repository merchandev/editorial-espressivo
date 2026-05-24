<?php
/**
 * Plantilla para posts individuales (Artículos de noticias).
 *
 * @package Pro
 */

get_header();
?>

<main id="primary" class="site-main container single-article-container">

    <?php
    while ( have_posts() ) :
        the_post();
        ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class('single-post-article'); ?>>
            
            <!-- Breadcrumbs / Migas de Pan -->
            <nav class="breadcrumbs" aria-label="Breadcrumb">
                <ol>
                    <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Inicio</a></li>
                    <?php 
                    $categories = get_the_category();
                    if ( ! empty( $categories ) ) {
                        echo '<li><a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">' . esc_html( $categories[0]->name ) . '</a></li>';
                    }
                    ?>
                    <li><span aria-current="page"><?php the_title(); ?></span></li>
                </ol>
            </nav>

            <header class="entry-header">
                <div class="entry-meta-top">
                        <?php pro_post_categories(); ?>
                </div>
                <h1 class="entry-title"><?php the_title(); ?></h1>
                
                <div class="entry-meta">
                    <span class="posted-on">Publicado el <time datetime="<?php echo get_the_date('c'); ?>"><?php echo get_the_date('j \d\e F \d\e Y'); ?></time></span>
                </div>
            </header>

            <?php if ( has_post_thumbnail() ) : ?>
                <figure class="post-thumbnail single-hero-image">
                    <?php the_post_thumbnail( 'full', array( 'loading' => 'eager' ) ); ?>
                    <?php if ( wp_get_attachment_caption( get_post_thumbnail_id() ) ) : ?>
                        <figcaption><?php echo wp_get_attachment_caption( get_post_thumbnail_id() ); ?></figcaption>
                    <?php endif; ?>
                </figure>
            <?php endif; ?>

            <div class="entry-content" data-paywall="content">
                <?php
                the_content();
                ?>
                
                <!-- Ad In-Feed dentro del contenido -->
                <?php if ( is_active_sidebar( 'ad-in-feed' ) ) : ?>
                    <div class="content-ad-break">
                        <?php dynamic_sidebar( 'ad-in-feed' ); ?>
                    </div>
                <?php endif; ?>
            </div><!-- .entry-content -->

            <footer class="entry-footer">
                <div class="tags-links">
                    <?php the_tags( 'Etiquetas: ', ', ', '' ); ?>
                </div>
            </footer>
        </article><!-- #post-<?php the_ID(); ?> -->

        <!-- Marcado Schema.org NewsArticle -->
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "NewsArticle",
            "headline": "<?php echo esc_js( get_the_title() ); ?>",
            "image": [
                "<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'full' ) ); ?>"
            ],
            "datePublished": "<?php echo get_the_date('c'); ?>",
            "dateModified": "<?php echo get_the_modified_date('c'); ?>",
            "author": [{
                "@type": "Person",
                "name": "<?php echo esc_js( get_the_author() ); ?>"
            }]
        }
        </script>

        <?php
    endwhile; // End of the loop.
    ?>

</main><!-- #primary -->

<?php
get_footer();
