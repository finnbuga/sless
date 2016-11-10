<?php

/**
 * Delete unneeded roles
 */
add_action( 'after_switch_theme', 'lfr_delete_roles' );
function lfr_delete_roles() {
	remove_role( 'subscriber' );
}

/**
 * Change the default number of posts per page
 *
 * For most countries there are less than 100 pages.
 * List them all so that it's easy to change the hierarchy with the drag and drop tool.
 */
add_filter( 'edit_posts_per_page', 'lfr_change_default_post_per_page', 10, 2 );
function lfr_change_default_post_per_page( $per_page, $post_type ) {
	return 100;
}

/**
 * Add header and footer widget areas
 */
add_action( 'widgets_init', 'lfr_add_header_and_footer_widget_areas', 11 );
function lfr_add_header_and_footer_widget_areas(){
	register_sidebar( array(
		'name' => __( 'Footer', 'lfr' ),
		'id' => 'sidebar-footer',
		'description' => esc_html__( 'Appears in the footer', 'lfr' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}

/**
 * Cleanup the backend or non-admins
 */
add_action( 'init', 'cleanup_admin' );
function cleanup_admin() {
	if ( ! current_user_can( 'manage_options' ) ) {
		add_action( 'admin_enqueue_scripts', 'lfr_add_backend_stylesheet' );
		add_action( 'admin_bar_menu', 'lfr_cleanup_admin_bar', 999 );
		add_action( 'admin_menu', 'lfr_cleanup_admin_menu', 999 );
		add_filter( 'custom_menu_order', '__return_true' );
		add_filter( 'menu_order', 'lfr_reorder_admin_menu' );
		add_action( 'add_meta_boxes', 'lfr_rename_page_attributes_meta_box' );
	}
}

/**
 * Add backend stylesheet
 */
function lfr_add_backend_stylesheet() {
	wp_enqueue_style( 'guides-admin', get_template_directory_uri() . '/backend.css' );
}

/**
 * Cleanup the admin bar
 */
function lfr_cleanup_admin_bar( $wp_admin_bar ) {
	$menu_items = $wp_admin_bar->get_nodes();

	// Rename the Home menu item
	$site_name = $menu_items['site-name'];
	$site_name->title = is_admin() ? __( 'Home', 'lfr' ) : __( 'Backend', 'lfr' );

	// Keep a copy of the View menu item
	$view = isset($menu_items['view']) ? $menu_items['view'] : null;

	// Keep a copy of the Edit menu item
	$edit = isset($menu_items['edit']) ? $menu_items['edit'] : null;

	// Remove all menu items
	foreach ( $menu_items as $key => $menu_item ) {
		$wp_admin_bar->remove_node( $key );
	}

	// Add the Home and the View / Edit menu items back
	$wp_admin_bar->add_node( $site_name );
	$wp_admin_bar->add_node( $view );
	$wp_admin_bar->add_node( $edit );
}

/**
 * Cleanup the admin menu
 *
 * Remove menu items that are not needed.
 */
function lfr_cleanup_admin_menu() {
	// Dashboard
	remove_menu_page( 'index.php' );
	// Posts
	remove_menu_page( 'edit.php' );
	// Pages
	remove_menu_page( 'edit.php?post_type=page' );
	// Media
	remove_menu_page( 'upload.php' );
	// Tools
	remove_menu_page( 'tools.php' );
	// Users submenus
	remove_submenu_page( "users.php", "user-new.php" );
	remove_submenu_page( "users.php", "profile.php" );
	// Post types submenus
	foreach ( get_post_types() as $post_type ) {
		remove_submenu_page( "edit.php?post_type=$post_type", "post-new.php?post_type=$post_type" );
	}
}

/**
 * Reorder the admin menu
 *
 * Move Users and Pages on top
 */
function lfr_reorder_admin_menu( $menu_ord ) {
	return array(
		'users.php',
		'edit.php?post_type=page',
	);
}

/**
 * Rename "Page Attributes" meta box to "Hierarchy"
 *
 * It's less confusing
 *
 * @global array $wp_meta_boxes
 */
function lfr_rename_page_attributes_meta_box() {
	global $wp_meta_boxes;
	foreach ( get_post_types() as $post_type ) {
		if ( isset( $wp_meta_boxes[ $post_type ]['side']['core']['pageparentdiv']['title'] ) ) {
			$wp_meta_boxes[ $post_type ]['side']['core']['pageparentdiv']['title'] = __( 'Hierarchy' );
		}
	}
}
