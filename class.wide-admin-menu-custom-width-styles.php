<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access allowed' );
}

if ( ! defined( 'Wide_Admin_Menu_Custom_Width_Styles' ) ) {
	class Wide_Admin_Menu_Custom_Width_Styles {
		private Wide_Admin_Menu_Renderer $renderer;
		private int $admin_menu_width;

		function __construct(
			Wide_Admin_Menu_Renderer $renderer,
			int $admin_menu_width
		) {
			$this->renderer         = $renderer;
			$this->admin_menu_width = $admin_menu_width;
			add_action(
				'admin_head',
				array(
					$this,
					'render_wide_admin_menu_custom_styles'
				)
			);
		}

		public function render_wide_admin_menu_custom_styles(): void {
			$this->renderer->render(
				'wide-admin-menu-head-styles',
				array(
					'width' => $this->admin_menu_width
				)
			);
		}
	}
}
