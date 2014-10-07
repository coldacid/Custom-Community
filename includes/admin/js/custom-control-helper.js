/**
 * cc2 Customizer Helper library
 * NOTE: We are operating INSIDE the customizer UI (NOT the preview frame) - that nice sidebar to your left .. ;)
 * 
 * @author Fabian Wolf
 * @package cc2
 * @since 2.0
 */
( function( exports, $ ) {
	var api = top.wp;
	
	/**
	 * Actions persued when chaning the navigation background / skin settings (dark / light)
	 */
	 
	
	
	
	
	/**
	 * Custom Color Control
	 * 
	 * @see http://shibashake.com/wordpress-theme/wordpress-theme-customizer-javascript-interface
	 */

	jQuery(document).on('click', '.color-picker-set-transparent', function(elem) {
		//console.log(  );
		var myId = jQuery(this).parents('li.customize-control').attr('id'),
			myControlId = myId.replace('customize-control-', '');
		
		//console.log( myControlId );
		
		//console.log( top.wp.customize.control.instance(  );
		
		mySetColor( myControlId, 'transparent' );
		/*var parentContainer = jQuery(this).parents('.cc2-customize-control-content');
		console.log ( 'parentContainer parent parent', parentContainer.parent().parent(), parentContainer.parents('li.customize-control') );
		*/
		
	});

	/**
	 * Fixing the background color / image refresh
	 */

	/*
	jQuery(document).on('click', '#customize-control-background_color .color-picker-hex', function( elem ) {
		window.console.log( 'background_color changed', jQuery(this).val() );
	} );*/

	/**
	 * Hide slideshow options if post slideshow + display notice about different post slider types
	 */
	 
	jQuery(document).on('change', '#customize-control-cc_slideshow_template select', function(e ) {
		var currentOption = jQuery(this).prop('options')[ jQuery(this).prop('selectedIndex')],
			currentOptionText = jQuery( currentOption ).text();
		
			
		
		//window.console.info( 'optionsText', jQuery( currentOption).text(), 'vs', currentOptionText );
		//jQuery( jQuery('#customize-control-cc_slideshow_template select').prop('options')[ jQuery('#customize-control-cc_slideshow_template select').prop('selectedIndex') ] ).val()
		
		
	});

	/*jQuery(document).on('click', '#customize-control-background_color', function( elem ) {
		var myControlId = this.attr('id').replace('customize-control-', '' );
		
		
	});*/
	
	function getColorPickerColor( cname ) {
		var api = top.wp.customize;
		
		var control = api.control.instance(cname);
			picker = control.container.find('.color-picker-hex');
			 
		picker.val( control.setting() ); return;
	}

	function mySetColor(cname, newColor) {
		var api = top.wp.customize;
		
		var control = api.control.instance(cname);
			picker = control.container.find('.color-picker-hex');
			 
		control.setting.set(newColor);
		picker.val( control.setting() ); return;
	}
} )( top.wp, jQuery );
