<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       jr@iamjabulani.tech
 * @since      1.0.0
 *
 * @package    Cm_Toplist
 * @subpackage Cm_Toplist/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cm_Toplist
 * @subpackage Cm_Toplist/admin
 * @author     Jabulani Robbins <jr@iamjabulani.tech>
 */
class Cm_Toplist_Admin {

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
	 * @param    string    $plugin_name       The name of this plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Define the menu
	 *
	 * @return void
	 */
	public function cm_display_admin_page() {
		add_menu_page(
			'Catena Media Toplist',     // Page Title.
			'CM Toplist',               // Menu Title.
			'manage_options',           // Capabilities.
			'cm-toplist-admin',         // Menu Slug.
			array( $this, 'cm_display_page' ), // Function.
			'dashicons-admin-generic',
			'5',                        // Menu Position.
		);
	}

	/**
	 * Pull in the custom admin page template
	 *
	 * @return void
	 */
	public function cm_display_page() {
		include plugin_dir_path( __FILE__ ) . 'partials/cm-toplist-admin-display.php';
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
		 * defined in Cm_Toplist_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cm_Toplist_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cm-toplist-admin.css', array(), $this->version, 'all' );

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
		 * defined in Cm_Toplist_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cm_Toplist_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		$current_screen = get_current_screen();

		if (  $current_screen->id === 'toplevel_page_cm-toplist-admin' ) {

			wp_enqueue_script( 'VueJS', 'https://cdn.jsdelivr.net/npm/vue/dist/vue.js', array(), '2.6.11' );
			wp_enqueue_script( 'Axios', 'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js', array( 'VueJS' ), null, false );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cm-toplist-admin.js', array( 'VueJS', 'Axios' ), $this->version, true );	

			/**
			 *  Pass the nonce value and rest url
			 *  for js/cm-toplist-admin.js to use
			 *  in api requests
			 */
			wp_localize_script(  $this->plugin_name, 'wpApiSettings', array(
				'url' => esc_url_raw( rest_url() . 'cm-toplist/v1/route/' ),
				'root' => esc_url_raw( rest_url() . 'cm-toplist/v1/route/' ),
				'nonce' => wp_create_nonce( 'wp_rest' ),
				)
			);

		}
	}


}
