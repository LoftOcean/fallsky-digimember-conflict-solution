<?php
/*
Plugin Name: Fallsky Homepage Widgets Backup
Plugin URI: http://www.loftocean.com/
Description: Backup current homepage widget settings.
Version: 1.0.0
Author: Loft.Ocean
Author URI: http://www.loftocean.com/
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if(!class_exists('Fallsky_Homepage_Widget_Backup_Plugin')) {
	class Fallsky_Homepage_Widget_Backup_Plugin {
		private $message = '';
		public function __construct(){
			$plugin = plugin_basename( __FILE__ );
			add_action(	'after_setup_theme', 	array( $this, 'generate_backup_settings' ) );
			add_action( 'admin_menu', 		array( $this, 'submenu' ), 30 );
		}
		public function generate_backup_settings(){
			if( isset( $_REQUEST[ 'fallsky_hwb_nonce' ] ) && wp_verify_nonce( $_REQUEST[ 'fallsky_hwb_nonce' ], 'fallsky_hwb_nonce' ) ) {
				if(isset($_REQUEST[ 'fallsky_hwb_action' ] ) && ( 'import' == $_REQUEST['fallsky_hwb_action'] ) ) {
					if( isset( $_FILES ) && isset( $_FILES[ 'fallsky_hws_file' ] ) ) {
						$homepage_widgets = file_get_contents( $_FILES[ 'fallsky_hws_file' ][ 'tmp_name' ] );
						$homepage_widgets = maybe_unserialize( $homepage_widgets ); 
						if( !empty( $homepage_widgets ) && is_array( $homepage_widgets ) ) {
							foreach( $homepage_widgets as $hwn => $hwv ) {
								update_option( $hwn, $hwv );
							}
						}
						$this->message = 'The homepage widget settings import has finished.';
					}
					else { 
						$this->message = 'Please choose a valid import file.';
					}
				}
				else{
					$widgets = array();
					$names = array(
						'widget_fallsky-homepage-widget-posts', 
						'widget_fallsky-homepage-widget-banner', 
						'widget_fallsky-homepage-widget-featured-category', 
						'widget_fallsky-homepage-widget-call-action', 
						'widget_fallsky-homepage-widget-custom-content',
						'widget_fallsky-homepage-widget-mc4wp-singup', 
						'widget_fallsky-homepage-widget-products',
						'widget_fallsky-homepage-widget-product-categories'
					);
					foreach($names as $name){
						$value = get_option($name, false);
						if(!empty($value)){
							$widgets[$name] = $value;
						}
					}
					if(!empty($widgets)){
						$charset = get_option( 'blog_charset' );
						header( 'Content-disposition: attachment; filename=fallsky-homepage-widget-settings-export.dat' );
						header( 'Content-Type: application/octet-stream; charset=' . $charset );
						print( maybe_serialize( $widgets ) );
						die();
					}
				}
			}
		}
		/**
		* Add sub menu for instagram settings
		*/
		public function submenu(){
			$title = 'Fallsky Homepage Widgets';
			add_submenu_page('themes.php', $title, $title, 'manage_options', 'fallsky-homepage-widgets', array($this, 'render_settings'));
		}
		/**
		* Render the instagram setting page
		*/
		public function render_settings(){ 
			$nonce = wp_create_nonce('fallsky_hwb_nonce');
			$url = add_query_arg(array('fallsky_hwb_nonce' => $nonce), admin_url( 'themes.php' ) ); ?>
			<div class="wrap fallsky-homepage-widgets">
				<h2>Fallsky Homepage Widget Backup/Restore</h2>
				<form name="form" method="post" enctype="multipart/form-data">
					<?php if(!empty($this->message)) : ?><h4><?php print($this->message); ?></h4><?php endif; ?>
					<table class="form-table"><tbody>
						<tr>
							<th>Backup</th>
							<td><a target="_blank" href="<?php echo esc_url( $url ); ?>" title="Backup" class="button">Click to Export</a></td>
						</tr>
						<tr>
							<th>Restore</th>
							<td>
								<input type="file" name="fallsky_hws_file" /><br>
								<input type="submit" value="import" class="button" /><br>
								<input type="hidden" name="fallsky_hwb_nonce" value="<?php echo $nonce; ?>" />
								<input type="hidden" name="fallsky_hwb_action" value="import" />
							</td>
						</tr>
					</tbody></table>
				</form>
			</div> <?php
		}
	}
	new Fallsky_Homepage_Widget_Backup_Plugin();
}