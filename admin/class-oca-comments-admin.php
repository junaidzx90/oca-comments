<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    OCA_Comments
 * @subpackage OCA_Comments/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    OCA_Comments
 * @subpackage OCA_Comments/admin
 * @author     junaidzx90 <admin@easeare.com>
 */
class OCA_Comments_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
	}

	/**
	 * Register the stylesheets for the admin area.
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
		wp_enqueue_style( 'wp-color-picker');
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/oca-comments-admin.css', array(), microtime(), 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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
		
		wp_enqueue_script( 'wp-color-picker');
		wp_enqueue_media(  );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/oca-comments-admin.js', array( 'jquery' ), microtime(), false );
		wp_localize_script( $this->plugin_name, 'adminajax', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'categories' => $this->categories_options_for_change_text()
		) );
	}

	function categories_options_for_change_text(){
		if(isset($_GET['page']) && $_GET['page'] === 'oca-settings' && isset($_GET['tab']) && $_GET['tab'] === 'comments-to-other'){
			$categories = get_categories( array('hide_empty' => false) );

			$output = '';
			if($categories){
				$output .= '<option value="">Select</option>';
				foreach($categories as $category){
					$output .= '<option value="'.$category->term_id.'">'.$category->name.'</option>';
				}
			}

			return $output;
		}
	}

	// Menupage
	function oca_comments_menupage(){
		add_menu_page( 'OCA Comments', ((!empty(oca_get_email_notification())) ? 'OCA Comm..'.oca_get_email_notification() : 'OCA Comments'), 'manage_options', 'oca-comments', [$this, 'oca_comments_html'], 'dashicons-format-status', 45 );

		add_submenu_page( 'oca-comments', 'Statistics', 'Statistics', 'manage_options', 'oca-comments', [$this, 'oca_comments_html'] );
		add_submenu_page( 'oca-comments', 'Settings', 'Settings', 'manage_options', 'oca-settings', [$this, 'oca_comments_filter_comments'] );
		add_submenu_page( 'oca-comments', 'Star Logs', ((!empty(oca_get_email_notification())) ? 'Star Logs'.oca_get_email_notification() : 'Star Logs'), 'manage_options', 'oca-logs', [$this, 'oca_star_logs'] );

		add_settings_section( 'general_opt_section', '', '', 'general_opt_page' );
		add_settings_section( 'oca_email_opt_section', '', '', 'oca_email_opt_page' );
		add_settings_section( 'oca_activecampaign_opt_section', '', '', 'oca_activecampaign_opt_page' );
		add_settings_section( 'oca_top10_opt_section', '', '', 'oca_top10_opt_page' );

		// Post select
		add_settings_field( 'cat_of_post_filter', 'Include plugin module by category', [$this, 'cat_of_post_filter_cb'], 'general_opt_page','general_opt_section' );
		register_setting( 'general_opt_section', 'cat_of_post_filter' );
		// Exclude Comments by category
		add_settings_field( 'cat_of_exclude', 'Exclude Comments by category', [$this, 'cat_of_exclude_cb'], 'general_opt_page','general_opt_section' );
		register_setting( 'general_opt_section', 'cat_of_exclude' );
		// Exclude Comments by category
		add_settings_field( 'star_explaing_page', 'Explaining page (Star System)', [$this, 'star_explaing_page_cb'], 'general_opt_page','general_opt_section' );
		register_setting( 'general_opt_section', 'star_explaing_page' );
		// Commenter Page
		add_settings_field( 'commenter_page', 'Commenter Page', [$this, 'commenter_page_cb'], 'general_opt_page','general_opt_section' );
		register_setting( 'general_opt_section', 'commenter_page' );
		// List of commenters
		add_settings_field( 'list_of_commenters', 'List of commenters', [$this, 'list_of_commenters_cb'], 'general_opt_page','general_opt_section' );
		register_setting( 'general_opt_section', 'list_of_commenters' );
		// Tooltip Background
		add_settings_field( 'oca_tooltipbg', 'Tooltip Background', [$this, 'oca_tooltipbg_cb'], 'general_opt_page','general_opt_section' );
		register_setting( 'general_opt_section', 'oca_tooltipbg' );
		// Tooltip Text Color
		add_settings_field( 'oca_tooltip_txt_color', 'Tooltip Text Color', [$this, 'oca_tooltip_txt_color_cb'], 'general_opt_page','general_opt_section' );
		register_setting( 'general_opt_section', 'oca_tooltip_txt_color' );
		// Minimum comments to show on statistic result
		add_settings_field( 'oca_statistic_min_comments', 'Minimum comments to show', [$this, 'oca_statistic_min_comments_cb'], 'general_opt_page','general_opt_section' );
		register_setting( 'general_opt_section', 'oca_statistic_min_comments' );
		
		// PopUp background color (Generic logo)
		add_settings_field( 'avatar_popup_bg_color', 'PopUp background color (Generic logo)', [$this, 'avatar_popup_bg_color_cb'], 'general_opt_page','general_opt_section' );
		register_setting( 'general_opt_section', 'avatar_popup_bg_color' );
		// PopUp title color (Generic logo)
		add_settings_field( 'avatar_popup_title_color', 'PopUp title color (Generic logo)', [$this, 'avatar_popup_title_color_cb'], 'general_opt_page','general_opt_section' );
		register_setting( 'general_opt_section', 'avatar_popup_title_color' );
		// PopUp body text color (Generic logo)
		add_settings_field( 'avatar_popup_text_color', 'PopUp body text color (Generic logo)', [$this, 'avatar_popup_text_color_cb'], 'general_opt_page','general_opt_section' );
		register_setting( 'general_opt_section', 'avatar_popup_text_color' );
		// Download Generic Avatars
		add_settings_field( 'download_generic_avatar_commenters', 'Download Generic Avatars', [$this, 'download_generic_avatar_commenters_cb'], 'general_opt_page','general_opt_section' );
		register_setting( 'general_opt_section', 'download_generic_avatar_commenters' );
		// Regenerate commenter fullname
		add_settings_field( 'regenerate_fullname', 'Regenerate commenter fullname', [$this, 'regenerate_fullname_cb'], 'general_opt_page','general_opt_section' );
		register_setting( 'general_opt_section', 'regenerate_fullname' );
		// Rename stupid name
		add_settings_field( 'rename_stupid_name', 'Rename stupid name', [$this, 'rename_stupid_name_cb'], 'general_opt_page','general_opt_section' );
		register_setting( 'general_opt_section', 'rename_stupid_name' );
		// Categories to include author search
		add_settings_field( 'include_search_category', 'Categories to include author search', [$this, 'include_search_category_cb'], 'general_opt_page','general_opt_section' );
		register_setting( 'general_opt_section', 'include_search_category' );
		// Restrict contents
		add_settings_field( 'restrict_shortcodes', 'Restrict contents', [$this, 'restrict_shortcodes_cb'], 'general_opt_page','general_opt_section' );
		register_setting( 'general_opt_section', 'restrict_shortcodes' );
		// Cookie #1 expiry days
		add_settings_field( 'cookie_1_expiry_days', 'Cookie #1 expiry days', [$this, 'cookie_1_expiry_days_cb'], 'general_opt_page','general_opt_section' );
		register_setting( 'general_opt_section', 'cookie_1_expiry_days' );
		// Cookie #2 expiry days
		add_settings_field( 'cookie_2_expiry_days', 'Cookie #2 expiry days', [$this, 'cookie_2_expiry_days_cb'], 'general_opt_page','general_opt_section' );
		register_setting( 'general_opt_section', 'cookie_2_expiry_days' );
		// Starting Date
		add_settings_field( 'stats_starting_date', 'Starting Date', [$this, 'stats_starting_date_cb'], 'general_opt_page','general_opt_section' );
		register_setting( 'general_opt_section', 'stats_starting_date' );
		// Comments Stats
		add_settings_field( 'comments_stats_shortcode', 'Comments Stats', [$this, 'comments_stats_shortcode_cb'], 'general_opt_page','general_opt_section' );
		register_setting( 'general_opt_section', 'comments_stats_shortcode' );
		
		// 1 star count
		add_settings_field( 'oca_one_star_length', 'One star', [$this, 'oca_one_star_length_cb'], 'oca_email_opt_page','oca_email_opt_section' );
		register_setting( 'oca_email_opt_section', 'oca_one_star_length' );
		// 2 star count
		add_settings_field( 'oca_two_star_length', 'Two stars', [$this, 'oca_two_star_length_cb'], 'oca_email_opt_page','oca_email_opt_section' );
		register_setting( 'oca_email_opt_section', 'oca_two_star_length' );
		// 3 star count
		add_settings_field( 'oca_three_star_length', 'Three stars', [$this, 'oca_three_star_length_cb'], 'oca_email_opt_page','oca_email_opt_section' );
		register_setting( 'oca_email_opt_section', 'oca_three_star_length' );
		// 4 star count
		add_settings_field( 'oca_four_star_length', 'Four stars', [$this, 'oca_four_star_length_cb'], 'oca_email_opt_page','oca_email_opt_section' );
		register_setting( 'oca_email_opt_section', 'oca_four_star_length' );
		// 5 star count
		add_settings_field( 'oca_five_star_length', 'Five stars', [$this, 'oca_five_star_length_cb'], 'oca_email_opt_page','oca_email_opt_section' );
		register_setting( 'oca_email_opt_section', 'oca_five_star_length' );


		// ActiveCampaign URL
		add_settings_field( 'activecampaign_url', 'URL', [$this, 'activecampaign_url_cb'], 'oca_activecampaign_opt_page','oca_activecampaign_opt_section' );
		register_setting( 'oca_activecampaign_opt_section', 'activecampaign_url' );
		// ActiveCampaign API
		add_settings_field( 'activecampaign_api', 'API', [$this, 'activecampaign_api_cb'], 'oca_activecampaign_opt_page','oca_activecampaign_opt_section' );
		register_setting( 'oca_activecampaign_opt_section', 'activecampaign_api' );
		// ActiveCampaign ActId
		add_settings_field( 'activecampaign_account_id', 'Account ID', [$this, 'activecampaign_account_id_cb'], 'oca_activecampaign_opt_page','oca_activecampaign_opt_section' );
		register_setting( 'oca_activecampaign_opt_section', 'activecampaign_account_id' );
		// Event Key
		add_settings_field( 'activecampaign_event_key', 'Event Key', [$this, 'activecampaign_event_key_cb'], 'oca_activecampaign_opt_page','oca_activecampaign_opt_section' );
		register_setting( 'oca_activecampaign_opt_section', 'activecampaign_event_key' );
		// ActiveCampaign Account email
		add_settings_field( 'activecampaign_account_email', 'Account email', [$this, 'activecampaign_account_email_cb'], 'oca_activecampaign_opt_page','oca_activecampaign_opt_section' );
		register_setting( 'oca_activecampaign_opt_section', 'activecampaign_account_email' );
		// Custom fields
		add_settings_field( 'activecampaign_custom_fileds', 'Custom fields', [$this, 'activecampaign_custom_fileds_cb'], 'oca_activecampaign_opt_page','oca_activecampaign_opt_section' );
		register_setting( 'oca_activecampaign_opt_section', 'activecampaign_custom_fileds' );
		// Stars
		add_settings_field( 'ac_stars_field', 'Stars', [$this, 'ac_stars_field_cb'], 'oca_activecampaign_opt_page','oca_activecampaign_opt_section' );
		register_setting( 'oca_activecampaign_opt_section', 'ac_stars_field' );
		// Number of comments
		add_settings_field( 'ac_number_of_comments', 'Number of comments', [$this, 'ac_number_of_comments_cb'], 'oca_activecampaign_opt_page','oca_activecampaign_opt_section' );
		register_setting( 'oca_activecampaign_opt_section', 'ac_number_of_comments' );
		// Number of missing comments
		add_settings_field( 'ac_number_of_missing_comments', 'Number of missing comments', [$this, 'ac_number_of_missing_comments_cb'], 'oca_activecampaign_opt_page','oca_activecampaign_opt_section' );
		register_setting( 'oca_activecampaign_opt_section', 'ac_number_of_missing_comments' );
		// One Star
		add_settings_field( 'ac_one_star', 'One star', [$this, 'ac_one_star_cb'], 'oca_activecampaign_opt_page','oca_activecampaign_opt_section' );
		register_setting( 'oca_activecampaign_opt_section', 'ac_one_star' );
		// Two Stars
		add_settings_field( 'ac_two_stars', 'Two stars', [$this, 'ac_two_stars_cb'], 'oca_activecampaign_opt_page','oca_activecampaign_opt_section' );
		register_setting( 'oca_activecampaign_opt_section', 'ac_two_stars' );
		// three Stars
		add_settings_field( 'ac_three_stars', 'Three stars', [$this, 'ac_three_stars_cb'], 'oca_activecampaign_opt_page','oca_activecampaign_opt_section' );
		register_setting( 'oca_activecampaign_opt_section', 'ac_three_stars' );
		// four Stars
		add_settings_field( 'ac_four_stars', 'Four stars', [$this, 'ac_four_stars_cb'], 'oca_activecampaign_opt_page','oca_activecampaign_opt_section' );
		register_setting( 'oca_activecampaign_opt_section', 'ac_four_stars' );
		// five Stars
		add_settings_field( 'ac_five_stars', 'Five stars', [$this, 'ac_five_stars_cb'], 'oca_activecampaign_opt_page','oca_activecampaign_opt_section' );
		register_setting( 'oca_activecampaign_opt_section', 'ac_five_stars' );
		// New
		add_settings_field( 'ac_new_field', 'New', [$this, 'ac_new_field_cb'], 'oca_activecampaign_opt_page','oca_activecampaign_opt_section' );
		register_setting( 'oca_activecampaign_opt_section', 'ac_new_field' );
		// Last Comment
		add_settings_field( 'ac_last_comment', 'Last Comment', [$this, 'ac_last_comment_cb'], 'oca_activecampaign_opt_page','oca_activecampaign_opt_section' );
		register_setting( 'oca_activecampaign_opt_section', 'ac_last_comment' );


		// Top 10 results page
		add_settings_field( 'oca_top_results', 'Top commenters Shortcode', [$this, 'oca_top_results_cb'], 'oca_top10_opt_page','oca_top10_opt_section' );
		register_setting( 'oca_top10_opt_section', 'oca_top_results' );
		// Vote button color
		add_settings_field( 'oca_vote_btn_color', 'Like button color', [$this, 'oca_vote_btn_color_cb'], 'oca_top10_opt_page','oca_top10_opt_section' );
		register_setting( 'oca_top10_opt_section', 'oca_vote_btn_color' );
		// UnVote button color
		add_settings_field( 'oca_unvote_btn_color', 'Deslike button color', [$this, 'oca_unvote_btn_color_cb'], 'oca_top10_opt_page','oca_top10_opt_section' );
		register_setting( 'oca_top10_opt_section', 'oca_unvote_btn_color' );
	}

	// Statistics Form
	function oca_comments_html(){
		$statistics = new OCA_Statistics();
		?>
		<div class="wrap" id="statistics-table">
			<h3 class="heading3">Statistics</h3>
			<hr>
			<?php $statistics->prepare_items(); ?>
			<?php $statistics->display(); ?>
		</div>
		<?php
	}

	// Settings
	function oca_comments_filter_comments(){
		require_once plugin_dir_path( __FILE__ ).'partials/oca-comments-admin-display.php';
	}

	// Star Logs
	function oca_star_logs(){
		echo '<h3>Star Logs</h3>';
		if(isset($_POST['clearlogs'])){
			delete_option('oca_star_logs');
			delete_option('new_star_arrive');
		}
		
		?>
		<div id="logs">
			<ul>
				<?php 
				$got_star = get_option( 'new_star_arrive' );
				if(!is_array($got_star)){
					$got_star = array();
				}
				if(get_option( 'oca_star_logs' )){
					$logs = get_option( 'oca_star_logs' );
					
					if(is_array($logs)){
						foreach($logs as $key => $log){
							echo '<li>'.($key+1).' | '.$log.' '.((in_array($log, $got_star)) ? '<span class="newlog">New</span>' : '').'</li>';
						}
					}
				}else{
					echo '<li>No logs are available.</li>';
				}
				?>
			</ul>
		</div>
		<form action="" method="post">
			<button name="clearlogs" class="clearlogs button-secondary">Clear All</button>
		</form>
		
		<?php
		delete_option('new_star_arrive');
	}

	// Option fields start

	function cat_of_post_filter_cb(){
		$categories = get_categories( array('hide_empty' => false) );
		if($categories){
			echo '<select name="cat_of_post_filter[]" id="cat_of_post_filter" multiple class="widefat">';
			$selected = get_option( 'cat_of_post_filter' );
			if(!is_array($selected))
				$selected = array();
			foreach($categories as $category){
				echo '<option '.(in_array($category->term_id, $selected) ? 'selected' : '').' value="'.$category->term_id.'">'.$category->name.'</option>';
			}
			echo '</select>';
		}
	}

	function cat_of_exclude_cb(){
		$categories = get_categories( array('hide_empty' => false) );
		if($categories){
			echo '<select name="cat_of_exclude[]" id="cat_of_exclude" multiple class="widefat">';
			$selected = get_option( 'cat_of_exclude' );
			if(!is_array($selected))
				$selected = array();
			foreach($categories as $category){
				echo '<option '.(in_array($category->term_id, $selected) ? 'selected' : '').' value="'.$category->term_id.'">'.$category->name.'</option>';
			}
			echo '</select>';
		}
	}

	// Explaing page
	function star_explaing_page_cb(){
		$dropdown_args = array(
			'post_type'        => 'page',
			'selected'         => get_option('star_explaing_page'),
			'name'             => 'star_explaing_page',
			'show_option_none' => 'Select',
			'echo'             => 0,
		);
		
		echo wp_dropdown_pages( $dropdown_args );
	}

	// Commenter page
	function commenter_page_cb(){
		$dropdown_args = array(
			'post_type'        => 'page',
			'selected'         => get_option('commenter_page'),
			'name'             => 'commenter_page',
			'show_option_none' => 'Select',
			'echo'             => 0,
		);
		
		echo wp_dropdown_pages( $dropdown_args );
		echo '<p>Use <code>[commenter]</code> shortcode. (Optional email parameter [commenter email=""])</p>';
	}

	function list_of_commenters_cb(){
		echo '<code>[commenters_list]</code>';
		echo '<p><strong>Parameters:</strong> stars="x", min="x", max="x"</p>';
		echo '<a href="?page=oca-settings&action=listofcommenters" class="button-secondary">Download List of Commenters</a>';
	}

	// Toolti bg
	function oca_tooltipbg_cb(){
		echo '<input type="text" data-default-color="#000000" name="oca_tooltipbg" id="oca_tooltipbg" value="'.((get_option('oca_tooltipbg')) ? get_option('oca_tooltipbg') : '#000000').'">';
	}

	// Toolti bg
	function oca_tooltip_txt_color_cb(){
		echo '<input type="text" data-default-color="#ffffff" name="oca_tooltip_txt_color" id="oca_tooltip_txt_color" value="'.((get_option('oca_tooltip_txt_color')) ? get_option('oca_tooltip_txt_color') : '#ffffff').'">';
	}

	// Statistic perpage
	function oca_statistic_min_comments_cb(){
		echo '<input type="number" name="oca_statistic_min_comments" id="oca_statistic_min_comments" value="'.get_option('oca_statistic_min_comments').'" placeholder="10">';
	}

	// Popup bg color
	function avatar_popup_bg_color_cb(){
		echo '<input type="text" data-default-color="#ffffff" name="avatar_popup_bg_color" id="avatar_popup_bg_color" value="'.((get_option('avatar_popup_bg_color')) ? get_option('avatar_popup_bg_color') : '#ffffff').'">';
	}
	// Popup title color
	function avatar_popup_title_color_cb(){
		echo '<input type="text" data-default-color="#4b4f58" name="avatar_popup_title_color" id="avatar_popup_title_color" value="'.((get_option('avatar_popup_title_color')) ? get_option('avatar_popup_title_color') : '#4b4f58').'">';
	}
	// Popup text color
	function avatar_popup_text_color_cb(){
		echo '<input type="text" data-default-color="#4b4f58" name="avatar_popup_text_color" id="avatar_popup_text_color" value="'.((get_option('avatar_popup_text_color')) ? get_option('avatar_popup_text_color') : '#4b4f58').'">';
	}
	// Popup text color
	function download_generic_avatar_commenters_cb(){
		echo '<a href="?page=oca-settings&action=listofgenericavatars" class="button-secondary">Download Generic Avatars</a>';
	}

	// regenerate_fullname
	function regenerate_fullname_cb(){
		echo '<button class="button-secondary" id="regenrate_fullname">Regenrate</button>';
	}

	// Rename stupid name
	function rename_stupid_name_cb(){
		echo '<input type="email" id="stupidnameemail" placeholder="Email">';
		echo '<input type="text" id="stupidname" placeholder="Full name">';
		echo '<button class="button-secondary" id="renamebtn">Rename</button>';
	}

	function include_search_category_cb(){
		$categories = get_categories( array('hide_empty' => false) );
		if($categories){
			echo '<select name="include_search_category[]" id="include_search_category" multiple class="widefat">';
			$selected = get_option( 'include_search_category' );
			if(!is_array($selected))
				$selected = array();
			foreach($categories as $category){
				echo '<option '.(in_array($category->term_id, $selected) ? 'selected' : '').' value="'.$category->term_id.'">'.$category->name.'</option>';
			}
			echo '</select>';
		}
	}

	function restrict_shortcodes_cb(){
		echo '1.<code>[hide-when-no-comment]...[/hide-when-no-comment]</code><br>';
		echo '2.<code>[show-when-no-comment]...[/show-when-no-comment]</code>';
		echo '<p><b>Parameter:</b> max_stars</p>';
	}

	function cookie_1_expiry_days_cb(){
		echo '<input type="number" min="1" placeholder="1 day" name="cookie_1_expiry_days" id="cookie_1_expiry_days" value="'.get_option('cookie_1_expiry_days').'">';
	}
	function cookie_2_expiry_days_cb(){
		echo '<input type="number" min="1" placeholder="1 day" name="cookie_2_expiry_days" id="cookie_2_expiry_days" value="'.get_option('cookie_2_expiry_days').'">';
	}
	function stats_starting_date_cb(){
		echo '<input type="date" name="stats_starting_date" id="stats_starting_date" value="'.get_option('stats_starting_date').'">';
	}
	function comments_stats_shortcode_cb(){
		echo '<code>[comments_stats display=""]</code>';
		echo '<p><b>Parameter values:</b> total, post, month, week, day, person</p>';
	}

	// Ajax call
	function rename_stupid_names(){
		if(isset($_POST['email']) && isset($_POST['name'])){
			$email = sanitize_email( $_POST['email'] );
			$name = sanitize_text_field( $_POST['name'] );

			global $wpdb;
			$comments = $wpdb->get_results("SELECT comment_author_email, comment_ID FROM {$wpdb->prefix}comments WHERE comment_author_email = '$email'");
			if($comments){
				foreach($comments as $comment){
					$authoreml = $comment->comment_author_email;
					$fullname = $name;
					$comment_ID = $comment->comment_ID;

					if($fullname){
						$wpdb->update($wpdb->prefix.'comments', array(
							'comment_author' => $fullname
						),array('comment_ID' => $comment_ID));
					}
				}

				echo json_encode(array('success' => 'success'));
				die;
			}

			echo json_encode(array('success' => 'Success'));
			die;
		}
		echo json_encode(array('error' => 'error'));
		die;
	}

	// One star
	function oca_one_star_length_cb(){
		echo '<input placeholder="25" type="number" name="oca_one_star_length" id="oca_one_star_length" value="'.get_option('oca_one_star_length').'"> Comments.';
	}
	// Two star
	function oca_two_star_length_cb(){
		echo '<input placeholder="75" type="number" name="oca_two_star_length" id="oca_two_star_length" value="'.get_option('oca_two_star_length').'"> Comments.';
	}
	// Three star
	function oca_three_star_length_cb(){
		echo '<input placeholder="150" type="number" name="oca_three_star_length" id="oca_three_star_length" value="'.get_option('oca_three_star_length').'"> Comments.';
	}
	// Four star
	function oca_four_star_length_cb(){
		echo '<input placeholder="250" type="number" name="oca_four_star_length" id="oca_four_star_length" value="'.get_option('oca_four_star_length').'"> Comments.';
	}
	// Five star
	function oca_five_star_length_cb(){
		echo '<input placeholder="375" type="number" name="oca_five_star_length" id="oca_five_star_length" value="'.get_option('oca_five_star_length').'"> Comments.';
	}

	// Vote btn color
	function oca_vote_btn_color_cb(){
		echo '<input type="text" data-default-color="#0170b9" name="oca_vote_btn_color" id="oca_vote_btn_color" value="'.((get_option('oca_vote_btn_color')) ? get_option('oca_vote_btn_color') : '#0170b9').'">';
	}

	// Unvote Btn color
	function oca_unvote_btn_color_cb(){
		echo '<input type="text" data-default-color="#707070" name="oca_unvote_btn_color" id="oca_unvote_btn_color" value="'.((get_option('oca_unvote_btn_color')) ? get_option('oca_unvote_btn_color') : '#707070').'">';
	}
	

	function activecampaign_url_cb(){
		echo '<input required type="url" name="activecampaign_url" class="widefat" value="'.get_option('activecampaign_url').'">';
	}
	function activecampaign_api_cb(){
		echo '<input required type="text" name="activecampaign_api" class="widefat" value="'.get_option('activecampaign_api').'">';
	}
	function activecampaign_account_id_cb(){
		echo '<input required type="text" name="activecampaign_account_id" value="'.get_option('activecampaign_account_id').'">';
	}
	function activecampaign_event_key_cb(){
		echo '<input required type="text" name="activecampaign_event_key" class="widefat" value="'.get_option('activecampaign_event_key').'">';
	}
	function activecampaign_account_email_cb(){
		echo '<input class="widefat" type="email" name="activecampaign_account_email" value="'.get_option('activecampaign_account_email').'">';
		echo '<p>If you don’t associate an email address, the event will be stored with the commenter\'s email and the event will appear on that contact when the contact is present.</p>';
	}
	function activecampaign_custom_fileds_cb(){
		echo '<hr>';
	}
	function ac_stars_field_cb(){
		global $activecampaignFields;
		$value = get_option( 'ac_stars_field' );

		echo '<select name="ac_stars_field" id="ac_stars_field">';
		echo '<option value="">Select a field</option>';
		if(is_array($activecampaignFields)){
			foreach($activecampaignFields as $field){
				echo '<option '.(($value === $field['id']) ? 'selected' : '').' value="'.$field['id'].'">'.$field['title'].'</option>';
			}
		}
		echo '</select>';
	}
	function ac_number_of_comments_cb(){
		global $activecampaignFields;
		$value = get_option( 'ac_number_of_comments' );

		echo '<select name="ac_number_of_comments" id="ac_number_of_comments">';
		echo '<option value="">Select a field</option>';
		if(is_array($activecampaignFields)){
			foreach($activecampaignFields as $field){
				echo '<option '.(($value === $field['id']) ? 'selected' : '').' value="'.$field['id'].'">'.$field['title'].'</option>';
			}
		}
		echo '</select>';
	}
	function ac_number_of_missing_comments_cb(){
		global $activecampaignFields;
		$value = get_option( 'ac_number_of_missing_comments' );

		echo '<select name="ac_number_of_missing_comments" id="ac_number_of_missing_comments">';
		echo '<option value="">Select a field</option>';
		if(is_array($activecampaignFields)){
			foreach($activecampaignFields as $field){
				echo '<option '.(($value === $field['id']) ? 'selected' : '').' value="'.$field['id'].'">'.$field['title'].'</option>';
			}
		}
		echo '</select>';
	}
	function ac_one_star_cb(){
		global $activecampaignOptions;
		$selected = get_option( 'ac_one_star' );

		echo '<select name="ac_one_star" id="ac_one_star">';
		echo '<option value="">Select a field</option>';
		if(is_array($activecampaignOptions)){
			foreach($activecampaignOptions as $value){
				echo '<option '.(($selected === $value) ? 'selected' : '').' value="'.$value.'">'.$value.'</option>';
			}
		}
		echo '</select>';
	}
	function ac_two_stars_cb(){
		global $activecampaignOptions;
		$selected = get_option( 'ac_two_stars' );

		echo '<select name="ac_two_stars" id="ac_two_stars">';
		echo '<option value="">Select a field</option>';
		if(is_array($activecampaignOptions)){
			foreach($activecampaignOptions as $value){
				echo '<option '.(($selected === $value) ? 'selected' : '').' value="'.$value.'">'.$value.'</option>';
			}
		}
		echo '</select>';
	}
	function ac_three_stars_cb(){
		global $activecampaignOptions;
		$selected = get_option( 'ac_three_stars' );
		
		echo '<select name="ac_three_stars" id="ac_three_stars">';
		echo '<option value="">Select a field</option>';
		if(is_array($activecampaignOptions)){
			foreach($activecampaignOptions as $value){
				echo '<option '.(($selected === $value) ? 'selected' : '').' value="'.$value.'">'.$value.'</option>';
			}
		}
		echo '</select>';
	}
	function ac_four_stars_cb(){
		global $activecampaignOptions;
		$selected = get_option( 'ac_four_stars' );

		echo '<select name="ac_four_stars" id="ac_four_stars">';
		echo '<option value="">Select a field</option>';
		if(is_array($activecampaignOptions)){
			foreach($activecampaignOptions as $value){
				echo '<option '.(($selected === $value) ? 'selected' : '').' value="'.$value.'">'.$value.'</option>';
			}
		}
		echo '</select>';
	}
	function ac_five_stars_cb(){
		global $activecampaignOptions;
		$selected = get_option( 'ac_five_stars' );

		echo '<select name="ac_five_stars" id="ac_five_stars">';
		echo '<option value="">Select a field</option>';
		if(is_array($activecampaignOptions)){
			foreach($activecampaignOptions as $value){
				echo '<option '.(($selected === $value) ? 'selected' : '').' value="'.$value.'">'.$value.'</option>';
			}
		}
		echo '</select>';
	}

	function ac_new_field_cb(){
		global $activecampaignFields;
		$value = get_option( 'ac_new_field' );

		echo '<select name="ac_new_field" id="ac_new_field">';
		echo '<option value="">Select a field</option>';
		if(is_array($activecampaignFields)){
			foreach($activecampaignFields as $field){
				echo '<option '.(($value === $field['id']) ? 'selected' : '').' value="'.$field['id'].'">'.$field['title'].'</option>';
			}
		}
		echo '</select>';
	}
	function ac_last_comment_cb(){
		global $activecampaignFields;
		$value = get_option( 'ac_last_comment' );

		echo '<select name="ac_last_comment" id="ac_last_comment">';
		echo '<option value="">Select a field</option>';
		if(is_array($activecampaignFields)){
			foreach($activecampaignFields as $field){
				echo '<option '.(($value === $field['id']) ? 'selected' : '').' value="'.$field['id'].'">'.$field['title'].'</option>';
			}
		}
		echo '</select>';
	}

	// Top results shortcode
	function oca_top_results_cb(){
		echo '<code>[top_commenters count="10"]</code>';
	}
	
	// Option fields end

	// Send email
	function sendActiveCampaignEvent( $comment_author_email, $star){
		$eventString = '';
		switch ($star) {
			case 1:
				$eventString = "1-star";
				break;
			case 2:
				$eventString = "2-star";
				break;
			case 3:
				$eventString = "3-star";
				break;
			case 4:
				$eventString = "4-star";
				break;
			case 5:
				$eventString = "5-star";
				break;
		}

		$actid = get_option('activecampaign_account_id');
		$account_email = get_option('activecampaign_account_email');
		$event_key = get_option('activecampaign_event_key');
		
		if($actid && $event_key){
			$ac = new ActiveCampaign(ACTIVECAMPAIGN_URL, ACTIVECAMPAIGN_API_KEY);
			$ac->track_actid = $actid;
			$ac->track_key = $event_key;

			if($account_email){
				$ac->track_email = $account_email;
			}else{
				$ac->track_email = $comment_author_email;
			}

			$post_data = array(
				"event" => $eventString,
				"eventdata" => "Email: $comment_author_email. Stars: $star"
			);
			$response = $ac->api("tracking/log",$post_data);
		}
	}

	// triggerActivecampaign
	function triggerActivecampaign( $position, $comment_author_email ){
		$eventString = '';
		switch ($position) {
			case 0:
				$eventString = 'target1';
				break;
			case 1:
				$eventString = 'target2';
				break;
			case 2:
				$eventString = 'target3';
				break;
			case 3:
				$eventString = 'target4';
				break;
			case 4:
				$eventString = 'target5';
				break;
		}

		$actid = get_option('activecampaign_account_id');
		$account_email = get_option('activecampaign_account_email');
		$event_key = get_option('activecampaign_event_key');
		
		if($actid && $event_key){
			$ac = new ActiveCampaign(ACTIVECAMPAIGN_URL, ACTIVECAMPAIGN_API_KEY);
			$ac->track_actid = $actid;
			$ac->track_key = $event_key;

			if($account_email){
				$ac->track_email = $account_email;
			}else{
				$ac->track_email = $comment_author_email;
			}

			$post_data = array(
				"event" => $eventString,
				"eventdata" => "Success"
			);
			$response = $ac->api("tracking/log", $post_data);
		}
	}

	// triggerActivecampaign
	function triggerActivecampaignCelebration( $comment_author_email ){
		$actid = get_option('activecampaign_account_id');
		$account_email = get_option('activecampaign_account_email');
		$event_key = get_option('activecampaign_event_key');
		
		if($actid && $event_key){
			$ac = new ActiveCampaign(ACTIVECAMPAIGN_URL, ACTIVECAMPAIGN_API_KEY);
			$ac->track_actid = $actid;
			$ac->track_key = $event_key;

			if($account_email){
				$ac->track_email = $account_email;
			}else{
				$ac->track_email = $comment_author_email;
			}

			$post_data = array(
				"event" => 'celebration',
				"eventdata" => "Success"
			);
			$response = $ac->api("tracking/log", $post_data);
		}
	}

	// Update contact
	function activecampaign_update_contact($commenterObj){
		$email = $commenterObj['email'];
		$star = intval($commenterObj['star']);
		$comments = $commenterObj['comments'];
		$missing = $commenterObj['missing'];

		if(empty($email)){
			return;
		}

		$new_field_id = get_option( 'ac_new_field' );
		$last_comment_id = get_option( 'ac_last_comment' );
		$star_id = get_option( 'ac_stars_field' );
		$comments_id = get_option( 'ac_number_of_comments' );
		$missing_id = get_option( 'ac_number_of_missing_comments' );

		$starValue = '';
		
		switch ($star) {
			case 1:
				$starValue = get_option( 'ac_one_star' );
				break;
			case 2:
				$starValue = get_option( 'ac_two_stars' );
				break;
			case 3:
				$starValue = get_option( 'ac_three_stars' );
				break;
			case 4:
				$starValue = get_option( 'ac_four_stars' );
				break;
			case 5:
				$starValue = get_option( 'ac_five_stars' );
				break;
		}

		$body = array(
			"contact" => [
				"fieldValues" => [
					[
						"field" => $star_id,
						"value" => "||$starValue||"
					],
					[
						"field" => $comments_id,
						"value" => $comments
					],
					[
						"field" => $missing_id,
						"value" => $missing
					],
					[
						"field" => $new_field_id,
						"value" => 'NEW'
					],
					[
						"field" => $last_comment_id,
						"value" => date("F j, Y")
					]
				]
			]
		);

		try {
			$contact_id = get_ac_contact_id_by_email($email);

			if(!$contact_id){
				return;
			}

			$url = get_option('activecampaign_url');
			$api = get_option('activecampaign_api');

			if(!empty($url) && !empty($api)){
				$response = wp_remote_post( $url."/api/3/contacts/$contact_id", array(
					'method'  => 'PUT',
					'headers' => array(
						'Api-Token' => $api
					),
					'body' => json_encode($body)
				));
			}
		} catch (\Throwable $th) {
			//throw $th;
		}
	}

	// Update current commenter fullname
	function udate_fullname_iimmediately($author_email, $comment_ID){
		try {
			global $wpdb;
			if($author_email && $comment_ID){
				$fullname = get_fullname_if_user_exist($author_email);

				if($fullname){
					$wpdb->update($wpdb->prefix.'comments', array(
						'comment_author' => $fullname
					),array('comment_ID' => $comment_ID));
				}
				
			}
		} catch (\Throwable $th) {
			//throw $th;
		}
	}

	// Conditional check only for popup
	function conditionalCloserCheck($comments, $star){
		$need = null;
		if($comments < $star && $comments+5 === $star){
			$need = $star - $comments;
		}
		if($comments < $star && $comments+10 === $star){
			$need = $star - $comments;
		}
		if($comments < $star && $comments+15 === $star){
			$need = $star - $comments;
		}
		return $need;
	}

	// Only with required condition with predefined star value
	function soCloseToStar($comments){
		$one_star_length = ((get_option('oca_one_star_length')) ? intval(get_option('oca_one_star_length')) : 25 );
		$two_star_length = ((get_option('oca_two_star_length')) ? intval(get_option('oca_two_star_length')) : 75 );
		$three_star_length = ((get_option('oca_three_star_length')) ? intval(get_option('oca_three_star_length')) : 150 );
		$four_star_length = ((get_option('oca_four_star_length')) ? intval(get_option('oca_four_star_length')) : 250 );
		$five_star_length = ((get_option('oca_five_star_length')) ? intval(get_option('oca_five_star_length')) : 375 );

		$close_to_the_star = null; // Popup contents
		if($this->conditionalCloserCheck(intval($comments), $one_star_length) !== null){
			$close_to_the_star = $this->conditionalCloserCheck(intval($comments), $one_star_length);
		}
		if($this->conditionalCloserCheck(intval($comments), $two_star_length) !== null){
			$close_to_the_star = $this->conditionalCloserCheck(intval($comments), $two_star_length);
		}
		if($this->conditionalCloserCheck(intval($comments), $three_star_length) !== null){
			$close_to_the_star = $this->conditionalCloserCheck(intval($comments), $three_star_length);
		}
		if($this->conditionalCloserCheck(intval($comments), $four_star_length) !== null){
			$close_to_the_star = $this->conditionalCloserCheck(intval($comments), $four_star_length);
		}
		if($this->conditionalCloserCheck(intval($comments), $five_star_length) !== null){
			$close_to_the_star = $this->conditionalCloserCheck(intval($comments), $five_star_length);
		}

		return $close_to_the_star;
	}

	// overall star missing comments 
	function missingCommentsToNextStar($comments){
		$one_star_length = ((get_option('oca_one_star_length')) ? intval(get_option('oca_one_star_length')) : 25 );
		$two_star_length = ((get_option('oca_two_star_length')) ? intval(get_option('oca_two_star_length')) : 75 );
		$three_star_length = ((get_option('oca_three_star_length')) ? intval(get_option('oca_three_star_length')) : 150 );
		$four_star_length = ((get_option('oca_four_star_length')) ? intval(get_option('oca_four_star_length')) : 250 );
		$five_star_length = ((get_option('oca_five_star_length')) ? intval(get_option('oca_five_star_length')) : 375 );

		$close_to_the_star = 0;
		if(intval($comments) < $one_star_length){
			$close_to_the_star = $one_star_length - intval($comments);
		}
		if($close_to_the_star === 0){
			if(intval($comments) < $two_star_length){
				$close_to_the_star = $two_star_length - intval($comments);
			}
		}
		if($close_to_the_star === 0){
			if(intval($comments) < $three_star_length){
				$close_to_the_star = $three_star_length - intval($comments);
			}
		}
		if($close_to_the_star === 0){
			if(intval($comments) < $four_star_length){
				$close_to_the_star = $four_star_length - intval($comments);
			}
		}
		if($close_to_the_star === 0){
			if(intval($comments) < $five_star_length){
				$close_to_the_star = $five_star_length - intval($comments);
			}
		}

		return $close_to_the_star;
	}

	function check_top_priorities_comments($comments){
		$priorities_popups = get_option("manual_priorities_popups");
		$popupsArr = [];
		if($priorities_popups && is_array($priorities_popups)){
			$popupsArr = array_values($priorities_popups);
		}

		if(sizeof($popupsArr)){
			foreach($popupsArr as $key => $rr){
				if(intval($rr['comments']) === $comments){
					return [
						'position' => $key,
						'data' => $rr
					];
				}
			}
		}
	}

	// Comment action
	function comment_post_action( $comment_ID, $comment_approved ) {
		global $wpdb;
		$row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}comments WHERE comment_ID = $comment_ID");

		$current_post = $row->comment_post_ID; // Post id

		$comment_author_email = $row->comment_author_email;
		$comments = oca_get_author_comments_count($comment_author_email);

		$this->udate_fullname_iimmediately($comment_author_email, $comment_ID); // Update fullname

		$one_star_length = ((get_option('oca_one_star_length')) ? intval(get_option('oca_one_star_length')) : 25 );
		$two_star_length = ((get_option('oca_two_star_length')) ? intval(get_option('oca_two_star_length')) : 75 );
		$three_star_length = ((get_option('oca_three_star_length')) ? intval(get_option('oca_three_star_length')) : 150 );
		$four_star_length = ((get_option('oca_four_star_length')) ? intval(get_option('oca_four_star_length')) : 250 );
		$five_star_length = ((get_option('oca_five_star_length')) ? intval(get_option('oca_five_star_length')) : 375 );

		// Popup contents
		$hasInTop10 = checkin_top10($comment_author_email);

		$close_to_the_star = $this->soCloseToStar($comments);

		$categories = get_the_category( $current_post );
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
			
			$gotStar = ''; // Popup contents

			// Activecampaign Event tracking
			switch ($comments) {
				case $one_star_length:
					$gotStar = 1;
					$this->sendActiveCampaignEvent($comment_author_email, 1);
					break;
				case $two_star_length:
					$gotStar = 2;
					$this->sendActiveCampaignEvent($comment_author_email, 2);
					break;
				case $three_star_length:
					$gotStar = 3;
					$this->sendActiveCampaignEvent($comment_author_email, 3);
					break;
				case $four_star_length:
					$gotStar = 4;
					$this->sendActiveCampaignEvent($comment_author_email, 4);
					break;
				case $five_star_length:
					$gotStar = 5;
					$this->sendActiveCampaignEvent($comment_author_email, 5);
					break;
				default:
					# code...
					break;
			}

			$comment_author_name = $wpdb->get_var("SELECT comment_author FROM {$wpdb->prefix}comments WHERE `comment_author_email` = '$comment_author_email'");

			$array_commenterObj = array(
				'id'	=> $comment_ID,
				'comments' => $comments,
				'missing' => $this->missingCommentsToNextStar($comments),
				'star' => oca_get_stars($comment_author_email, false),
				'name' => $comment_author_name,
				'email' => $comment_author_email
			);
			
			// update activecampaign contact
			$this->activecampaign_update_contact($array_commenterObj);

			if(isset($_SESSION['got_new_star']))
				unset($_SESSION['got_new_star']);
			if(isset($_SESSION['close_to_the_star']))
				unset($_SESSION['close_to_the_star']);
			if(isset($_SESSION['isinvalidlogo']))
				unset($_SESSION['isinvalidlogo']);
			if(isset($_SESSION['has_in_top_10']))
				unset($_SESSION['has_in_top_10']);
			if(isset($_SESSION['otherCases']))
				unset($_SESSION['otherCases']);

			$_SESSION['oca_comment'] = json_encode($array_commenterObj);

			// ----
			$cookie1Expiry = ((get_option("cookie_1_expiry_days")) ? get_option("cookie_1_expiry_days") : 1);
			$cookie2Expiry = ((get_option("cookie_2_expiry_days")) ? get_option("cookie_2_expiry_days") : 1);
			setcookie("oca_cookie_1", "yes", strtotime("+$cookie1Expiry days"));
			setcookie("oca_cookie_2", $array_commenterObj['star'], strtotime("+$cookie2Expiry days"));
			// ----

			$isPopupOpen = true;

			// Get top priorities popup
			$topPopup = $this->check_top_priorities_comments($comments);

			$celebratsData = get_option("nextcelebration_popup");
			$nextcelebrationArr = [];
			if($celebratsData && is_array($celebratsData)){
				$nextcelebrationArr = $celebratsData;
			}
			$calebration = 0;
			if(array_key_exists("target", $nextcelebrationArr)){
				$calebration = intval($nextcelebrationArr['target']);
			}

			$total_comments = $wpdb->query("SELECT * FROM {$wpdb->prefix}comments WHERE `comment_approved` = 1");
			if($total_comments === $calebration){
				$isPopupOpen = false;
				$_SESSION['nextcelebration_popup'] = json_encode($nextcelebrationArr);
				// Trigger event 
				$this->triggerActivecampaignCelebration($comment_author_email);
			}elseif($topPopup){
				$isPopupOpen = false;
				$_SESSION['top_priorities_popup'] = json_encode($topPopup['data']);
				// trigger events 
				$this->triggerActivecampaign($topPopup['position'], $comment_author_email);
			}elseif(!empty($gotStar) && $isPopupOpen){
				$isPopupOpen = false;
				$_SESSION['got_new_star'] = "true";

				// Store logs
				$logs = get_option('oca_star_logs');
				if(!is_array($logs)){
					$logs = array();
				}

				$logs[] = date("F j, Y, g:i a").', ('.$comment_author_email.') got a new star- '.$gotStar;
				update_option('oca_star_logs', $logs);
				
				$star_arrive = get_option('new_star_arrive');
				if(!is_array($star_arrive)){
					$star_arrive = array();
				}

				$star_arrive[] = date("F j, Y, g:i a").' --- ('.$comment_author_email.') --- '.$gotStar.' Star.';
				update_option( 'new_star_arrive', $star_arrive );
			}elseif($close_to_the_star !== null && $isPopupOpen){
				$isPopupOpen = false;
				$_SESSION['close_to_the_star'] = "true";
			}elseif( $isPopupOpen && intval($comments) === 3 || intval($comments) === 4 || intval($comments) === 9 || intval($comments) === 14 || intval($comments) === 19 || intval($comments) === 24 ){
				// If not is generic logo
				if(!oca_validate_gravatar($comment_author_email)){
					$_SESSION['isinvalidlogo'] = "true";
					$isPopupOpen = false;
				}
			}elseif($hasInTop10 && $isPopupOpen){
				$isPopupOpen = false;
				$_SESSION['has_in_top_10'] = "true";
			}else{
				$_SESSION['otherCases'] = "true";
			}
		}
	}

	// Filter default comments
	function comment_text_filter($comment_text, $comment = null){
		$replaced_comment = oca_comment_replacing($comment_text);
		return $replaced_comment;
	}

	// Admin area comments highlights
	function highlight_admin_comments($classes, $class, $comment_id, $comment){
		global $wpdb;
		if($wpdb->get_var("SELECT comment_ID FROM {$wpdb->prefix}comments WHERE comment_approved = 1 AND comment_ID = $comment_id")){
			$highlight_emails = get_option( 'highlight_emails' );
			if(!is_array($highlight_emails)){
				$highlight_emails = array();
			}

			$highlight = '';
			if($comment->comment_author_email){
				if(in_array($comment->comment_author_email, $highlight_emails)){
					$highlight = 'highlighted';
				}
			}
	
			if( $highlight ) {
				$classes[] = $highlight;
			}
		}

		return $classes;
	}

	function highlight_admin_comments_style(){
		$highlight_color = ((get_option( 'highlight_color' )) ? get_option( 'highlight_color' ) : 'transparent');
		?>
		<style>
			tr.highlighted {
				background-color: <?php echo $highlight_color ?> !important;
			}
		</style>
		<?php
	}

	// Save oca settings
	function save_oca_settings(){
		// Texts for replacing
		if(isset($_POST['replacing_text'])){
			$finalData = array();
			if(array_key_exists('replace_texts',$_POST) && is_array($_POST['replace_texts']) && sizeof($_POST['replace_texts']) > 0){
				$data = $_POST['replace_texts'];
				foreach($data as $row){
					if(!empty($row['search']) && !empty($row['replace'])){
						$rowData = array(
							'search' => $row['search'],
							'replace' => $row['replace'],
						);
						$finalData[] = $rowData;
					}
				}
			}
			update_option( 'replaceing_comment_texts', $finalData );
		}

		// Urls for replacing
		if(isset($_POST['replacing_urls'])){
			$finalUrls = array();
			if(array_key_exists('replace_urls',$_POST) && is_array($_POST['replace_urls']) && sizeof($_POST['replace_urls']) > 0){
				$Urls = $_POST['replace_urls'];
				foreach($Urls as $row){
					if(!empty($row['search']) && !empty($row['replace'])){
						$rowUrls = array(
							'search' => $row['search'],
							'replace' => $row['replace'],
						);
						$finalUrls[] = $rowUrls;
					}
				}
			}
			update_option( 'replaceing_comment_text_to_urls', $finalUrls );
		}

		// Highlight emails
		if(isset($_POST['update_highlights_emls'])){
			if(isset($_POST['highlight_color'])){
				update_option( 'highlight_color', $_POST['highlight_color'] );
			}

			$arrayVal = array();
			if(isset($_POST['highlight_emails'])){
				$emails = $_POST['highlight_emails'];
				if(is_array($emails)){
					foreach($emails as $email){
						if(!empty($email)){
							$arrayVal[] = $email;
						}
					}
				}
			}
			update_option( 'highlight_emails', $arrayVal );
		}

		// Commenter ranks
		if(isset($_POST['commenter_ranks'])){
			$exclude_rank_emails = array();
			if(isset($_POST['exclude_rank_emails'])){
				$rank_emails = $_POST['exclude_rank_emails'];
				if(is_array($rank_emails)){
					foreach($rank_emails as $email){
						if(!empty($email)){
							$exclude_rank_emails[] = $email;
						}
					}
				}
			}
			update_option( 'exclude_rank_emails', $exclude_rank_emails );
		}

		// Custom profile URLS
		if(isset($_POST['custom_profiles'])){
			$profilesData = array();
			if(isset($_POST['profiles'])){
				$profiles = $_POST['profiles'];
				if(is_array($profiles)){
					foreach($profiles as $email){
						if(!empty($email)){
							$profilesData[] = $email;
						}
					}
				}
			}

			update_option( 'custom_profiles', $profilesData );
		}

		// Custom profile URLS
		if(isset($_POST['comments-to-other'])){
			$comments_texts = array();
			$comments_texts = $_POST['comments_texts'];
			update_option( 'oca_comments_texts', $comments_texts );
		}

		if(isset($_POST['messages_informations'])){
			// Animated images
			if(isset($_POST['anim_images'])){
				$images = $_POST['anim_images'];
				update_option( 'oca_animated_images', $images );
			}

			// generic_avatar
			$msg0 = $_POST['generic_avatar'];
			update_option( '_generic_avatar_popup', $msg0 );
			
			// when_got_oca_star
			$msg1 = $_POST['when_got_oca_star'];
			update_option( 'when_got_oca_star_popup', $msg1 );

			// when_in_top_10_commenter
			$msg2 = $_POST['when_in_top_10_commenter'];
			update_option( 'when_in_top_10_commenter_popup', $msg2 );
				
			// when_so_close_oca_star
			$msg3 = $_POST['when_so_close_oca_star'];
			update_option( 'when_so_close_oca_star_popup', $msg3 );
				
			// other_fallback_msg 1
			$otherMsg1 = $_POST['other_fallback_1'];
			update_option( 'other_fallback_1', $otherMsg1 );
			
			$otherMsg2 = $_POST['other_fallback_2'];
			update_option( 'other_fallback_2', $otherMsg2 );

			$otherMsg3 = $_POST['other_fallback_3'];
			update_option( 'other_fallback_3', $otherMsg3 );

			$otherMsg4 = $_POST['other_fallback_4'];
			update_option( 'other_fallback_4', $otherMsg4 );

			$otherMsg5 = $_POST['other_fallback_5'];
			update_option( 'other_fallback_5', $otherMsg5 );

			// Manual priorities
			$priorities_popups = $_POST['manual_priorities'];
			update_option( 'manual_priorities_popups', $priorities_popups );

			// Manual priorities
			$nextcelebration = $_POST['nextcelebration'];
			update_option( 'nextcelebration_popup', $nextcelebration );
			
		}
		
	}

	// Regenrate commenter fullname by ajax
	function generate_fullname_of_commenter(){
		global $wpdb;
		$comments = $wpdb->get_results("SELECT comment_author_email, comment_ID FROM {$wpdb->prefix}comments");
		if($comments){
			foreach($comments as $comment){
				$authoreml = $comment->comment_author_email;
				$fullname = get_fullname_if_user_exist($authoreml);
				$comment_ID = $comment->comment_ID;

				if($fullname){
					$wpdb->update($wpdb->prefix.'comments', array(
						'comment_author' => $fullname
					),array('comment_ID' => $comment_ID));
				}
			}
			echo json_encode(array('success' => 'Success'));
			die;
		}
		echo json_encode(array('success' => 'Success'));
		die;
	}
	
	// Get all the commenters list
	function get_commenters_list($post_id = ''){
		global $wpdb;
		$data = array();

		$commenters = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}comments WHERE `comment_approved` = 1 GROUP BY comment_author_email");
		if(!empty($post_id)){
			$commenters = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}comments WHERE comment_post_ID = $post_id AND `comment_approved` = 1 GROUP BY comment_author_email");
		}

        if($commenters){
            foreach($commenters as $commenter){
				$comments = oca_get_author_comments_count($commenter->comment_author_email);
                $arr = array(
                    'ID' => $commenter->comment_ID,
                    'email' => $commenter->comment_author_email,
                    'name' => $commenter->comment_author,
                    'stars' => oca_get_stars( $commenter->comment_author_email, false ),
                    'comments' => $comments,
					'next_star' => $this->missingCommentsToNextStar($comments)
                );

				if($commenter->comment_author_email){
					$data[] = $arr;
				}
            }
        }

		$mostComments = array();
		foreach ($data as $key => $row)
		{
			$mostComments[$key] = $row['comments'];
		}
		array_multisort($mostComments, SORT_DESC, $data);

		return $data;
	}

	// Export list of coomenters
	function export_list_of_commenters(){
		if(isset($_GET['page']) && $_GET['page'] === "oca-settings" && isset($_GET['action']) && $_GET['action'] === "listofcommenters"){
			$commenters = $this->get_commenters_list();

			if(is_array($commenters) && sizeof($commenters) > 0){ 
				$delimiter = ","; 
				$filename = "Commenters-" . date('Y-m-d') . ".csv"; 
				 
				// Create a file pointer 
				$f = fopen('php://memory', 'w'); 
				 
				// Set column headers 
				$fields = array('Name', 'Email', 'Comments', 'Stars', 'Next star'); 
				fputcsv($f, $fields, $delimiter); 
				 
				// Output each row of the data, format line as csv and write to file pointer 
				foreach($commenters as $commenter){
					$lineData = array(
						$commenter['name'], 
						$commenter['email'], 
						$commenter['comments'], 
						$commenter['stars'],
						$commenter['next_star']
					); 
					fputcsv($f, $lineData, $delimiter);
				} 
				 
				// Move back to beginning of file 
				fseek($f, 0); 
				 
				// Set headers to download file rather than displayed 
				header('Content-Type: text/csv'); 
				header('Content-Disposition: attachment; filename="' . $filename . '";'); 
				 
				//output all remaining data on a file pointer 
				fpassthru($f); 
				exit;
			} 
		}
	}

	// Export list of coomenters
	function export_list_of_generic_avatars_commenters(){
		if(isset($_GET['page']) && $_GET['page'] === "oca-settings" && isset($_GET['action']) && $_GET['action'] === "listofgenericavatars"){
			$commenters = $this->get_commenters_list();

			if(is_array($commenters) && sizeof($commenters) > 0){ 
				$delimiter = ","; 
				$filename = "Commenters-with-generic-avatar-" . date('Y-m-d') . ".csv"; 
				
				// Create a file pointer 
				$f = fopen('php://memory', 'w'); 
				
				// Set column headers 
				$fields = array('Name', 'Email', 'Comments'); 
				fputcsv($f, $fields, $delimiter);
				
				// Output each row of the data, format line as csv and write to file pointer 
				foreach($commenters as $commenter){
					if(!oca_validate_gravatar($commenter['email'])){ // Check valid avatars
						$lineData = array(
							$commenter['name'], 
							$commenter['email'], 
							$commenter['comments']
						); 
						fputcsv($f, $lineData, $delimiter);
					}
				} 
				
				// Move back to beginning of file 
				fseek($f, 0); 
				
				// Set headers to download file rather than displayed 
				header('Content-Type: text/csv'); 
				header('Content-Disposition: attachment; filename="' . $filename . '";'); 
				
				//output all remaining data on a file pointer 
				fpassthru($f); 
				exit;
			} 
		}
	}

	// Export post commenter column
	function export_post_commenters_columns($columns){
		$columns['export_commenters'] = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#3c434a" version="1.1" id="Capa_1" x="0px" y="0px" width="25px" height="25px" viewBox="0 0 428.428 428.428" xml:space="preserve"> <g> <g> <g> <path d="M145.978,96.146h163.125c-1.146-15.041-13.742-26.931-29.073-26.931H29.169C13.085,69.215,0,82.301,0,98.384v129.44     c0,16.084,13.085,29.17,29.169,29.17h22.029v51.382c0,3.552,2.072,6.778,5.302,8.255c1.208,0.553,2.497,0.823,3.775,0.823     c2.14,0,4.255-0.755,5.938-2.21l39.048-33.74c-1.338-4.141-2.069-8.551-2.069-13.131v-129.44     C103.191,115.341,122.385,96.146,145.978,96.146z"/> <path d="M399.259,110.975h-250.86c-16.084,0-29.17,13.085-29.17,29.169v129.441c0,16.084,13.086,29.169,29.17,29.169h146.403     l67.414,58.25c1.683,1.453,3.798,2.209,5.938,2.209c1.276,0,2.564-0.271,3.773-0.823c3.23-1.478,5.303-4.702,5.303-8.255v-51.38     h22.028c16.084,0,29.169-13.085,29.169-29.169V140.145C428.428,124.061,415.343,110.975,399.259,110.975z M201.202,226.324     c-12.785,0-23.15-10.365-23.15-23.15s10.365-23.149,23.15-23.149c12.785,0,23.149,10.365,23.149,23.149     C224.352,215.96,213.987,226.324,201.202,226.324z M273.829,226.324c-12.785,0-23.149-10.365-23.149-23.15     s10.365-23.149,23.149-23.149c12.785,0,23.148,10.365,23.148,23.149C296.979,215.96,286.614,226.324,273.829,226.324z      M346.456,226.324c-12.785,0-23.15-10.365-23.15-23.15s10.365-23.149,23.15-23.149s23.147,10.365,23.147,23.149     C369.604,215.96,359.24,226.324,346.456,226.324z"/> </g> </g> </g></svg>';
		return $columns;
	}

	function export_post_commenter_custom_column($column, $post_id){
		switch ($column) {
			case 'export_commenters':
				global $wpdb;
				$cfound = $wpdb->get_var("SELECT comment_ID FROM {$wpdb->prefix}comments WHERE comment_post_ID = $post_id");

				if($cfound){
					echo '<a href="?act=oca-export-post-commenters&id='.$post_id.'"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" fill="#3c434a" id="Layer_1" x="0px" y="0px" width="25px" height="25px" viewBox="0 0 100 100" xml:space="preserve"> <g> <path d="M94.284,65.553L75.825,52.411c-0.389-0.276-0.887-0.312-1.312-0.093c-0.424,0.218-0.684,0.694-0.685,1.173l0.009,6.221   H57.231c-0.706,0-1.391,0.497-1.391,1.204v11.442c0,0.707,0.685,1.194,1.391,1.194h16.774v6.27c0,0.478,0.184,0.917,0.609,1.136   s0.853,0.182,1.242-0.097l18.432-13.228c0.335-0.239,0.477-0.626,0.477-1.038c0-0.002,0-0.002,0-0.002   C94.765,66.179,94.621,65.793,94.284,65.553z"/> <path d="M64.06,78.553h-6.49h0c-0.956,0-1.73,0.774-1.73,1.73h-0.007v3.01H15.191V36.16h17.723c0.956,0,1.73-0.774,1.73-1.73   V16.707h21.188l0,36.356h0.011c0.021,0.937,0.784,1.691,1.726,1.691h6.49c0.943,0,1.705-0.754,1.726-1.691h0.004v-0.038   c0,0,0-0.001,0-0.001c0-0.001,0-0.001,0-0.002l0-40.522h-0.005V8.48c0-0.956-0.774-1.73-1.73-1.73h-2.45v0H32.914v0h-1.73   L5.235,32.7v2.447v1.013v52.912v2.447c0,0.956,0.774,1.73,1.73,1.73h1.582h53.925h1.582c0.956,0,1.73-0.774,1.73-1.73v-2.448h0.005   l0-8.789l0-0.001C65.79,79.328,65.015,78.553,64.06,78.553z"/> <path d="M26.18,64.173c0.831,0,1.55,0.623,1.786,1.342l2.408-1.121c-0.553-1.273-1.771-2.685-4.193-2.685   c-2.893,0-5.079,1.924-5.079,4.775c0,2.837,2.187,4.774,5.079,4.774c2.422,0,3.654-1.467,4.193-2.699l-2.408-1.107   c-0.235,0.719-0.955,1.342-1.786,1.342c-1.342,0-2.242-1.024-2.242-2.311S24.837,64.173,26.18,64.173z"/> <path d="M35.656,68.907c-1.246,0-2.284-0.526-2.976-1.19l-1.453,2.076c0.982,0.886,2.325,1.467,4.291,1.467   c2.477,0,3.986-1.176,3.986-3.211c0-3.432-5.135-2.685-5.135-3.557c0-0.235,0.152-0.415,0.706-0.415   c0.872,0,1.91,0.304,2.712,0.913l1.495-1.979c-1.052-0.858-2.408-1.287-3.917-1.287c-2.533,0-3.833,1.495-3.833,3.059   c0,3.64,5.148,2.74,5.148,3.626C36.68,68.768,36.182,68.907,35.656,68.907z"/> <polygon points="43.271,61.862 40.102,61.862 43.506,71.093 47.022,71.093 50.426,61.862 47.257,61.862 45.264,68.076  "/> </g> </svg></a>';
				}
				
				break;
			
			default:
				# code...
				break;
		}
	}

	// Export post list of coomenters
	function export_post_of_commenters(){
		if(isset($_GET['act']) && $_GET['act'] === "oca-export-post-commenters" && isset($_GET['id']) && !empty($_GET['id'])){
			$post_id = intval($_GET['id']);

			$commenters = $this->get_commenters_list($post_id);

			if(is_array($commenters) && sizeof($commenters) > 0){ 
				$delimiter = ","; 
				$filename = "Commenters-" . date('Y-m-d') . ".csv"; 
				 
				// Create a file pointer 
				$f = fopen('php://memory', 'w'); 
				 
				// Set column headers 
				$fields = array('Name', 'Email', 'Comments', 'Stars', 'Next star'); 
				fputcsv($f, $fields, $delimiter); 
				 
				// Output each row of the data, format line as csv and write to file pointer 
				foreach($commenters as $commenter){

					$close_to_the_star = $this->missingCommentsToNextStar($commenter['comments']);

					$lineData = array(
						$commenter['name'], 
						$commenter['email'], 
						$commenter['comments'], 
						$commenter['stars'],
						$close_to_the_star
					); 
					fputcsv($f, $lineData, $delimiter);
				} 
				 
				// Move back to beginning of file 
				fseek($f, 0); 
				 
				// Set headers to download file rather than displayed 
				header('Content-Type: text/csv'); 
				header('Content-Disposition: attachment; filename="' . $filename . '";'); 
				 
				//output all remaining data on a file pointer 
				fpassthru($f); 
				exit;
			}
			return;
		}
	}
}
