<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once 'constants.php';

if ( ! class_exists( 'Wide_Admin_Menu_Settings_Page' ) ) {
	class Wide_Admin_Menu_Settings_Page {
		private Wide_Admin_Menu_Renderer $renderer;
		private int $width;

		public function __construct( Wide_Admin_Menu_Renderer $renderer, int $width ) {
			$this->renderer = $renderer;
			$this->width    = $width;
			add_action( 'admin_init', array( $this, 'register_settings' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
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

			$this->renderer->render( 'wide-admin-menu-admin-options-page', array(
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
						'wide_admin_menu_width' => $this->width
					),
				)
			);
			add_settings_section(
				'wide-admin-menu-settings-section-general',
				esc_html__( 'General', 'wide-admin-menu' ),
				array( $this, 'section_general' ),
				'wide-admin-menu-settings-page-general'
			);
			add_settings_field(
				'wide-admin-menu-width',
				esc_html__( 'Width', 'wide-admin-menu' ),
				array( $this, 'menu_width_field' ),
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

		public function section_general(): void {
			$this->renderer->render( 'wide-admin-menu-settings-section-general-description', array(
				'section_description' => esc_html__(
					'General settings for the Wide Admin Menu plugin.',
					'wide-admin-menu'
				)
			) );
		}

		public function menu_width_field(): void {
			$field_value       = esc_attr( $this->width );
			$field_description = esc_html__(
				'The width for the Admin menu.',
				'wide-admin-menu'
			);
			$this->renderer->render(
				'wide-admin-menu-width-input-field',
				array(
					'field_value'       => $field_value,
					'field_description' => $field_description
				)
			);
		}
	}
}
