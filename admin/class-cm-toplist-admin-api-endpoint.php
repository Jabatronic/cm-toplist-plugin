<?php
/**
 * TODO: Extract database operations to dedicated class
 *
 * @link       jr@iamjabulani.tech
 * @since      1.0.0
 *
 * @package    Cm_Toplist
 * @subpackage Cm_Toplist/includes
 *
 * https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/#examples
 */

/**
 * Controller class for the rest endpoints
 */
class CM_Toplist_Admin_API_Endpoint extends WP_REST_Controller {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The options name prefix for API Boilerplate
	 *
	 * @since   0.1
	 * @access  private
	 * @var         string      $option_name    Option name prefix for API Boilerplate
	 */
	private $option_name;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.1.0
	 * @param    string $plugin_name          The name of this plugin.
	 * @param    string $version              The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Admin nag message if WP API not enabled.
	 *
	 * @since    0.1.0
	 */
	public function cm_toplist_api_nag_message() {

		global $wp_version;

		// WP v4.7 was the first WP version with the API fully baked in.
		if ( $wp_version >= 4.7 ) {

			return;

		} elseif ( is_plugin_active( 'WP-API-develop/plugin.php' ) || is_plugin_active( 'rest-api/plugin.php' ) || is_plugin_active( 'WP-API/plugin.php' ) ) {

				return;

		} else { ?>

			<div class="update-nag notice">

				<p>
					<?php __( 'To use <strong>Catena Media Toplist API</strong>, you need to update to the latest version of WordPress (version 4.7 or above). To use an older version of WordPress, you can install the <a href="https://wordpress.org/plugins/rest-api/">WP API Plugin</a> plugin. However, we&apos;d strongly advise youto update WordPress.', 'cm-toplist' ); ?>
				</p>

			</div>

			<?php
		}

	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function cm_toplist_api_route_constructor() {
		$version   = '1';
		$namespace = $this->plugin_name . '/v' . $version;
		$base      = 'route';
		register_rest_route(
			$namespace,
			'/' . $base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => array(),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( true ),
				),
			)
		);

		register_rest_route(
			$namespace,
			'/' . $base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'context' => array(
							'default' => 'view',
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( false ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
					'args'                => array(
						'force' => array(
							'default' => false,
						),
					),
				),
			)
		);
		register_rest_route(
			$namespace,
			'/' . $base . '/schema',
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Get a collection of items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		global $wpdb;

		$brands_table_name   = $wpdb->prefix . 'toplist_brands';
		$brand_ratings_table = $wpdb->prefix . 'toplist_brand_ratings';

		$sql = "
		SELECT 
		{$brands_table_name}.id,
		{$brands_table_name}.name,
		{$brand_ratings_table}.rating
		FROM {$brands_table_name}
		JOIN {$brand_ratings_table} ON {$brands_table_name}.id = {$brand_ratings_table}.brand_id
		ORDER BY {$brand_ratings_table}.rating DESC
	";

		$items = $wpdb->get_results( $sql, ARRAY_A );

		return new WP_REST_Response( $items, 200 );
	}

	/**
	 * Create one item
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function create_item( $request ) {

		/**
		 * Check that correct data exists in the request
		 *
		 * TODO: Add value limits (min/max) for rating.
		 */
		if ( empty( $request['brand_name'] ) ) {

			wp_send_json_error( new WP_Error( 'cant-create', __( 'Please provide a brand name.', 'text-domain' ), array( 'status' => 500 ) ) );

		} elseif ( empty( $request['brand_rating'] ) ) {

			wp_send_json_error( new WP_Error( 'cant-create', __( 'Please provide a brand name AND a rating.', 'text-domain' ), array( 'status' => 500 ) ) );

		} else {

			if ( method_exists( $this, 'cm_toplist_add_rating' ) ) {

				$item = $this->prepare_item_for_database( $request );

				$data = $this->cm_toplist_add_rating( $item );

				if ( is_wp_error( $data ) ) {

					wp_send_json_error( $data );

				} else {

					return new WP_REST_Response( $data, 200 );
				}
			}
		}
	}

	/**
	 * NOT CURRENTLY IN USE
	 * Prepare the item for the REST response
	 *
	 * @param mixed           $item WordPress representation of the item.
	 * @param WP_REST_Request $request Request object.
	 * @return mixed
	 */
	public function prepare_item_for_response( $item, $request ) {
		// var_dump($item);

		// $schema = $this->get_item_schema();
		// $data   = array();

		// $data['brand_name']   = $item[1]->name;
		// $data['brand_rating'] = $item[1]->rating;

		// return $data;
	}

	/**
	 * Create a new record and return the $item or an error
	 *
	 * @param  Array $item
	 * @return mixed
	 */
	public function cm_toplist_add_rating( $item ) {
		global $wpdb;
		$brands_table_name   = $wpdb->prefix . 'toplist_brands';
		$brand_ratings_table = $wpdb->prefix . 'toplist_brand_ratings';
		$brand_name          = $item['brand_name'];
		$brand_rating        = $item['brand_rating'];

		$brand_name_test = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$brands_table_name} WHERE name LIKE %s",
				$brand_name
			)
		);

		if ( is_array( $brand_name_test ) && count( $brand_name_test ) < 1 ) {
			$result = $wpdb->insert(
				$brands_table_name,
				array(
					'name' => $brand_name,
				)
			);

			$brand_id         = $wpdb->insert_id;
			$item['brand_id'] = $brand_id;

			$result2 = $wpdb->insert(
				$brand_ratings_table,
				array(
					'brand_id' => $brand_id,
					'rating'   => $brand_rating,
				)
			);

			if ( $result && $result2 ) {

				return $item;

			} else {

				return new WP_Error( 'error_toplist_add_rating', __( 'There was an error adding this rating. Please check your data and try again.', 'cm-toplist' ), array( 'status' => 500 ) );
			}
		} else {

			return new WP_Error( 'error_toplist_add_rating', __( 'This brand already exists in the database. Please check your data and try again.', 'cm-toplist' ), array( 'status' => 500 ) );

		}
	}

	/**
	 * Prepare the item for create or update operation
	 *
	 * @param WP_REST_Request $request Request object
	 * @return WP_Error|object $prepared_item
	 */
	protected function prepare_item_for_database( $request ) {
		$request_brand_name   = $request->get_param( 'brand_name' );
		$request_brand_rating = $request->get_param( 'brand_rating' );
		$request_brand_id     = $request->get_param( 'brand_id' );

		if ( isset( $request_brand_name ) ) {
			$brand_name = wp_filter_nohtml_kses( sanitize_text_field( $request_brand_name ) );
		} else {
			$brand_name = '';
		}

		if ( isset( $request_brand_rating ) ) {
			$brand_rating = intval( absint( $request_brand_rating ) );
		} else {
			$brand_rating = '';
		}

		if ( isset( $request_brand_id ) ) {
			$brand_id = wp_filter_nohtml_kses( sanitize_text_field( $request_brand_id ) );
		} else {
			$brand_id = '';
		}

		$item = array(
			'brand_name'   => $brand_name,
			'brand_rating' => $brand_rating,
			'brand_id'     => $brand_id,
		);

		return $item;
	}

	/**
	 * Delete one item from the collection
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function delete_item( $request ) {
		$item = $this->prepare_item_for_database( $request );

		if ( method_exists( $this, 'cm_toplist_remove_rating' ) ) {
			$deleted = $this->cm_toplist_remove_rating( $item );

			if ( is_wp_error( $deleted ) ) {
				$delete_error = $deleted;
				wp_send_json_error( $delete_error );
			}

			if ( $deleted ) {
				return new WP_REST_Response(
					array(
						'success' => true,
						'data'    =>
						array(
							'code'    => 'successfully-deleted',
							'message' => $deleted,
						),
					),
					200
				);
			} else {
				wp_send_json_error( new WP_Error( 'cant-delete', __( 'This item does not exist in the database!', 'text-domain' ), array( 'status' => 500 ) ) );

			}
		} else {
			wp_send_json_error( new WP_Error( 'cant-delete', __( 'no endpoint exists to delete this item!', 'text-domain' ), array( 'status' => 500 ) ) );
		}
	}

	/**
	 * Remove a new record and return id
	 *
	 * @param  Array $item
	 * @return mixed $brand_id|WP_Error
	 */
	public function cm_toplist_remove_rating( $item ) {
		global $wpdb;
		$brands_table_name   = $wpdb->prefix . 'toplist_brands';
		$brand_ratings_table = $wpdb->prefix . 'toplist_brand_ratings';
		$brand_id            = $item['brand_id'];

		$brand_id_test = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$brands_table_name} WHERE id = %s",
				$brand_id
			)
		);

		if ( $brand_id_test ) {
			$result = $wpdb->delete(
				$brands_table_name,
				array(
					'id' => $brand_id,
				),
				array( '%d' )
			);

			/**
			 * If first delete operation was successful then attempt to delete
			 * the associated data in the ratings table
			 */
			if ( $result ) {
				$result2 = $wpdb->delete(
					$brand_ratings_table,
					array(
						'brand_id' => $brand_id,
					),
					array( '%d' )
				);
			}

			if ( $result && $result2 ) {

				return $brand_id;

			} else {

				return new WP_Error( 'cant-delete', __( 'There was a problem removing this item. Please inform SysAdmin', 'text-domain' ), array( 'status' => 500 ) );

			}
		}
	}

	/**
	 * Check if a given request has access to get items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function get_items_permissions_check( $request ) {
		return true; // <--use to make readable by all.
		// return current_user_can( 'edit_something' );
	}


	/**
	 * Check if a given request has access to create items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function create_item_permissions_check( $request ) {
		return current_user_can( 'administrator' );
	}

	/**
	 * Check if a given request has access to delete a specific item
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function delete_item_permissions_check( $request ) {
		return $this->create_item_permissions_check( $request );
	}

	/**
	 * Get the query params for collections
	 *
	 * @return array
	 */
	public function get_collection_params() {
		return array(
			'brand_name'   => array(
				'description'       => 'The name of the brand to be rated.',
				'type'              => 'string',
				'sanitize_callback' => 'string',
			),
			'brand_rating' => array(
				'description'       => 'The rating for the brand.',
				'type'              => 'integer',
				'default'           => 1,
				'sanitize_callback' => 'absint',
			),
			'brand_id'     => array(
				'description'       => 'The id of the brand.',
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
			),
		);
	}

} // End Class.
