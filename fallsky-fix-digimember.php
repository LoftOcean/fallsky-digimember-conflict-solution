<?php
/*
Plugin Name: Fallsky Digimember Conflict Solution
Plugin URI: http://www.loftocean.com/
Description: Fix the conflict with plugin Digimember.
Version: 1.0.0
Author: Loft.Ocean
Author URI: http://www.loftocean.com/
Text Domain: loftocean
Domain Path: /languages
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if( !class_exists( 'Fallsky_Extension_Fix_Digimember' ) ) {
	class Fallsky_Extension_Fix_Digimember {
		private static $instance = null;
		function __construct(){
			add_action( 'after_setup_theme', array( $this, 'fix_conflic' ) );
		}
		public function fix_conflic(){
			if ( class_exists( 'ncore_HtmlLogic' ) ) {
				add_action('customize_controls_print_scripts', array( 'ncore_HtmlLogic', 'cbLoadScripts' ), 998931);
			}
		}
		/**
		* @descirption initialize extenstion
		*/
		public static function _instance(){
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}
	}
	add_action('fallsky_extension_init', 'Fallsky_Extension_Fix_Digimember::_instance');
}