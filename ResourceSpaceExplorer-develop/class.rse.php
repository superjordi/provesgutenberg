<?php
/*
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/

class ResourceSpaceExplorer {

	private static $TEST_JSON = "[{\"score\":\"0\",\"ref\":\"2\",\"resource_type\":\"1\",\"has_image\":\"0\",\"is_transcoding\":\"0\",\"creation_date\":\"2017-12-19 11:27:39\",\"rating\":\"\",\"user_rating\":\"\",\"user_rating_count\":\"\",\"user_rating_total\":\"\",\"file_extension\":\"jpg\",\"preview_extension\":\"jpg\",\"image_red\":\"\",\"image_green\":\"\",\"image_blue\":\"\",\"thumb_width\":\"\",\"thumb_height\":\"\",\"archive\":\"0\",\"access\":\"0\",\"colour_key\":\"\",\"created_by\":\"1\",\"file_modified\":\"2017-12-19 11:27:39\",\"file_checksum\":\"\",\"request_count\":\"0\",\"new_hit_count\":\"1\",\"expiry_notification_sent\":\"0\",\"preview_tweaks\":\"0|1\",\"file_path\":\"\",\"group_access\":\"\",\"user_access\":\"\",\"field12\":\"2016-02-28 11:55:56\",\"field8\":\"Test!tot\",\"field3\":\"\",\"total_hit_count\":\"1\"},{\"score\":\"0\",\"ref\":\"1\",\"resource_type\":\"1\",\"has_image\":\"0\",\"is_transcoding\":\"0\",\"creation_date\":\"2017-12-19 11:21:55\",\"rating\":\"\",\"user_rating\":\"\",\"user_rating_count\":\"\",\"user_rating_total\":\"\",\"file_extension\":\"jpg\",\"preview_extension\":\"jpg\",\"image_red\":\"\",\"image_green\":\"\",\"image_blue\":\"\",\"thumb_width\":\"\",\"thumb_height\":\"\",\"archive\":\"0\",\"access\":\"0\",\"colour_key\":\"\",\"created_by\":\"1\",\"file_modified\":\"2017-12-19 11:21:55\",\"file_checksum\":\"\",\"request_count\":\"0\",\"new_hit_count\":\"1\",\"expiry_notification_sent\":\"0\",\"preview_tweaks\":\"0|1\",\"file_path\":\"\",\"group_access\":\"\",\"user_access\":\"\",\"field12\":\"2017-12-19 10:21\",\"field8\":\"Test!\",\"field3\":\"\",\"total_hit_count\":\"1\"},{\"score\":\"0\",\"ref\":\"3\",\"resource_type\":\"1\",\"has_image\":\"1\",\"is_transcoding\":\"0\",\"creation_date\":\"2017-12-19 11:36:27\",\"rating\":\"\",\"user_rating\":\"\",\"user_rating_count\":\"\",\"user_rating_total\":\"\",\"file_extension\":\"jpg\",\"preview_extension\":\"jpg\",\"image_red\":\"636\",\"image_green\":\"737\",\"image_blue\":\"187\",\"thumb_width\":\"150\",\"thumb_height\":\"66\",\"archive\":\"0\",\"access\":\"0\",\"colour_key\":\"EKWNG\",\"created_by\":\"1\",\"file_modified\":\"2017-12-19 12:53:56\",\"file_checksum\":\"\",\"request_count\":\"0\",\"new_hit_count\":\"2\",\"expiry_notification_sent\":\"0\",\"preview_tweaks\":\"0|1\",\"file_path\":\"\",\"group_access\":\"\",\"user_access\":\"\",\"field12\":\"2002-06-13 12:17:18\",\"field8\":\"Test!tot tyu\",\"field3\":\"\",\"total_hit_count\":\"2\",\"url_pre\":\"http:\/\/mediateca.fundesplai.org\/filestore\/3_d1debe2892e0c22\/3pre_69674cd7eac8311.jpg?v=2017-12-19+12%3A53%3A56\"}]";
	private static $TEST_JSON2 = "[{\"score\":\"2\",\"ref\":\"3\",\"resource_type\":\"1\",\"has_image\":\"1\",\"is_transcoding\":\"0\",\"creation_date\":\"2017-12-19 11:36:27\",\"rating\":\"\",\"user_rating\":\"\",\"user_rating_count\":\"\",\"user_rating_total\":\"\",\"file_extension\":\"jpg\",\"preview_extension\":\"jpg\",\"image_red\":\"636\",\"image_green\":\"737\",\"image_blue\":\"187\",\"thumb_width\":\"150\",\"thumb_height\":\"66\",\"archive\":\"0\",\"access\":\"0\",\"colour_key\":\"EKWNG\",\"created_by\":\"1\",\"file_modified\":\"2017-12-19 12:53:56\",\"file_checksum\":\"\",\"request_count\":\"0\",\"new_hit_count\":\"2\",\"expiry_notification_sent\":\"0\",\"preview_tweaks\":\"0|1\",\"file_path\":\"\",\"group_access\":\"\",\"user_access\":\"\",\"field12\":\"2002-06-13 12:17:18\",\"field8\":\"Test!tot tyu\",\"field3\":\"\",\"total_hit_count\":\"2\",\"url_pre\":\"http:\/\/www.escolesproves.fundesplai.org\/wp-content\/uploads\/2015\/10\/destacat-sortides-d-un-dia.jpg\"}]";
	
	private $template;

	/**
	 * Singleton instantiator.
	 *
	 * @param string $file The plugin file (usually __FILE__) (optional)
	 * @return ResourceSpaceExplorer
	 */
	public static function init( $file = null ) {

		static $instance = null;

		if ( !$instance )
			$instance = new ResourceSpaceExplorer( $file );

		return $instance;

	}

	/**
	 * Class constructor. Set up some actions and filters.
	 *
	 * @return null
	 */
	protected function __construct( $file ) {
		# Actions:
		// add_action( 'plugins_loaded',        array( $this, 'action_plugins_loaded' ) );
		add_action( 'init',                  array( $this, 'action_init' ) );
		add_action( 'wp_enqueue_media',      array( $this, 'action_enqueue_media' ) );
		add_action( 'print_media_templates', array( $this, 'action_print_media_templates' ) );

		# AJAX actions:
		add_action( 'wp_ajax_resource_space_explorer_request',   array( $this, 'ajax_request' ) );
		add_action( 'wp_ajax_resource_space_explorer_get_resource', array( $this, 'ajax_get_resource' ) );
		
		$this->file = $file;
	}
	
	/**
	 * Load text domain and localisation files.
	 * Populate the array of Service objects.
	 *
	 * @action init
	 * @return null
	 */
	public function action_init() {

		load_plugin_textdomain( 'rsexplorer', false, dirname( $this->plugin_base() ) . '/languages/' );
		
		$this->template = new RSE_Template();

	}

	/**
	 * Enqueue and localise the JS and CSS we need for the media manager.
	 *
	 * @action enqueue_media
	 * @return null
	 */
	public function action_enqueue_media() {

		$rse = array(
			'_nonce'    => wp_create_nonce( 'resource_space_explorer_request' ),
			'base_url'  => untrailingslashit( $this->plugin_url() ),
			'admin_url' => untrailingslashit( admin_url() ),
			'tabs'      => $this->get_tabs(),
			'labels'    => array(
				'title'     => get_option('rse_insert_label')
											? get_option('rse_insert_label')
											: __( 'Insert from ResourceSpace', 'rsexplorer' ),
				'insert'    => __( 'Insert', 'rsexplorer' ),
				'noresults' => __( 'No resource matched your search query', 'rsexplorer' ),
				'gmaps_url' => set_url_scheme( 'https://maps.google.com/maps/api/js', 'https' ),
				'loadmore'  => __( 'Load more resources', 'rsexplorer' ),
			),
		);
		
		wp_enqueue_script(
			'resource-space-explorer',
			$this->plugin_url( 'js/rse.js' ),
			array( 'jquery', 'media-views' ),
			$this->plugin_ver( 'js/rse.js' )
		);

		wp_localize_script(
			'resource-space-explorer',
			'resource_space_explorer', // var name in JS
			$rse
		);

		wp_enqueue_style(
			'resource-space-explorer',
			$this->plugin_url( 'css/rse.css' ),
			array( /*'wp-admin'*/ ),
			$this->plugin_ver( 'css/rse.css' )
		);

	}

	/**
	 * Load the Backbone templates for each of our registered services.
	 *
	 * @action print_media_templates
	 * @return null
	 */
	public function action_print_media_templates() {
		$tabs = $this->get_tabs();
		
		$template = $this->template;

		# @TODO this list of templates should be somewhere else. where?
		foreach ( array( 'search', 'item' ) as $t ) {

			foreach ( $tabs as $tab_id => $tab ) {

				$id = sprintf( 'rse-%s-%s',
					esc_attr( $t ),
					esc_attr( $tab_id )
				);
				
				$template->before_template( $id, $tab_id );
				call_user_func( array( $template, $t ), $id, $tab_id );
				$template->after_template( $id, $tab_id );

			}

		}

		foreach ( array( 'thumbnail' ) as $t ) {

			$id = sprintf( 'rse-%s',
				esc_attr( $t )
			);

			$template->before_template( $id );
			call_user_func( array( $template, $t ), $id );
			$template->after_template( $id );

		}

	}

	/**
	 * Process an AJAX request and output the resulting JSON.
	 *
	 * @action wp_ajax_resource_space_explorer_request
	 * @return null
	 */
	public function ajax_request() {
		
		if ( !isset( $_POST['_nonce'] ) or !wp_verify_nonce( $_POST['_nonce'], 'resource_space_explorer_request' ) )
			die( '-1' );

		$request = wp_parse_args( stripslashes_deep( $_POST ), array(
			'params'  => array(),
			'tab'     => null,
			'min_id'  => null,
			'max_id'  => null,
			'page'    => 1,
		) );
		$request['page'] = absint( $request['page'] );
		$request['user_id'] = absint( get_current_user_id() );

		$response = $this->processRequest( $request );

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( array(
				'error_code'    => $response->get_error_code(),
				'error_message' => $response->get_error_message()
			) );

		} else if ( is_a( $response, 'RSE_Response' ) ) {
			wp_send_json_success( $response->output() );
		} else {
			wp_send_json_success( false );
		}
	}

	/**
	 * Ajax handler to retrieve content from Resource space and add as attachment.
	 */
	function ajax_get_resource() {
		$resource_id    = intval( $_POST['resource_id'] );
		$parent_post_id = isset( $_POST['post'] ) ? intval( $_POST['post'] ) : 0;

		if ( empty( $resource_id ) ) {
			wp_send_json_error( esc_html__( 'Empty resource id', 'rsexplorer' ) );
			add_filter( 'http_request_host_is_external', '__return_true' );
		}

		$args = array_map( 'rawurlencode', array(
			'user'              => 'programacio',
			'function'         => 'search_get_previews',
			'param1'           => '!list' . $resource_id,
			'param2'         => 1, // Restrict to images only.
			'param3'         => 'relevance', // order by
			'param4'         => 0, // Archive status
			'param5' => 10, // Number of rows PJ_RESOURCE_SPACE_RESULTS_PER_PAGE,
			'param6' => "asc", // sort
			'param7' => "10000", //
			'param8'      => 'pre',
			// 'key'              => get_option('rse_key'),
			// 'search'           => '!list' . $resource_id,
			// 'prettyfieldnames' => false,
			// 'original'         => true,
			// 'previewsize'      => 'pre',
			// 'metadata'         => true,
		) );
		
		$query = "";
		foreach ($args as $k => $v) {
			$query .= $query ? "&" : "";
			$query .= $k . "=" . $v;
		}
		// $query = "user=programacio&function=do_search&param1=test";
		$sign = hash("sha256", get_option('rse_key') . $query);
		$api_url = sprintf( '%s/api/', get_option('rse_baseurl') ) . "?" . $query . "&sign=" . $sign;
		
		$request_args = array( 'headers' => array() );

		// // Pass basic auth header if available.
		// if ( defined( 'PJ_RESOURCE_SPACE_AUTHL' ) &&  defined( 'PJ_RESOURCE_SPACE_AUTHP' ) ) {
		// 	$request_args['headers']['Authorization'] = 'Basic ' . base64_encode( PJ_RESOURCE_SPACE_AUTHL . ':' . PJ_RESOURCE_SPACE_AUTHP );
		// }
		
		if (false) {
			$response = wp_remote_get( $api_url, $request_args );

			if ( 200 == wp_remote_retrieve_response_code( $response ) ) {
				$data = json_decode( wp_remote_retrieve_body( $response ) );
			} else {
				wp_send_json_error( esc_html__( 'Unable to query API', 'rsexplorer' ) );
			}

			if ( count( $data ) < 1 ) {
				wp_send_json_error( esc_html__( 'Resource not found', 'rsexplorer' ) );
			}
		} else {
			$data = json_decode(self::$TEST_JSON2);
		}
		
		// Calculate post_name
		
		// Look wether post exists (get_post($post_name)
		$creationDate = $data[0]->creation_date;
		$post_name = preg_replace("/[^0-9]/", "", $creationDate) . "_" . $data[0]->ref;
		
		$attachment_id = $this->get_ID_from_name($post_name);
		
		if ($attachment_id === false) {
			// Otherwise, download file from RSE
			// Request original URL.
			$attachment_id = $this->wpcom_vip_download_image( $data[0]->url_pre, $post_name );
		}

		// Update post to show proper values in wp attachment views
		$post = array(
			'ID' => $attachment_id,
			'post_title' => isset( $data[0]->field8 ) ? $data[0]->field8 : '', // Title in Resourcespace
			'post_excerpt' => isset( $data[0]->field18 ) ? $data[0]->field18 : '' // Caption in Resourcespace
		);

		wp_update_post( $post );

		// Update Metadata.
		update_post_meta( $attachment_id, 'resource_space', 1 );

		// Metadata for connecting resource between Wp and RS
		update_post_meta( $attachment_id, 'resource_external_id', $data[0]->ref );

		// Allow plugins to hook in here.
		do_action( 'resourcespace_import_complete', $attachment_id, $data[0] );

		if ( is_wp_error( $attachment_id ) ) {
			wp_send_json_error( $attachment_id->get_error_message() );
		} else {
			wp_send_json_success( wp_prepare_attachment_for_js( $attachment_id ) );
		}

		exit();
	}
	
	/**
	 * Returns the URL for for a file/dir within this plugin.
	 *
	 * @param string $path The path within this plugin, e.g. '/js/clever-fx.js'
	 * @return string URL
	 * @author John Blackbourn
	 */
	public function plugin_url( $file = '' ) {
		return $this->_plugin( 'url', $file );
	}

	/**
	 * Returns the filesystem path for a file/dir within this plugin.
	 *
	 * @param string $path The path within this plugin, e.g. '/js/clever-fx.js'
	 * @return string Filesystem path
	 * @author John Blackbourn
	 */
	public function plugin_path( $file = '' ) {
		return $this->_plugin( 'path', $file );
	}

	/**
	 * Returns a version number for the given plugin file.
	 *
	 * @param string $path The path within this plugin, e.g. '/js/clever-fx.js'
	 * @return string Version
	 * @author John Blackbourn
	 */
	public function plugin_ver( $file ) {
		return filemtime( $this->plugin_path( $file ) );
	}

	/**
	 * Returns the current plugin's basename, eg. 'my_plugin/my_plugin.php'.
	 *
	 * @return string Basename
	 * @author John Blackbourn
	 */
	public function plugin_base() {
		return $this->_plugin( 'base' );
	}

	/**
	 * Populates the current plugin info if necessary, and returns the requested item.
	 *
	 * @param string $item The name of the requested item. One of 'url', 'path', or 'base'.
	 * @param string $file The file name to append to the returned value (optional).
	 * @return string The value of the requested item.
	 * @author John Blackbourn
	 */
	protected function _plugin( $item, $file = '' ) {
		if ( !isset( $this->plugin ) ) {
			$this->plugin = array(
				'url'  => plugin_dir_url( $this->file ),
				'path' => plugin_dir_path( $this->file ),
				'base' => plugin_basename( $this->file )
			);
		}
		return $this->plugin[$item] . ltrim( $file, '/' );
	}
	
	/**
	 * From https://wordpress.stackexchange.com/a/11296/81793
	 */
	private function get_ID_from_name($post_name) {
		global $wpdb;
		
		$post_type = "attachment";
		$post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type= %s", $post_name, $post_type));
		
		if ($post_id) {
			return $post_id;
		} else {
			return false;
		}
		
		$posts = get_posts(array(
			'name' => $slug,
			'posts_per_page' => 1,
		));
		
		if (!$posts) {
			return false;
		} else {
			return $post[0]->ID;
		}
	}

	/**
	 * From: https://vip-svn.wordpress.com/plugins/vip-do-not-include-on-wpcom/vip-media.php
	 * 
	 * Downloads an external image and optionally attaches it to a post.
	 *
	 * Contains most of core's media_sideload_image() but returns an attachment ID instead of HTML.
	 *
	 * Note: this function does not validate the domain that the image is coming from. Please make sure
	 *   to validate this before downloading the image. Should only pull down images from trusted sources.
	 *
	 * Note: This function does not support GET params because these will not work on WPCOM production servers see r157060
	 *
	 * @param string $image_url URL of the image.
	 * @param int $post_ID ID of the post it should be attached to.
	 * @return $thumbnail_id id of the thumbnail attachment post id
	 */
	private function wpcom_vip_download_image( $image_url, $post_name = '', $post_id = 0, $description = '' ) {
		if ( isset( $_SERVER['REQUEST_METHOD'] ) && strtoupper( $_SERVER['REQUEST_METHOD'] ) == 'GET' ) {
			return new WP_Error( 'invalid-request-method', 'Media sideloading is not supported via GET. Use POST.' );
		}

		if ( ! is_admin() ) {
			return new WP_Error( 'not-in-admin', 'Media sideloading can only be done in when `true === is_admin()`.' );
		}

		if ( $post_id < 0 ) {
			return new WP_Error( 'invalid-post-id', 'Please specify a valid post ID.' );
		}

		if ( ! filter_var( $image_url, FILTER_VALIDATE_URL ) ) {
			return new WP_Error( 'not-a-url', 'Please specify a valid URL.' );
		}

		$image_url_path = parse_url( $image_url, PHP_URL_PATH );
		$image_path_info = pathinfo( $image_url_path );

		if ( ! in_array( strtolower( $image_path_info['extension'] ), array( 'jpg', 'jpe', 'jpeg', 'gif', 'png' ) ) ) {
			return new WP_Error( 'not-an-image', 'Specified URL does not have a valid image extension.' );
		}

		// Download file to temp location; short timeout, because we don't have all day.
		$downloaded_url = download_url( $image_url, 30 );

		// We couldn't download and store to a temporary location, so bail.
		if ( is_wp_error( $downloaded_url ) ) {
			return $downloaded_url;
		}

		$file_array['name'] = $image_path_info['basename'];
		$file_array['tmp_name'] = $downloaded_url;

		if ( empty( $description ) ) {
			$description = $image_path_info['filename'];
		}
		
		$post_data = array();
		
		if ($post_name != '') {
			$post_data['post_name'] = $post_name;
		}
		
		// Now, let's sideload it.
		$attachment_id = media_handle_sideload( $file_array, $post_id, $description, $post_data );

		// If error storing permanently, unlink and return the error
		if ( is_wp_error( $attachment_id ) ) {
			@unlink( $file_array['tmp_name'] ); // unlink can throw errors if the file isn't there
			return $attachment_id;
		}

		return $attachment_id;
	}
	
	public function processRequest( array $request ) {

		$response = new RSE_Response();

		// Ensure that 'page' is never 0. This breaks things.
		$request['page'] = ( $request['page'] < 1 ) ? 1 : $request['page'];

		// Build the request URL.
		// Args from https://www.resourcespace.com/knowledge-base/api/search_get_previews
		$args = array_map( 'rawurlencode', apply_filters( 'resourcespace_request_args', array(
				'user'              => 'programacio',
				'function'         => 'search_get_previews',
				'param1'           => sanitize_text_field( $request['params']['q'] ),
				'param2'         => 1, // Restrict to images only.
				'param3'         => 'relevance', // order by
				'param4'         => 0, // Archive status
				'param5' => 10, // Number of rows PJ_RESOURCE_SPACE_RESULTS_PER_PAGE,
				'param6' => "asc", // sort
				'param7' => "10000", //
				'param8'      => 'pre',
				// 'original'         => true,
				// 'page'             => absint( $request['page'] ),
		) ) );
		
		$query = http_build_query($args);
		$query = "";
		foreach ($args as $k => $v) {
			$query .= $query ? "&" : "";
			$query .= $k . "=" . $v;
		}
		// $query = "user=programacio&function=do_search&param1=test";
		$sign = hash("sha256", get_option('rse_key') . $query);
		$api_url = sprintf( '%s/api/', get_option('rse_baseurl') ) . "?" . $query . "&sign=" . $sign;
		
		// echo $api_url;
		// die();
		
		$request_args = array(
			'headers' => array()
		);
		
    // 
		// // Pass basic auth header if available.
		// if ( defined( 'PJ_RESOURCE_SPACE_AUTHL' ) &&  defined( 'PJ_RESOURCE_SPACE_AUTHP' ) ) {
		// 	$request_args['headers']['Authorization'] = 'Basic ' . base64_encode( PJ_RESOURCE_SPACE_AUTHL . ':' . PJ_RESOURCE_SPACE_AUTHP );
		// }
		
		if (false) { // Estem desconnectats!
			$api_response = wp_remote_get( $api_url, $request_args );
			//var_dump($api_response);
			//die("prout");
			if ( 200 !== wp_remote_retrieve_response_code( $api_response ) ) {
				return $api_response;
			}
			
			$response_data = json_decode( wp_remote_retrieve_body( $api_response ) );
		} else {
			$response_data = json_decode(self::$TEST_JSON);
		}
		
		foreach ( $response_data as $resource ) {

			$dirty_data = array(
				'title'       => $resource->field8,
				'date'        => strtotime( $resource->creation_date ),
				'id'          => $resource->ref,
				'thumbnail'   => $resource->url_pre,
				'url'         => null,
			);

			$dirty_data = apply_filters( 'resourcespace_parse_raw_image_data', $dirty_data, $resource );
			$clean_data = array();

			foreach ( $this->get_item_fields() as $field => $args ) {

				$clean_data[ $field ] = '';

				if ( isset( $dirty_data[ $field ] ) ) {
					$clean_data[ $field ] = call_user_func( $args['sanitize_callback'], $dirty_data[ $field ] );
				} elseif ( ! isset( $dirty_data[ $field ] ) && isset( $args['default'] ) ) {
					$clean_data[ $field ] = $args['default'];
				}

			}

			$item = new RSE_Response_Item();
			$item->set_content( $clean_data['title'] );
			$item->set_date( $clean_data['date'] );
			$item->set_date_format( $clean_data['date_format'] );
			$item->set_id( $clean_data['id'] );
			$item->set_url( $clean_data['url'] );
			$item->set_thumbnail( $clean_data['thumbnail'] );

			$response->add_item( $item );

		}

		// $response->add_meta( 'per_page', $response_data->pagination->per_page );
		// $response->add_meta( 'page', $response_data->pagination->page );
		// $response->add_meta( 'total_pages', $response_data->pagination->total_pages );
		// $response->add_meta( 'total_resources', $response_data->pagination->total_resources );
		
		// var_dump($response);
		// die();
		
		$response->add_meta( 'per_page', 10);
		$response->add_meta( 'page', 1);
		$response->add_meta( 'total_pages', 2);
		$response->add_meta( 'total_resources', 10);

		return $response;
	}

	public function get_item_fields() {
		return apply_filters( 'resourcespace_fields', array(
			'title' => array(
				'sanitize_callback' => 'sanitize_text_field',
			),
			'date' => array(
				'sanitize_callback' => 'absint',
			),
			'date_format' => array(
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => 'j M y',
			),
			'id' => array(
				'sanitize_callback' => 'absint',
				'default'           => 0,
			),
			'thumbnail' => array(
				'sanitize_callback' => 'sanitize_text_field',
			),
			'url' => array(
				'sanitize_callback' => 'esc_url_raw',
			),
		) );

	}
	
	/**
	 * Les pestanyes que es veuran a la mediateca
	 */
	public function get_tabs() {
		return array(
			'all' => array(
				'text'       => _x( 'All', 'Tab title', 'rsexplorer'),
				'defaultTab' => true
			),
			'hashtag' => array(
				'text' => _x( 'With Hashtag', 'Tab title', 'rsexplorer'),
			),
			#'images' => array(
			#	'text' => _x( 'With Images', 'Tab title', 'rsexplorer'),
			#),
			'by_user' => array(
				'text' => _x( 'By User', 'Tab title', 'rsexplorer'),
			),
			'to_user' => array(
				'text' => _x( 'To User', 'Tab title', 'rsexplorer'),
			),
			'location' => array(
				'text' => _x( 'By Location', 'Tab title', 'rsexplorer'),
			),
		);
	}

}
