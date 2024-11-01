"use strict";
let wpmgAPP = (function($){
	let that = {};
    
    /*-----------------------------------------
    //Initialize ready script
    -----------------------------------------*/
    that.init = function () {
		_constructor()
		_wpmgPanel()
		add_tags()
		wpmgUpdateItems()
		wpmgDelete()
		addTags()
		deleteTags()
		defaultTag()
		editTags()
		createNewGallery()
		deleteGallery()
		typeChange()
		filterSetting()
		paginationSettings()
		_updateSettings()
	}

	that.onload = function () {
		// drawTable()
	}

	let ajaxurl = wpmg.ajax;

	let _constructor = function () {
		let orderList = [];
		if( $('#filtertbl > tr[data-row]').length > 1 && $('#filtertbl').hasClass('shortable') ){
			$( "#filtertbl" ).sortable({
		    	stop: function( event, ui ) {
		    		orderList = [];
		    		var start = 1;
		    		$('#filtertbl > tr[data-row]').each(function(index, el) {
		    			orderList.push( $(el).attr('data-row') )
		    		});
		    		$( "#filtertbl" ).closest('.wpmg-panel-body').addClass('busy')
		    		$.post(wpmg.ajax, { action : 'wpmg_update_filter_order', orderList : orderList }, function(data, textStatus, xhr) {
						$( "#filtertbl" ).closest('.wpmg-panel-body').removeClass('busy')
					});
		    	}
		    });
		}
	}


	let _busy = function(busy = true, which = false){
		if( which == false ){
			if( busy )
				$('#tbl-wpmg').addClass('busy')
			else
				$('#tbl-wpmg').removeClass('busy')
		} else {
			if( busy )
				$(which).closest('#tbl-wpmg').addClass('busy')
			else
				$(which).closest('#tbl-wpmg').removeClass('busy')
		}
	}


	let getHashtag  = function(hash){
		var url = location.href;
		hashtag = (url.indexOf(hash) !== -1) ? decodeURI(url.substring(url.indexOf(hash)+1,url.length)) : false;
		if(hashtag){  hashtag = hashtag.replace(/<|>/g,''); }
		return hashtag;
	};
	
	let setHashtag = function(hash){
		if(typeof hash == 'undefined') return;
		location.hash = hash;
	};
	
	let clearHashtag  = function(){
		if ( location.href.indexOf('#wpmg') !== -1 ) location.hash = "wpmg";
	}
	
	let getParam = function(name,url){
        name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
        var regexS = "[\\?&]"+name+"=([^&#]*)";
        var regex = new RegExp( regexS );
        var results = regex.exec( url );
        return ( results == null ) ? "" : results[1];
	}

	let _wpmgPanel = function(){
		jQuery('html').on('click', '.wpmg-panel-head', function(e) {
			e.preventDefault();
			let _this = $(this);
			_this.next('.wpmg-panel-body').slideToggle(400, function(){
				_this.parent('.wpmg-panel').toggleClass('closed');

			})
		});
	}

	let add_tags = function(){
		jQuery('html').on('click', '.wpmgtags-list input[type="checkbox"]', function(e) {
			// e.preventDefault();
			let _this = $(this);

			let selected = _this.closest('.wpmgtags').find('.wpmgtags-field').val();
			selected = selected.trim().split(',')

			if( _this.prop('checked') )
				selected.push(_this.val())
			else 
				selected = selected.filter(tag => tag != _this.val())

			_this.closest('.wpmgtags').find('.wpmgtags-field').val(selected.toString().replace(/,\s*$/, "").replace(/^,/, ''))
		});
	}

	let wpmgUpdateItems = async function(){
		$('html').on('click', '.wpmg-save', async function(event) {
			event.preventDefault();
			let _this = $(this);
			let _id   = _this.attr('data-id')

			_busy(true, _this)
			await $.post(
				wpmg.ajax, 
				{
					action		: 'wpmg_update_item_ajax',
					id 			: _this.attr('data-id'),
					caption 	: $(`textarea[name="caption[${_id}][]"]`).val().replace(/"/g, '`'),
					description : $(`textarea[name="description[${_id}][]"]`).val().replace(/"/g, '`'),
					type : $(`select[name="type[${_id}][]"]`).val(),
					url  : $(`input[name="url[${_id}][]"]`).val(),
					tags : $(`input[name="tagsInput[${_id}][]"]`).val(),
					cta  : $(`input[name="cta[${_id}][]"]`).val(),
					cta_text  : $(`input[name="ctaTxt[${_id}][]"]`).val(),
					subscribe : $(`input[name="subscribe[${_id}][]"]`).prop('checked')
				}, 
			function(data, textStatus, xhr) 
			{
				$(`textarea[name="caption[${_id}][]"]`).val($(`textarea[name="caption[${_id}][]"]`).val().replace(/"/g, '`'))
				$(`textarea[name="description[${_id}][]"]`).val($(`textarea[name="description[${_id}][]"]`).val().replace(/"/g, '`'))
			});
			
			_busy(false, _this)
		});
	}

	let wpmgDelete = function(){
		$('html').on('click', '.wpmg-delete', async function(event) {
			event.preventDefault();
			let _this = $(this);
			let _id   = _this.attr('data-id')

			if( ! confirm('Are You Sure, You Want To DELETE This Item?') ) return false;

			_busy(true, _this)
			await $.post(wpmg.ajax, { action : 'wpmg_delete_item', id : _this.attr('data-id') }, function(data, textStatus, xhr) {
				var _data = jQuery.parseJSON(data)
				if( textStatus == 'success' && _data.success ){
					_busy(false, _this)
					_this.closest('tr').remove()
				}
			});
			_busy(false, _this)
		});
	}

	let addTags = function(){
		$('html').on('click', '.addTagWrapper a', async function(event) {
			event.preventDefault();
			let _this = $(this);
			let _val  = $('.addTagWrapper input').val()
			let _gid  = $('#gallery_id').val()

			if( _val.length < 2 ) return false

			if( ! parseInt(jQuery('.addTagWrapper a').attr('data-id')) > 0 ){
				_busy(true, _this)
				await $.post(wpmg.ajax, { action : 'wpmg_add_tags', title : _val, gallery_id : _gid }, function(data, textStatus, xhr) {
					
					var _data = jQuery.parseJSON(data);
					if( textStatus == 'success' && _data.success ){
						var _html = `
							<tr data-row="${_data.id}">
				                <td data-title="Name">${_val}</td>
				                <td data-title="Default"><input type="radio" name="default-tag" data-id="${_data.id}" value="${_data.id}" class="default-tag"</td>
				                <td data-title="Action" class="actions">
				                    <a href="#" data-id="${_data.id}" class="button delete-wpmgTag">Delete</a>
				                    <a href="#" data-id="${_data.id}" data-title="${_val}" class="button edit-wpmgTag">Edit</a>
				                </td>
				            </tr>
						`;
						$('.tagsListTbl').append(_html)
						$('.addTagWrapper input').val('')

						$('.addTagWrapper').removeClass('editTag')
					}
				});
				_busy(false, _this)
			} else {
				let _id   	= _this.attr('data-id')
				let _title	= $('.addTagWrapper input').val()


				_busy(true, _this)
				await $.post(wpmg.ajax, { action : 'wpmg_update_tags_ajax', id : _id, title : _title }, function(data, textStatus, xhr) {
					var _data = jQuery.parseJSON(data);
					$('.addTagWrapper > a').removeAttr('data-id')
					$('.addTagWrapper > a > name').html('Add Filter')

					$(`tr[data-row="${_id}"] > td[data-title="Name"]`).html(_title)
					$(`.edit-wpmgTag[data-id="${_id}"]`).attr('data-title', _title)

					$('.addTagWrapper input').val('')
					if( textStatus == 'success' && _data.success ){
					} else {
						alert('Action failed, Please try again.')
					}

					$('.addTagWrapper').removeClass('editTag')
				});
				_busy(false, _this)
			}
		});

		$('html').on('click', '.wpmgCancelTagEdit', async function(event) {
			event.preventDefault();
			let _this = $(this);
			$('.addTagWrapper > a').removeAttr('data-id')
			$('.addTagWrapper > a > name').html('Add Filter')
			$('.addTagWrapper input').val('')
			$('.addTagWrapper').removeClass('editTag')
		});
	}

	let editTags = function(){
		$('html').on('click', '.edit-wpmgTag', function(event) {
			event.preventDefault();
			let _this 	= $(this)
			let _id   	= _this.attr('data-id')
			let _title	= _this.attr('data-title')

			$('.addTagWrapper').addClass('editTag')
			$('.addTagWrapper input').val(_title)
			$('.addTagWrapper > a').attr('data-id', _id).addClass('editTag')
			$('.addTagWrapper > a > name').html('Update Filter')
		});
	}

	let deleteTags = function(){
		$('html').on('click', '.delete-wpmgTag', async function(event) {
			event.preventDefault()
			let _this = $(this)
			let _val  = _this.attr('data-id')

			_busy(true, _this)
			await $.post(wpmg.ajax, { action : 'wpmg_delete_tags', id : _val }, function(data, textStatus, xhr) {
				var _data = jQuery.parseJSON(data)
				if( textStatus == 'success' && _data.success ){
					_busy(false, _this)
					_this.closest('tr').remove()
					// Remove selectable tags
					jQuery(`.wpmgtags-list input[type="checkbox"][value="${_val}"]`).closest('li').remove()
				}
			});
		});
	}

	let defaultTag = function() {
		$('input.default-tag').on('change', '', async function(event) {
			event.preventDefault()
			let _this 	= $(this)
			let _id 	= _this.attr('data-id')
			let _gid	= $('#gallery_id').val()

			_busy(true, _this)
			await $.post(wpmg.ajax, { action : 'wpmg_update_default_tags_ajax', id : _id, _gid : _gid });
			_busy(false, _this)
		});
	}

	let createNewGallery = function(){
		$('html').on('click', '#addNewGalleryBtn', async function(event) {
			event.preventDefault()
			let _this = $(this)
			let _val  = $('#addNewGalleryTitle').val()

			if(_val.length < 2 || _val == '') return false

			_busy(true, _this)
			await $.post(wpmg.ajax, { action : 'wpmg_create_gallery_ajax', title : _val }, function(data, textStatus, xhr) {
				var _data = jQuery.parseJSON(data)
				if( textStatus == 'success' && _data.success ){
					window.location = _data.redirect
				}
			});
			_busy(false, _this)
		});
	}

	let deleteGallery = function(){
		$('html').on('click', '.delete-wpmGallery', async function(event) {
			event.preventDefault()
			let _this = $(this)
			let _val  = _this.attr('data-id')

			var _confirm = confirm('Are You Sure, You Want To DELETE This Gallery?')
			if( !_confirm ) return false

			_busy(true, _this)
			await $.post(wpmg.ajax, { action : 'wpmg_delete_gallery', id : _val }, function(data, textStatus, xhr) {
				var _data = jQuery.parseJSON(data)
				if( textStatus == 'success' && _data.success ){
					_busy(false, _this)
					_this.closest('tr').remove()
				}
			});
		});
	}

	let typeChange = function(){
		$('html').on('change', 'select[name^="type"]', function(event) {
			event.preventDefault()
			let _this = $(this)

			if( _this.val() == 'image' )
				_this.next('input').prop('disabled', true)
			else 
				_this.next('input').prop('disabled', false)

		});
	}

	let filterSetting = function(){
		$('html').on('click', 'a.button.wpmg-update-filter-settings', async function(event) {
			event.preventDefault()
			let _this = $(this)

			let fwbg = $('#filter-wrapper-bg').val()
			let ftc = $('#filter-text-color').val()
			let fbc = $('#filter-bg-color').val()
			let fbrc = $('#filter-border-color').val()
			let aftc = $('#act-filter-text-color').val()
			let afbc = $('#act-filter-bg-color').val()
			let afbrc = $('#act-filter-border-color').val()

			_this.closest('.wpmgInnerTbl').addClass('busy')
			await $.post(wpmg.ajax, { 
				action : 'wpmg_filter_settings_ajax',
				'filter-wrapper-bg' : fwbg,
				'filter-text-color' : ftc,
				'filter-bg-color' : fbc,
				'filter-border-color' : fbrc,
				'act-filter-text-color' : aftc,
				'act-filter-bg-color' : afbc,
				'act-filter-border-color' : afbrc,
			});
			_this.closest('.wpmgInnerTbl').removeClass('busy')
		});
	}

	let paginationSettings = function(){
		$('html').on('click', 'a.button.wpmg-update-paginate-settings', async function(event) {
			event.preventDefault()
			let _this = $(this)

			let ptc = $('#paginate-text-color').val()
			let pbc = $('#paginate-bg-color').val()
		
			let aptc = $('#act-paginate-text-color').val()
			let apbc = $('#act-paginate-bg-color').val()

			_this.closest('.wpmgInnerTbl').addClass('busy')
			await $.post(wpmg.ajax, { 
				action : 'wpmg_paginate_settings_ajax',
				'paginate-text-color' : ptc,
				'paginate-bg-color' : pbc,
				'act-paginate-text-color' : aptc,
				'act-paginate-bg-color' : apbc
			});
			_this.closest('.wpmgInnerTbl').removeClass('busy')
		});
	}

	let _updateSettings = function(){
		$('html').on('click', 'a.button.wpmg-update-settings', async function(event) {
			event.preventDefault()
			let _this = $(this)

			let ycid = $('#youtube-chaneel-id').val()
			let hashTag = $('#social-media-hastag').val()
			let lightBoxType = $('input[name="lightBoxType"]:checked').val()

			_this.closest('.wpmgInnerTbl').addClass('busy')
			await $.post(wpmg.ajax, { 
				action : 'wpmg_general_settings_ajax',
				'youtube-chaneel-id' : ycid,
				'social-media-hastag' : hashTag,
				'lightBoxType' : lightBoxType
			});
			_this.closest('.wpmgInnerTbl').removeClass('busy')
		});

		$('html').on('change', 'input[name="filter-align"]', async function(event) {
			event.preventDefault()
			let _this = $(this)

			_this.closest('.wpmgInnerTbl').addClass('busy')
			await $.post(wpmg.ajax, { 
				action : 'wpmg_filter_alignment_ajax',
				'align' : _this.val()
			});
			_this.closest('.wpmgInnerTbl').removeClass('busy')
		});

	}

	return that;
})(jQuery);
jQuery(document).ready(function($) {
	wpmgAPP.init();
});
jQuery(window).load(function($) {
	wpmgAPP.onload();

	if(typeof wpmgTriggerNewGallery != 'undefined' && wpmgTriggerNewGallery)
		jQuery('.wpmgAddNewGallery').trigger('click')
});