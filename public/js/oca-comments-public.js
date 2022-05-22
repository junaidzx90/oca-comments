jQuery(document).ready(function ($) {

	let allcomments = $("#fc-list").html();
	let resond = $('#respond').clone();

	
	function get_oca_comments(search) {
		if (search.length > 0) {
			$.ajax({
				type: "get",
				  url: publicajax.ajaxurl,
				  data: {
					action: "search_comments_author",
					term: search,
					postid: $("#postid").val()
				},
				dataType: "json",
				beforeSend: ()=>{
					$('.loading-wrapper').removeClass('fcnone');
					$('.searchIcon svg').addClass('fcnone');
				},
				success: function( data ) {
					$('.loading-wrapper').addClass('fcnone');
					$('.searchIcon svg').removeClass('fcnone');
					
					if(data.comment){
						$("#fc-list").html(data.comment);
					}else{
						$("#fc-list").html(allcomments);
						$('.lazyload, .lazyloading').css('opacity', '1');
					}
					get_tooltip();
	
					if ($(document).find('#respond').length === 0) {
						$("#fc-list").after(resond);
					}
				}
			});
		}
	}
	
	$("#authorname").on("keyup", function (e) {
		if ($(this).val().length === 0) {
			$("#fc-list").html(allcomments);
			$('.lazyload, .lazyloading').css('opacity', '1');
			get_tooltip();
			
			if ($(document).find('#respond').length === 0) {
				$("#fc-list").after(resond);
			}
		}
		if (e.keyCode === 13) {
			let search = $('#authorname').val();
			get_oca_comments(search);
		}
	});

	$('.searchIcon').on("click", function () {
		let search = $('#authorname').val();
		get_oca_comments(search);
	});

	function is_touch_device() {
		var prefixes = ' -webkit- -moz- -o- -ms- '.split(' ');
		var mq = function (query) {
		  return window.matchMedia(query).matches;
		}
  
		if (('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch) {
		  return true;
		}
  
		var query = ['(', prefixes.join('touch-enabled),('), 'heartz', ')'].join('');
		return mq(query);
	}

	get_tooltip();
	function get_tooltip() {
		if (!is_touch_device()) {
			$('._authorname, ._authorimg, ._comments_count').jBox('Mouse', {
				theme: 'TooltipDark',
				getContent: 'data-content',
				addClass: "oca-tooltip"
			});

			$('.star_link').jBox('Mouse', {
				theme: 'TooltipDark',
				content: 'Click on the stars to get rewards',
				addClass: "oca-tooltip"
			});
		}
	}
});