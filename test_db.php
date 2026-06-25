<?php
require_once('../../../wp-load.php');
$posts = get_posts(array('post_type' => 'cartel', 'numberposts' => 5));
foreach($posts as $p) {
    echo $p->post_title . " -> " . get_post_meta($p->ID, '_cartel_pdf_url', true) . "\n";
}
