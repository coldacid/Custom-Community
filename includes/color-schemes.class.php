<?php
/**
 * Implements color schemes
 * 
 * @author Fabian Wolf
 * @package cc2
 * @since 2.0-r2
 */

if( !class_exists( 'cc2_ColorSchemes' ) ) {
	class cc2_ColorSchemes {
		
		var $arrKnownLocations = array();
		
		function __construct() {
			// init variables
			
			$upload_dir = $upload_dir = wp_upload_dir();
			
			$arrLocations = array(
				$upload_dir['path'] . '/cc2/schemes/' => $upload_dir['url'] . '/cc2/schemes/',
				get_template_directory() . '/includes/schemes/' => get_template_directory_uri() . '/includes/schemes/',
			);
			
			$this->set_known_locations( $arrLocations );
			
			// add filters
			
			add_filter( 'cc2_style_css', array( $this, 'switch_color_scheme') );
		}
		
		public static function init() {
			
			new cc2_ColorSchemes();
		}

		public function set_known_locations( $arrLocations = array() ) {
			$return = false;
			
			if( !empty( $arrLocations ) && is_array( $arrLocations ) ) {
				$this->arrKnownLocations = apply_filters('cc2_color_schemes_set_locations', $arrLocations );
				
				$return = true;
			}
		
			return $return;
		}
		
	
		public function switch_color_scheme( $url ) {
			$return = $url;
			
			$current_scheme = apply_filters('cc2_get_current_color_scheme', $this->get_current_color_scheme() );
			
			//new __debug( array('current_scheme' => $current_scheme ), 'switch_color_scheme' );
			
			if( !empty( $current_scheme ) && isset( $current_scheme['slug'] ) && $current_scheme['slug'] != 'default' ) {
				//new __debug( array('current_scheme' => $current_scheme ), 'switch_color_scheme = true' );
				
				$return = apply_filters('cc2_set_style_url', $current_scheme['style_url'] );
			}
			
			return $return;
		}
		
	
		function get_current_color_scheme( $default = false ) {
			$return = $default;
			
			$current_scheme_slug = get_theme_mod('color_scheme', $default );
			
			
			
			if( !empty( $current_scheme_slug ) && defined('CC2_THEME_CONFIG' ) ) {
				
				$config = maybe_unserialize( CC2_THEME_CONFIG );
				
				
				if( !empty( $config['color_schemes'] ) && !empty( $config['color_schemes'][ $current_scheme_slug ] ) ) {
					
					$return = $config['color_schemes'][ $current_scheme_slug ];
					
					
					if( empty( $return['slug'] ) ) {
						$return['slug'] = $current_scheme_slug;
					}
					
					
					if( empty( $return['output_file'] ) != false ) {
						$strOutputFile = $current_scheme_slug . '.css';
						
						if( !empty( $return['file'] ) ) {
							$strOutputFile = basename( $return['file'], 'less' ) . '.css';
						}
						
						$return['output_file'] = $strOutputFile;
					}
					
					// check paths
					
					/**
					 * NOTE: A (do-)while-loop might be the better choice. Avoids nasty breaks.
					 */
					
					foreach( $this->arrKnownLocations as $strPath => $strURL ) { 
						
						if( file_exists( $strPath . $return['output_file'] ) ) {
							$return['style_path'] = $strPath . $return['output_file'];
							$return['style_url'] = $strURL . $return['output_file'];
							break;
						}
					}
					
				}
				
			}
			
			return $return;
		}
	}
}
