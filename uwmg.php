<?php
if (!defined('ABSPATH')) exit;
/*
	Plugin Name: Ultimate WP Multimedia Gallery
	Plugin URI: https://webpuzzlemaster.com/wordpress-multimedia-gallery/
	Description: Free responsive multimedia gallery displaying images and embedded video from YouTube and Vimeo while integrating social sharing and SEO elements.
	Author: Masud Rana
	Version: 1.0
	Author URI: http://w3codemaster.com/
	License: GPLv2 or later
*/

if( !class_exists('UWMGPRO') ){
	define( 'WPMG_VERSION', '1.0' );
	define( 'WPMG_MINIMUM_WP_VERSION', '4.7' );
	define( 'WPMG__DIR', plugin_dir_path( __FILE__ ) );
	define( 'WPMG__URL', plugins_url( '',__FILE__ ) );
}

class WPMG {
	private static $initiated = false;
	private static $_db_version = '1.0';
	public static $post_type = 'uwmg';
	public static $licenced = false;

	public static function init() {
		if ( ! self::$initiated && !class_exists('UWMGPRO') ) {
			self::init_hooks();
			self::wpmg_post_type();
		}
	}

	/*******************************
	 * Initializes WordPress hooks
	 *******************************/
	private static function init_hooks() {
		self::$initiated = true;
		include WPMG__DIR . 'admin/admin.php';
		$admin = new wpmgAdmin();
		$admin->init();

		include WPMG__DIR . 'front/front.php';
		$front = new wpmgFront();
		$front->init();
	}

	public static function wpmg_post_type(){
		$labels = array(
	        'name'                  => _x( 'Galleries', 'Post type general name', 'gallery' ),
	        'singular_name'         => _x( 'Gallery', 'Post type singular name', 'gallery' ),
	        'menu_name'             => _x( 'Galleries', 'Admin Menu text', 'gallery' ),
	        'name_admin_bar'        => _x( 'Gallery', 'Add New on Toolbar', 'gallery' ),
	        'add_new'               => __( 'Add New', 'gallery' ),
	        'add_new_item'          => __( 'Add New gallery', 'gallery' ),
	        'new_item'              => __( 'New Gallery', 'gallery' ),
	        'edit_item'             => __( 'Edit Gallery', 'gallery' ),
	        'view_item'             => __( 'View Gallery', 'gallery' ),
	        'all_items'             => __( 'All Galleries', 'gallery' ),
	        'search_items'          => __( 'Search Galleries', 'gallery' ),
	        'parent_item_colon'     => __( 'Parent Galleries:', 'gallery' ),
	        'not_found'             => __( 'No galleries found.', 'gallery' ),
	        'not_found_in_trash'    => __( 'No galleries found in Trash.', 'gallery' ),
	        'featured_image'        => _x( 'Gallery Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'gallery' ),
	        'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'gallery' ),
	        'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'gallery' ),
	        'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'gallery' ),
	        'archives'              => _x( 'Gallery archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'gallery' ),
	        'insert_into_item'      => _x( 'Insert into gallery', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'gallery' ),
	        'uploaded_to_this_item' => _x( 'Uploaded to this gallery', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'gallery' ),
	        'filter_items_list'     => _x( 'Filter galleries list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'gallery' ),
	        'items_list_navigation' => _x( 'Galleries list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'gallery' ),
	        'items_list'            => _x( 'Galleries list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'gallery' ),
	    );     
	    $args = array(
	        'labels'             => $labels,
	        'description'        => 'Gallery custom post type.',
	        'public'             => true,
	        'publicly_queryable' => true,
	        'show_ui'            => true,
	        'show_in_menu'       => false,
	        'query_var'          => true,
	        'rewrite'            => array( 'slug' => WPMG::$post_type ),
	        'capability_type'    => 'post',
	        'has_archive'        => false,
	        'hierarchical'       => false,
	        'menu_position'      => 20,
	        'supports'           => array( 'title', 'editor' ),
	        'taxonomies'         => [], // array( 'category', 'post_tag' ),
	        'show_in_rest'       => false
	    );
	    
	    register_post_type( WPMG::$post_type, $args );
	}
}
if( !class_exists('UWMGPRO') )
	add_action( 'init', array( 'WPMG', 'init' ) );

