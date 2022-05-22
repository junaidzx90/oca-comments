<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    OCA_Comments
 * @subpackage OCA_Comments/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    OCA_Comments
 * @subpackage OCA_Comments/public
 * @author     junaidzx90 <admin@easeare.com>
 */
class OCA_Comments_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_shortcode( 'commenter', [$this, 'oca_commenter_shortcode'] );
		add_shortcode( 'top_commenters', [$this, 'oca_top_commenters_shortcode'] );
		add_shortcode( 'commenters_list', [$this, 'oca_commenters_list'] );
		add_shortcode( 'hide-when-no-comment', [$this, 'hide_when_no_comment'] );
		add_shortcode( 'show-when-no-comment', [$this, 'show_when_no_comment'] );
		add_shortcode( 'comments_stats', [$this, 'comments_stats_callback'] );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in OCA_Comments_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The OCA_Comments_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( "jquery-ui", '//code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css', array(), $this->version, 'all' );
		wp_enqueue_style( "jBox", plugin_dir_url( __FILE__ ) .'css/jbox.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/oca-comments-public.css', array(), microtime(), 'all' );

	}

	function oca_header_style(){
		?>
		<style>
			:root{
				--oca-tooltip-bg: <?php echo ((get_option('oca_tooltipbg')) ? get_option('oca_tooltipbg') : '#000000') ?>;
				--oca-tooltip-color: <?php echo ((get_option('oca_tooltip_txt_color')) ? get_option('oca_tooltip_txt_color') : '#ffffff') ?>;
			}
		</style>
		<?php
	}
	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in OCA_Comments_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The OCA_Comments_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( "jquery-ui", 'https://code.jquery.com/ui/1.13.0/jquery-ui.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( "jBox", 'https://cdn.jsdelivr.net/gh/StephanWagner/jBox@v1.3.3/dist/jBox.all.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/oca-comments-public.js', array( 'jquery' ), microtime(), false );

		wp_localize_script( $this->plugin_name, 'publicajax', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'votecolor' => ((get_option('oca_vote_btn_color')) ? get_option('oca_vote_btn_color') : '#0170b9'),
			'unvotecolor' => ((get_option('oca_unvote_btn_color')) ? get_option('oca_unvote_btn_color') : '#707070'),
		) );
	}

	// Make warning if is not generic logo
	function footerScripts(){
		global $post, $wpdb;

		$isShowingPopup = true;
		$animatedImages = get_option( 'oca_animated_images' );
		if(!is_array($animatedImages)){
			$animatedImages = [];
		}

		shuffle($animatedImages);
		$randIndex = array_rand($animatedImages, 1);

		$currentImage = $animatedImages[$randIndex]; // Random gif animation
		
		$other_fallback_1 = get_option("other_fallback_1");
		$other_fallback_2 = get_option("other_fallback_2");
		$other_fallback_3 = get_option("other_fallback_3");
		$other_fallback_4 = get_option("other_fallback_4");
		$other_fallback_5 = get_option("other_fallback_5");

		$otherMsgs = array(
			$other_fallback_1,
			$other_fallback_2,
			$other_fallback_3,
			$other_fallback_4,
			$other_fallback_5
		);

		$oca_comment = null;
		$comment_author_name = null;
		$star = null;
		$missing = null;
		$comments = null;
		$commentDomId = 'comment';

		if(isset($_SESSION['oca_comment']) && !empty($_SESSION['oca_comment'])){
			$comment = $_SESSION['oca_comment'];
			$comment = json_decode($comment);

			$commentDomId = "comment-".$comment->id;
			$comment_author_name = $comment->name;
			$comment_author_email = $comment->email;
			$star = $comment->star;
			$missing = $comment->missing;
			$comments = $comment->comments;
		}

		$redirect = get_the_permalink( $post )."#$commentDomId";

		if(isset($_SESSION['nextcelebration_popup']) && !empty($_SESSION['nextcelebration_popup']) && $isShowingPopup){
			$isShowingPopup = false;

			$celebratsData = get_option("nextcelebration_popup");
			$contents = [];
			if($celebratsData && is_array($celebratsData)){
				$contents = $celebratsData;
			}

			$msg = '';
			$attachment = '';

			if(is_array($contents)){
				$msg = $contents['text'];
				$attachment = '';
				$wrap = '';
				switch ($contents['switch']) {
					case 'image':
						$attachment = '<img src="'.$contents['url'].'" alt="avatar">';
						break;
					case 'video':
						$wrap = 'style="border-radius: 0; display: flex; align-items: center; height: auto;"';
						$attachment = '<video controls width="228px" src="'.$contents['url'].'"></video>';
						break;
					default:
						$attachment = '<img src="'.$currentImage.'" alt="avatar">';
						break;
				}
			}
			
			$msg = str_replace("%name%", '<strong>'.$comment_author_name.'</strong>', $msg);
			$msg = str_replace("%star_count%", '<strong>'.$star.'</strong>', $msg);
			$msg = str_replace("%comments%", '<strong>'.$comments.'</strong>', $msg);
			$msg = str_replace("%missing%", '<strong>'.$missing.'</strong>', $msg);
			?>
			<script>
				let element3 = `<div class="animatedPopup">
						<div class="popupHeader">
							<span data-value="nextcelebration_popup" class="animatedPopup_popup_close">+</span>
						</div>
						<div class="animatedPopup_contents">
							<div class="imageWrap">
								<div <?php echo ((!empty($wrap)) ? $wrap : '') ?> class="animatedImageBox">
									<?php echo ((!empty($attachment)) ? $attachment : '') ?>
								</div>
							</div>

							<div class="popupdescription">
							<?php echo wpautop( $msg ) ?>
							</div>
						</div>
					</div>
				</div>`;
				jQuery('body').append(element3);
			</script>
			<?php
		}
		
		if(isset($_SESSION['top_priorities_popup']) &&  !empty($_SESSION['top_priorities_popup']) && $isShowingPopup){
			$isShowingPopup = false;

			$pcontent = json_decode($_SESSION['top_priorities_popup']);
			$attachment = '';
			$wrap = '';
			switch ($pcontent->switch) {
				case 'image':
					$attachment = '<img src="'.$pcontent->url.'" alt="avatar">';
					break;
				case 'video':
					$wrap = 'style="border-radius: 0; display: flex; align-items: center; height: auto;"';
					$attachment = '<video controls width="228px" src="'.$pcontent->url.'"></video>';
					break;
				default:
					$attachment = '<img src="'.$currentImage.'" alt="avatar">';
					break;
			}

			$msg1 = $pcontent->text;
			$msg1 = str_replace("%name%", '<strong>'.$comment_author_name.'</strong>', $msg1);
			$msg1 = str_replace("%star_count%", '<strong>'.$star.'</strong>', $msg1);
			$msg1 = str_replace("%comments%", '<strong>'.$comments.'</strong>', $msg1);
			$msg1 = str_replace("%missing%", '<strong>'.$missing.'</strong>', $msg1);
			?>
			<script>
				let element3 = `<div class="animatedPopup">
						<div class="popupHeader">
							<span data-value="top_priorities_popup" class="animatedPopup_popup_close">+</span>
						</div>
						<div class="animatedPopup_contents">
							<div class="imageWrap">
								<div <?php echo ((!empty($wrap)) ? $wrap : '') ?> class="animatedImageBox">
									<?php echo ((!empty($attachment)) ? $attachment : '') ?>
								</div>
							</div>

							<div class="popupdescription">
							<?php echo wpautop( $msg1 ) ?>
							</div>
						</div>
					</div>
				</div>`;
				jQuery('body').append(element3);
			</script>
			<?php
		}

		if(isset($_SESSION['got_new_star']) && $_SESSION['got_new_star'] === "true" && $isShowingPopup){
			$isShowingPopup = false;

			$contents = get_option("when_got_oca_star_popup");
			$msg1 = '';
			$attachment = '';

			if(is_array($contents)){
				$msg1 = $contents['text'];
				$attachment = '';
				$wrap = '';
				switch ($contents['switch']) {
					case 'image':
						$attachment = '<img src="'.$contents['url'].'" alt="avatar">';
						break;
					case 'video':
						$wrap = 'style="border-radius: 0; display: flex; align-items: center; height: auto;"';
						$attachment = '<video controls width="228px" src="'.$contents['url'].'"></video>';
						break;
					default:
						$attachment = '<img src="'.$currentImage.'" alt="avatar">';
						break;
				}
			}
			
			$msg1 = str_replace("%name%", '<strong>'.$comment_author_name.'</strong>', $msg1);
			$msg1 = str_replace("%star_count%", '<strong>'.$star.'</strong>', $msg1);
			$msg1 = str_replace("%comments%", '<strong>'.$comments.'</strong>', $msg1);
			$msg1 = str_replace("%missing%", '<strong>'.$missing.'</strong>', $msg1);
			?>
			<script>
				let element3 = `<div class="animatedPopup">
						<div class="popupHeader">
							<span data-value="got_new_star" class="animatedPopup_popup_close">+</span>
						</div>
						<div class="animatedPopup_contents">
							<div class="imageWrap">
								<div <?php echo ((!empty($wrap)) ? $wrap : '') ?> class="animatedImageBox">
									<?php echo ((!empty($attachment)) ? $attachment : '') ?>
								</div>
							</div>

							<div class="popupdescription">
							<?php echo wpautop( $msg1 ) ?>
							</div>
						</div>
					</div>
				</div>`;
				jQuery('body').append(element3);
			</script>
			<?php
		}
		
		if(isset($_SESSION['close_to_the_star']) && $_SESSION['close_to_the_star'] === "true" && $isShowingPopup){
			$isShowingPopup = false;

			$contents = get_option("when_so_close_oca_star_popup");
			$msg3 = '';
			$attachment = '';

			if(is_array($contents)){
				$msg3 = $contents['text'];
				$attachment = '';
				$wrap = '';
				switch ($contents['switch']) {
					case 'image':
						$attachment = '<img src="'.$contents['url'].'" alt="avatar">';
						break;
					case 'video':
						$wrap = 'style="border-radius: 0; display: flex; align-items: center; height: auto;"';
						$attachment = '<video controls width="228px" src="'.$contents['url'].'"></video>';
						break;
					default:
						$attachment = '<img src="'.$currentImage.'" alt="avatar">';
						break;
				}
			}

			$msg3 = str_replace("%name%", '<strong>'.$comment_author_name.'</strong>', $msg3);
			$msg3 = str_replace("%star_count%", '<strong>'.$star.'</strong>', $msg3);
			$msg3 = str_replace("%comments%", '<strong>'.$comments.'</strong>', $msg3);
			$msg3 = str_replace("%missing%", '<strong>'.$missing.'</strong>', $msg3);
			?>
			<script>
				let element2 = `<div class="animatedPopup">
						<div class="popupHeader">
							<span data-value="close_to_the_star" class="animatedPopup_popup_close">+</span>
						</div>
						<div class="animatedPopup_contents">
							<div class="imageWrap">
								<div <?php echo ((!empty($wrap)) ? $wrap : '') ?> class="animatedImageBox">
									<?php echo ((!empty($attachment)) ? $attachment : '') ?>
								</div>
							</div>

							<div class="popupdescription">
							<?php echo wpautop( $msg3 ) ?>
							</div>
						</div>
					</div>
				</div>`;
				jQuery('body').append(element2);
			</script>
			<?php
		}
		
		if(isset($_SESSION['has_in_top_10']) && $_SESSION['has_in_top_10'] === "true" && $isShowingPopup){
			$isShowingPopup = false;

			$contents = get_option("when_in_top_10_commenter_popup");
			$msg2 = '';
			$attachment = '';

			if(is_array($contents)){
				$msg2 = $contents['text'];
				$attachment = '';
				$wrap = '';
				switch ($contents['switch']) {
					case 'image':
						$attachment = '<img src="'.$contents['url'].'" alt="avatar">';
						break;
					case 'video':
						$wrap = 'style="border-radius: 0; display: flex; align-items: center; height: auto;"';
						$attachment = '<video controls width="228px" src="'.$contents['url'].'"></video>';
						break;
					default:
						$attachment = '<img src="'.$currentImage.'" alt="avatar">';
						break;
				}
			}

			$msg2 = str_replace("%name%", '<strong>'.$comment_author_name.'</strong>', $msg2);
			$msg2 = str_replace("%star_count%", '<strong>'.$star.'</strong>', $msg2);
			$msg2 = str_replace("%comments%", '<strong>'.$comments.'</strong>', $msg2);
			$msg2 = str_replace("%missing%", '<strong>'.$missing.'</strong>', $msg2);
			?>
			<script>
				let element1 = `<div class="animatedPopup">
						<div class="popupHeader">
							<span data-value="has_in_top_10" class="animatedPopup_popup_close">+</span>
						</div>
						<div class="animatedPopup_contents">
							<div class="imageWrap">
								<div <?php echo ((!empty($wrap)) ? $wrap : '') ?> class="animatedImageBox">
									<?php echo ((!empty($attachment)) ? $attachment : '') ?>
								</div>
							</div>

							<div class="popupdescription">
							<?php echo wpautop( $msg2 ) ?>
							</div>
						</div>
					</div>
				</div>`;
				jQuery('body').append(element1);
			</script>
			<?php
		}
		
		if(isset($_SESSION['otherCases']) && $_SESSION['otherCases'] === "true" && $isShowingPopup){
			$isShowingPopup = false;

			shuffle($otherMsgs);
			$randIndex2 = array_rand($otherMsgs, 1);
			$contents = $otherMsgs[$randIndex2];
			$msg4 = '';
			$attachment = '';

			if(is_array($contents)){
				$msg4 = $contents['text'];
				$attachment = '';
				$wrap = '';
				switch ($contents['switch']) {
					case 'image':
						$attachment = '<img src="'.$contents['url'].'" alt="avatar">';
						break;
					case 'video':
						$wrap = 'style="border-radius: 0; display: flex; align-items: center; height: auto;"';
						$attachment = '<video controls width="228px" src="'.$contents['url'].'"></video>';
						break;
					default:
						$attachment = '<img src="'.$currentImage.'" alt="avatar">';
						break;
				}
			}

			$msg4 = str_replace("%name%", '<strong>'.$comment_author_name.'</strong>', $msg4);
			$msg4 = str_replace("%star_count%", '<strong>'.$star.'</strong>', $msg4);
			$msg4 = str_replace("%comments%", '<strong>'.$comments.'</strong>', $msg4);
			$msg4 = str_replace("%missing%", '<strong>'.$missing.'</strong>', $msg4);
			?>
			<script>
				let element0 = `<div class="animatedPopup">
						<div class="popupHeader">
							<span data-value="otherCases" class="animatedPopup_popup_close">+</span>
						</div>
						<div class="animatedPopup_contents">
							<div class="imageWrap">
								<div <?php echo ((!empty($wrap)) ? $wrap : '') ?> class="animatedImageBox">
									<?php echo ((!empty($attachment)) ? $attachment : '') ?>
								</div>
							</div>

							<div class="popupdescription">
							<?php echo wpautop( $msg4 ) ?>
							</div>
						</div>
					</div>
				</div>`;
				jQuery('body').append(element0);
			</script>
			<?php
		}
		
		if(isset($_SESSION['isinvalidlogo']) && $_SESSION['isinvalidlogo'] === "true" && $isShowingPopup){
			$isShowingPopup = false;

			$contents = get_option("_generic_avatar_popup");
			$msg5 = '';
			$attachment = '';

			if(is_array($contents)){
				$msg5 = $contents['text'];
				$attachment = '';
				$wrap = '';
				switch ($contents['switch']) {
					case 'image':
						$attachment = '<img src="'.$contents['url'].'" alt="avatar">';
						break;
					case 'video':
						$wrap = 'style="border-radius: 0; display: flex; align-items: center; height: auto;"';
						$attachment = '<video controls width="228px" src="'.$contents['url'].'"></video>';
						break;
					default:
						$attachment = '<img src="'.$currentImage.'" alt="avatar">';
						break;
				}
			}

			$msg5 = str_replace("%name%", '<strong>'.$comment_author_name.'</strong>', $msg5);
			$msg5 = str_replace("%email%", '<strong>'.$comment_author_email.'</strong>', $msg5);
			$msg5 = str_replace("%star_count%", '<strong>'.$star.'</strong>', $msg5);
			$msg5 = str_replace("%comments%", '<strong>'.$comments.'</strong>', $msg5);
			$msg5 = str_replace("%missing%", '<strong>'.$missing.'</strong>', $msg5);
			?>
			<script>
				let element0 = `<div class="animatedPopup">
						<div class="popupHeader">
							<span data-value="isinvalidlogo" class="animatedPopup_popup_close">+</span>
						</div>

						<div class="animatedPopup_contents">
							<div class="imageWrap">
								<div <?php echo ((!empty($wrap)) ? $wrap : '') ?> class="animatedImageBox">
									<?php echo ((!empty($attachment)) ? $attachment : '') ?>
								</div>
							</div>

							<div class="popupdescription">
							<?php echo wpautop( $msg5 ) ?>
							</div>
						</div>
					</div>
				</div>`;
				jQuery('body').append(element0);
			</script>
			<?php
		}
		
		?>
		<script>
			jQuery(document).on("click", "span.animatedPopup_popup_close", function(){
				let session = jQuery(this).data("value");
				jQuery(document).find(".animatedPopup").fadeOut("slow");
				jQuery.ajax({
					type: "post",
					url: "<?php echo admin_url( 'admin-ajax.php' ) ?>",
					data: {
						action: "close_popup_via_ajax",
						session: session
					},
					dataType: "json",
					success: function (response) {
						jQuery(document).find(".animatedPopup").remove();
					}
				});
			});
		</script>
		<?php
	}

	function close_popup_via_ajax(){
		if(isset($_POST['session'])){
			$session = $_POST['session'];
			unset($_SESSION[$session]);

			echo json_encode(array("success" => "Success"));
			die;
		}
	}

	function oca_comments_template( $comment_template ) {
		global $post;
		if ( !( is_singular() && ( have_comments() || 'open' == $post->comment_status ) ) ) {
		   return;
		}

		$categories = get_the_category( $post->ID );
		$category_ids = [];
		if($categories){
			foreach($categories as $category){
				$category_ids[] = $category->term_id;
			}
		}

		$selected = get_option( 'cat_of_post_filter' );
		if(!is_array($selected)){
			$selected = array();
		}

		if(array_intersect($category_ids, $selected)){
			if($post->post_type == 'post'){
				
				global $post;
				if( $post->post_password && !post_password_required() ){
					return plugin_dir_path( __FILE__ ).'partials/comments.php';
				}else if(!$post->post_password){
					return plugin_dir_path( __FILE__ ).'partials/comments.php';
				}
				
			}else{
				return $comment_template;
			}
		}else{
			return $comment_template;
		}
    }

	// Ajax Search
	function search_comments_author(){
		if(isset($_GET['term']) && isset($_GET['postid'])){
			$term = stripslashes(sanitize_text_field( $_GET['term'] ));
			$post_id = intval($_GET['postid']);
			global $wpdb;

			$querys = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}comments WHERE comment_author LIKE '%$term%' AND comment_parent = 0 AND comment_approved = 1 AND comment_post_ID = $post_id ORDER BY comment_ID DESC");

			if($querys){
				$item = '';

				$highlight_emails = get_option( 'highlight_emails' );
				if(!is_array($highlight_emails)){
					$highlight_emails = array();
				}
				$highlight_color = ((get_option( 'highlight_color' )) ? get_option( 'highlight_color' ) : '#fff4e6');

				foreach($querys as $query){
					
					$commenterurlQuery = get_commenter_url($query->comment_author_email);

					$highlight = false;
					if(in_array($query->comment_author_email, $highlight_emails)){
						$highlight = true;
					}

					$user = get_user_by( 'email', $query->comment_author_email );
					$item .= '<li class="comment even thread-even depth-1" id="li-comment-'.$query->comment_ID.'">
					<article style="'.(($highlight) ? 'padding: 10px;margin-bottom: 5px; background-color: '.$highlight_color: '').'" id="comment-'.$query->comment_ID.'" class="ast-comment">
					<div class="ast-comment-info">
						<div class="ast-comment-avatar-wrap">
							<a class="_authorimg" data-content="'.get_commenter_tooltip_info($query->comment_author_email).'" href="'.$commenterurlQuery.'" target="_blank">'.get_avatar( $query, 50 ).'</a>
						</div>
						<header class="ast-comment-meta ast-row ast-comment-author vcard capitalize"><div class="ast-comment-cite-wrap"><cite><b class="fn">
							<a class="_authorname" data-content="'.get_commenter_tooltip_info($query->comment_author_email).'" href="'.$commenterurlQuery.'" target="_blank">'.ucfirst($query->comment_author).'</a>
						</b> </cite></div><div class="oca-comment-stars">
						'.oca_get_stars($query->comment_author_email, true, 'padding-left: 0px;').'
						</div>
						</header>
						</div>
						<section class="ast-comment-content comment">
							<p>'.oca_comment_replacing($query->comment_content).'</p>

							<div class="ast-comment-edit-reply-wrap">
								<span class="ast-edit-link"><a class="comment-edit-link" href="'.admin_url().'/comment.php?action=editcomment&amp;c='.$query->comment_ID.'">Edit</a></span>
								<span class="ast-reply-link"><a rel="nofollow" class="comment-reply-link" href="#comment-'.$query->comment_ID.'" data-commentid="'.$query->comment_ID.'" data-postid="'.$post_id.'" data-belowelement="comment-'.$query->comment_ID.'" data-respondelement="respond" data-replyto="Reply to '.$query->comment_author.'" aria-label="Reply to '.$query->comment_author.'">Reply</a></span>
							</div>
						</section>
					</article>';
	
					// Second tier
					$childcomments = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}comments WHERE comment_parent = {$query->comment_ID} AND comment_approved = 1 AND comment_post_ID = $post_id");
					
					if($childcomments){
						foreach($childcomments as $childquery){

							$highlight1 = false;
							if(in_array($childquery->comment_author_email, $highlight_emails)){
								$highlight1 = true;
							}

							$commenterurlChild = get_commenter_url($childquery->comment_author_email);

							$user1 = get_user_by( 'email', $childquery->comment_author_email );
							$item .= '<ol class="children">
							<li class="comment byuser comment-author-admin bypostauthor odd alt depth-'.$childquery->comment_ID.'" id="li-comment-'.$childquery->comment_ID.'">
							<article style="'.(($highlight1) ? 'padding: 10px;margin-bottom: 5px; background-color: '.$highlight_color: '').'" id="comment-'.$childquery->comment_ID.'" class="ast-comment">
							<div class="ast-comment-info">
								<div class="ast-comment-avatar-wrap">
								<a class="_authorimg" href="'.$commenterurlChild.'" data-content="'.get_commenter_tooltip_info($childquery->comment_author_email).'" target="_blank">'.get_avatar( $childquery, 50 ).'</a>
								</div>
								<header class="ast-comment-meta ast-row ast-comment-author vcard capitalize"><div class="ast-comment-cite-wrap"><cite><b class="fn">
								<a class="_authorname" href="'.$commenterurlChild.'" data-content="'.get_commenter_tooltip_info($childquery->comment_author_email).'" target="_blank">'.ucfirst($childquery->comment_author).'</a>
								</b> </cite></div><div class="oca-comment-stars">
								'.oca_get_stars($childquery->comment_author_email, true, 'padding-left: 0px;').'
								</div>
								</header>
								</div>
								<section class="ast-comment-content comment">
									<p>'.oca_comment_replacing($childquery->comment_content).'</p>
		
									<div class="ast-comment-edit-reply-wrap">
										<span class="ast-edit-link"><a class="comment-edit-link" href="'.admin_url().'/comment.php?action=editcomment&amp;c='.$childquery->comment_ID.'">Edit</a></span>
										<span class="ast-reply-link"><a rel="nofollow" class="comment-reply-link" href="#comment-'.$childquery->comment_ID.'" data-commentid="'.$childquery->comment_ID.'" data-postid="'.$post_id.'" data-belowelement="comment-'.$childquery->comment_ID.'" data-respondelement="respond" data-replyto="Reply to '.$childquery->comment_author.'" aria-label="Reply to '.$childquery->comment_author.'">Reply</a></span>
									</div>
								</section>
							</article>';

								// 3rd tier
								$childcomments3 = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}comments WHERE comment_parent = {$childquery->comment_ID} AND comment_approved = 1 AND comment_post_ID = $post_id");
					
								if($childcomments3){
									foreach($childcomments3 as $childquery3){

										$highlight3 = false;
										if(in_array($childquery3->comment_author_email, $highlight_emails)){
											$highlight3 = true;
										}

										$commenterurl3 = get_commenter_url($childquery3->comment_author_email);

										$user2 = get_user_by( 'email', $childquery3->comment_author_email );
										$item .= '<ol class="children">
										<li class="comment byuser comment-author-admin bypostauthor odd alt depth-'.$childquery3->comment_ID.'" id="li-comment-'.$childquery3->comment_ID.'">
										<article style="'.(($highlight3) ? 'padding: 10px;margin-bottom: 5px; background-color: '.$highlight_color: '').'" id="comment-'.$childquery3->comment_ID.'" class="ast-comment">
										<div class="ast-comment-info">
											<div class="ast-comment-avatar-wrap">
											<a class="_authorimg" data-content="'.get_commenter_tooltip_info($childquery3->comment_author_email).'" href="'.$commenterurl3.'" target="_blank">'.get_avatar( $childquery3, 50 ).'</a>
											</div>
											<header class="ast-comment-meta ast-row ast-comment-author vcard capitalize"><div class="ast-comment-cite-wrap"><cite><b class="fn">
											<a class="_authorname" data-content="'.get_commenter_tooltip_info($childquery3->comment_author_email).'" href="'.$commenterurl3.'" target="_blank">'.ucfirst($childquery3->comment_author).'</a>
											</b> </cite></div><div class="oca-comment-stars">
											'.oca_get_stars($childquery3->comment_author_email, true, 'padding-left: 0px;').'
											</div>
											</header>
											</div>
											<section class="ast-comment-content comment">
												<p>'.oca_comment_replacing($childquery3->comment_content).'</p>
					
												<div class="ast-comment-edit-reply-wrap">
													<span class="ast-edit-link"><a class="comment-edit-link" href="'.admin_url().'/comment.php?action=editcomment&amp;c='.$childquery3->comment_ID.'">Edit</a></span>
													<span class="ast-reply-link"><a rel="nofollow" class="comment-reply-link" href="#comment-'.$childquery3->comment_ID.'" data-commentid="'.$childquery3->comment_ID.'" data-postid="'.$post_id.'" data-belowelement="comment-'.$childquery3->comment_ID.'" data-respondelement="respond" data-replyto="Reply to '.$childquery3->comment_author.'" aria-label="Reply to '.$childquery3->comment_author.'">Reply</a></span>	
												</div>
											</section>
										</article>';
										
										// 4th tier
										$childcomments4 = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}comments WHERE comment_parent = {$childquery3->comment_ID} AND comment_approved = 1 AND comment_post_ID = $post_id");
							
										if($childcomments4){
											foreach($childcomments4 as $childquery4){

												$highlight4 = false;
												if(in_array($childquery4->comment_author_email, $highlight_emails)){
													$highlight4 = true;
												}

												$commenterurl4 = get_commenter_url($childquery4->comment_author_email);

												$user3 = get_user_by( 'email', $childquery4->comment_author_email );
												$item .= '<ol class="children">
												<li class="comment byuser comment-author-admin bypostauthor odd alt depth-'.$childquery4->comment_ID.'" id="li-comment-'.$childquery4->comment_ID.'">
												<article style="'.(($highlight4) ? 'padding: 10px;margin-bottom: 5px; background-color: '.$highlight_color: '').'" id="comment-'.$childquery4->comment_ID.'" class="ast-comment">
												<div class="ast-comment-info">
													<div class="ast-comment-avatar-wrap">
													<a class="_authorimg" data-content="'.get_commenter_tooltip_info($childquery4->comment_author_email).'" href="'.$commenterurl4.'" target="_blank">'.get_avatar( $childquery4, 50 ).'</a>
													</div>
													<header class="ast-comment-meta ast-row ast-comment-author vcard capitalize"><div class="ast-comment-cite-wrap"><cite><b class="fn">
													<a class="_authorname" data-content="'.get_commenter_tooltip_info($childquery4->comment_author_email).'" href="'.$commenterurl4.'" target="_blank">'.ucfirst($childquery4->comment_author).'</a>
													</b> </cite></div><div class="oca-comment-stars">
													'.oca_get_stars($childquery4->comment_author_email, true, 'padding-left: 0px;').'
													</div>
													</header>
													</div>
													<section class="ast-comment-content comment">
														<p>'.oca_comment_replacing($childquery4->comment_content).'</p>
							
														<div class="ast-comment-edit-reply-wrap">
															<span class="ast-edit-link"><a class="comment-edit-link" href="'.admin_url().'/comment.php?action=editcomment&amp;c='.$childquery4->comment_ID.'">Edit</a></span>
															<span class="ast-reply-link"><a rel="nofollow" class="comment-reply-link" href="#comment-'.$childquery4->comment_ID.'" data-commentid="'.$childquery4->comment_ID.'" data-postid="'.$post_id.'" data-belowelement="comment-'.$childquery4->comment_ID.'" data-respondelement="respond" data-replyto="Reply to '.$childquery4->comment_author.'" aria-label="Reply to '.$childquery4->comment_author.'">Reply</a></span>		
														</div>
													</section>
												</article>';
												
												// 5th tier
												$childcomments5 = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}comments WHERE comment_parent = {$childquery4->comment_ID} AND comment_approved = 1 AND comment_post_ID = $post_id");
									
												if($childcomments5){
													foreach($childcomments5 as $childquery5){

														$highlight5 = false;
														if(in_array($childquery5->comment_author_email, $highlight_emails)){
															$highlight5 = true;
														}

														$commenterurl5 = get_commenter_url($childquery5->comment_author_email);

														$user4 = get_user_by( 'email', $childquery5->comment_author_email );
														$item .= '<ol class="children">
														<li class="comment byuser comment-author-admin bypostauthor odd alt depth-'.$childquery5->comment_ID.'" id="li-comment-'.$childquery5->comment_ID.'">
														<article style="'.(($highlight5) ? 'padding: 10px;margin-bottom: 5px; background-color: '.$highlight_color: '').'" id="comment-'.$childquery5->comment_ID.'" class="ast-comment">
														<div class="ast-comment-info">
															<div class="ast-comment-avatar-wrap">
																<a class="_authorimg" data-content="'.get_commenter_tooltip_info($childquery5->comment_author_email).'" href="'.$commenterurl5.'" target="_blank">'.get_avatar( $childquery5, 50 ).'</a>
															</div>
															<header class="ast-comment-meta ast-row ast-comment-author vcard capitalize"><div class="ast-comment-cite-wrap"><cite><b class="fn">
																<a class="_authorname" data-content="'.get_commenter_tooltip_info($childquery5->comment_author_email).'" href="'.$commenterurl5.'" target="_blank">'.ucfirst($childquery5->comment_author).'</a>
															</b> </cite></div><div class="oca-comment-stars">
															'.oca_get_stars($childquery5->comment_author_email, true, 'padding-left: 0px;').'
															</div>
															</header>
															</div>
															<section class="ast-comment-content comment">
																<p>'.oca_comment_replacing($childquery5->comment_content).'</p>
									
																<div class="ast-comment-edit-reply-wrap">
																	<span class="ast-edit-link"><a class="comment-edit-link" href="'.admin_url().'/comment.php?action=editcomment&amp;c='.$childquery5->comment_ID.'">Edit</a></span>
																</div>
															</section>
														</article>';
														$item .= '</li>
														</ol>';
													}
												}

												$item .= '</li>
												</ol>';
											}
										}

										$item .= '</li>
										</ol>';
									}
								}

							$item .= '</li>
							</ol>';
						}
					}
						
					$item .= '</li>';
				}
				

				echo json_encode(array('comment' => $item));
				die;
			}

			echo json_encode(array('error' => 'error'));
			die;
		}

		echo json_encode(array('error' => 'error'));
		die;
	}

	function oca_commenter_shortcode( $attr ){
		ob_start();
		require_once plugin_dir_path( __FILE__ ).'partials/commenter.php';
		$output = ob_get_contents();
		ob_get_clean();
		return $output;
	}

	function oca_top_commenters_shortcode($attr){
		ob_start();
		$count = 10;
		if(is_array($attr) && array_key_exists('count',$attr)){
			$count = $attr['count'];
		}
		require_once plugin_dir_path( __FILE__ ).'partials/top_commenters.php';
		$output = ob_get_contents();
		ob_get_clean();
		return $output;
	}

	function get_commenters_list($min, $max, $stars){
		global $wpdb;
		$data = array();

        $commenters = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}comments GROUP BY comment_author_email");

        if($commenters){
            foreach($commenters as $commenter){
                $arr = array(
                    'ID' => $commenter->comment_ID,
                    'user_email' => $commenter->comment_author_email,
                    'user_name' => $commenter->comment_author,
                    'stars' => oca_get_stars( $commenter->comment_author_email, false ),
                    'user_comments' => oca_get_author_comments_count($commenter->comment_author_email)
                );

				if($stars !== null){
					if(intval($arr['stars']) === $stars){
						if($min !== null && $max === null){
							if(intval($arr['user_comments']) >= $min){
								if($commenter->comment_author_email){
									$data[] = $arr;
								}
							}
						}elseif($max !== null && $min === null){
							if(intval($arr['user_comments']) <= $max){
								if($commenter->comment_author_email){
									$data[] = $arr;
								}
							}
						}elseif($min !== null && $max !== null){
							if(intval($arr['user_comments']) >= $min && intval($arr['user_comments']) <= $max){
								if($commenter->comment_author_email){
									$data[] = $arr;
								}
							}
						}else{
							if($commenter->comment_author_email){
								$data[] = $arr;
							}
						}
					}
				}elseif($min !== null){
					if($max !== null){
						if(intval($arr['user_comments']) >= $min && intval($arr['user_comments']) <= $max){
							if($commenter->comment_author_email){
								$data[] = $arr;
							}
						}
					}elseif($max === null){
						if(intval($arr['user_comments']) >= $min){
							if($commenter->comment_author_email){
								$data[] = $arr;
							}
						}
					}else{
						if($commenter->comment_author_email){
							$data[] = $arr;
						}
					}
				}elseif($max !== null){
					if($min !== null){
						if(intval($arr['user_comments']) >= $min && intval($arr['user_comments']) <= $max){
							if($commenter->comment_author_email){
								$data[] = $arr;
							}
						}
					}elseif($min === null){
						if(intval($arr['user_comments']) <= $max){
							if($commenter->comment_author_email){
								$data[] = $arr;
							}
						}
					}else{
						if($commenter->comment_author_email){
							$data[] = $arr;
						}
					}
				}else{
					if($min === null && $max === null && $stars === null){
						if($commenter->comment_author_email){
							$data[] = $arr;
						}
					}
				}
            }
        }

		$mostComments = array();
		foreach ($data as $key => $row)
		{
			$mostComments[$key] = $row['user_comments'];
		}
		array_multisort($mostComments, SORT_DESC, $data);

		return $data;
	}

	function oca_commenters_list($attr){
		ob_start();
		$stars = null;
		if(is_array($attr) && array_key_exists('stars',$attr)){
			$stars = intval($attr['stars']);
		}
		$min = null;
		if(is_array($attr) && array_key_exists('min',$attr)){
			$min = intval($attr['min']);
		}
		$max = null;
		if(is_array($attr) && array_key_exists('max',$attr)){
			$max = intval($attr['max']);
		}
		
		require_once plugin_dir_path( __FILE__ ).'partials/commenters_list.php';
		$output = ob_get_contents();
		ob_get_clean();
		return $output;
	}

	function hide_when_no_comment( $atts, $conts = null ) {
		
		$a = shortcode_atts( array(
			'max_stars' => '1',
		), $atts );

		$contents = $conts;
		if(!isset($_COOKIE['oca_cookie_1'])){
			$contents = '';
		}else{
			$star = 0;
			if(isset($_COOKIE['oca_cookie_2'])){
				$star = intval($_COOKIE['oca_cookie_2']);
			}

			if(intval($a['max_stars']) === 0 && $star === 0){
				$contents = '';
			}elseif(intval($a['max_stars']) === 1 && $star <= 1){
				$contents = '';
			}elseif(intval($a['max_stars']) === 2 && $star <= 2){
				$contents = '';
			}elseif(intval($a['max_stars']) === 3 && $star <= 3){
				$contents = '';
			}elseif(intval($a['max_stars']) === 4 && $star <= 4){
				$contents = '';
			}elseif(intval($a['max_stars']) === 5 && $star <= 5){
				$contents = '';
			}else{
				$contents = $conts;
			}
		}
	
		return $contents;
	}

	function show_when_no_comment( $atts, $conts = null ) {
		
		$a = shortcode_atts( array(
			'max_stars' => '1',
		), $atts );

		$contents = '';
		if(!isset($_COOKIE['oca_cookie_1'])){
			$contents = $conts;
		}else{
			$star = 0;
			if(isset($_COOKIE['oca_cookie_2'])){
				$star = intval($_COOKIE['oca_cookie_2']);
			}

			if(intval($a['max_stars']) === 0 && $star === 0){
				$contents = $conts;
			}elseif(intval($a['max_stars']) === 1 && $star <= 1){
				$contents = $conts;
			}elseif(intval($a['max_stars']) === 2 && $star <= 2){
				$contents = $conts;
			}elseif(intval($a['max_stars']) === 3 && $star <= 3){
				$contents = $conts;
			}elseif(intval($a['max_stars']) === 4 && $star <= 4){
				$contents = $conts;
			}elseif(intval($a['max_stars']) === 5 && $star <= 5){
				$contents = $conts;
			}else{
				$contents = '';
			}
		}

		return $contents;
	}

	function number_of_months($starting_date, $currentDate){
		$ts1 = strtotime($starting_date);
		$ts2 = strtotime($currentDate);

		$year1 = date('Y', $ts1);
		$year2 = date('Y', $ts2);

		$month1 = date('m', $ts1);
		$month2 = date('m', $ts2);

		$diff = (($year2 - $year1) * 12) + ($month2 - $month1);

		return $diff;
	}

	function number_of_days($starting_date, $currentDate){
		$startDate = new DateTime($starting_date);
		$endDate = new DateTime($currentDate);

		$difference = $endDate->diff($startDate);
		return $difference->format("%a");
	}

	function comments_stats_callback($atts){
		$attribute = shortcode_atts( array(
			'display' => null,
		), $atts );

		ob_start();

		global $wpdb;
		$comments = $wpdb->query("SELECT * FROM {$wpdb->prefix}comments WHERE `comment_approved` = 1");

		switch ($attribute['display']) {
			case 'total':
				echo $comments;
				break;
			case 'post':
				$avg = 0;
				$posts = get_posts([
					'post_type' => 'post',
					'post_status' => 'publish',
					'numberposts' => -1,
					'fields' => 'ids'
				]);

				if($posts){
					$posts = sizeof($posts);
				}

				if($posts > 0){
					$avg = $comments/$posts;
				}

				echo round($avg);
				break;
			case 'month':
				$avg = 0;
				$start_date = get_option('stats_starting_date');
				if($start_date){
					$start_date = date("Y-m-d", strtotime(get_option('stats_starting_date')));
				}
				$months = $this->number_of_months($start_date, date("Y-m-d"));

				if($months > 0){
					$avg = $comments/$months;
				}
				
				echo round($avg);
				break;
			case 'week':
				$avg = 0;
				$start_date = get_option('stats_starting_date');
				if($start_date){
					$start_date = date("Y-m-d", strtotime(get_option('stats_starting_date')));
				}

				$weeks = 0;
				$days = $this->number_of_days($start_date, date("Y-m-d"));
				if($days > 0){
					$weeks = $days/7;
				}

				if($weeks > 0){
					$avg = $comments/$weeks;
				}

				echo round($avg);
				break;
			case 'day':
				$avg = 0;
				$start_date = get_option('stats_starting_date');
				if($start_date){
					$start_date = date("Y-m-d", strtotime(get_option('stats_starting_date')));
				}

				$days = $this->number_of_days($start_date, date("Y-m-d"));
				
				if($days > 0){
					$avg = $comments/$days;
				}

				echo round($avg);
				break;
			case 'person':
				$avg = 0;
				$commenters = $wpdb->query("SELECT * FROM {$wpdb->prefix}comments WHERE `comment_approved` = 1 GROUP BY comment_author_email");

				if($commenters > 0){
					$avg = $comments/$commenters;
				}
				echo round($avg);
				break;
		}

		return ob_get_clean();
	}
}
