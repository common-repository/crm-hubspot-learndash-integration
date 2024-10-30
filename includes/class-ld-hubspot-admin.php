<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class LD_HubSpot_Settings
 * @package  LD_HusSpot
 */
class LD_HubSpot_Settings {
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'sub_menu' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_filter( 'learndash_admin_tabs', [ $this, 'admin_tabs' ], 10, 1 );
		add_filter( 'learndash_admin_tabs_on_page', [ $this, 'admin_tabs_on_page' ], 10, 3 );
	}

	/**
	 * Enqueue admin scripts and styles
	 */
	public function enqueue_scripts() {
		if ( ! is_admin() ) {
			return;
		}

		// we need to load the LD plugin style.css, sfwd_module.css and sfwd_module.js because we want to replicate the styling on the admin tab.
		wp_enqueue_style( 'learndash_style', LEARNDASH_LMS_PLUGIN_URL . 'assets/css/style.css' );
		wp_enqueue_style( 'sfwd-module-style', LEARNDASH_LMS_PLUGIN_URL . 'assets/css/sfwd_module.css' );
		wp_enqueue_script( 'sfwd-module-script', LEARNDASH_LMS_PLUGIN_URL . 'assets/js/sfwd_module.js', [ 'jquery' ], LEARNDASH_VERSION, true );

		$data = [];
		$data = [ 'json' => json_encode( $data ) ];
		wp_localize_script( 'sfwd-module-script', 'sfwd_data', $data );

		// Load our admin JS
		// wp_enqueue_script( 'crm-hubspot-learndash-admin', LDHS_ASSETS_URL . 'js/crm-hubspot-learndash-admin.js', [ 'jquery' ], ld_hubspot()::VERSION, true );
	}

		/**
	 * Add admin tabs for settings page
	 * @param  array $tabs Original tabs
	 * @return array       New modified tabs
	 */
	public function admin_tabs( $tabs ) {
		$tabs['hubspot'] = [
			'link'      => 'admin.php?page=learndash-hubspot-settings',
			'name'      => __( 'HubSpot Settings', 'crm-hubspot-learndash' ),
			'id'        => 'admin_page_learndash-hubspot-settings',
			'menu_link' => 'edit.php?post_type=sfwd-courses&page=sfwd-lms_sfwd_lms.php_post_type_sfwd-courses',
		];

		return $tabs;
	}

	/**
	 * Display active tab on settings page
	 * @param  array $admin_tabs_on_page Original active tabs
	 * @param  array $admin_tabs         Available admin tabs
	 * @param  int   $current_page_id    ID of current page
	 * @return array                     Currenct active tabs
	 */
	public function admin_tabs_on_page( $admin_tabs_on_page, $admin_tabs, $current_page_id ) {
		foreach ( $admin_tabs as $key => $value ) {
			if ( $value['id'] == $current_page_id && 'edit.php?post_type=sfwd-courses&page=sfwd-lms_sfwd_lms.php_post_type_sfwd-courses' === $value['menu_link'] ) {

				$admin_tabs_on_page[ $current_page_id ][] = 'hubspot';
				return $admin_tabs_on_page;
			}
		}

		return $admin_tabs_on_page;
	}

	/**
	 * Add plugin's menu
	 */
	public function sub_menu() {
		add_submenu_page(
			'edit.php?post_type=sfwd-courses',
			__( 'HubSpot', 'crm-hubspot-learndash' ),
			__( 'HubSpot', 'crm-hubspot-learndash' ),
			'manage_options',
			'admin.php?page=learndash-hubspot-settings',
			[ $this, 'page' ]
		);

		add_submenu_page(
			'learndash-lms-non-existant',
			__( 'HubSpot', 'crm-hubspot-learndash' ),
			__( 'HubSpot', 'crm-hubspot-learndash' ),
			'manage_options',
			'learndash-hubspot-settings',
			[ $this, 'page' ]
		);
	}
}
