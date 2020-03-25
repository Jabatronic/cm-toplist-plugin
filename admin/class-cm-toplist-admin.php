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
	 * Create the tables to hold our data
	 *
	 * @return $success
	 */
	public function cm_initialise_custom_tables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$table_name = $wpdb->prefix . 'toplist_brands';

		$version = (int) get_site_option( 'toplist_db_version' );

		/**
		 * TODO: Add version checking to avoid adding duplicate data to database
		 */

		$sql = "CREATE TABLE $table_name (
			id int(11) NOT NULL AUTO_INCREMENT, 
			name varchar(255) NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		$table_2_name = $wpdb->prefix . 'toplist_brand_ratings';

		$sql2 = "CREATE TABLE $table_2_name (
			id int(11) NOT NULL AUTO_INCREMENT,
			brand_id int(11) NOT NULL, 
			rating int(11) NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $sql );
		dbDelta( $sql2 );

		$success = empty( $wpdb->last_error );

		update_site_option( 'toplist_db_version', 1 );

		return $success;
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cm-toplist-admin.js', array( 'jquery' ), $this->version, false );

	}

}
