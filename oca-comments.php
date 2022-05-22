<?php
ob_start();
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.fiverr.com/junaidzx90
 * @since             1.0.0
 * @package           OCA_Comments
 *
 * @wordpress-plugin
 * Plugin Name:       OCA Comments
 * Plugin URI:        https://www.fiverr.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            junaidzx90
 * Author URI:        https://www.fiverr.com/junaidzx90
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       oca-comments
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once plugin_dir_path( __FILE__ )."activecampaign/includes/ActiveCampaign.class.php";

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'OCA_COMMENTS_VERSION', '1.0.0' );
define( 'OCA_COMMENTS_URL', plugin_dir_url( __FILE__ ));
define( 'OCA_COMMENTS_PATH', plugin_dir_path( __FILE__ ));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-oca-comments-activator.php
 */
function activate_oca_comments() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-oca-comments-activator.php';
	OCA_Comments_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-oca-comments-deactivator.php
 */
function deactivate_oca_comments() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-oca-comments-deactivator.php';
	OCA_Comments_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_oca_comments' );
register_deactivation_hook( __FILE__, 'deactivate_oca_comments' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-oca-comments.php';


if(get_option( 'template' ) === 'astra'){

	function change_comments_string($post_id){
		$categories = get_the_category( $post_id );
		$category_ids = [];
		if($categories){
			foreach($categories as $category){
				$category_ids[] = $category->term_id;
			}
		}

		$changesIds = null;
		$changes = get_option( 'oca_comments_texts' );
		if($changes && is_array($changes)){
			$changesIds = wp_list_pluck( $changes, 'category' );
		}
		
		if(!is_array($changesIds)){
			$changesIds = [];
		}
		
		$foundColumn = array_intersect($changesIds, $category_ids);
		if($foundColumn){
			if(array_key_exists(key($foundColumn), $changes)){
				$data = $changes[key($foundColumn)];
				
				return array(
					'singular' => $data['singular'],
					'plural' => $data['plural']
				);
			}
		}
	}

	// Get contact id by email
	function get_ac_contact_id_by_email($email){
		$url = get_option('activecampaign_url');
		$api = get_option('activecampaign_api');

		if(!empty($url) && !empty($api)){
			$response = wp_remote_get( $url."/api/3/contacts?search=$email", array(
				'headers' => array(
					'Api-Token' => $api
				)
			) );
			
			$responseBody = wp_remote_retrieve_body( $response );
			$results = json_decode( $responseBody );

			$contact_id = null;
			if(!empty($results) && is_object($results)){
				$contacts = $results->contacts;
				if(is_array($contacts)){
					foreach($contacts as $contact){
						if($contact->email === $email){
							$contact_id = $contact->id;
						}
					}
				}
			}

			return $contact_id;
		}
	}

	// Call activecampaign all fields
	if(isset($_GET['page']) && $_GET['page'] === 'oca-settings' && isset($_GET['tab']) && $_GET['tab'] === 'activecampaign'){
		try {
			$url = get_option('activecampaign_url');
			$api = get_option('activecampaign_api');

			if(!empty($url) && !empty($api)){
				$response = wp_remote_get( $url.'/api/3/fields', array(
					'headers' => array(
						'Api-Token' => $api
					)
				) );
				
				$responseBody = wp_remote_retrieve_body( $response );
				$result = json_decode( $responseBody );

				$activecampaignFields = [];
				$activecampaignOptions = [];

				if($result !== null && is_object($result)){
					foreach($result as $key => $fields){
						if($key === 'fields'){
							foreach($fields as $field){
								$farr = array(
									'title' => $field->title,
									'id' => $field->id
								);

								$activecampaignFields[] = $farr;
							}
						}
						
						if($key === 'fieldOptions'){
							foreach($fields as $field){
								$order = $field->orderid;
								$activecampaignOptions[$order] = $field->label;
							}
						}
					}

					ksort($activecampaignOptions);
				}
			}
		} catch (\Throwable $th) {
			//throw $th;
		}
	}

	// search included category
	function get_search_included_cat($post_id){
		$categories = get_the_category( $post_id );
		$category_ids = [];
		if($categories){
			foreach($categories as $category){
				$category_ids[] = $category->term_id;
			}
		}

		$exclude = get_option( 'include_search_category' );
		if(!is_array($exclude)){
			$exclude = array();
		}

		return array_intersect($category_ids, $exclude);
	}
	
	function oca_get_top10_counts($email){
		$user_email = strtolower($email);

		if(!$user_email){
			return;
		}
		global $wpdb;
		$query = $wpdb->query("SELECT * FROM {$wpdb->prefix}comments WHERE DATE(comment_date) >= DATE(NOW()) - INTERVAL 30 DAY AND `comment_author_email` = '$user_email' AND `comment_approved` = 1");
	
		$returns = '---';
		if($query){
			$returns = $query;
		}
		return $returns;
	}

	function checkin_top10($uemail){
		global $wpdb;
        $emailsExc = get_option( 'exclude_rank_emails' );
        if(!is_array($emailsExc)){
            $emailsExc = array();
        }
        $emails = '""';
        if($emailsExc){
            $sep = '';
            $emails = '';
            foreach($emailsExc as $exc){
                $emails .= $sep.'"'.$exc.'"';
                $sep = ',';
            }
        }

        $commentsObj = $wpdb->get_results("SELECT *, COUNT(comment_author_email) AS counts FROM 
            {$wpdb->prefix}comments 
            WHERE DATE(comment_date) >= DATE(NOW()) - INTERVAL 30 DAY AND `comment_approved` = 1 
            AND comment_author_email NOT IN(".$emails.") AND comment_author_email != '' GROUP BY comment_author_email ORDER BY counts DESC LIMIT 3");

		if($commentsObj){
			foreach($commentsObj as $comment){
				if($comment->comment_author_email === $uemail){
					return true;
				}
			}
		}
	}
	
	// Generic logo detect
	function oca_validate_gravatar($e) {
		$email = strtolower($e);
		// Craft a potential url and test its headers
		$hash = md5(strtolower(trim($email)));
	
		$uri = 'http://www.gravatar.com/avatar/' . $hash . '?d=404';
		$headers = @get_headers($uri);
	
		if (!preg_match("|200|", $headers[0])) {
			$has_valid_avatar = false;
		} else {
			$has_valid_avatar = true;
		}
	
		return $has_valid_avatar;
	}
	
	function oca_get_author_comments_count($email){
		$comment_author_email = strtolower($email);

		if(!$comment_author_email){
			return;
		}
		global $wpdb;
		$query = $wpdb->query("SELECT * FROM {$wpdb->prefix}comments WHERE `comment_author_email` = '$comment_author_email' AND `comment_approved` = 1");
		if($query){
			return $query;
		}
	}

	function get_total_published_comments_count(){
		global $wpdb;
		$comments = $wpdb->query("SELECT comment_ID FROM {$wpdb->prefix}comments WHERE `comment_approved` = 1");
		return $comments;
	}
	
	function oca_get_author_last_comment_date($email){
		$comment_author_email = strtolower($email);
		if(!$comment_author_email){
			return;
		}
		global $wpdb;
		$comment_date = $wpdb->get_var("SELECT comment_date FROM {$wpdb->prefix}comments WHERE `comment_author_email` = '$comment_author_email' ORDER BY comment_date DESC");
		if($comment_date){
			return date("Y-m-d", strtotime($comment_date));
		}
	}

	// Get custom profile
	function get_custom_profile($e){
		$email = strtolower($e);

		$rows = get_option('custom_profiles');
		if(!is_array($rows)){
			$rows = array();
		}

		if(sizeof($rows) > 0){
			foreach($rows as $row){
				if($row['email'] === $email){
					return $row['url'];
				}
			}
		}
	}

	// Get_commenter url
	function get_commenter_url($e){
		$email = strtolower($e);

		$commenterurl = '#';
		if(get_custom_profile($email)){
			$commenterurl = get_custom_profile($email);
		}else{
			if(get_commenter_page_url()){
				$useremail = base64_encode($email);
				$commenterurl = esc_url( get_commenter_page_url().'?user='.$useremail );
			}
		}
		return $commenterurl;
	}

	function get_commenter_tooltip_info($e){
		$email = strtolower($e);
		$authorname = get_fullname_if_user_exist($email);

		$info = "Click here to read all the comments of $authorname";
		if(get_custom_profile($email)){
			$info = "Click here for $authorname profile";
		}

		return $info;
	}
	
	// Global function
	function oca_get_stars($email, $comments = true, $para = ''){
		$comment_author_email = strtolower($email);

		global $wpdb;
		if(!$comment_author_email){
			return;
		}
		$author_comments = oca_get_author_comments_count($comment_author_email);
		
		$output = '';
		$authorname = get_fullname_if_user_exist($comment_author_email);
		
		$one_star_length = ((get_option('oca_one_star_length')) ? intval(get_option('oca_one_star_length')) : 25 );
		$two_star_length = ((get_option('oca_two_star_length')) ? intval(get_option('oca_two_star_length')) : 75 );
		$three_star_length = ((get_option('oca_three_star_length')) ? intval(get_option('oca_three_star_length')) : 150 );
		$four_star_length = ((get_option('oca_four_star_length')) ? intval(get_option('oca_four_star_length')) : 250 );
		$five_star_length = ((get_option('oca_five_star_length')) ? intval(get_option('oca_five_star_length')) : 375 );

		$back = '';
		if($comments){
			$output .= '<div class="author_stars_box">';
			$output .= '<p style="'.$para.'">';

			$starpath = OCA_COMMENTS_URL.'public/images/';

			$commenterurl = get_commenter_url($comment_author_email);
			
			$output .= '<a class="_comments_count" data-content="'.get_commenter_tooltip_info($comment_author_email).'" target="_b" href="'.$commenterurl.'" class="comcounts commentslink">'.(($author_comments) ? $author_comments : 0).' '.(($author_comments <= 1) ? 'comment' : 'comments').'</a>';

			// Star System Explaination page
			$explaingPage = ((get_option('star_explaing_page')) ? esc_url(get_the_permalink( get_option('star_explaing_page') )) : '#');

			if($author_comments == 0 || $author_comments < $one_star_length){
				$output .= '<a class="star_link" href="'.$explaingPage.'" target="_blank"><img src="'.$starpath.'0-stars.png'.'" alt="star-0"/> </a>';
			}else if($author_comments >= $one_star_length && $author_comments < $two_star_length){
				$output .= '<a class="star_link" href="'.$explaingPage.'" target="_blank"><img src="'.$starpath.'1-stars.png'.'" alt="star-1"/> </a>';
			}else if($author_comments >= $two_star_length && $author_comments < $three_star_length){
				$output .= '<a class="star_link" href="'.$explaingPage.'" target="_blank"><img src="'.$starpath.'2-stars.png'.'" alt="star-2"/> </a>';
			}else if($author_comments >= $three_star_length && $author_comments < $four_star_length){
				$output .= '<a class="star_link" href="'.$explaingPage.'" target="_blank"><img src="'.$starpath.'3-stars.png'.'" alt="star-3"/> </a>';
			}else if($author_comments >= $four_star_length && $author_comments < $five_star_length){
				$output .= '<a class="star_link" href="'.$explaingPage.'" target="_blank"><img src="'.$starpath.'4-stars.png'.'" alt="star-4"/> </a>';
			}else if($author_comments >= $five_star_length){
				$output .= '<a class="star_link" href="'.$explaingPage.'" target="_blank"><img src="'.$starpath.'5-stars.png'.'" alt="star-5"/> </a>';
			}

			$output .= '</p>';
			$output .= '</div>';

			return $output;
		}else{
			if($author_comments == 0 || $author_comments < $one_star_length){
				$back .= 0;
			}else if($author_comments >= $one_star_length && $author_comments < $two_star_length){
				$back .= 1;
			}else if($author_comments >= $two_star_length && $author_comments < $three_star_length){
				$back .= 2;
			}else if($author_comments >= $three_star_length && $author_comments < $four_star_length){
				$back .= 3;
			}else if($author_comments >= $four_star_length && $author_comments < $five_star_length){
				$back .= 4;
			}else if($author_comments >= $five_star_length){
				$back .= 5;
			}
			return $back;
		}
	}

	/**
	 * Deprecated
	 */
	function oca_get_top_10_stars($email, $comments){
		$comment_author_email = strtolower($email);

		global $wpdb;
		if(!$comment_author_email){
			return;
		}
		$author_comments = oca_get_author_comments_count($comment_author_email);
		
		$output = '';
		$authorname = get_fullname_if_user_exist($comment_author_email);
		
		$one_star_length = ((get_option('oca_one_star_length')) ? intval(get_option('oca_one_star_length')) : 25 );
		$two_star_length = ((get_option('oca_two_star_length')) ? intval(get_option('oca_two_star_length')) : 75 );
		$three_star_length = ((get_option('oca_three_star_length')) ? intval(get_option('oca_three_star_length')) : 150 );
		$four_star_length = ((get_option('oca_four_star_length')) ? intval(get_option('oca_four_star_length')) : 250 );
		$five_star_length = ((get_option('oca_five_star_length')) ? intval(get_option('oca_five_star_length')) : 375 );

		$output .= '<div class="author_stars_box">';
		$output .= '<p>';

		$starpath = OCA_COMMENTS_URL.'public/images/';

		$commenterurl = $commenterurl = get_commenter_url($comment_author_email);
		
		$output .= '<a class="_comments_count" data-content="'.get_commenter_tooltip_info($comment_author_email).'" target="_b" href="'.$commenterurl.'" class="comcounts commentslink">'.(($comments) ? $comments : 0).' '.(($comments <= 1) ? 'comment' : 'comments').'</a>';

		// Star System Explaination page
		$explaingPage = ((get_option('star_explaing_page')) ? esc_url(get_the_permalink( get_option('star_explaing_page') )) : '#');

		if($author_comments == 0 || $author_comments < $one_star_length){
			$output .= '<a class="star_link" href="'.$explaingPage.'" target="_blank"><img src="'.$starpath.'0-stars.png'.'" alt="star-0"/> </a>';
		}else if($author_comments >= $one_star_length && $author_comments < $two_star_length){
			$output .= '<a class="star_link" href="'.$explaingPage.'" target="_blank"><img src="'.$starpath.'1-stars.png'.'" alt="star-1"/> </a>';
		}else if($author_comments >= $two_star_length && $author_comments < $three_star_length){
			$output .= '<a class="star_link" href="'.$explaingPage.'" target="_blank"><img src="'.$starpath.'2-stars.png'.'" alt="star-2"/> </a>';
		}else if($author_comments >= $three_star_length && $author_comments < $four_star_length){
			$output .= '<a class="star_link" href="'.$explaingPage.'" target="_blank"><img src="'.$starpath.'3-stars.png'.'" alt="star-3"/> </a>';
		}else if($author_comments >= $four_star_length && $author_comments < $five_star_length){
			$output .= '<a class="star_link" href="'.$explaingPage.'" target="_blank"><img src="'.$starpath.'4-stars.png'.'" alt="star-4"/> </a>';
		}else if($author_comments >= $five_star_length){
			$output .= '<a class="star_link" href="'.$explaingPage.'" target="_blank"><img src="'.$starpath.'5-stars.png'.'" alt="star-5"/> </a>';
		}

		$output .= '</p>';
		$output .= '</div>';

		return $output;
	}
	
	remove_filter( 'comment_text' , 'wptexturize' ); // Remove comments magic qoutes
	function oca_comment_replacing($comment_text){
		$comment_texts =  $comment_text;

		$definedText = get_option('replaceing_comment_texts');
		if(!is_array($definedText)){
			$definedText = array();
		}

		$replaced_comment = '';

		foreach($definedText as $text){
			$search = stripcslashes(sanitize_text_field( $text['search'] ));
			$replace = stripcslashes(sanitize_text_field( $text['replace'] ));

			$replaced_comment = preg_replace("/\b(?i)$search\b/"," $replace ", ((!empty($replaced_comment)) ? $replaced_comment : $comment_texts));
		}

		$definedUrls = get_option('replaceing_comment_text_to_urls');
		if(!is_array($definedUrls)){
			$definedUrls = array();
		}

		foreach($definedUrls as $url){
			$searchtxt = stripcslashes(sanitize_text_field( $url['search'] ));
			$replaceUrl = sanitize_text_field( $url['replace'] );

			$replaced_comment = preg_replace("/\b(?i)$searchtxt\b/","<a target='_blank' href='".$replaceUrl."'>$searchtxt</a>", ((!empty($replaced_comment)) ? $replaced_comment : $comment_texts));
		}
		

		return ((!empty($replaced_comment)) ? $replaced_comment : $comment_texts);
	}
	
	function get_commenter_page_url(){
		if(get_option('commenter_page')){
			return get_the_permalink( get_option('commenter_page') );
		}
		else{
			return null;
		}
	}
	
	function oca_tinymce_settings( $tinymce_init_settings ) {
		$tinymce_init_settings['forced_root_block'] = false;
		return $tinymce_init_settings;
	}
	add_filter( 'tiny_mce_before_init', 'oca_tinymce_settings' );

	// Get fullname_if_user_exist()
	function get_fullname_if_user_exist($user_email){
		global $wpdb;

		if(!$user_email){
			return;
		}

		$comment_author = $wpdb->get_results("SELECT CHAR_LENGTH(comment_author) as leng, comment_author FROM {$wpdb->prefix}comments WHERE comment_author_email = '$user_email' ORDER BY leng DESC LIMIT 1");
		
		return $comment_author[0]->comment_author;
	}
	
	function oca_get_email_notification(){
		$got_star = get_option( 'new_star_arrive' );
		if(is_array($got_star)){
			return '<span class="oca_notification">'.sizeof($got_star).'</span>';
		}
	}

	run_oca_comments();
}else{
	add_action('admin_notices', function () {
		$class = 'notice notice-error';
		$message = '<b>OCA Comments</b> Please install or activate <a target="_b" href="https://wordpress.org/themes/astra/">Astra</a> theme to work perfectly.';
		printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
	});
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_oca_comments() {

	$plugin = new OCA_Comments();
	$plugin->run();

}