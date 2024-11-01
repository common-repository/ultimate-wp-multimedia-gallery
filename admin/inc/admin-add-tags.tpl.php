<?php
if (!defined('ABSPATH')) exit;

global $wpdb;
$_wpmg_gallery_tags = $wpdb->prefix . 'a_wpmg_gallery_tags';
$tags = $wpdb->get_results(" SELECT * FROM  $_wpmg_gallery_tags WHERE gallery_id = $gallery_id  ORDER BY $_wpmg_gallery_tags.`menu_order` ASC");
?>

<div id="wpmg_wraps">

	<div class="CSSTableGenerator" id="tbl-wpmg" style="border: none;padding: 0;margin: 0;background: none; overflow: visible;">
		<p class="addTagWrapper">
			<input type="text" placeholder="Filter name here..">
			<a class="button" href="#">
				<span class="dashicons dashicons-plus"></span> 
				<name>Add Filter</name>
			</a>
			<span class="wpmgCancelTagEdit">x</span>
		</p>
        <table class="widefat responsive">
            <thead>
	            <tr class="not-mobile">
	                <td> Name </td>
	                <td> Default </td>
	                <td width="120"> Action </td>
	            </tr>
            </thead>
            <tbody id="filtertbl" class="tagsListTbl <?php if(WPMG::$licenced) echo 'shortable' ?>">
            	<?php foreach ($tags as $key => $tag) : ?>
	            <tr id="<?php echo $tag->id ?>" data-row="<?php echo $tag->id ?>" title="Drag to Re-order <?php if(!WPMG::$licenced) echo ' (Pro Only)' ?>">
	                <td data-title="Name">
	                    <?php echo $tag->title ?>
	                </td>
	                <td data-title="default">
	                    <input type="radio" name="default-tag" data-id="<?php echo $tag->id; ?>" <?php if($tag->first == '1') echo 'checked'?> value="<?php echo $tag->id; ?>" class="default-tag">
	                </td>
	                <td data-title="Action" class="actions">
	                    <a href="#" data-id="<?php echo $tag->id ?>" class="button delete-wpmgTag">Delete</a>
	                    <a href="#" data-id="<?php echo $tag->id ?>" data-title="<?php echo $tag->title ?>" class="button edit-wpmgTag">Edit</a>
	                </td>
	            </tr>
	        	<?php endforeach; ?>
        	</tbody>
        </table>
    </div>
</div>