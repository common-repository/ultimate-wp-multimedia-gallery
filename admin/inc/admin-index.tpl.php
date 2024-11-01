<?php
if (!defined('ABSPATH')) exit;

global $wpdb;
$_wpmg_gallery = $wpdb->prefix . 'a_wpmg_gallery';
$galleries = $wpdb->get_results("SELECT * FROM  $_wpmg_gallery");
?>

<div id="wpmg_wrap">
	<?php include 'admin-header.tpl.php'; ?>

	<div class="CSSTableGenerator" id="tbl-wpmg">
		<p>
			<a href="#TB_inline?&width=400&height=135&inlineId=addNewGalleryThickbox" class="button thickbox wpmgAddNewGallery">
				<span class="dashicons dashicons-plus" style="line-height: 33px;"></span>
				Add New Gallery			
			</a>
			<?php include 'admin-add-new-gallery-thickbox.tpl.php'; ?>
		</p>
        <table class="widefat responsive">
            <thead>
	            <tr class="not-mobile">
	                <td> Name </td>
	                <td> Shortcode </td>
	                <td width="120"> Action </td>
	            </tr>
            </thead>
            <tbody id="filtertbl">
            	<?php foreach ($galleries as $key => $gallery) : ?>
	            <tr>
	                <td data-title="Name" style="width:250px">
	                    <?php echo $gallery->title ?>
	                </td>
	                <td data-title="Shortcode">
	                    <strong><?php echo '[wpm-gallery id="'.$gallery->id.'"]' ?></strong>
	                    OR 
	                    <strong><?php echo '[wpm-gallery id="'.$gallery->id.'" filter="no"]' ?></strong>
	                </td>
	                <td data-title="Action" class="actions">
	                    <a href="<?php echo get_admin_url() . '/admin.php?page=wpm-gallery-add&id=' . $gallery->id ?>" class="button">Edit</a>
	                    <a href="#" data-id="<?php echo $gallery->id ?>" class="button delete-wpmGallery">Delete</a>
	                </td>
	            </tr>
	        	<?php endforeach; ?>
        	</tbody>
        </table>
    </div>
</div>
