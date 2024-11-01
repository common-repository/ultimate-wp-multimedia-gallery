<?php
if (!defined('ABSPATH')) exit;

register_activation_hook( __FILE__, array( 'wpmgAdmin', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'wpmgAdmin', 'plugin_deactivation' ) );

class wpmgAdmin {
	private static $initiated = false;
	public static $wpmg_db_version = '1.0';

	public function init() {
		if ( ! SELF::$initiated ) {
			SELF::init_hooks();
		}
	}

	/**
	 * Initializes WordPress hooks
	 */
	private static function init_hooks() {
		SELF::$initiated = true;
		// add_action( 'plugins_loaded', ['wpmgAdmin', '_update_db_check'] );
		add_action( 'admin_enqueue_scripts', ['wpmgAdmin', '_admin_script'] );
		add_action( 'admin_menu', ['wpmgAdmin', '_admin_menu'] );
		add_action( 'wp_ajax_wpmg_save_gallery_items', ['wpmgAdmin', 'wpmg_save_gallery_items'] );
		add_action( 'wp_ajax_wpmg_update_item_ajax', ['wpmgAdmin', 'wpmg_update_item_action'] );
		add_action( 'wp_ajax_wpmg_delete_item', ['wpmgAdmin', 'wpmg_delete_item'] );
		add_action( 'wp_ajax_wpmg_add_tags', ['wpmgAdmin', 'wpmg_add_tags_action'] ); 
		add_action( 'wp_ajax_wpmg_delete_tags', ['wpmgAdmin', 'wpmg_delete_tags_action'] ); 
		add_action( 'wp_ajax_wpmg_create_gallery_ajax', ['wpmgAdmin', 'wpmg_create_gallery_action'] ); 
		add_action( 'wp_ajax_wpmg_delete_gallery', ['wpmgAdmin', 'wpmg_delete_gallery_action'] ); 
		add_action( 'wp_ajax_wpmg_update_tags_ajax', ['wpmgAdmin', 'wpmg_update_tags_action'] ); 
		add_action( 'wp_ajax_wpmg_update_default_tags_ajax', ['wpmgAdmin', 'wpmg_update_default_tags_action'] ); 
		add_action( 'wp_ajax_wpmg_filter_settings_ajax', ['wpmgAdmin', 'wpmg_filter_settings_action'] );
		add_action( 'wp_ajax_wpmg_paginate_settings_ajax', ['wpmgAdmin', 'wpmg_paginate_settings_action'] );
		add_action( 'wp_ajax_wpmg_general_settings_ajax', ['wpmgAdmin', 'wpmg_general_settings_action'] );
		add_action( 'wp_ajax_wpmg_filter_alignment_ajax', ['wpmgAdmin', 'wpmg_filter_alignment_action'] );
		add_action( 'wp_ajax_wpmg_update_filter_order', ['wpmgAdmin', 'wpmg_update_filter_order_action'] );

		add_action( 'wp_ajax_searchGalleryItems', ['wpmgAdmin', 'searchGalleryItems_action'] );

		SELF::plugin_activation();
		
	}

	public static function plugin_activation() {
        flush_rewrite_rules();
        global $wpmg_db_version;
        if ( get_site_option( 'wpmg_db_version' ) != SELF::$wpmg_db_version ) 
            SELF::UWMG_admin_install();
    }

    public static function plugin_deactivation(){
        flush_rewrite_rules();
    }

	public static function _update_db_check() {
        global $wpmg_db_version;
        if ( get_site_option( 'wpmg_db_version' ) != $wpmg_db_version ) 
            SELF::UWMG_admin_install();
    }

    public static function UWMG_admin_install() {
        global $wpdb;
        global $wpmg_db_version;

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'a_wpmg_gallery';
        $sqlg = "CREATE TABLE $table_name (
            `id` mediumint(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) NOT NULL,
            `title` varchar(191) NOT NULL,
            `status` int(1) NOT NULL,
            PRIMARY KEY (`id`)
        ) $charset_collate;";
        dbDelta( $sqlg );

        $gallery_items = $wpdb->prefix . 'a_wpmg_gallery_items';
        $sqli = "CREATE TABLE $gallery_items (
            `id` mediumint(11) NOT NULL AUTO_INCREMENT,
            `gallery_id` int(11) NOT NULL,
            `attachment_id` int(11) NOT NULL,
            `image` varchar(191) NOT NULL,
            `caption` varchar(191) NOT NULL,
            `description` varchar(191) NOT NULL,
            `type` varchar(191) NOT NULL DEFAULT 'image',
            `url` varchar(191) NOT NULL,
            `tags` varchar(191) NOT NULL,
            `post_id` int(11) NOT NULL,
            `cta` varchar(191) NOT NULL,
            `cta_text` varchar(191) NOT NULL,
            `subscribe` tinyint(1) NOT NULL DEFAULT '0',
            PRIMARY KEY (`id`)
        ) $charset_collate;";
        dbDelta( $sqli );

        $table_name = $wpdb->prefix . 'a_wpmg_gallery_tags';
        $sqlt = "CREATE TABLE $table_name (
            `id` mediumint(11) NOT NULL AUTO_INCREMENT,
            `title` varchar(191) NOT NULL,
            `gallery_id` int(11) NOT NULL,
            `first` tinyint(1) NOT NULL DEFAULT '0',
            `menu_order` int(11) NOT NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) $charset_collate;";
        dbDelta( $sqlt );

        $table_name = $wpdb->prefix . 'a_wpmg_gallery_item_tag_terms';
        $sqlt = "CREATE TABLE $table_name (
            `id` mediumint(11) NOT NULL AUTO_INCREMENT,
            `item_id` int(11) NOT NULL,
            `tag_id` int(11) NOT NULL,
            PRIMARY KEY (`id`)
        ) $charset_collate;";
        dbDelta( $sqlt );

        update_option( 'wpmg_db_version', SELF::$wpmg_db_version );
    }

	public static function _admin_script(){
		wp_enqueue_style( 'wpmg-admin-style', plugins_url( '/style/wpmg-admin-style.css' , __FILE__ ) );
    	wp_enqueue_script( 'wpmg-admin-script', plugins_url( '/script/script.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
    	wp_localize_script( 'wpmg-admin-script', 'wpmg', array(
	            'ajax'     => admin_url('admin-ajax.php'),
	            'site_url' => get_site_url()
	        )
		);
	}

	public static function _admin_menu(){
		add_menu_page('UWPM Gallery', 'UWPM Gallery', 'edit_posts', 'wpmg-settings', ['wpmgAdmin', 'wpmg_settings'], plugins_url( '/images/icon.png' , __FILE__ ), 40);
        add_submenu_page( 'wpmg-settings', 'Settings', 'Settings', 'edit_posts', 'wpmg-settings', ['wpmgAdmin','wpmg_settings']);
        add_submenu_page( 'wpmg-settings', 'Galleries', 'Galleries', 'edit_posts', 'wpm-gallery', ['wpmgAdmin','wpmg_index']);
        add_submenu_page( 'wpmg-settings', 'Add Gallery', 'Add Gallery', 'edit_posts', 'wpm-gallery-add', ['wpmgAdmin','wpmg_add']);
	}


	public static function wpmg_settings(){
		include WPMG__DIR . '/admin/inc/admin-settings.tpl.php';
	}

	public static function wpmg_index(){
		include WPMG__DIR . '/admin/inc/admin-index.tpl.php';
	}

	public static function wpmg_add(){
		include WPMG__DIR . '/admin/inc/admin-add.tpl.php';
	}

	public static function wpmg_save_gallery_items(){
		$ids   			= (int)$_POST['ids'];
		$gallery_id 	= (int)$_POST['gallery_id'];
		$attachment_id 	= (int)$_POST['attachment_id'];
		$image 			= sanitize_text_field($_POST['image']);
		$caption 		= sanitize_text_field($_POST['caption']);
		$description 	= sanitize_text_field($_POST['description']);
		$type 			= sanitize_text_field($_POST['type']);
		$url 			= sanitize_text_field($_POST['url']);		

		$video 			= (isset($_POST['video']) && sanitize_text_field($_POST['video']) == 'true') ? true : false;

		if( count($_POST) < 9 || count($_POST) > 10 ){
			echo json_encode([ 'success' => false ]);
			exit;
		}

		global $wpdb;
		$item_tbl = $wpdb->prefix . 'a_wpmg_gallery_items';
		
		$wpdb->insert(
			$item_tbl,
			[
				'gallery_id' 	=> $gallery_id,
				'attachment_id' => $attachment_id,
				'image' 		=> ( $video ) ? WPMG__URL.'/admin/images/video_1280x720.jpg' : $image,
				'caption' 		=> $caption,
				'description' 	=> $description,
				'type' 			=> $type
			],
			[ '%d', '%d', '%s', '%s', '%s', '%s' ]
		);

		echo json_encode([ 'success' => true ]);
		exit;
	}

	public static function wpmg_update_item_action(){
		global $wpdb;
		$item_tbl = $wpdb->prefix . 'a_wpmg_gallery_items';

		$item_id 		= (int)$_POST['id'];
		$_caption 		= sanitize_text_field($_POST['caption']);
		$_description 	= sanitize_text_field($_POST['description']);
		$_type 			= sanitize_text_field($_POST['type']);
		$_url 			= sanitize_text_field($_POST['url']);
		$_tags 			= sanitize_text_field($_POST['tags']);

		$_cta 			= (WPMG::$licenced) ? esc_url_raw($_POST['cta']) : '';
		$_cta_text	 	= (WPMG::$licenced) ? sanitize_text_field($_POST['cta_text']) : '';
		$_subscribe 	= (WPMG::$licenced) ? sanitize_text_field($_POST['subscribe']) : 'false';

		$synch   = [];
		$update  = false;
		$putPost = false;

		// update post
		$get_item 	= $wpdb->get_results("SELECT * FROM $item_tbl WHERE id = $item_id LIMIT 1");
		if( count($get_item) == 1 ){
			$get_item = $get_item[0];

			/***********Update gItem*************/
			$update = $wpdb->update(
				$item_tbl,
				[
					'caption' 		=> $_caption,
					'description' 	=> $_description,
					'type' 			=> $_type,
					'url' 			=> $_url,
					'tags' 			=> $_tags,
					'cta' 			=> $_cta,
					'cta_text' 		=> $_cta_text,
					'subscribe' 	=> ($_subscribe == 'true') ? 1 : 0,
				],
				[ 'id' => $item_id ],
				[ '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d' ],
				[ '%d' ]
			);

			if( strlen($_tags) >= 1 )
				$synch = wpmgAdmin::synch_item_tags( $item_id, explode(',', $_tags) );

			/***********Update gPost*************/

			if( $_type == 'youtube' ){
				$post_content = '<div class="fitVids-wrapper">
							<iframe width="1200" height="675" src="https://www.youtube.com/embed/'.$_url.'?controls=0&autoplay=1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
						</div>';
				$seo_image = 'https://i3.ytimg.com/vi/'.$_url.'/hqdefault.jpg';
			} elseif ( $_type == 'vimeo' ) {
				$post_content = '<div class="fitVids-wrapper">
							<iframe src="https://player.vimeo.com/video/'.$_url.'?autoplay=1" width="1200" height="675" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
						</div>';
				$seo_image = wpmgFront::getVimeoThumb($_url);
			} else {
				$post_content = '<img src="'.$get_item->image.'" alt="'.$_caption.'" style="max-width: 100%">';
				$seo_image = $get_item->image;
			}

			$thePost = get_post($get_item->post_id);
			if( $thePost == null ){
				// carete post
				$putPost = wp_insert_post(
					[
						'post_title' 	=> wp_strip_all_tags( $_caption ),
						'post_content'  => $post_content,
	  					'post_status'   => 'publish',
	  					'post_type'     => WPMG::$post_type,
					]
				);
				if( $putPost ){
					$wpdb->update( $item_tbl, [ 'post_id' => $putPost ], [ 'id' => $item_id ] );

					update_post_meta( $putPost, '_wp_page_template', 'template-microsite.php' );
					update_post_meta( $putPost, '_dt_sidebar_position', 'disabled' );
					update_post_meta( $putPost, '_dt_header_title', 'disabled' );

					// SEO Tags
					update_post_meta( $putPost, '_yoast_wpseo_opengraph-title', wp_strip_all_tags( $_caption ) );
					update_post_meta( $putPost, '_yoast_wpseo_opengraph-description', wp_strip_all_tags( $_description.' ' . get_option('social-media-hastag') ) );
					update_post_meta( $putPost, '_yoast_wpseo_opengraph-image', $seo_image );

					update_post_meta( $putPost, '_yoast_wpseo_twitter-title', wp_strip_all_tags( $_caption ) );
					update_post_meta( $putPost, '_yoast_wpseo_twitter-description', wp_strip_all_tags( $_description.' ' . get_option('social-media-hastag') ) );
					update_post_meta( $putPost, '_yoast_wpseo_twitter-image', $seo_image );
				}
			} else {
				// update post
				if( $thePost->post_type == WPMG::$post_type ){
					$putPost = wp_update_post(
						[
							'ID' 			=> $get_item->post_id,
							'post_title' 	=> wp_strip_all_tags( $_caption ),
							'post_content'  => $post_content,
		  					'post_status'   => 'publish',
		  					'post_type'     => WPMG::$post_type,
						]
					);

					// SEO Tags
					update_post_meta( $thePost->ID, '_yoast_wpseo_opengraph-title', wp_strip_all_tags( $_caption ) );
					update_post_meta( $thePost->ID, '_yoast_wpseo_opengraph-description', wp_strip_all_tags( $_description.' ' . get_option('social-media-hastag') ) );
					update_post_meta( $thePost->ID, '_yoast_wpseo_opengraph-image', $seo_image );

					update_post_meta( $thePost->ID, '_yoast_wpseo_twitter-title', wp_strip_all_tags( $_caption ) );
					update_post_meta( $thePost->ID, '_yoast_wpseo_twitter-description', wp_strip_all_tags( $_description.' ' . get_option('social-media-hastag') ) );
					update_post_meta( $thePost->ID, '_yoast_wpseo_twitter-image', $seo_image );
				}
			}
		}
	
		echo json_encode(['success' => $update, 'synch' => $synch]);
		exit;
	}

	public static function wpmg_delete_item(){
		$_id 	= (int)$_POST['id'];
		global $wpdb;
		$item_tbl = $wpdb->prefix . 'a_wpmg_gallery_items';
		$delete = $wpdb->delete( $item_tbl, [ 'id' => $_id ], [ '%d' ] );
		echo json_encode(['success' => $delete]);
		exit;
	}

	public static function wpmg_add_tags_action(){
		global $wpdb;
		$tag_tbl 	= $wpdb->prefix . 'a_wpmg_gallery_tags';
		$gallery_id = (int)$_POST['gallery_id'];
		$title 		= sanitize_text_field($_POST['title']);
		$getTags 	= $wpdb->get_results("SELECT * FROM $tag_tbl WHERE `gallery_id` = $gallery_id ORDER BY `wx_a_wpmg_gallery_tags`.`menu_order` ASC");
		if( count($getTags) > 0 ) {
			$lastNumber = array_pop($getTags);
			$lastNumber = ++$lastNumber->menu_order;
		} else {
			$lastNumber = 1;
		}
		$insert = $wpdb->insert( $tag_tbl,
			[ 'title' => $title, 'gallery_id' => $gallery_id, 'menu_order' => $lastNumber ],
			[ '%s', '%d', '%d' ]
		);
		echo json_encode(['success' => $insert, 'id' => $wpdb->insert_id]);
		exit;
	}

	public static function wpmg_delete_tags_action(){
		$_id 	= (int)$_POST['id'];
		global $wpdb;
		$item_tbl 		= $wpdb->prefix . 'a_wpmg_gallery_tags';
		$tag_terms_tbl 	= $wpdb->prefix . 'a_wpmg_gallery_item_tag_terms';

		$delete = $wpdb->delete( $item_tbl, [ 'id' => $_id ], [ '%d' ] );
		// remove relations 
		$trms = $wpdb->delete( $tag_terms_tbl, [ 'tag_id' => $_id ], [ '%d' ] );

		echo json_encode(['success' => $delete]);
		exit;
	}

	public static function wpmg_create_gallery_action(){
		global $wpdb;
		$tag_tbl = $wpdb->prefix . 'a_wpmg_gallery';
		$title   = sanitize_text_field($_POST['title']);
		$insert = $wpdb->insert( $tag_tbl, [ 'title' => $title ], [ '%s' ] );
		echo json_encode(['success' => $insert, 'redirect' => get_admin_url().'/admin.php?page=wpm-gallery-add&id='.$wpdb->insert_id]);
		exit;
	}

	public static function wpmg_delete_gallery_action(){
		$_id 	= (int)$_POST['id'];

		global $wpdb;
		$gall_tbl 	= $wpdb->prefix . 'a_wpmg_gallery';
		$item_tbl 	= $wpdb->prefix . 'a_wpmg_gallery_items';
		$delete 	= $wpdb->delete( $gall_tbl, [ 'id' => $_id ], [ '%d' ] );

		if( $delete )
			$wpdb->delete( $item_tbl, [ 'gallery_id' => $_id ], [ '%d' ] );

		echo json_encode(['success' => $delete]);
		exit;
	}

	public static function wpmg_update_tags_action(){
		$_id 	= (int)$_POST['id'];

		global $wpdb;
		$item_tbl = $wpdb->prefix . 'a_wpmg_gallery_tags';
		$title    = sanitize_text_field($_POST['title']);
		$update = $wpdb->update( $item_tbl, [ 'title' => $title ], [ 'id' => $_id ], [ '%s' ], [ '%d' ] );
		echo json_encode(['success' => $update]);
		exit;
	}

	public static function wpmg_update_default_tags_action(){
		$tagId = (int)$_POST['id'];
		$gId   = (int)$_POST['_gid'];
		
		global $wpdb;
		$item_tbl = $wpdb->prefix . 'a_wpmg_gallery_tags';
		$find  	  = $wpdb->get_results(" SELECT * FROM  $item_tbl WHERE `first` = 1 AND gallery_id = $gId");
		if( count($find) > 0 ){
			foreach ($find as $key => $defaultTags) {
				$wpdb->update( $item_tbl, [ 'first' => 0 ], [ 'id' => $defaultTags->id ], [ '%d' ], [ '%d' ] );
			}
		}
		$update = $wpdb->update( $item_tbl, [ 'first' => 1 ], [ 'id' => $tagId, 'gallery_id' => $gId ], [ '%d' ], [ '%d', '%d' ] );
		echo json_encode(['success' => $update]);
		exit;
	}


	public static function wpmg_filter_settings_action(){
		if( is_array($_POST) && count($_POST) <= 8 ){
			$dataKeySet = ['filter-wrapper-bg', 'filter-text-color', 'filter-bg-color', 'filter-border-color', 'act-filter-text-color',
			 'act-filter-bg-color', 'act-filter-border-color'];

			foreach ($dataKeySet as $key => $value)
				if( isset($_POST[$value]) )
					update_option($value, sanitize_text_field($_POST[$value]), false);
		}
		exit;	
	}

	public static function wpmg_paginate_settings_action(){
		if( is_array($_POST) && count($_POST) <= 5 ){
			$dataKeySet = ['paginate-text-color', 'paginate-bg-color', 'act-paginate-text-color', 'act-paginate-bg-color'];

			foreach ($dataKeySet as $key => $value)
				if( isset($_POST[$value]) )
					update_option($value, sanitize_text_field($_POST[$value]), false);
		}
		exit;	
	}

	public static function wpmg_general_settings_action(){
		if( is_array($_POST) && count($_POST) <= 4 ){
			$dataKeySet = ['youtube-chaneel-id', 'social-media-hastag', 'lightBoxType'];

			foreach ($dataKeySet as $key => $value)
				if( isset($_POST[$value]) )
					update_option($value, sanitize_text_field($_POST[$value]), false);
		}
		exit;
	}

	public static function wpmg_filter_alignment_action(){
		if( is_array($_POST) && count($_POST) <= 2 ){
			$dataKeySet = ['wpmg-filter-align' => 'align'];

			foreach ($dataKeySet as $key => $value)
				if( isset($_POST[$value]) )
					update_option($key, sanitize_text_field($_POST[$value]), false);
		}
		exit;
	}

	public static function synch_item_tags($item_id = 0, $tags = [1,5]){
		if( $item_id == 0 || count($tags) == 0 ) return false;

		global $wpdb;
		$tag_terms_tbl = $wpdb->prefix . 'a_wpmg_gallery_item_tag_terms';

		$wpdb->delete( $tag_terms_tbl, [ 'item_id' => $item_id ], [ '%d' ] );

		$queryStr = "INSERT INTO $tag_terms_tbl (`id`, `item_id`, `tag_id`) VALUES ";

		$numItems = count($tags);
		$i = 0;
		foreach ($tags as $key => $t) {
			if(++$i === $numItems)
				$queryStr .= "(NULL, $item_id, $t)";
			else
				$queryStr .= "(NULL, $item_id, $t), ";
		}
		return $wpdb->query($queryStr);
	}


	public static function searchGalleryItems_action(){
		$string = sanitize_text_field($_GET['query']);
		$gid    = (int)$_GET['gid'];
		if( $gid < 1 || strlen($string) < 1 ){
			echo json_encode([]);
			exit;
		}
		global $wpdb;
		$items_tbl = $wpdb->prefix.'a_wpmg_gallery_items';
		$get_results = $wpdb->get_results("SELECT * FROM $items_tbl WHERE (`caption` LIKE '%{$string}%' OR `description` LIKE '%{$string}%') AND gallery_id = {$gid}");
		
		$suggestions = [];
		if( count($get_results) > 0 ){
			foreach ($get_results as $key => $item) {
				array_push($suggestions, ['value' => $item->caption, 'data' => $item->caption, 'row' => $item]);
			}
		}
		$json = [
		    "query" => $string,
		    "suggestions" => $suggestions,
		];
		echo json_encode($json);
		exit;
	}

	public static function wpmg_update_filter_order_action(){
		echo json_encode( ['success' => true, 'msg' => 'Future Feature'] );
		exit;
	}
}