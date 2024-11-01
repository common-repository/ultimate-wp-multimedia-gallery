<?php
if (!defined('ABSPATH')) exit;
class wpmgFront {
	private static $initiated = false;

	public function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}

	/**
	 * Initializes WordPress hooks
	 */
	private static function init_hooks() {
		self::$initiated = true;
		add_action( 'wp_enqueue_scripts', ['wpmgFront', '_load_script'] );
		add_shortcode( 'wpm-gallery', ['wpmgFront', 'wpmgGalleryShortcode'] );
	}

	public static function _load_script(){
		
	}

	public static function getVimeoThumb($id) {
		$request = wp_remote_get( "https://vimeo.com/api/v2/video/{$id}.json" );
		if( is_wp_error( $request ) ) {
			return WPMG__URL . '/front/images/video_1280x720.jpg';
		}
		$body = json_decode(wp_remote_retrieve_body( $request ));
		if( is_array($body) && count($body) > 0 ){
			$body = array_pop($body);
			return $body->thumbnail_large;
		} else {
			return WPMG__URL . '/front/images/video_1280x720.jpg';
		}
	}

	public static function _getGalleryItems($_id){
		global $wpdb;
		$_a_wpmg_gallery_items = $wpdb->prefix . 'a_wpmg_gallery_items';
		$gall_items = $wpdb->get_results("SELECT * FROM  $_a_wpmg_gallery_items where gallery_id = {$_id} ORDER BY $_a_wpmg_gallery_items.`id` DESC");
		return ( count($gall_items) > 0 ) ? $gall_items : [];
	}

	public static function wpmgGalleryShortcode($attr){
		$_id = (int)$attr['id'];
		$gall_items = SELF::_getGalleryItems($_id);
		ob_start();
		include 'inc/index.php';
		wp_enqueue_style( 'uwmg-style', plugins_url( '/style/style.css' , __FILE__ ) );

		wp_enqueue_script( 'uwmg-libs', 		plugins_url( '/script/wpmg.libs.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
		wp_enqueue_script( 'uwmg-script', 		plugins_url( '/script/wpmg-script.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );
		wp_localize_script( 'uwmg-script', 'wpmg', array(
		        'ajax'     => admin_url('admin-ajax.php'),
		        'site_url' => get_site_url(),
		        'hashtag'  => '',
		        'youtubeChaneelId' 	=> '',
		        'lightBoxType' 		=> get_option('lightBoxType'),
		        'isPro'    => 'false'
		    )
		);
		return ob_get_clean();
	}

	public static function wpmg_get_filters($gallery_id = 0){
		global $wpdb;
		$_wpmg_gallery_tags = $wpdb->prefix . 'a_wpmg_gallery_tags';
		$get_tags = $wpdb->get_results(" SELECT * FROM  $_wpmg_gallery_tags WHERE gallery_id = $gallery_id  ORDER BY $_wpmg_gallery_tags.`menu_order` ASC");
		?>
		<div class="wpmg-filter controls <?php echo get_option('wpmg-filter-align') ?> wpmg-filter-<?php echo $gallery_id ?>">
			<div class="mixitup_menu_group simplefilter">
				<button type="button" class="control" data-filter="all">Show All</button>
				<?php foreach ($get_tags as $key => $tag) {
					if( strtoupper($tag->title) == "ALL" && $tag->first == '0' ) continue;
					$ifDefault = ( $tag->first == '1' ) ? 'default-tag active' : '';
					echo '<button type="button" class="control fltr-controls '.$ifDefault.'" data-filter="'.$tag->id.'">'.$tag->title.'</button>';
				} ?>
			</div>
			
			<div class="mixitup_menu_group mixitup_menu_dropdown">
				<select name="wpmg-filter-dropdown" id="wpmg-filter-dropdown">
					<!-- <option value=".filter-11">All</option> -->
					<?php foreach ($get_tags as $key => $tag) {
						if( strtoupper($tag->title) == "ALL" && $tag->first == '0' ) continue;
						$ifDefault = ( $tag->first == '1' ) ? 'selected' : '';
						echo '<option '.$ifDefault.' value="'.$tag->id.'">'.$tag->title.'</option>';
					} ?>
				</select>
			</div>

		</div>
		<?php
	}

	public static function wpmg_get_tag_ref($tags, $idOnly = false){
		$tagsArr = explode(',', $tags);
		$srt     = ''; 

		$lastElement = end($tagsArr);
		foreach ($tagsArr as $key => $tag) {
			if($idOnly){
				if($tag == $lastElement)
					$srt .= (int)$tag . '';
				else
					$srt .= (int)$tag . ', ';
			}
			else {
				$srt .= 'filter-'.(int)$tag . ' ';
			}

		}

		
		return $srt;
	}

	public static function slugify($text)
	{
		// replace non letter or digits by -
		$text = preg_replace('~[^\pL\d]+~u', '-', $text);

		// transliterate
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);

		// trim
		$text = trim($text, '-');

		// remove duplicate -
		$text = preg_replace('~-+~', '-', $text);

		// lowercase
		$text = strtolower($text);

		if (empty($text)) {
			return 'n-a';
		}

		return $text;
	}

	public static function frontDebug(){
		
	}
}
?>