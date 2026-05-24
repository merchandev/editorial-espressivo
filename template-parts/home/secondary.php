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
            $cat_obj = get_category_by_slug($slug);
            $cat_link = $cat_obj ? esc_url(get_category_link($cat_obj->term_id)) : '#';
            
            $sec_q = new WP_Query(array('category_name' => $slug, 'posts_per_page' => 1));
            ?>
            <div class="sec-column-block">
                <h3 style="font-family:var(--font-heading); font-size:1.3rem; border-bottom:1px solid var(--color-border); padding-bottom:5px; margin-bottom:15px;">
                    <a href="<?php echo $cat_link; ?>" style="color:var(--color-text); text-decoration:none;" onmouseover="this.style.color='var(--color-accent)'" onmouseout="this.style.color='var(--color-text)'"><?php echo esc_html($name); ?></a>
                </h3>
                
                <?php if($sec_q->have_posts()): while($sec_q->have_posts()): $sec_q->the_post(); ?>
                    <article class="wapo-list-item mini-card" style="padding:0; margin-bottom:15px; border:none; display:flex; gap:15px; align-items:center;">
                        <a href="<?php the_permalink(); ?>" class="post-thumbnail" style="flex-shrink:0; width:90px; height:70px; border-radius:4px; overflow:hidden;">
                            <?php 
                            if (has_post_thumbnail()) {
                                the_post_thumbnail('thumbnail', array('style' => 'width:100%; height:100%; object-fit:cover;'));
                            } else {
                                echo '<div style="width:100%; height:100%; background:var(--color-bg-secondary); border:1px solid var(--color-border); display:flex; align-items:center; justify-content:center; font-size:0.7rem; color:var(--color-text-muted);">Foto</div>';
                            }
                            ?>
                        </a>
                        <div class="mini-card-content">
                            <h4 class="entry-title" style="font-size:0.95rem; margin:0 0 5px 0; line-height:1.4;"><a href="<?php the_permalink(); ?>" style="color:var(--color-text);"><?php echo wp_trim_words(get_the_title(), 10, '...'); ?></a></h4>
                            <div style="font-size:0.75rem; color:var(--color-text-muted);"><?php echo get_the_date(); ?></div>
                        </div>
                    </article>
                <?php endwhile; else: ?>
                    <article class="wapo-list-item mini-card" style="padding:0; margin-bottom:15px; border:none; display:flex; gap:15px; align-items:center;">
                        <div class="post-thumbnail placeholder-image" style="flex-shrink:0; width:90px; height:70px; border-radius:4px; background:var(--color-bg-secondary); border:1px solid var(--color-border); display:flex; align-items:center; justify-content:center; font-size:0.7rem; color:var(--color-text-muted);">
                            Foto
                        </div>
                        <div class="mini-card-content">
                            <h4 class="entry-title placeholder-text" style="font-size:0.95rem; margin:0 0 5px 0; line-height:1.4; color:#cbd5e1;">Aún no hay noticias en <?php echo esc_html($name); ?></h4>
                        </div>
                    </article>
                <?php endif; wp_reset_postdata(); ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>
