<?php
/**
 * LearnDash Settings Section for Course Themes Metabox.
 *
 * @package LearnDash
 * @subpackage Settings
 */

if ( ( class_exists( 'LearnDash_Settings_Section' ) ) && ( ! class_exists( 'LearnDash_Settings_HubSpot_Deal' ) ) ) {
	/**
	 * Class to create the settings section.
	 */
	class LearnDash_Settings_HubSpot_Deal extends LearnDash_Settings_Section {
		
	/**
		 * Protected constructor for class
		 */
		protected function __construct() {
			$this->settings_page_id = 'learndash-hubspot-settings';

			// This is the 'option_name' key used in the wp_options table.
			$this->setting_option_key = 'learndash-hubspot-settings-deal';

			// This is the HTML form field prefix used.
			$this->setting_field_prefix = 'learndash-hubspot-settings-deal';

			// Used within the Settings API to uniquely identify this section.
			$this->settings_section_key = 'hubspot-settings-deal';

			// Section label/header.
			$this->settings_section_label = esc_html__( 'Hubspot Deal Settings', 'crm-hubspot-learndash' );

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

			//get all hubspot deal properties
			$deal_properties_json = wp_remote_get( 'https://api.hubapi.com/properties/v1/deals/properties/?hapikey='.$hubspot_api );
			///@return if wp error
			if ( is_wp_error( $deal_properties_json ) ) {
				return;
			}
			//decode json data and create deal properties array
			$deal_properties = json_decode( $deal_properties_json['body'] );
			//@return if api error
			if ( is_object( $deal_properties ) && $deal_properties->status ==  "error" ) {
				return;
			}

			$filtered_deals=['' =>'',];
			foreach ( $deal_properties as $deal_property ):
				$filtered_deals += [ $deal_property->name => $deal_property->label ];
			endforeach;
			
			//create settings deal settings array
			$this->setting_option_fields =	[
				'course_title'	=>	[
					'name'			=>	'course_title',
					'type'			=>	'select',
					'label'			=>	'Course Title',
					'lazy_load' 	=>	true,
					'help_text'		=>	esc_html__( '', 'crm-hubspot-learndash' ),
					'options' 		=>	$filtered_deals,
					'value'			=>	$this->setting_option_values['course_title'] ?? " ",
				],
				'course_status'	=>	[
					'name'			=>	'course_status',
					'type'			=>	'select',
					'label'			=>	esc_html__( 'Course Status', 'crm-hubspot-learndash' ),
					'lazy_load' 	=>	true,
					'help_text'		=>	esc_html__( '', 'crm-hubspot-learndash' ),
					'options' 		=>	$filtered_deals,
					'value'			=>	$this->setting_option_values['course_status'] ?? " ",
				],
				'quiz_score_field'	=>	[
					'name'			=>	'quiz_score_field',
					'type'			=>	'select',
					'label'			=>	esc_html__( 'Quiz Score', 'crm-hubspot-learndash' ),
					'lazy_load' 	=>	true,
					'help_text'		=>	esc_html__( '', 'crm-hubspot-learndash' ),
					'options' 		=>	$filtered_deals,
					'value'			=>	$this->setting_option_values['quiz_score_field'] ?? " ",
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
		LearnDash_Settings_HubSpot_Deal::add_section_instance();
	}
);
