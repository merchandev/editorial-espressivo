<?php
$local_cats = array('maturin' => 'Maturín', 'monagas' => 'Monagas');

ob_start();
foreach($local_cats as $slug => $name) :
    $local_q = new WP_Query(array('category_name' => $slug, 'posts_per_page' => 1));
    if($local_q->have_posts()): while($local_q->have_posts()): $local_q->the_post(); ?>
        <div class="local-news-card">
            <div class="cat-label" style="background:var(--color-primary); color:#fff; display:inline-block; padding:2px 10px; border-radius:20px; font-size:0.8rem; margin-bottom:10px; font-weight:bold;"><?php echo esc_html($name); ?></div>
            <h3 class="entry-title" style="font-size:1.5rem; margin-bottom:10px;"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <div class="entry-summary" style="font-size:0.95rem; color:var(--color-text-muted);"><?php echo wp_trim_words(get_the_excerpt(), 15); ?></div>
        </div>
    <?php endwhile; endif; wp_reset_postdata(); 
endforeach;
$local_content = ob_get_clean();

if ( !empty(trim($local_content)) ) :
?>
<section class="wapo-category-section local-zone">
    <h2 class="wapo-section-title"><span>Noticias Locales</span></h2>
    <div class="local-news-block">
        <?php echo $local_content; ?>
    </div>
</section>
<?php endif; ?>
