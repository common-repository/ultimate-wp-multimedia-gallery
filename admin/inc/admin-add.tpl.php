<?php
if (!defined('ABSPATH')) exit;
wp_enqueue_media();
global $wpdb;
$gallery_id = (isset($_GET['id'])) ? (int)$_GET['id'] : 0;

$_a_wpmg_gallery = $wpdb->prefix . 'a_wpmg_gallery';
$_a_wpmg_gallery_items = $wpdb->prefix . 'a_wpmg_gallery_items';
$a_wpmg_items_terms    = $wpdb->prefix . 'a_wpmg_gallery_item_tag_terms';


$gall_result = $wpdb->get_results(" SELECT * FROM  $_a_wpmg_gallery where id = {$gallery_id}");

if( count($gall_result) != 1 ){
	echo '<script>var wpmgTriggerNewGallery = true</script>';
} else {
	echo '<script>var wpmgalleries = '. json_encode($gall_result).'; var gallery_id = '.$gallery_id.'</script>';
	$isTag = ( isset($_GET['tag']) && (int)$_GET['tag'] > 0 ) ? true : false;

	// 1. Get the current page number
	$pageno = (isset($_GET['pageno'])) ? (int)$_GET['pageno'] : 1;

	// 2. The formula for php pagination
	$no_of_records_per_page = 30;
	$offset = ($pageno-1) * $no_of_records_per_page; 

	// 3. Get the number of total number of pages
	$total_pages_sql 	= "SELECT COUNT(*) FROM $_a_wpmg_gallery_items";
	$total_rows 		= $wpdb->get_var($total_pages_sql);
	$total_pages 		= ceil($total_rows / $no_of_records_per_page);

	// 4. Constructing the SQL Query for pagination
	if( $isTag ){
		$tagId   = (int)$_GET['tag'];
		$sqlTags = "SELECT * FROM $a_wpmg_items_terms  WHERE `tag_id` = $tagId";
		$tag_rel_result = $wpdb->get_results($sqlTags);
		$inTagRelation  = [];
		foreach ($tag_rel_result as $key => $rel) {
			array_push($inTagRelation, $rel->item_id);
		}
		
		if( count($inTagRelation) > 0 ){
			$inTagRelation = implode(',', $inTagRelation);
			$sql = "SELECT * FROM  $_a_wpmg_gallery_items where gallery_id = {$gallery_id} AND id IN ($inTagRelation) ORDER BY $_a_wpmg_gallery_items.`id` DESC LIMIT $no_of_records_per_page OFFSET $offset";
		}
		else{
			$sql = "SELECT * FROM  $_a_wpmg_gallery_items where gallery_id = {$gallery_id} AND id IN (0) ORDER BY $_a_wpmg_gallery_items.`id` DESC LIMIT $no_of_records_per_page OFFSET $offset"; 
		} 
	} else {
		$tagId = 0;
		$sql = "SELECT * FROM  $_a_wpmg_gallery_items where gallery_id = {$gallery_id} ORDER BY $_a_wpmg_gallery_items.`id` DESC LIMIT $no_of_records_per_page OFFSET $offset"; 
	}
	
	$gall_item_result = $wpdb->get_results($sql);
	echo '<script>var wpmgItems = '. json_encode($gall_item_result).'</script>';

	// get tags
	$_gallery_tags = $wpdb->prefix . 'a_wpmg_gallery_tags';
	$tags_result = $wpdb->get_results(" SELECT * FROM  $_gallery_tags  WHERE gallery_id = $gallery_id  ORDER BY $_gallery_tags.`menu_order` ASC");
	echo '<script>var wpmtags = '. json_encode($tags_result).'</script>';
}
$lightBoxType = ( get_option('lightBoxType') != '' ) ? get_option('lightBoxType') : 'light_rounded';
?>

<div id="wpmg_wrap">
	<?php include 'admin-header.tpl.php'; ?>
	
	<?php if( count($gall_result) == 1 ) : ?>
	<div class="CSSTableGenerator" id="tbl-wpmg">

		<div class="wpmg-panel closed">
			<div class="wpmg-panel-head">
				<h3>Create/Edit Filters For This Gallery:</h3>
			</div>
			<div class="wpmg-panel-body">
				<?php include 'admin-add-tags.tpl.php'; ?>
			</div>
		</div>

		<input type="hidden" value="<?php echo $gallery_id ?>" id="gallery_id">
		<p class="wpmg-add-action">
			<a href="#" class="button get-media wpmgAddMediaToGallery">
				<span class="dashicons dashicons-plus" style="line-height: 33px;"></span> Add Image
			</a>
			<a href="#" class="button wpmgAddVideoToGallery">
				<span class="dashicons dashicons-plus" style="line-height: 33px;"></span> Add Video
			</a>
			<input type="text" name="wpmg-search" placeholder="Search.." id="autocomplete">
		</p>
		<p>
			<a href="<?php echo get_admin_url() ?>admin.php?page=wpm-gallery-add&id=<?php echo $gallery_id;?>" class="btn button <?php if(!$isTag) echo 'active-tag' ?> show-all-tags">
				Show All
			</a>
			<?php foreach ($tags_result as $key => $tag) : ?>
			<a href="<?php echo get_admin_url() ?>admin.php?page=wpm-gallery-add&id=<?php echo $gallery_id;?>&tag=<?php echo $tag->id ?>" class="btn button <?php if($isTag && $tagId == $tag->id) echo 'active-tag' ?>">
				<?php echo $tag->title ?>
			</a>
			<?php endforeach; ?>
		</p>
        <table class="widefat responsive wpmgItemsListTbl">
            <thead>
	            <tr>
	                <td> # </td>
	                <td width="150"> Image </td>
	                <td> Detail </td>
	                <td width="115"> Actions </td>
	            </tr>
            </thead>
            <tbody id="filtertbl" class="galleryItemTable">
            	<?php foreach ($gall_item_result as $key => $item) :?>
					<tr>
						<td>#<?php echo $item->id ?> </td>
		                <td> <img src="<?php echo $item->image ?>" width="150" /> </td>
		                <td>	
							<table class="wpmgInnerTbl">	
								<tbody>	
									<tr>	
										<td>Caption</td>
										<td><textarea placeholder="Caption.." name="caption[<?php echo $item->id ?>][]" maxlength="190"  onkeyup="countChar(this)"><?php echo stripslashes($item->caption) ?></textarea>
											<span class="charNum"><?php echo ( 190 - strlen($item->caption) ); ?></span>
										</td>
									</tr>
									<tr>	
										<td>Description</td>
										<td> 
											<textarea placeholder="Description.." name="description[<?php echo $item->id ?>][]" maxlength="190"  onkeyup="countChar(this)"><?php echo stripslashes($item->description) ?></textarea> 
											<span class="charNum"><?php echo ( 190 - strlen($item->description) ); ?></span>
										</td>
									</tr>
									<tr>
										<td>Item Type</td>
										<td>
											<div class="wpmgGridFull">
				                			<select name="type[<?php echo $item->id ?>][]">
												<option <?php if($item->type=='image') echo "selected"; ?> value="image">Image</option>
												<option <?php if($item->type=='youtube') echo "selected"; ?> value="youtube">Youtube</option>
												<option <?php if($item->type=='vimeo') echo "selected"; ?> value="vimeo">Vimeo</option>
											</select>
											<input type="text" name="url[<?php echo $item->id ?>][]" value="<?php echo $item->url ?>" placeholder="Video ID" <?php if($item->type=='image') echo "disabled"; ?>> 
											</div>
				                		</td>
									</tr>
									<tr>
										<td>Tags</td>
										<td> 
											<div class="wpmgtags" data-id="<?php echo $item->id ?>">
												<ul class="wpmgtags-list">
		  											<?php $tagSelected = explode(',',$item->tags); ?>
													<?php foreach ($tags_result as $key => $tag) : ?>
													<li>
														<input <?php if(in_array($tag->id, $tagSelected)) echo "checked"; ?> type="checkbox" value="<?php echo $tag->id ?>" name="tags[<?php echo $item->id ?>][]" id="tags[<?php echo $item->id ?>][<?php echo $tag->id ?>]" />
														<label for="tags[<?php echo $item->id ?>][<?php echo $tag->id ?>]"><?php echo $tag->title ?></label>
													</li>
													<?php endforeach; ?>
												</ul>
				                				<input type="hidden" class="wpmgtags-field" name="tagsInput[<?php echo $item->id ?>][]" value="<?php echo $item->tags ?>" />
				                			</div>
			                			</td>
									</tr>
									<tr title="<?php if(!WPMG::$licenced) echo 'Pro Only' ?>">
										<td>CTA</td>
										<td>
											<div class="wpmgGridFull">
											<input type="text" name="cta[<?php echo $item->id ?>][]" placeholder="CTA link" class="wpmgCTA" value="<?php echo $item->cta ?>" <?php if(!WPMG::$licenced) echo 'disabled' ?>>
											<input type="text" name="ctaTxt[<?php echo $item->id ?>][]" placeholder="CTA Button Text.." class="wpmgCTATxt" value="<?php echo $item->cta_text ?>" <?php if(!WPMG::$licenced) echo 'disabled' ?>>
											</div>
										</td>
									</tr>
									<tr title="<?php if(!WPMG::$licenced) echo 'Pro Only' ?>">
										<td>YouTube Subscribe</td>
										<td><input type="checkbox" <?php if( $item->subscribe == '1' ) echo 'checked' ?> value="1" name="subscribe[<?php echo $item->id ?>][]" class="YouTubeChk" placeholder="CTA.." <?php if(!WPMG::$licenced) echo 'disabled' ?>></td>
									</tr>
								</tbody>
							</table>
		                </td>
		                <td>
							<a href="#" class="button wpmg-save" data-id="<?php echo $item->id ?>">Save</a>
							<a href="#" class="button wpmg-delete" data-id="<?php echo $item->id ?>">Delete</a>
				        </td>
					</tr>
            	<?php endforeach; ?>
        	</tbody>
        	<tfoot>
        		<tr>
        			<td colspan="4">
	        			<ul class="wpmgPagination">
						    <li><a href="<?php echo get_admin_url() ?>admin.php?page=wpm-gallery-add&id=<?php echo $gallery_id;?>&pageno=1<?php if($isTag) echo '&tag='.$tagId ?>">First</a></li>
						    <li class="<?php if($pageno <= 1){ echo 'disabled'; } ?>">
						        <a href="<?php echo get_admin_url() ?>admin.php?page=wpm-gallery-add&id=<?php echo $gallery_id;?><?php if($pageno <= 1){ echo '#'; } else { echo "&pageno=".($pageno - 1); } ?><?php if($isTag) echo '&tag='.$tagId ?>">Prev</a>
						    </li>
						    <li class="<?php if($pageno >= $total_pages){ echo 'disabled'; } ?>">
						        <a href="<?php echo get_admin_url() ?>admin.php?page=wpm-gallery-add&id=<?php echo $gallery_id;?><?php if($pageno >= $total_pages){ echo '#'; } else { echo "&pageno=".($pageno + 1); } ?><?php if($isTag) echo '&tag='.$tagId ?>">Next</a>
						    </li>
						    <li><a href="<?php echo get_admin_url() ?>admin.php?page=wpm-gallery-add&id=<?php echo $gallery_id;?>&pageno=<?php echo $total_pages; ?><?php if($isTag) echo '&tag='.$tagId ?>">Last</a></li>
						</ul>
					</td>
        		</tr>
        	</tfoot>
        </table>
    </div>
	<?php else: ?>

    <p>
		<a href="#TB_inline?&width=400&height=135&inlineId=addNewGalleryThickbox" class="button thickbox wpmgAddNewGallery">
			<span class="dashicons dashicons-plus" style="line-height: 48px;"></span>
			Add New Gallery
		</a>
		<?php include 'admin-add-new-gallery-thickbox.tpl.php'; ?>
	</p>
	<?php endif; ?>
</div>

<?php wp_enqueue_script( 'wpmg-admin-autocomplete', plugins_url( '../script/jquery.autocomplete.min.js', __FILE__ ), array( 'jquery' ), '1.0.0', true ); ?>
<script>
jQuery(function($){

	let getTags = function(item_id, tags){
		var tags = tags.split(',')
		if( wpmtags.length <= 0 ) return '';

		var _html = '';
		$.each( wpmtags, function( key, value ) {
			var exists = tags.filter(element => element == value.id);
			_checked   = ( exists.length > 0 ) ? 'checked' : '';

			_html += `
				<li>
					<input ${_checked} type="checkbox" value="${value.id}" name="tags[${item_id}][]" id="tags[${item_id}][${value.id}]">
					<label for="tags[${item_id}][${value.id}]">${value.title}</label>
				</li>
			`
		});

		return _html;
	}

	$('#autocomplete').autocomplete({
	    serviceUrl: "<?php echo admin_url('admin-ajax.php').'?action=searchGalleryItems&gid='.$gallery_id ?>",
	    noSuggestionNotice: 'No results',
	    onSelect: function (suggestion) {
	        var _youtube = ( suggestion.row.subscribe == '1' ) ? 'checked' : '';
	        var _caption = decodeURI(suggestion.row.caption).replace(/\\'/g, "'");
	        var _descrip = decodeURI(suggestion.row.description).replace(/\\'/g, "'");
	        console.log(_caption.replace(/\\'/g, "'"));
	        var _html = `
	        <tr>
				<td>#${suggestion.row.id}</td>
                <td> <img src="${suggestion.row.image}" width="150"> </td>
                <td>
					<table class="wpmgInnerTbl">
						<tbody>
							<tr>
								<td>Caption</td>
								<td><textarea placeholder="Caption.." name="caption[${suggestion.row.id}][]" maxlength="190" onkeyup="countChar(this)">${_caption}</textarea>
									<span class="charNum">${190 - suggestion.row.caption.length}</span>
								</td>
							</tr>
							<tr>
								<td>Description</td>
								<td> 
									<textarea placeholder="Description.." name="description[${suggestion.row.id}][]" maxlength="190" onkeyup="countChar(this)">${_descrip}</textarea> 
									<span class="charNum">${190 - suggestion.row.description.length}</span>
								</td>
							</tr>
							<tr>
								<td>Item Type</td>
								<td>
									<div class="wpmgGridFull">
		                			<select name="type[${suggestion.row.id}][]">
										<option selected="" value="image">Image</option>
										<option value="youtube">Youtube</option>
										<option value="vimeo">Vimeo</option>
									</select>
									<input type="text" name="url[${suggestion.row.id}][]" value="" placeholder="Video ID" disabled=""> 
									</div>
		                		</td>
							</tr>
							<tr>
								<td>Tags</td>
								<td> 
									<div class="wpmgtags" data-id="${suggestion.row.id}">
										<ul class="wpmgtags-list">
											${getTags(suggestion.row.id, suggestion.row.tags)}
										</ul>
		                				<input type="hidden" class="wpmgtags-field" name="tagsInput[${suggestion.row.id}][]" value="11">
		                			</div>
	                			</td>
							</tr>
							<tr>
								<td>CTA</td>
								<td>
									<div class="wpmgGridFull">
									<input type="text" name="cta[${suggestion.row.id}][]" placeholder="CTA link" class="wpmgCTA" value="${suggestion.row.cta}">
									<input type="text" name="ctaTxt[${suggestion.row.id}][]" placeholder="CTA Button Text.." class="wpmgCTATxt" value="${suggestion.row.cta_text}">
									</div>
								</td>
							</tr>
							<tr>
								<td>YouTube Subscribe</td>
								<td><input type="checkbox" value="${suggestion.row.gallery_id}" name="subscribe[${suggestion.row.id}][]" class="YouTubeChk" placeholder="CTA.." ${_youtube}></td>
							</tr>
						</tbody>
					</table>
                </td>
                <td>
					<a href="#" class="button wpmg-save" data-id="${suggestion.row.id}">Save</a>
					<a href="#" class="button wpmg-delete" data-id="${suggestion.row.id}">Delete</a>
		        </td>
			</tr>
	        `;

	        $('.galleryItemTable').html(_html)
	        $('a.btn.button.active-tag').removeClass('active-tag');
	    }
	});


	var frame, images = '', selection = loadImages(images);

	jQuery('a.get-media').on('click', function(e) {
		e.preventDefault();

		// Set options for 1st frame render
		var options = {
			multiple: false,
			title: 'Gallery Post Format',
			state: 'insert',
			frame: 'post',
			selection: selection,
		};

		// Check if frame or gallery already exist
		if( frame || selection ) {
			options['title'] = 'Gallery Post Format';
		}

		frame = wp.media(options).open();

		// Tweak views
		frame.menu.get('view').unset('cancel');
		frame.menu.get('view').unset('separateCancel');
		// frame.menu.get('view').get('gallery-edit').el.innerHTML = 'Edit Gallery';
		// frame.content.get('view').sidebar.unset('gallery'); // Hide Gallery Settings in sidebar

		frame.on( 'insert', function() {
			// Get media attachment details from the frame state
      		var attachment = frame.state().get('selection').first().toJSON();
			_busy(true)
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					ids  			: 0,
					action 			: 'wpmg_save_gallery_items',
					gallery_id 		: $('#gallery_id').val(),
					attachment_id 	: attachment.id,
					image  			: attachment.url,
					caption  		: attachment.caption,
					description  	: attachment.description,
					type  			: "image",
					url  			: "#image"
				},
				success: function(){
					
				},
				dataType: 'html'
			}).done( function( data ) {
				window.location = jQuery('.show-all-tags').attr('href')
				_busy(false)
			});

		})
	});

	function _get_type_fields(_id){
		return `
			<select name="type[${_id}][]">
				<option value="image">Image</option>
				<option value="youtube">Youtube</option>
				<option value="vimeo">Vimeo</option>
			</select>
		`;
	}

	function _get_tags_fields(item_id){
		let _itemHTML = '';
		$.each(wpmtags, function(index, tag) {
			_itemHTML += `<li style="margin-right:3px">
							<input type="checkbox" value="${tag.id}" name="tags[${item_id}][]" id="tags[${item_id}][${tag.id}]">
							<label for="tags[${item_id}][${tag.id}]">${tag.title}</label>
						</li>`
		});
		return _itemHTML
	}

	// Load images
	function loadImages(images) {
		if( images ){
			var shortcode = new wp.shortcode({
				tag:    'gallery',
				attrs:   { ids: images },
				type:   'single'
			});

			var attachments = wp.media.gallery.attachments( shortcode );

			var selection = new wp.media.model.Selection( attachments.models, {
				props:    attachments.props.toJSON(),
				multiple: true
			});

			selection.gallery = attachments.gallery;

			// Fetch the query's attachments, and then break ties from the
			// query to allow for sorting.
			selection.more().done( function() {
				// Break ties with the query.
				selection.props.set({ query: false });
				selection.unmirror();
				selection.props.unset('orderby');
			});

			return selection;
		}

		return false;
	}

	function _busy(busy = true){
		if( busy )
			$('#tbl-wpmg').addClass('busy')
		else
			$('#tbl-wpmg').removeClass('busy')
	}

	jQuery('html').on('click', '.wpmgAddVideoToGallery', function(event) {
		event.preventDefault();
		_busy(true)
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				ids: 0,
				gallery_id 		: $('#gallery_id').val(),
				action 			: 'wpmg_save_gallery_items',
				attachment_id 	: 0,
				image  			: 'video_1280x720.jpg',
				caption  		: '',
				description  	: '',
				type  			: 'youtube',
				url  			: '#image',
				video 			: true
			},
			success: function(){
				
			},
			dataType: 'html'
		}).done( function( data ) {
			window.location = jQuery('.show-all-tags').attr('href')
			_busy(false)
		});
	});
});
function countChar(val) {
	var len = val.value.length;
	if (len >= 190) {
		val.value = val.value.substring(0, 190);
	} else {
		jQuery(val).next('.charNum').text(190 - len);
	}
}
</script>

<style>
span.charNum {float: right; background: #007cba; padding: 0 3px; font-size: 14px; color: #fff; font-weight: 500; margin-top: -5px; }
</style>