<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    OCA_Comments
 * @subpackage OCA_Comments/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    OCA_Comments
 * @subpackage OCA_Comments/includes
 * @author     junaidzx90 <admin@easeare.com>
 */
class OCA_Comments {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      OCA_Comments_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'OCA_COMMENTS_VERSION' ) ) {
			$this->version = OCA_COMMENTS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'oca-comments';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - OCA_Comments_Loader. Orchestrates the hooks of the plugin.
	 * - OCA_Comments_i18n. Defines internationalization functionality.
	 * - OCA_Comments_Admin. Defines all hooks for the admin area.
	 * - OCA_Comments_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-oca-comments-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-oca-comments-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-oca-comments-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-oca-comments-public.php';

		$this->loader = new OCA_Comments_Loader();

		if( ! class_exists( 'WP_List_Table' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
		}

		// statistics
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-oca-comments-statistics.php';
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the OCA_Comments_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new OCA_Comments_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new OCA_Comments_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'oca_comments_menupage' );

		$this->loader->add_action( 'comment_post', $plugin_admin, 'comment_post_action', 10, 2 );
		$this->loader->add_action( 'comment_text', $plugin_admin, 'comment_text_filter', 10, 2 );
		$this->loader->add_action( 'comment_class', $plugin_admin, 'highlight_admin_comments', 10, 4 );
		$this->loader->add_action( 'admin_head', $plugin_admin, 'highlight_admin_comments_style', 10, 4 );

		$this->loader->add_action( 'init', $plugin_admin, 'save_oca_settings' );

		$this->loader->add_action( "wp_ajax_generate_fullname_of_commenter", $plugin_admin, "generate_fullname_of_commenter" );
		$this->loader->add_action( "wp_ajax_nopriv_generate_fullname_of_commenter", $plugin_admin, "generate_fullname_of_commenter" );

		$this->loader->add_action( "wp_ajax_rename_stupid_names", $plugin_admin, "rename_stupid_names" );
		$this->loader->add_action( "wp_ajax_nopriv_rename_stupid_names", $plugin_admin, "rename_stupid_names" );
		// Export list of commenters
		$this->loader->add_action( "init", $plugin_admin, "export_list_of_commenters" );
		$this->loader->add_action( "init", $plugin_admin, "export_list_of_generic_avatars_commenters" );
		$this->loader->add_action( "init", $plugin_admin, "export_post_of_commenters" );

		$this->loader->add_action( 'manage_post_posts_columns' , $plugin_admin, 'export_post_commenters_columns', 10, 1 );
		$this->loader->add_action( 'manage_post_posts_custom_column' , $plugin_admin, 'export_post_commenter_custom_column', 10, 2 );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new OCA_Comments_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_filter( "comments_template", $plugin_public, "oca_comments_template", 99 );

		// Ajax call for search
		$this->loader->add_action( "wp_ajax_search_comments_author", $plugin_public, "search_comments_author" );
		$this->loader->add_action( "wp_ajax_nopriv_search_comments_author", $plugin_public, "search_comments_author" );
		$this->loader->add_action( "wp_head", $plugin_public, "oca_header_style" );
		$this->loader->add_action( "wp_footer", $plugin_public, "footerScripts" );

		$this->loader->add_action( "wp_ajax_hit_vote_button", $plugin_public, "hit_vote_button" );
		$this->loader->add_action( "wp_ajax_nopriv_hit_vote_button", $plugin_public, "hit_vote_button" );

		$this->loader->add_action( "wp_ajax_close_popup_via_ajax", $plugin_public, "close_popup_via_ajax" );
		$this->loader->add_action( "wp_ajax_nopriv_close_popup_via_ajax", $plugin_public, "close_popup_via_ajax" );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    OCA_Comments_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
