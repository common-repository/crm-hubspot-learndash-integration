<?php
/**
 * LearnDash Settings Page HubSpot.
 *
 * @package LearnDash
 * @subpackage Settings
 */

if ( ( class_exists( 'LearnDash_Settings_Page' ) ) && ( ! class_exists( 'LearnDash_Settings_Page_HubSpot_Settings' ) ) ) {
	/**
	 * Class to create the settings page.
	 */
	class LearnDash_Settings_Page_HubSpot_Settings extends LearnDash_Settings_Page {

		/**
		 * Public constructor for class
		 */
		public function __construct() {
			$this->parent_menu_page_url  = 'admin.php?page=learndash-hubspot-settings';
			$this->menu_page_capability  = LEARNDASH_ADMIN_CAPABILITY_CHECK;
			$this->settings_page_id      = 'learndash-hubspot-settings';
			$this->settings_page_title   = esc_html__( 'HubSpot Settings', 'learndash' );
			$this->settings_tab_title    = $this->settings_page_title;
			$this->settings_tab_priority = 20;
			$this->show_quick_links_meta = false;
			parent::__construct();
		}
	}
}
add_action(
	'learndash_settings_pages_init',
	function() {
		LearnDash_Settings_Page_HubSpot_Settings::add_page_instance();
	}
);
