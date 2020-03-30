<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       jr@iamjabulani.tech
 * @since      1.0.0
 *
 * @package    Cm_Toplist
 * @subpackage Cm_Toplist/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Cm_Toplist
 * @subpackage Cm_Toplist/public
 * @author     Jabulani Robbins <jr@iamjabulani.tech>
 */
class Cm_Toplist_Public {

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
		$this->version     = $version;

	}

	/**
	 * Show toplist data
	 *
	 * Used by Shortcode [cm-toplist]
	 *
	 * @param  Array $atts
	 * @return void
	 */
	public function cm_show_toplist( $atts ) {
		global $wpdb;
		$querystr = "
			SELECT wp_toplist_brands.name,
			wp_toplist_brand_ratings.rating
			FROM wp_toplist_brands
			JOIN wp_toplist_brand_ratings ON wp_toplist_brands.id = wp_toplist_brand_ratings.brand_id
			ORDER BY wp_toplist_brand_ratings.rating DESC
		";

		$data = $wpdb->get_results( $querystr );
		// var_dump($data);

		/**
		 * Only show data on specific pages
		 * (</freespins>, </casinobonuses>)
		 */
		$allowed_pages = array( 'freespins', 'casinobonuses' );

		echo '<div class="shortcode-wrap">';
		if ( is_page( $allowed_pages ) ) {
			if ( $data !== [] ) {
				echo '<table id="cm_toplist_table" class="cm_toplist_table">
				<thead class="cm_toplist_thead has-yellow-bg">
					<tr class="cm_toplist_thead__row">
						<th class="cm_toplist_thead__cell">Casino</th>
						<th class="cm_toplist_thead__cell">Rating</th>
					</tr>
				</thead>
				<tbody>';

				foreach ( $data as $item ) {
					echo '<tr class="cm_toplist_tbody__row"><td class="cm_toplist_tbody__cell">' . esc_html( $item->name ) . '</td><td>' . esc_html( $item->rating ) . '</td></tr>';
				}
				echo '</tbody>';
				echo '</table>';
			} else {
				echo "<p>There isn't any data to display yet!</p>
				<p>If you're a site administrator you can add some data in the CM Toplist plugin settings page.";
			}
		} else {
			echo 'This shortcode can only display data on the following pages: <pre>' . site_url( $allowed_pages[0]) . '</pre> or <pre>' . site_url( $allowed_pages[1]) . '</pre>';
		}
		echo '</div>';

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
		 * defined in Cm_Toplist_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cm_Toplist_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cm-toplist-public.css', array(), $this->version, 'all' );

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
		 * defined in Cm_Toplist_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cm_Toplist_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cm-toplist-public.js', array( 'jquery' ), $this->version, false );

	}

}
