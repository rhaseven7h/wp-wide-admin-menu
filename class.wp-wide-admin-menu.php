<?php

require 'vendor/autoload.php';

if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access allowed' );
}

if ( ! class_exists( 'WP_Wide_Admin_Menu' ) ) {
	class WP_Wide_Admin_Menu {
		public static mixed $options;
		public static string $width;
		public static Mustache_Engine $m;

		public function __construct() {
			self::$options = get_option( 'wp_wide_admin_menu', array( "width" => "320px" ) );
			self::$width   = self::$options['width'];

			$views_path       = dirname( __FILE__ ) . '/views';
			$mustache_loader  = new Mustache_Loader_FilesystemLoader( $views_path );
			$mustache_options = array( 'loader' => $mustache_loader );
			self::$m          = new Mustache_Engine( $mustache_options );

			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_head', array( $this, 'admin_head' ) );
		}

		public function admin_menu(): void {
			add_options_page(
				'WP Wide Admin',
				'WP Wide Admin',
				'manage_options',
				'wp-wide-admin-menu',
				array(
					$this,
					'admin_page'
				),
				99
			);
		}

		public function admin_page(): void {
			if ( isset( $_POST['wp_wide_admin_menu'] ) ) {
				self::$options['width'] = $_POST['wp_wide_admin_menu']['width'];
				update_option( 'wp_wide_admin_menu', self::$options );
			}

			echo self::$m->render( 'admin-page', array(
				'width' => self::$width
			) );
		}

		public function admin_head(): void {
			echo self::$m->render( 'wp-wide-admin-menu', array(
				'width' => self::$width
			) );
		}

		public static function activate(): void {
			flush_rewrite_rules();
			update_option( 'rewrite_rules', '' );
		}

		public static function deactivate(): void {
			update_option( 'rewrite_rules', '' );
			flush_rewrite_rules();
		}

		public static function uninstall() {
			// Uninstall stuff here
		}
	}
}

if ( class_exists( 'WP_Wide_Admin_Menu' ) ) {
	register_activation_hook( __FILE__, array( 'WP_Wide_Admin_Menu', 'activate' ) );
	register_deactivation_hook( __FILE__, array( 'WP_Wide_Admin_Menu', 'deactivate' ) );
	register_uninstall_hook( __FILE__, array( 'WP_Wide_Admin_Menu', 'uninstall' ) );
	new WP_Wide_Admin_Menu();
}
