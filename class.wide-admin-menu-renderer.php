<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'No direct access allowed' );
}

require_once 'constants.php';

if ( ! class_exists( 'Wide_Admin_Menu_Renderer' ) ) {
	class Wide_Admin_Menu_Renderer {
		private Mustache_Engine $mustache_engine;

		public function __construct( string $views_path ) {
			$mustache_loader       = new Mustache_Loader_FilesystemLoader( $views_path );
			$mustache_options      = array( 'loader' => $mustache_loader );
			$this->mustache_engine = new Mustache_Engine( $mustache_options );
		}

		public function render( $template_name, $data = array() ): void {
			echo $this->mustache_engine->render( $template_name, $data );
		}
	}
}
