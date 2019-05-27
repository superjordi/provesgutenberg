<?php
/**
 * Setting page for ResourceSpace Explorer
 */
class ResourceSpaceExplorer_Settings {
	
	public function __construct() {
		// Register settings
		add_action( 'admin_init', array($this, 'rse_settings_init'));
		 
		// Add item to main menu
		add_action( 'admin_menu', array($this, 'rse_options_page'));
	}

	/**
	 * Singleton instantiator.
	 * @return ResourceSpaceExplorer_Settings
	 */
	public static function init() {

		static $instance = null;

		if ( !$instance )
			$instance = new ResourceSpaceExplorer_Settings();

		return $instance;

	}
	
	/**
	 * Register settings
	 */
	public function rse_settings_init() {
		/**
		 * General settings section
		 */
		
		// register a new setting for "rse" page
		register_setting( 'rsexplorer', 'rse_key' );
		register_setting( 'rsexplorer', 'rse_baseurl' );
		
		// register a new section in the "rse" page
		add_settings_section(
			'rse_section_general',
			__( 'General settings', 'rsexplorer'),
			array($this, 'rse_section_general_html'),
			'rsexplorer'
		);

		// register a new field in the "rse_section_general" section, inside the "rse" page
		add_settings_field(
			'rse_field_key',
			__('API key', 'rsexplorer' ),
			array($this, 'rse_field_key_html'),
			'rsexplorer',
			'rse_section_general',
			array(
				'label_for' => 'rse_field_key'
			)
		);

		// register a new field in the "rse_section_general" section, inside the "rse" page
		add_settings_field(
			'rse_field_baseurl',
			__('Base URL', 'rsexplorer' ),
			array($this, 'rse_field_baseurl_html'),
			'rsexplorer',
			'rse_section_general',
			array(
				'label_for' => 'rse_field_baseurl'
			)
		);
		
		/**
		 * UI settings section
		 */
		
		// register a new setting for "rse" page
		register_setting( 'rsexplorer', 'rse_insert_label' );
		
		// register a new section in the "rse" page
		add_settings_section(
			'rse_section_ui',
			__( 'UI settings', 'rsexplorer'),
			array($this, 'rse_section_ui_html'),
			'rsexplorer'
		);

		// register a new field in the "rse_section_general" section, inside the "rse" page
		add_settings_field(
			'rse_field_key',
			__('Insert label', 'rsexplorer' ),
			array($this, 'rse_field_insert_label_html'),
			'rsexplorer',
			'rse_section_ui',
			array(
				'label_for' => 'rse_field_insert_label'
			)
		);
	}
	
	/**
	 * General section callback
	 */
	function rse_section_general_html( $args ) {
		?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>">
			<?php esc_html_e( 'Follow the white rabbit.', 'rsexplorer' ); ?>
		</p>
		<?php
	}
	
	/**
	 * UI section callback
	 */
	function rse_section_ui_html( $args ) {
		?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>">
			<?php esc_html_e( 'Follow the white rabbit.', 'rsexplorer' ); ?>
		</p>
		<?php
	}
	
	/**
	 * Callback for field rse_key
	 */
	function rse_field_key_html( $args ) {
		// get the value of the setting we've registered with register_setting()
		$key = get_option('rse_key');
		
		?>
		<input type="text" 
			id="<?php echo esc_attr( $args['label_for'] ); ?>"
			name="rse_key"
			class="large-text"
			value="<?php echo esc_attr($key) ?>"
			/>
		<p class="description">
			<?php esc_html_e( 'You take the blue pill and the story ends. You wake in your bed and you believe whatever you want to believe.', 'rsexplorer' ); ?>
		</p>
		<?php
	}
	
	/**
	 * Callback for field rse_baseurl
	 */
	function rse_field_baseurl_html( $args ) {
		// get the value of the setting we've registered with register_setting()
		$baseurl = get_option('rse_baseurl');
		
		?>
		<input type="text" 
			id="<?php echo esc_attr( $args['label_for'] ); ?>"
			name="rse_baseurl"
			class="large-text"
			value="<?php echo esc_attr($baseurl) ?>"
			/>
		<p class="description">
			<?php esc_html_e( 'You take the blue pill and the story ends. You wake in your bed and you believe whatever you want to believe.', 'rsexplorer' ); ?>
		</p>
		<?php
	}
	
	/**
	 * Callback for field rse_insert_label
	 */
	function rse_field_insert_label_html( $args ) {
		// get the value of the setting we've registered with register_setting()
		$insert_label = get_option('rse_insert_label');
		
		?>
		<input type="text" 
			id="<?php echo esc_attr( $args['label_for'] ); ?>"
			name="rse_insert_label"
			class="large-text"
			value="<?php echo esc_attr($insert_label) ?>"
			/>
		<p class="description">
			<?php esc_html_e( 'You take the blue pill and the story ends. You wake in your bed and you believe whatever you want to believe.', 'rsexplorer' ); ?>
		</p>
		<?php
	}
	 
	/**
	 * Add menu item to access our setting page
	 */
	function rse_options_page() {
		// add menu page under the Options top menu
		add_submenu_page(
			'options-general.php',
			__('ResourceSpace Explorer Settings', 'rsexplorer'),
			__('ResourceSpace Explorer', 'rsexplorer'),
			'manage_options',
			'rsexplorer',
			array($this, 'rse_options_page_html')
		);
	}
	 
	/**
	 * Display setting page
	 */
	function rse_options_page_html() {
		// check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
	 
		// // add error/update messages
    // 
		// // check if the user have submitted the settings
		// // wordpress will add the "settings-updated" $_GET parameter to the url
		// if (isset( $_GET['settings-updated'])) {
		// 	// add settings saved message with the class of "updated"
		// 	add_settings_error( 'rse_messages', 'rse_message', __( 'Settings Saved', 'rsexplorer' ), 'updated' );
		// }

		// show error/update messages
		settings_errors( 'rse_messages' );
		?>
	 <div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			// output security fields for the registered setting "rse"
			settings_fields( 'rsexplorer' );
			// output setting sections and their fields
			// (sections are registered for "rse", each field is registered to a specific section)
			do_settings_sections( 'rsexplorer' );
			// output save settings button
			submit_button(__('Save Settings', 'rsexplorer'));
			?>
		</form>
	 </div>
	 <?php
	}
}
