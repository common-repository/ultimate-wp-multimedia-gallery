"use strict";
let wpmgApp = (function($){
	let that = {};
	let ajaxurl = wpmg.ajax;
    
    /*-----------------------------------------
    //Initialize ready script
    -----------------------------------------*/
    that.init = function () {
		_constructor()
		_initLighbox()
		_socialShare()
	}
	
	let _constructor = function () {
		const observer = lozad(); // lazy loads elements with default selector as '.lozad'
		observer.observe();
	}

	that.loaded = function () {
		_initFilterizr()
		switchNavigation()
	}

	that.resized = function () {
		switchNavigation()
	}

	let getHashtag  = function(hash){
		var url = location.href;
		hashtag = (url.indexOf(hash) !== -1) ? decodeURI(url.substring(url.indexOf(hash)+1,url.length)) : false;
		if(hashtag){  hashtag = hashtag.replace(/<|>/g,''); }
		return hashtag;
	}

	let setHashtag = function(hash){
		if(typeof hash == 'undefined') return;
		location.hash = hash;
	}

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

	let _socialShare = function(){
		jQuery('html').on('click', '.pp_social a', function(){
			var post = $('.pp_custom_actions').attr('data-post')
			post = ( post.length > 10) ? post : false;

			let _href = decodeURI(jQuery(this).attr('href'))

			if( jQuery(this).hasClass('facebook') ){
				var _link 			= getParam('link', _href);
				var _picture 		= getParam('picture', _href);
				var _name 			= getParam('name', _href);
				var _caption 		= getParam('caption', _href);
				var _description 	= getParam('description', _href);
				var _display 		= getParam('display', _href);

				_href = `https://www.facebook.com/sharer/sharer.php?u=${post}`;
			} else {
				var _text 			= getParam('text', _href)
				if( _text.length > 0 ){
					var _textArr = _text.split('-id-');
					if( _textArr.length > 1 )
						_text = _textArr[0] + ' #Yourhash';
				}

				var url = new URL(_href);
				url.searchParams.set('text', _text)
				// url.searchParams.set('url', post)
				_href = url;

				var _twitterURL = `https://twitter.com/intent/tweet?url=${post}&text=${_text}`;
				console.log(_twitterURL)
				_href = new URL(_twitterURL);
			}

			if( post ){
		        var newwindow = window.open(_href,'','height=400,width=550');
		        if (window.focus) {newwindow.focus()}
			}
	        return false;
	    });
	}

	let _initLighbox = function(){
		$("a[rel^='pp']").prettyPhoto({
			theme: ( wpmg.lightBoxType !='' ) ? wpmg.lightBoxType : 'light_rounded', /* light_rounded / dark_rounded / light_square / dark_square / facebook / pp_default*/
			// overlay_gallery: false,
			default_width: 500,
			default_height: 344,
			deeplinking:true,
			show_title: false,
			horizontal_padding: 20,
			ajaxcallback:function( event){
				
			},
			markup: ' \
				<div class="pp_pic_holder"> \
				    <div class="ppt">&nbsp;</div> \
				    <div class="pp_top"> \
				        <div class="pp_left"></div> \
				        <div class="pp_middle"></div> \
				        <div class="pp_right"></div> \
				    </div> \
				    <div class="pp_content_container"> \
				        <div class="pp_left"> \
				            <div class="pp_right"> \
				                <div class="pp_content"> \
				                    <div class="pp_loaderIcon"></div> \
				                    <div class="pp_fade"> \
				                        <a href="#" class="pp_expand" title="Expand the image">Expand</a>  \
				                        <div class="pp_hoverContainer"> \
				                        	<a class="pp_next" href="#">next</a> \
				                        	<a class="pp_previous" href="#">previous</a> \
				                        </div> \
				                        <div id="pp_full_res"></div> \
				                        <div class="pp_details"> \
				                            <p class="pp_description"></p> \
				                            <div class="pp_social">{pp_social}</div> \
				                            <div class="pp_custom_actions"></div> \
				                        </div> \
				                        <a class="pp_close" href="#">Close</a>  \
				                    </div> \
				                </div> \
				            </div> \
				        </div> \
				    </div> \
				    <div class="pp_bottom"> \
				        <div class="pp_left"></div> \
				        <div class="pp_middle"></div> \
				        <div class="pp_right"></div> \
				    </div> \
				</div> \
				<div class="pp_overlay"></div> \
			',
			social_tools: ' \
				<a class="facebook" href="">\
					<i class="fc-facebook" aria-hidden="true"></i>\
	            </a>\
	            <a class="twitter" href="">\
					<i class="fc-twitter" aria-hidden="true"></i>\
	            </a>',
			changepicturecallback: function(event){
				$('.pp_custom_actions').html('');
				var _id, _desc = $('.pp_description').html().split('-id-')
				if( _desc.length == 2 && parseInt(_desc[1]) > 0){

					$('.pp_description').html(_desc[0])

					_id = parseInt(_desc[1])
					var post = $(`a[ref="ref-${_id}"]`).attr('data-post');
					$('.pp_custom_actions').attr('data-post', post);
					if(post.length > 0)
						setTimeout(() => { $('.pp_custom_actions').show() }, 3000)

					if( jQuery('.pp_content').height() < jQuery('.pp_fade').innerHeight() ){
						jQuery('.pp_content').height(jQuery('.pp_fade').innerHeight())
					}
					
				}
			}
		});
	}

	let _initFilterizr = function(){
		if( $('.filtr-wrapper').length < 1 ) return false;
		$('.filtr-wrapper').removeClass('noFilterizr')
		$('.wpmg-wrap').removeClass('wpmg-loading')

		$('html').on('change', '#wpmg-filter-dropdown', function(event) {
			event.preventDefault();
			let _this = $(this)
			let _val  = _this.val()
			$(`button.control[data-filter="${_val}"]`).click()
		})

		$('.wpmfgmixer').each(function(i,e){
			var _id = $(e).attr('data-id')
			window.filterizr = new window.Filterizr(`#uwmg-${_id} .filtr-wrapper`, {
			    controlsSelector: `#uwmg-${_id} .fltr-controls`,
			    layout: 'sameSize',
			    gutterPixels: 20,
			    spinner: {
			        enabled: true,
			    },
			});

			const simpleFilters = document.querySelectorAll(`#uwmg-${_id} .simplefilter button`);
		    Array.from(simpleFilters).forEach((node) =>
		        node.addEventListener('click', function() {
		            simpleFilters.forEach((filter) => filter.classList.remove('mixitup-control-active'));
		            node.classList.add('mixitup-control-active');
		        })
		    );
		})

		$('.wpmg-filter').each(function(i,e){
        	$(e).find('.default-tag').trigger('click')
        })
	}

	let switchNavigation = function(){
		setTimeout(function(){
			let _nav = $('.wpmg-filter.controls')
			if( _nav.height() > 70 ){
				_nav.addClass('wpmg-filter-dropdown')
			} else {
				_nav.removeClass('wpmg-filter-dropdown')
			}

		}, 1000)
	}

	return that;
})(jQuery);

jQuery(document).ready(function($) {
	wpmgApp.init();
});

jQuery(window).load(function($) {
	wpmgApp.loaded();
});

var doit;
function resizedw(){
	jQuery('.wpmg-filter.controls').each(function(index, el) {
    	let _nav = jQuery(el)
		if( _nav.height() > 70 ){
			_nav.addClass('wpmg-filter-dropdown')
		} else {
			_nav.removeClass('wpmg-filter-dropdown')
		}
	});
}
window.onresize = function() {
    clearTimeout(doit);
    doit = setTimeout(function() {
        resizedw();
    }, 1000);
};