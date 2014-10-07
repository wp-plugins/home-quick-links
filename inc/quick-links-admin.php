<?php
// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'add_meta_boxes', 'armd_ql_add_meta_box' );
// add URL metabox
function armd_ql_add_meta_box() {
    add_meta_box( 'armd-ql-url', 'Image Link', 'armd_ql_add_meta', 'home_quick_link', 'normal' );
}

// print URL metabox content
function armd_ql_add_meta( $post ) {

    // Get existing data, if any
    $this_url = get_post_meta( $post->ID, 'armd_ql_url', true );

    // Add an nonce field so we can check for it later.
    wp_nonce_field( 'armd_ql_form_picker_meta_box', 'armd_ql_form_picker_meta_box_nonce' );

    echo '<input type="url" name="armd_ql_url" placeholder="' . get_home_url() . '/"';
        // fill with existing data, if present
        if ( isset( $this_url ) ) { echo ' value="' . $this_url . '" '; }
    echo 'size="100%"><br/>';
    echo '<label for="armd_ql_url">Add the URL this Quick Link should link to.</label>';

}

add_action( 'save_post', 'armd_ql_save_meta_box_data' );
// save URL when post is saved
function armd_ql_save_meta_box_data( $post_id ) {

	// Check if our nonce is set.
	if ( ! isset( $_POST['armd_ql_form_picker_meta_box_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['armd_ql_form_picker_meta_box_nonce'], 'armd_ql_form_picker_meta_box' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'home_quick_link' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_pages', $post_id ) ) {
			return;
		}
	} else {
		if ( ! current_user_can( 'edit_posts', $post_id ) ) {
			return;
		}
	}

	// Make sure that it is set.
	if ( ! isset( $_POST['armd_ql_url'] ) ) {
		return;
	}

	// Sanitize user input.
	$my_data = sanitize_text_field( $_POST['armd_ql_url'] );

	// Update the meta field in the database.
	update_post_meta( $post_id, 'armd_ql_url', $my_data );

}

add_action('do_meta_boxes', 'meta_box_position');
// move featured image metabox
#TODO: figure out why it's not moving
function meta_box_position() {
	remove_meta_box( 'postimagediv', 'post', 'side' );
	add_meta_box('postimagediv', __('Featured Image'), 'post_thumbnail_meta_box', 'post', 'normal', 'high');
}