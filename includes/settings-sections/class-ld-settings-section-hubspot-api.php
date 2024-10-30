<?php
/**
 * LearnDash Settings Section for Course Themes Metabox.
 *
 * @package LearnDash
 * @subpackage Settings
 */

if ( ( class_exists( 'LearnDash_Settings_Section' ) ) && ( ! class_exists( 'LearnDash_Settings_HubSpot_API' ) ) ) {
	/**
	 * Class to create the settings section.
	 */
	class LearnDash_Settings_HubSpot_API extends LearnDash_Settings_Section {
		/**
		 * Protected constructor for class
		 */
		protected function __construct() {
			$this->settings_page_id = 'learndash-hubspot-settings';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'learndash-hubspot-settings-key';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'learndash-hubspot-settings-key';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'hubspot-settings-key';

			// Section label/header.
			$this->settings_section_label = esc_html__( 'API Settings', 'crm-hubspot-learndash' );

			parent::__construct();
		}

		/**
		 * Initialize the metabox settings values.
		 */
		public function load_settings_values() {	
			parent::load_settings_values();
		}
		
		/**
		 * Initialize the metabox settings fields.
		 */
		public function load_settings_fields() {

			//create api settings array
			$this->setting_option_fields = array(
				'api_key'	=> array(
					'name'      => 'api_key',
					'type'      => 'text',
					'label'     => esc_html__( 'API Key', 'crm-hubspot-learndash' ),
					'help_text' => esc_html__( 'To obtain your API key, click the settings icon in the main navigation bar and then in the left sidebar menu, navigate to Integrations > API key.', 'crm-hubspot-learndash' ),
					'value'     => isset( $this->setting_option_values['api_key'] ) ? $this->setting_option_values['api_key'] : '',
					'class'     => 'regular-text',
				),
			);
			$this->setting_option_fields = apply_filters( 'learndash_settings_fields', $this->setting_option_fields, $this->settings_section_key );
			parent::load_settings_fields();
		}
	}
}

add_action(
	'learndash_settings_sections_init',
	function() {
		LearnDash_Settings_HubSpot_API::add_section_instance();
	}
);
