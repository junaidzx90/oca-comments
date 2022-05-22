jQuery(function( $ ) {
	'use strict';

	custom_profiles();
	function custom_profiles() {
		function pgroupMaintain() {
			$('.profile_rows').children('.inputbox').each(function (index, vals) {
				$(this).find("input.profile_email").attr("name", "profiles[" + index + "][email]");
				$(this).find("input.profile_url").attr("name", "profiles[" + index + "][url]");
			});
		}
	
		let inputProfileRows = `<div class="inputbox">
			<input type="email" placeholder="Email address" class="profile_email" name="profiles[]">
			<input type="url" required placeholder="Profile URL" class="profile_url" name="profiles[]">
			<span class="remove_profile_inp">+</span>
		</div>`;
		
		$('.addmore_profile_input').on("click", function () {
			$('.profile_rows').append(inputProfileRows);
			pgroupMaintain();
		});
	
		$(document).on("click", ".remove_profile_inp", function () {
			$(this).parent().remove();
			pgroupMaintain();
		});
	}

	text_replacing();
	function text_replacing() {
		function groupMaintain() {
			$('.text_input_rows').children('.inputbox').each(function (index, vals) {
				$(this).find("input.from_text").attr("name", "replace_texts[" + index + "][search]");
				$(this).find("input.replace_text").attr("name", "replace_texts[" + index + "][replace]");
			});
		}
	
		let inputTextRows = `<div class="inputbox">
			<input type="text" placeholder="Search" class="from_text" name="replace_texts[]">
			<input type="text" required placeholder="Replace with" class="replace_text" name="replace_texts[]">
			<span class="remove_text_inp">+</span>
		</div>`;
		
		$('.addmore_text_input').on("click", function () {
			$('.text_input_rows').append(inputTextRows);
			groupMaintain();
		});
	
		$(document).on("click", ".remove_text_inp", function () {
			$(this).parent().remove();
			groupMaintain();
		});
	}

	text_to_url_replacing();
	function text_to_url_replacing() {
		function groupMaintain() {
			$('.input_urls_rows').children('.inputbox').each(function (index, vals) {
				$(this).find("input.from_text").attr("name", "replace_urls[" + index + "][search]");
				$(this).find("input.replace_text").attr("name", "replace_urls[" + index + "][replace]");
			});
		}
	
		let inputUrlRows = `<div class="inputbox">
			<input type="text" placeholder="Search" class="from_text" name="replace_urls[]">
			<input type="url" required placeholder="Replace with" class="replace_text" name="replace_urls[]">
			<span class="remove_url_inp">+</span>
		</div>`;
		
		$('.addmore_url_input').on("click", function () {
			$('.input_urls_rows').append(inputUrlRows);
			groupMaintain();
		});
	
		$(document).on("click", ".remove_url_inp", function () {
			$(this).parent().remove();
			groupMaintain();
		});
	}

	highlight_emails();
	function highlight_emails() {
		let emlEl = `<li>
			<input class="widefat" placeholder="Commenter Email" type="email" name="highlight_emails[]" id="highlight-email">
			<span class="remove_eml_inp">+</span>
		</li>`;
		
		$('.addmore_eml_input').on("click", function () {
			$('.user_emails ul').append(emlEl);
		});
	
		$(document).on("click", ".remove_eml_inp", function () {
			$(this).parent().remove();
		});
	}

	exclide_ranks_emails();
	function exclide_ranks_emails() {
		let emlEl = `<li class="exclude_eml_item">
			<input class="widefat" placeholder="Commenter Email" type="email" name="exclude_rank_emails[]" id="exclude_rank_emails" value="">
			<span class="remove_exc_eml_inp">+</span>
		</li>`;
		
		$('.addmore_exc_eml_input').on("click", function () {
			$('#exclude_emls').append(emlEl);
		});
	
		$(document).on("click", ".remove_exc_eml_inp", function () {
			$(this).parent().remove();
		});
	}

	$('#removeLogo').on("click", function (e) {
		e.preventDefault();
		$('#oca_email_logo').val("");
		$('#email_logo').attr("src", "");
	});

	// Confirmation before delete logs
	$('.clearlogs').on("click", function (e) {
		if (!confirm("All the logs data will be removed.")) {
			e.preventDefault();
		}
	});

	if ($(document).find('#regenrate_fullname').length > 0) {
		$('#regenrate_fullname').on("click", function (e) {
			let btn = $(this);
			e.preventDefault();
			if (confirm("This action will generate all the commenters fullname.")) {
				$.ajax({
					type: "post",
					url: adminajax.ajaxurl,
					data: {
						action: "generate_fullname_of_commenter"
					},
					dataType: "json",
					beforeSend: () => {
						btn.text("Generating...");	
					},
					success: function (response) {
						btn.text("Regenrate");
						alert(response.success);
					}
				});
			}
		});
	}

	if ($(document).find('#renamebtn').length > 0) { 
		$('#renamebtn').on("click", function (e) {
			e.preventDefault();
			let btn = $(this);
			let email = $('#stupidnameemail');
			let name = $('#stupidname');

			if (confirm("The current name will be replaced.")) {
				$.ajax({
					type: "post",
					url: adminajax.ajaxurl,
					data: {
						action: "rename_stupid_names",
						email: email.val(),
						name: name.val(),
					},
					dataType: "json",
					beforeSend: () => {
						btn.text("Processing...");	
					},
					success: function (response) {
						email.val("");
						name.val("");
						btn.text("Rename");
						alert(response.success);
					}
				});
			}
		});
	}

	function loadAnimatedImg() {
		var imgfile, selectedFiles;
		// If the frame already exists, re-open it.
		if (imgfile) {
			imgfile.open();
			return;
		}
		//Extend the wp.media object
		imgfile = wp.media.frames.file_frame = wp.media({
			title: 'Choose Animations',
			button: {
				text: 'Upload'
			},
			multiple: true
		});

		//When a file is selected, grab the URL and set it as the text field's value
		imgfile.on('select', function () {
			selectedFiles = imgfile.state().get('selection');
			selectedFiles.map( function( attachment ) {
				let file = attachment.toJSON();
				let imageEl = `<div class="anim-image">
					<span class="remove_anim">+</span>
					<img src="${file.url}">
					<input type="hidden" name="anim_images[]" value="${file.url}">
				</div>`;

				$("#imagesWrap").append(imageEl);
			});
		});

		//Open the uploader dialog
		imgfile.open();
	}

	$(document).on("click", ".remove_anim", function () {
		$(this).parent(".anim-image").remove();
	});

	$(document).find("#add_animated_image").on("click", function (e) {
		e.preventDefault();
		loadAnimatedImg();
	});

	$('#oca_tooltipbg').wpColorPicker();
	$('#oca_tooltip_txt_color').wpColorPicker();
	$('#avatar_popup_bg_color').wpColorPicker();
	$('#avatar_popup_title_color').wpColorPicker();
	$('#avatar_popup_text_color').wpColorPicker();
	$('#oca_vote_btn_color').wpColorPicker();
	$('#oca_unvote_btn_color').wpColorPicker();
	$('#highlight_color').wpColorPicker();

	$(".target_switch__input").on("change", function(){
		switch ($(this).val()) {
			case "image":
				$(this).parents(".switch_attachement").find(".attachment__url").removeClass("hiddingclass");
				break;
			case "video":
				$(this).parents(".switch_attachement").find(".attachment__url").removeClass("hiddingclass");
				break;
			default:
				$(this).parents(".switch_attachement").find(".attachment__url").addClass("hiddingclass");
				break;
		}
	});
							
	comments_texts_changing();
	function comments_texts_changing() {
		groupMaintain();
		function groupMaintain() {
			$('#comments-to-other_rows').children('.inputbox').each(function (index, vals) {
				$(this).find("input.singular").attr("name", "comments_texts[" + index + "][singular]");
				$(this).find("input.plural").attr("name", "comments_texts[" + index + "][plural]");
				$(this).find("select").attr("name", "comments_texts[" + index + "][category]");
			});
		}

		let rule = `<div class="inputbox"> <select required name="comments_texts[][category]" id="comments_texts" class="widefat">${adminajax.categories}</select><input required type="text" class="singular" placeholder="Singular" name="comments_texts[][singular]"><input required type="text" class="plural" placeholder="plural" name="comments_texts[][plural]"><span class="remove_rool_inp">+</span></div>`;
		
		$('.addmore_rool_input').on("click", function () {
			$('#comments-to-other_rows').append(rule);
			groupMaintain();
		});
	
		$(document).on("click", ".remove_rool_inp", function () {
			$(this).parent().remove();
			groupMaintain();
		});
	}
});
