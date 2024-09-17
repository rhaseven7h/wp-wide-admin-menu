<?php

require 'vendor/autoload.php';

if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access allowed' );
}

if ( ! class_exists( 'WP_Wide_Admin_Menu' ) ) {
	class WP_Wide_Admin_Menu {
		public static mixed $options;
		public static Mustache_Engine $m;

		public function __construct() {
			$this->define_constants();

			$this->load_textdomain();

			self::$options = get_option(
				'wp-wide-admin-menu-options',
				array( "wp-wide-admin-menu-width" => 240 )
			);

			$views_path       = WP_WIDE_ADMIN_MENU_PATH . '/views';
			$mustache_loader  = new Mustache_Loader_FilesystemLoader( $views_path );
			$mustache_options = array( 'loader' => $mustache_loader );
			self::$m          = new Mustache_Engine( $mustache_options );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_init', array( $this, 'register_settings' ) );
			add_action( 'admin_head', array( $this, 'admin_head' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		}

		private function define_constants(): void {
			define( 'WP_WIDE_ADMIN_MENU_VERSION', '1.0' );
			define( 'WP_WIDE_ADMIN_MENU_FILE', __FILE__ );
			define( 'WP_WIDE_ADMIN_MENU_PATH', plugin_dir_path( WP_WIDE_ADMIN_MENU_FILE ) );
			define( 'WP_WIDE_ADMIN_MENU_URL', plugin_dir_url( WP_WIDE_ADMIN_MENU_FILE ) );
		}

		public function load_textdomain(): void {
			load_plugin_textdomain(
				'wp-wide-admin-menu',
				false,
				dirname( plugin_basename( __FILE__ ) ) . '/languages'
			);
		}

		public function admin_menu(): void {
			add_options_page(
				__( 'WP Wide Admin', 'wp-wide-admin-menu' ),
				__( 'WP Wide Admin', 'wp-wide-admin-menu' ),
				'manage_options',
				'wp-wide-admin-menu',
				array(
					$this,
					'admin_options_page'
				),
				99
			);
		}

		public function admin_head(): void {
			echo self::$m->render( 'wp-wide-admin-menu', array(
				'width' => self::$options['wp-wide-admin-menu-width']
			) );
		}

		public function enqueue_scripts(): void {
			wp_enqueue_script(
				'wp-wide-admin-menu-tailwind-css',
				WP_WIDE_ADMIN_MENU_URL . '/assets/js/tailwindcss/tailwind-v3.4.10.js',
				array(),
				WP_WIDE_ADMIN_MENU_VERSION,
				true
			);
		}

		public function admin_options_page(): void {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			if ( isset( $_GET['settings-updated'] ) ) {
				add_settings_error(
					'wp-wide-admin-menu-options',
					'wp-wide-admin-menu',
					esc_html__( 'Settings Saved', 'wp-wide-admin-menu' ),
					'success'
				);
			}

			ob_start();
			settings_fields( 'wp-wide-admin-menu-option-group' );
			$settings_fields = ob_get_clean();

			ob_start();
			do_settings_sections( 'wp-wide-admin-menu-settings-page-general' );
			$do_settings_sections = ob_get_clean();

			ob_start();
			submit_button( __( 'Save Settings', 'wp-wide-admin-menu' ) );
			$submit_button = ob_get_clean();

			echo self::$m->render( 'admin-page', array(
				'pageTitle'            => esc_html( get_admin_page_title() ),
				'width'                => self::$options['wp-wide-admin-menu-width'],
				'settings_fields'      => $settings_fields,
				'do_settings_sections' => $do_settings_sections,
				'submit_button'        => $submit_button
			) );
		}

		public function register_settings(): void {
			register_setting(
				'wp-wide-admin-menu-option-group',
				'wp-wide-admin-menu-options',
				array(
					'label'             => __( 'WP Wide Admin Menu', 'wp-wide-admin-menu' ),
					'description'       => __( 'Settings for the WP Wide Admin Menu plugin.', 'wp-wide-admin-menu' ),
					'sanitize_callback' => array( $this, 'sanitize_options' ),
					'show_in_rest'      => true,
					'default'           => array(
						'wp_wide_admin_menu_width' => 240
					),
				)
			);
			add_settings_section(
				'wp-wide-admin-menu-settings-section-general',
				__( 'General', 'wp-wide-admin-menu' ),
				array( $this, 'wp_wide_admin_menu_settings_section_general' ),
				'wp-wide-admin-menu-settings-page-general'
			);
			add_settings_field(
				'wp-wide-admin-menu-width',
				__( 'Width', 'wp-wide-admin-menu' ),
				array( $this, 'wp_wide_admin_menu_width' ),
				'wp-wide-admin-menu-settings-page-general',
				'wp-wide-admin-menu-settings-section-general',
				array( 'label_for' => 'wp-wide-admin-menu-width' )
			);
		}

		public function sanitize_options( $options ): array {
			$options['wp-wide-admin-menu-width'] = sanitize_text_field( $options['wp-wide-admin-menu-width'] );
			$options['wp-wide-admin-menu-width'] = (int) $options['wp-wide-admin-menu-width'];
			if ( $options['wp-wide-admin-menu-width'] < 160 ) {
				$options['wp-wide-admin-menu-width'] = 160;
			}
			if ( $options['wp-wide-admin-menu-width'] > 512 ) {
				$options['wp-wide-admin-menu-width'] = 512;
			}

			return $options;
		}

		public function wp_wide_admin_menu_settings_section_general(): void {
			echo self::$m->render( 'wp-wide-admin-menu-settings-section-general', array(
				'section_description' => __(
					'General settings for the WP Wide Admin Menu plugin.',
					'wp-wide-admin-menu'
				)
			) );
		}

		/** @noinspection PhpUnusedParameterInspection */
		public function wp_wide_admin_menu_width( $args ): void {
			$field_value       = isset( self::$options['wp-wide-admin-menu-width'] )
				? esc_attr( self::$options['wp-wide-admin-menu-width'] )
				: 240;
			$field_description = __( 'The width for the Admin menu.', 'wp-wide-admin-menu' );
			echo self::$m->render( 'wp-wide-admin-menu-width', array(
				'field_value'       => $field_value,
				'field_description' => $field_description
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

		public static function uninstall(): void {
			delete_option( 'wp-wide-admin-menu-options' );
		}
	}
}

if ( class_exists( 'WP_Wide_Admin_Menu' ) ) {
	register_activation_hook( __FILE__, array( 'WP_Wide_Admin_Menu', 'activate' ) );
	register_deactivation_hook( __FILE__, array( 'WP_Wide_Admin_Menu', 'deactivate' ) );
	register_uninstall_hook( __FILE__, array( 'WP_Wide_Admin_Menu', 'uninstall' ) );
	new WP_Wide_Admin_Menu();
}
