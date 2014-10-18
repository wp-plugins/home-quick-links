<?php
/* Prevent this file from being accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Add in post content:
 * [quick_links]
 *
 * Can also be called directly in theme PHP file:
 * <?php if ( function_exists( 'home_quick_links' ) ) { home_quick_links(); } ?>
 */
function home_quick_links_shortcode() {
    ob_start();
    home_quick_links();
    $quick_links_content = ob_get_clean();
    return $quick_links_content;
}
add_shortcode( 'quick_links', 'home_quick_links_shortcode' );

// the quick links loop
function home_quick_links() {

    // get all home_quick_link posts
    $home_links_query = new WP_Query( 'post_type=home_quick_link' );

    // The Loop
    if ( $home_links_query->have_posts() ) {

        // start content container
        $output = '<section class="home-quick-links-container">';

        while ( $home_links_query->have_posts() ) {

            $home_links_query->the_post();
            $URL = get_post_meta( get_the_ID(), 'armd_ql_url', true );
            $image_URL_array = wp_get_attachment_image_src( get_post_thumbnail_ID( get_the_ID() ), 'full' );
            $image_URL = reset( $image_URL_array );

            $output .= '<figure class="home-quick-link">';
            $output .= '<a href="' . $URL . '"><img src="' . $image_URL . '" /></a>';
            $output .= '</figure>';

        }
        $output .= '</section><!-- .home-links-container -->';

        echo $output;

    } else {
        // no posts found
    }

    // Restore original Post Data
    wp_reset_postdata();

}
