<?php
/**
 * Plugin Name: CRM HubSpot LearnDash Integration
 * Description: Integrates your course enrollments with HubSpot CRM
 * Version: 1.1.0
 * Author: qfnetwork, rahilwazir, zeeshanalam
 * Author URI: https://www.qfnetwork.org
 * Text Domain: crm-hubspot-learndash-integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class LD_HusSpot
 * @property LD_HubSpot $instance
 * @property LD_HubSpot_Settings $settings
 * @property LD_HubSpot_Integration $integration
 */
class LD_HubSpot {
	const VERSION = '1.1.0';

	/**
	 * @var LD_HubSpot
	 */
	private static $instance;

	/**
	 * @return LD_HubSpot
	 */
	public static function instance() {
		if ( is_null( self::$instance ) && ! ( self::$instance instanceof LD_HubSpot ) ) {
			self::$instance = new self;

			self::$instance->setup_constants();
			self::$instance->includes();
			self::$instance->hooks();

			if ( is_admin() ) {
				self::$instance->settings = new LD_HubSpot_Settings();
			}
			self::$instance->hubspot = new LD_HubSpot_Integration();
		}

		return self::$instance;
	}

	/**
		 * Setup Constants
		 */
	private function setup_constants() {
		/**
			 * Plugin Text Domain
			 */
		define( 'LDHS_TEXT_DOMAIN', 'LD_HusSpot' );

		/**
			 * Plugin Directory
			 */
		define( 'LDHS_DIR', plugin_dir_path( __FILE__ ) );
		define( 'LDHS_DIR_FILE', LDHS_DIR . basename( __FILE__ ) );
		define( 'LDHS_INCLUDES_DIR', trailingslashit( LDHS_DIR . 'includes' ) );
		define( 'LDHS_BASE_DIR', plugin_basename( __FILE__ ) );

		/**
		 * Plugin URLS
		 */
		define( 'LDHS_URL', trailingslashit( plugins_url( '', __FILE__ ) ) );
		define( 'LDHS_ASSETS_URL', trailingslashit( LDHS_URL . 'assets' ) );
	}

	/**
		 * Pugin Include Required Files
		 */
	private function includes() {
		require_once 'vendor/autoload.php';
		if ( is_admin() ) {
			require_once 'includes/class-ld-hubspot-admin.php';

			require_once 'includes/class-ld-hubspot-settings.php';
			require_once 'includes/settings-sections/class-ld-settings-section-hubspot-api.php';
			require_once 'includes/settings-sections/class-ld-settings-section-hubspot-contact.php';
			require_once 'includes/settings-sections/class-ld-settings-section-hubspot-deal.php';
		}
		require_once 'includes/class-ld-hubspot-integration.php';
	}

	private function hooks() {
		add_filter( 'plugin_action_links_' . LDHS_BASE_DIR, [ $this, 'settings_link' ], 10, 1 );
		add_action( 'plugins_loaded', [ $this, 'upgrade' ] );
	}

	/**
	 * Add settings link on plugin page
	 *
	 * @return void
	 */
	public function settings_link( $links ) {
		$settings_link = '<a href="' . admin_url( 'admin.php?page=learndash-hubspot-settings' ) . '">' . __( 'Settings', 'crm-hubspot-learndash-integration' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}
}

/**
 * @return LD_HubSpot|bool
 */
function ld_hubspot() {
	if ( ! class_exists( 'SFWD_LMS' ) ) {
		add_action( 'admin_notices', [ 'LD_HubSpot', 'admin_notice' ] );
		return false;
	}

	return LD_HubSpot::instance();
}
add_action( 'plugins_loaded', 'ld_hubspot' );
