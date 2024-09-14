<?php


if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access allowed' );
}

if ( ! class_exists( 'WP_Wide_Admin_Menu' ) ) {
	class WP_Wide_Admin_Menu {
		public static mixed $options;
		public static string $width;

		public function __construct() {
			self::$options = get_option( 'wp_wide_admin_menu', array( "width" => "240px" ) );
			self::$width   = self::$options['width'];
			error_log( "Option Width: " . self::$width );
			add_action( 'admin_head', array( $this, 'admin_head' ) );
		}

		public function admin_head(): void {
			?>
            <style>
                #adminmenu, #adminmenuback, #adminmenuwrap, .wp-submenu {
                    width: <?php echo self::$width ?> !important;
                }

                .wp-not-current-submenu .wp-submenu {
                    left: <?php echo self::$width ?> !important;
                }

                #wpcontent, #wpfooter {
                    margin-left: <?php echo self::$width ?> !important;
                }
            </style>
			<?php
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
