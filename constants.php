<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access allowed' );
}

if ( ! defined( 'WIDE_ADMIN_MENU_VERSION' ) ) {
	define( 'WIDE_ADMIN_MENU_VERSION', '1.0' );
}

if ( ! defined( 'WIDE_ADMIN_MENU_FILE' ) ) {
	define( 'WIDE_ADMIN_MENU_FILE', __FILE__ );
}

if ( ! defined( 'WIDE_ADMIN_MENU_PATH' ) ) {
	define( 'WIDE_ADMIN_MENU_PATH', plugin_dir_path( WIDE_ADMIN_MENU_FILE ) );
}

if ( ! defined( 'WIDE_ADMIN_MENU_URL' ) ) {
	define( 'WIDE_ADMIN_MENU_URL', plugin_dir_url( WIDE_ADMIN_MENU_FILE ) );
}

if ( ! defined( 'WIDE_ADMIN_MENU_DEFAULT_WIDTH' ) ) {
	define( 'WIDE_ADMIN_MENU_DEFAULT_WIDTH', 160 );
}
