<?php
$sec_cats = array(
    'deportes' => 'Deportes', 'cultura-y-entretenimiento' => 'Cultura', 
    'ciencia-y-tecnologia' => 'Ciencia', 'opinion' => 'Opinión', 
    'salud' => 'Salud', 'educacion' => 'Educación', 
    'servicios-publicos' => 'Servicios', 'comunidad' => 'Comunidad'
);
?>
<section class="wapo-category-section secondary-zone">
    <h2 class="wapo-section-title"><span>Más Secciones</span></h2>
    <div class="news-grid-3col">
        <?php foreach($sec_cats as $slug => $name) :
            $sec_q = new WP_Query(array('category_name' => $slug, 'posts_per_page' => 3));
            ?>
            <div class="sec-column-block">
                <h3 style="font-family:var(--font-heading); font-size:1.3rem; border-bottom:1px solid var(--color-border); padding-bottom:5px; margin-bottom:15px;"><?php echo esc_html($name); ?></h3>
                <?php if($sec_q->have_posts()): while($sec_q->have_posts()): $sec_q->the_post(); ?>
                    <article class="wapo-list-item" style="padding:0; margin-bottom:15px; border:none;">
                        <h4 class="entry-title" style="font-size:1rem; margin-bottom:0;"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                    </article>
                <?php endwhile; else: ?>
                    <article class="wapo-list-item" style="padding:0; margin-bottom:15px; border:none;">
                        <h4 class="entry-title placeholder-text" style="font-size:1rem;">Noticia de <?php echo esc_html($name); ?></h4>
                    </article>
                <?php endif; wp_reset_postdata(); ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>
