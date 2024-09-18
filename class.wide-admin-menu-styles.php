<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access allowed' );
}

require_once 'constants.php';

if ( ! class_exists( 'Wide_Admin_Menu_Styles' ) ) {
	class Wide_Admin_Menu_Styles {
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		public function enqueue_scripts(): void {
			wp_enqueue_style(
				'wide-admin-menu-styles',
				WIDE_ADMIN_MENU_URL . '/assets/css/styles.dist.css',
				array(),
				WIDE_ADMIN_MENU_VERSION
			);
		}
	}
}
