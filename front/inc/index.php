<?php
//silance
if (!defined('ABSPATH')) exit;

if( count($gall_items) > 0 ) :
	echo "<div class='wpmg-wrap wpmg-dark wpmg-loading' id='uwmg-{$_id}'>";
	include 'loading-spinner.tpl.php';
?>

	<div class="wpmfgmixer gcontainer <?php echo 'wpmg-'.$_id ?>" data-id="<?php echo $_id ?>">
		<?php 
		if( !isset($attr['filter']) )
			wpmgFront::wpmg_get_filters($_id)
		?>
		<div class="targets filtr-wrapper noFilterizr">
			<?php foreach ($gall_items as $key => $item) :
				if( $item->type == 'youtube' && (int)$item->attachment_id > 0 )
				{
					echo '
					<div data-category="'.wpmgFront::wpmg_get_tag_ref($item->tags, true).'" class="mix filtr-item '.$item->type. ' " data-id="'.$item->id.'">
						<a href="https://www.youtube.com/watch?v='.$item->url.'?rel=0&width=940&height=528" ref="ref-'.$item->id.'" rel="pp['.$item->type.']" title="'.stripslashes($item->description).'-id-'.$item->id.'" caption="'.stripslashes($item->caption).'" data-cta-link="'.$item->cta.'" data-cta-text="'.$item->cta_text.'" 
						data-subs="'.$item->subscribe.'"  data-post="'.get_the_permalink($item->post_id).'"
							class="lozad"
							data-background-image="'.$item->image.'"
						>
							
						</a>
					</div>';
				}
				elseif( $item->type == 'vimeo' && (int)$item->attachment_id > 0 )
				{
					echo '
					<div data-category="'.wpmgFront::wpmg_get_tag_ref($item->tags, true).'" class="mix filtr-item '.$item->type. '" data-id="'.$item->id.'">
						<a href="https://vimeo.com/'.$item->url.'?rel=0&width=940&height=528" ref="ref-'.$item->id.'" rel="pp['.$item->type.']" title="'.stripslashes($item->description).'-id-'.$item->id.'" caption="'.stripslashes($item->caption).'" data-cta-link="'.$item->cta.'" data-cta-text="'.$item->cta_text.'" 
						data-subs="'.$item->subscribe.'" data-post="'.get_the_permalink($item->post_id).'"
							class="lozad"
							data-background-image="'.$item->image.'"
						>
							
						</a>
					</div>';
				}
				elseif( $item->type == 'youtube' && (int)$item->attachment_id == 0 )
				{
					$image = 'https://i3.ytimg.com/vi/'.$item->url.'/hqdefault.jpg'; //maxresdefault
					echo '
					<div data-category="'.wpmgFront::wpmg_get_tag_ref($item->tags, true).'" class="mix filtr-item '.$item->type. '"  data-id="'.$item->id.'">
						<a href="https://www.youtube.com/watch?v='.$item->url.'?rel=0&width=940&height=528" ref="ref-'.$item->id.'" rel="pp['.$item->type.']" title="'.stripslashes($item->description).'-id-'.$item->id.'" caption="'.stripslashes($item->caption).'" data-cta-link="'.$item->cta.'" data-cta-text="'.$item->cta_text.'" 
						data-subs="'.$item->subscribe.'" data-post="'.get_the_permalink($item->post_id).'"
							class="lozad"
							data-background-image="'.$image.'"
						>
							
						</a>
					</div>';
				} 
				elseif ( $item->type == 'vimeo' && (int)$item->attachment_id == 0 ) 
				{
					$image = SELF::getVimeoThumb($item->url);
					echo '
					<div data-category="'.wpmgFront::wpmg_get_tag_ref($item->tags, true).'" class="mix filtr-item '.$item->type. '"  data-id="'.$item->id.'">
						<a href="https://vimeo.com/'.$item->url.'?rel=0&width=940&height=528" ref="ref-'.$item->id.'" rel="pp['.$item->type.']" title="'.stripslashes($item->description).'-id-'.$item->id.'" caption="'.stripslashes($item->caption).'" data-cta-link="'.$item->cta.'" data-cta-text="'.$item->cta_text.'" 
						data-subs="'.$item->subscribe.'" data-post="'.get_the_permalink($item->post_id).'"
							class="lozad"
							data-background-image="'.$image.'"
						>
							
						</a>
					</div>';
				} 
				else
				{
					echo '
					<div data-category="'.wpmgFront::wpmg_get_tag_ref($item->tags, true).'" class="mix filtr-item '.$item->type. '"  data-id="'.$item->id.'">
						<a href="'.$item->image.'?rel=0&width=940&height=528" ref="ref-'.$item->id.'" rel="pp['.$item->type.']" title="'.stripslashes($item->description).'-id-'.$item->id.'" caption="'.stripslashes($item->caption).'" data-cta-link="'.$item->cta.'" data-cta-text="'.$item->cta_text.'" 
						data-subs="'.$item->subscribe.'" data-post="'.get_the_permalink($item->post_id).'"
							class="lozad"
							data-background-image="'.$item->image.'"
						>
							
						</a>
					</div>';
				}
			endforeach; ?>
		</div>

	</div>
	
	<div class="wpmgEnd" data-id="<?php echo $_id ?>"></div>
	<style>
		/*Filter*/
		.wpmg-filter.controls.center-aligned{text-align: center; display: flex; justify-content: center;}
		.wpmg-filter.controls.center-aligned .mixitup_menu_group.mixitup_menu_sort{float: none; display: inline-flex;}
		.wpmg-wrap .wpmg-filter.controls{ 
			<?php if( get_option('filter-wrapper-bg') != "" ) echo 'background: '.get_option('filter-wrapper-bg').';' ?>
		}
		.wpmg-wrap .wpmg-filter button,
		.wpmg-wrap button.mixitup-control{
			<?php if( get_option('filter-text-color') != "" ) echo 'color: '.get_option('filter-text-color').';' ?>
			<?php if( get_option('filter-bg-color') != "" ) echo 'background: '.get_option('filter-bg-color').';' ?>
			<?php if( get_option('filter-border-color') != "" ) echo 'border: 2px solid '.get_option('filter-border-color').';' ?>
		}
		.wpmg-wrap .svg-icon polyline{
			<?php if( get_option('filter-text-color') != "" ) echo 'stroke: '.get_option('filter-text-color').';' ?>
		}
		.wpmg-wrap .mixitup-control-active .svg-icon polyline{
			<?php if( get_option('act-filter-text-color') != "" ) echo 'stroke: '.get_option('act-filter-text-color').';' ?>
		}
		.wpmg-wrap .svg-icon path{
			<?php if( get_option('filter-text-color') != "" ) echo 'fill: '.get_option('filter-text-color').';' ?>
		}
		.wpmg-wrap .mixitup-control-active .svg-icon path {
		    <?php if( get_option('act-filter-text-color') != "" ) echo 'fill: '.get_option('act-filter-text-color').';' ?>
		}
		.wpmg-wrap .wpmg-filter button.mixitup-control-active{
			<?php if( get_option('act-filter-text-color') != "" ) echo 'color: '.get_option('act-filter-text-color').';' ?>
			<?php if( get_option('act-filter-bg-color') != "" ) echo 'background: '.get_option('act-filter-bg-color').';' ?>
			<?php if( get_option('act-filter-border-color') != "" ) echo 'border: 2px solid '.get_option('act-filter-border-color').';' ?>
		}
		<?php if( (get_option('filter-border-color') != "") || (get_option('act-filter-border-color') != "") ) : ?>
		.wpmg-wrap .mixitup_menu_sort button{ height: 40px; width: 40px; }
		<?php endif; ?>
		/*Paginate*/
		.wpmg-wrap .mixitup-page-list button.mixitup-control{
			<?php if( get_option('filter-bg-color') != "" ) echo 'background: '.get_option('filter-bg-color').';' ?>
		}
		.wpmg-wrap button.mixitup-control.mixitup-control-active{
			<?php if( get_option('act-paginate-text-color') != "" ) echo 'color: '.get_option('act-paginate-text-color').';' ?>
			<?php if( get_option('act-paginate-bg-color') != "" ) echo 'background: '.get_option('act-paginate-bg-color').' !important;' ?>
		}
		.wpmg-wrap #wpmg-filter-dropdown{
			<?php if( get_option('filter-text-color') != "" ) echo 'color: '.get_option('filter-text-color').' !important;' ?>
			<?php if( get_option('filter-bg-color') != "" ) echo 'background-color: '.get_option('filter-bg-color').';' ?>
			<?php if( get_option('filter-border-color') != "" ) echo 'border: 2px solid '.get_option('filter-border-color').';' ?>
		}
	</style>
	</div>
<?php
endif;