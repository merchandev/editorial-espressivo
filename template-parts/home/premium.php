<?php
/**
 * @author Arturo Merchan | Merchan.Dev | Espressivo Venezuela,C.A
 * 
 * ADVERTENCIA LEGAL:
 * Queda totalmente prohibida su reproduccion, edicion, venta, propaganda, alteracion 
 * o cualquier otra accion que de una u otra forma violente la propiedad intelectual, 
 * material y digital de este proyecto. Esta infraccion esta prohibida y penada por la ley.
 */
$premium_cats = array('nacional' => 'Nacional', 'internacional' => 'Internacional', 'economia' => 'Economía', 'sucesos' => 'Sucesos');
foreach ( $premium_cats as $cat_slug => $cat_name ) :
    $cat_args = array('category_name' => $cat_slug, 'posts_per_page' => 4, 'post_status' => 'publish');
    $cat_query = new WP_Query( $cat_args );
    if ( $cat_query->have_posts() ) :
    ?>
    <section class="wapo-category-section">
        <h2 class="wapo-section-title"><span><?php echo esc_html( $cat_name ); ?></span></h2>
        <div class="wapo-grid">
            <?php
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
            ?>
        </div>
    </section>
    <?php endif; wp_reset_postdata(); ?>
    
    <?php if ( $cat_slug === 'internacional' ) : ?>
        <!-- Publicidad Extra Entre Secciones -->
        <div class="premium-ads-single" style="margin: 40px 0;">
            <?php get_template_part('template-parts/ads/in-feed', null, array('location' => 'internacional-ad', 'size' => '1200x200')); ?>
        </div>
    <?php endif; ?>

<?php endforeach; ?>
