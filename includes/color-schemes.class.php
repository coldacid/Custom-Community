<?php
/**
 * Implements color schemes
 * 
 * @author Fabian Wolf
 * @package cc2
 * @since 2.0.1
 */

if( !class_exists( 'cc2_ColorSchemes' ) ) {
	class cc2_ColorSchemes {
		function __construct() {
			add_filter( 'cc2_style_css', array( $this, 'switch_color_scheme') );
			
		}
	
		public function switch_color_scheme( $url ) {
			$return = $url;
			
			$current_scheme = apply_filters('cc2_get_current_color_scheme', $this->get_current_color_scheme() );
			
			if( !empty( $current_scheme ) && isset( $current_scheme['slug'] ) && $current_scheme['slug'] != 'default' && file_exists( $current_scheme['path'] ) ) {
				$return = $current_scheme['path'];
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
					
					if( empty( $return['file'] ) != false ) {
						$return['file'] = $current_theme_slug . '.css';
					}
							
					$return['path'] = get_template_directory() . '/includes/schemes/' . $return['file'];
				}
				
			}
			
			return $return;
		}
	}
}
