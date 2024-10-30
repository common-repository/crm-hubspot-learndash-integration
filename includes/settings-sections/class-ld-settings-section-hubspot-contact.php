<?php
/**
 * LearnDash Settings Section for Course Themes Metabox.
 *
 * @package LearnDash
 * @subpackage Settings
 */

if ( ( class_exists( 'LearnDash_Settings_Section' ) ) && ( ! class_exists( 'LearnDash_Settings_HubSpot_Contact' ) ) ) {
	/**
	 * Class to create the settings section.
	 */
	class LearnDash_Settings_HubSpot_Contact extends LearnDash_Settings_Section {
		
	/**
		 * Protected constructor for class
		 */
		protected function __construct() {
			$this->settings_page_id = 'learndash-hubspot-settings';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'learndash-hubspot-settings-contact';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'learndash-hubspot-settings-contact';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'hubspot-settings-contact';

			// Section label/header.
			$this->settings_section_label = esc_html__( 'Hubspot Contact Settings', 'crm-hubspot-learndash' );

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
			//get hubspot api
			$hubspot_array = get_option( 'learndash-hubspot-settings-key' );
			if ( is_array( $hubspot_array ) ) {
				$hubspot_api = $hubspot_array[ 'api_key' ];
			}

			//Call API request on respective page
            if ( empty( $hubspot_api ) ) {
			    return;
            }

			//get all hubspot contacts properties
			$contact_properties_json = wp_remote_get( 'https://api.hubapi.com/properties/v1/contacts/properties?hapikey='.$hubspot_api );
			//@return if wp error
			if ( is_wp_error( $contact_properties_json ) ) {
				return;
			}
			//decode json data and create deal properties array
			$contact_properties = json_decode( $contact_properties_json['body'] );
			//@return if response status is error
			if ( is_object( $contact_properties ) && $contact_properties->status ==  "error" ) {
				return;
			}

			$filtered_contacts=['' =>'',];
			foreach ( $contact_properties as $contact_property ):
				$filtered_contacts += [ $contact_property->name => $contact_property->label ];
			endforeach;
			
			//create settings contact settings array
			$this->setting_option_fields =	[
				'first_name'	=>	[
					'name'			=>	'first_name',
					'type'			=>	'select',
					'label'			=>	'First Name',
					'lazy_load' 	=> true,
					'help_text'		=>	esc_html__( '', 'crm-hubspot-learndash' ),
					'options' 		=>	$filtered_contacts,
					'value'			=>	$this->setting_option_values['first_name'] ?? " ",
				],
				'last_name'	=>	[
					'name'			=>	'last_name',
					'type'			=>	'select',
					'label'			=>	esc_html__( 'Last Name', 'crm-hubspot-learndash' ),
					'lazy_load' 	=>	true,
					'help_text'		=>	esc_html__( '', 'crm-hubspot-learndash' ),
					'options' 		=>	$filtered_contacts,
					'value'			=>	$this->setting_option_values['last_name'] ?? " ",
				],
				'display_name'	=>	[
					'name'			=>	'display_name',
					'type'			=>	'select',
					'label'			=>	esc_html__( 'Display Name', 'crm-hubspot-learndash' ),
					'lazy_load' 	=>	true,
					'help_text'		=>	esc_html__( '', 'crm-hubspot-learndash' ),
					'options' 		=>	$filtered_contacts,
					'value'			=>	$this->setting_option_values['display_name'] ?? " ",
				],
				'user_login'	=>	[
					'name'			=>	'user_login',
					'type'			=>	'select',
					'label'			=>	esc_html__( 'Username', 'crm-hubspot-learndash' ),
					'lazy_load' 	=>	true,
					'help_text'		=>	esc_html__( '', 'crm-hubspot-learndash' ),
					'options' 		=>	$filtered_contacts,
					'value'			=>	$this->setting_option_values['user_login'] ?? " ",
				],
				'roles'	=>	[
					'name'			=>	'roles',
					'type'			=>	'select',
					'label'			=>	esc_html__( 'User Role', 'crm-hubspot-learndash' ),
					'lazy_load' 	=>	true,
					'help_text'		=>	esc_html__( '', 'crm-hubspot-learndash' ),
					'options' 		=>	$filtered_contacts,
					'value'			=>	$this->setting_option_values['roles'] ?? " ",
				],
				'user_url'	=>	[
					'name'			=>	'user_url',
					'type'			=>	'select',
					'label'			=>	esc_html__( 'Website', 'crm-hubspot-learndash' ),
					'lazy_load' 	=>	true,
					'help_text'		=>	esc_html__( '', 'crm-hubspot-learndash' ),
					'options' 		=>	$filtered_contacts,
					'value'			=>	$this->setting_option_values['user_url'] ?? " ",
				],
				'user_registered'	=>	[
					'name'			=>	'user_registered',
					'type'			=>	'select',
					'label'			=>	esc_html__( 'Registered Date', 'crm-hubspot-learndash' ),
					'lazy_load' 	=>	true,
					'help_text'		=>	esc_html__( '', 'crm-hubspot-learndash' ),
					'options' 		=>	$filtered_contacts,
					'value'			=>	$this->setting_option_values['user_registered'] ?? " ",
				],
			];
			$this->setting_option_fields = apply_filters( 'learndash_settings_fields', $this->setting_option_fields, $this->settings_section_key );
			parent::load_settings_fields();
		}
	}
}

add_action(
	'learndash_settings_sections_init',
	function() {
		LearnDash_Settings_HubSpot_Contact::add_section_instance();
	}
);
