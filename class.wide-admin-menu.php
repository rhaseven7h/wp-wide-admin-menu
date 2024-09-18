<?php

require 'vendor/autoload.php';

if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access allowed' );
}

require_once 'constants.php';
require_once 'class.wide-admin-menu-styles.php';
require_once 'class.wide-admin-menu-renderer.php';
require_once 'class.wide-admin-menu-settings-page.php';
require_once 'class.wide-admin-menu-custom-width-styles.php';

if ( ! class_exists( 'Wide_Admin_Menu' ) ) {
	class Wide_Admin_Menu {
		public static mixed $options;
		public static Wide_Admin_Menu_Renderer $renderer;

		public function __construct() {
			load_plugin_textdomain(
				'wide-admin-menu',
				false,
				dirname( plugin_basename( __FILE__ ) ) . '/languages'
			);

			self::$options = get_option(
				'wide-admin-menu-options',
				array( "wide-admin-menu-width" => WIDE_ADMIN_MENU_DEFAULT_WIDTH )
			);

			self::$renderer = new Wide_Admin_Menu_Renderer( WIDE_ADMIN_MENU_PATH . '/views' );

			new Wide_Admin_Menu_Styles();

			new Wide_Admin_Menu_Settings_Page(
				self::$renderer,
				self::$options['wide-admin-menu-width']
			);

			new Wide_Admin_Menu_Custom_Width_Styles(
				self::$renderer,
				self::$options['wide-admin-menu-width']
			);
		}

		public static function activate(): void {
			flush_rewrite_rules();
			update_option( 'rewrite_rules', '' );
		}

		public static function deactivate(): void {
			update_option( 'rewrite_rules', '' );
			flush_rewrite_rules();
		}

		public static function uninstall(): void {
			delete_option( 'wide-admin-menu-options' );
		}
	}
}

if ( class_exists( 'Wide_Admin_Menu' ) ) {
	register_activation_hook( __FILE__, array( 'Wide_Admin_Menu', 'activate' ) );
	register_deactivation_hook( __FILE__, array( 'Wide_Admin_Menu', 'deactivate' ) );
	register_uninstall_hook( __FILE__, array( 'Wide_Admin_Menu', 'uninstall' ) );
	new Wide_Admin_Menu();
}
