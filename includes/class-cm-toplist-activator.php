<?php

/**
 * Fired during plugin activation
 *
 * @link       jr@iamjabulani.tech
 * @since      1.0.0
 *
 * @package    Cm_Toplist
 * @subpackage Cm_Toplist/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Cm_Toplist
 * @subpackage Cm_Toplist/includes
 * @author     Jabulani Robbins <jr@iamjabulani.tech>
 */
class Cm_Toplist_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
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

		update_site_option( 'toplist_db_version', 1 );
	}
}
