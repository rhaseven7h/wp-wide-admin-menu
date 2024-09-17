<?php

require 'vendor/autoload.php';

if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access allowed' );
}

if ( ! class_exists( 'Wide_Admin_Menu' ) ) {
	class Wide_Admin_Menu {
		public static mixed $options;
		public static Mustache_Engine $m;

		public function __construct() {
			$this->define_constants();

			$this->load_textdomain();

			self::$options = get_option(
				'wide-admin-menu-options',
				array( "wide-admin-menu-width" => 240 )
			);

			$views_path       = WIDE_ADMIN_MENU_PATH . '/views';
			$mustache_loader  = new Mustache_Loader_FilesystemLoader( $views_path );
			$mustache_options = array( 'loader' => $mustache_loader );
			self::$m          = new Mustache_Engine( $mustache_options );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_init', array( $this, 'register_settings' ) );
			add_action( 'admin_head', array( $this, 'admin_head' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		}

		public static function render_template( $template_name, $data = array() ): void {
			echo self::$m->render( $template_name, $data );
		}

		private function define_constants(): void {
			define( 'WIDE_ADMIN_MENU_VERSION', '1.0' );
			define( 'WIDE_ADMIN_MENU_FILE', __FILE__ );
			define( 'WIDE_ADMIN_MENU_PATH', plugin_dir_path( WIDE_ADMIN_MENU_FILE ) );
			define( 'WIDE_ADMIN_MENU_URL', plugin_dir_url( WIDE_ADMIN_MENU_FILE ) );
		}

		public function load_textdomain(): void {
			load_plugin_textdomain(
				'wide-admin-menu',
				false,
				dirname( plugin_basename( __FILE__ ) ) . '/languages'
			);
		}

		public function admin_menu(): void {
			add_options_page(
				esc_html__( 'Wide Admin Menu', 'wide-admin-menu' ),
				esc_html__( 'Wide Admin Menu', 'wide-admin-menu' ),
				'manage_options',
				'wide-admin-menu',
				array(
					$this,
					'admin_options_page'
				),
				99
			);
		}

		public function admin_head(): void {
			$this->render_template( 'wide-admin-menu-head-styles', array(
				'width' => self::$options['wide-admin-menu-width']
			) );
		}

		public function enqueue_scripts(): void {
			wp_enqueue_style(
				'wide-admin-menu-styles',
				WIDE_ADMIN_MENU_URL . '/assets/css/styles.dist.css',
				array(),
				WIDE_ADMIN_MENU_VERSION
			);
		}

		public function admin_options_page(): void {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			if ( isset( $_GET['settings-updated'] ) ) {
				add_settings_error(
					'wide-admin-menu-options',
					'wide-admin-menu',
					esc_html__( 'Settings Saved', 'wide-admin-menu' ),
					'success'
				);
			}

			ob_start();
			settings_fields( 'wide-admin-menu-option-group' );
			$settings_fields = ob_get_clean();

			ob_start();
			do_settings_sections( 'wide-admin-menu-settings-page-general' );
			$do_settings_sections = ob_get_clean();

			ob_start();
			submit_button( __( 'Save Settings', 'wide-admin-menu' ) );
			$submit_button = ob_get_clean();

			$this->render_template( 'wide-admin-menu-admin-options-page', array(
				'pageTitle'            => esc_html( get_admin_page_title() ),
				'settings_fields'      => $settings_fields,
				'do_settings_sections' => $do_settings_sections,
				'submit_button'        => $submit_button
			) );
		}

		public function register_settings(): void {
			register_setting(
				'wide-admin-menu-option-group',
				'wide-admin-menu-options',
				array(
					'label'             => esc_html__( 'Wide Admin Menu', 'wide-admin-menu' ),
					'description'       => esc_html__( 'Settings for the Wide Admin Menu plugin.', 'wide-admin-menu' ),
					'sanitize_callback' => array( $this, 'sanitize_options' ),
					'show_in_rest'      => true,
					'default'           => array(
						'wide_admin_menu_width' => 240
					),
				)
			);
			add_settings_section(
				'wide-admin-menu-settings-section-general',
				esc_html__( 'General', 'wide-admin-menu' ),
				array( $this, 'wide_admin_menu_settings_section_general' ),
				'wide-admin-menu-settings-page-general'
			);
			add_settings_field(
				'wide-admin-menu-width',
				esc_html__( 'Width', 'wide-admin-menu' ),
				array( $this, 'wide_admin_menu_width' ),
				'wide-admin-menu-settings-page-general',
				'wide-admin-menu-settings-section-general',
				array( 'label_for' => 'wide-admin-menu-width' )
			);
		}

		public function sanitize_options( $options ): array {
			$options['wide-admin-menu-width'] = sanitize_text_field( $options['wide-admin-menu-width'] );
			$options['wide-admin-menu-width'] = (int) $options['wide-admin-menu-width'];
			if ( $options['wide-admin-menu-width'] < 160 ) {
				$options['wide-admin-menu-width'] = 160;
			}
			if ( $options['wide-admin-menu-width'] > 512 ) {
				$options['wide-admin-menu-width'] = 512;
			}

			return $options;
		}

		public function wide_admin_menu_settings_section_general(): void {
			$this->render_template( 'wide-admin-menu-settings-section-general-description', array(
				'section_description' => esc_html__(
					'General settings for the Wide Admin Menu plugin.',
					'wide-admin-menu'
				)
			) );
		}

		/** @noinspection PhpUnusedParameterInspection */
		public function wide_admin_menu_width( $args ): void {
			$field_value       = isset( self::$options['wide-admin-menu-width'] )
				? esc_attr( self::$options['wide-admin-menu-width'] )
				: 240;
			$field_description = esc_html__( 'The width for the Admin menu.', 'wide-admin-menu' );
			$this->render_template( 'wide-admin-menu-width-input-field', array(
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
