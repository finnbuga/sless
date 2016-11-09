<?php

/**
 * Add stylesheet
 */
add_action( 'wp_enqueue_scripts', 'lfr_add_stylesheet' );
function lfr_add_stylesheet() {
	wp_enqueue_style( 'dashicons' );
	wp_enqueue_style( 'lfr-frontend-css', get_template_directory_uri() . '/frontend.css', 'dashicons' );
}


/**
 * Remove 'Private: ' from private posts titles
 */
add_filter( 'the_title', 'lrf_remove_private_from_title' );
function lrf_remove_private_from_title( $title, $id = null ) {
	return get_post_status( $id ) == 'private' ?
		preg_replace( '/^Private: /', '', $title ) :
		$title;
}

/**
 * Redirect user to homepage on login / logout
 */
add_filter( 'login_redirect', create_function( '$url,$query,$user', 'return home_url();' ), 10, 3 );
add_filter( 'logout_redirect', create_function( '$url,$query,$user', 'return home_url();' ), 10, 3 );

/**
 * Print breadcrumb
 */
function lfr_print_breadcrumb( $post = null ) {
	if ( ! $ancestors = get_post_ancestors( $post ) ) {
		return;
	}
	// Get parents in the right order
	$ancestors = array_reverse( $ancestors );

	echo '<div id="breadcrumb">';
	foreach ( $ancestors as $ancestor ) {
		echo '<li><a href="' . esc_url(get_permalink( $ancestor )) . '" title="' . get_the_title( $ancestor ) . '">' . get_the_title( $ancestor ) . '</a></li>';
	}
	echo '<li>' . get_the_title() . '</li>';
	echo '</div>';
}

/**
 * Remove "Int-" from the Widget titles
 *
 * "Int-" is used to mark International guides that have the old hierarchy structure. E.g. Int-USA, Int-Canada.
 * This should not be displayed on the front end.
 */
add_filter( 'widget_title', 'lfr_hide_int' );
function lfr_hide_int( $widget_title ) {
	return substr( $widget_title, 0, 4 ) === 'Int-' ? substr( $widget_title, 4 ) : $widget_title;
}
