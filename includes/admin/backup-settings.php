<?php
/**
 * Export / Import Settings
 * 
 * @author Fabian Wolf
 * @package cc2
 * @since 2.0r1
 */



if( !class_exists('cc2_Admin_ExportImport') ) :

/**
 * NOTE: Seperate init, because we are also using WP ajax calls
 */

class cc2_Admin_ExportImport {
	var $classPrefix = 'cc2_export_import_',
		$className = 'Backup & Reset Settings',
		$optionName = 'cc2_export_import_settings',
		$arrKnownFormats = array('json' => 'JSON');
		
	/**
	 * Plugin instance.
	 *
	 * @see get_instance()
	 * @type object
	 */
	protected static $instance = NULL;
		
	/**
	 * Implements Factory pattern
	 * Strongly inspired by WP Maintenance Mode of Frank Bueltge ^_^ (@link https://github.com/bueltge/WP-Maintenance-Mode)
	 * 
	 * Access this plugins working instance
	 *
	 * @wp-hook after_setup_theme
	 * @return object of this class
	 */
	public static function get_instance() {

		NULL === self::$instance and self::$instance = new self;

		return self::$instance;
	}
	
	function __construct() {
		// init variables
		$this->arrDataItems =  array(
			'theme_mods' => array(
				'title' => __('Customizer Settings', 'cc2' ),
				'option_name' => 'theme_mods_cc2',
			),
			'settings' => array(
				'title' => __('Advanced Settings', 'cc2' ),
				'option_name' => 'cc2_advanced_settings',
			),
			'slideshows' => array(
				'title' => __('Slideshows', 'cc2' ),
				'option_name' => 'cc2_slideshows',
			),
		);
		
		// register required settings, sections etc.
		add_action( 'admin_init', array( $this, 'register_admin_settings' ), 11 );
		
		add_action('admin_enqueue_scripts', array( $this, 'init_admin_js' ) );

		// save settings
		/*
		add_action( 'wp_ajax_' . $this->classPrefix . 'save', array( $this, 'update_settings') );
		add_action( 'wp_ajax_nopriv_' . $this->classPrefix . 'save', array( $this, 'update_settings') );*/
		
		
	}
	
	



	/*
	 * Register the admin settings
	 * 
	 * @author Fabian Wolf
	 * @author Sven Lehnert
	 * @package cc2  
	 * @since 2.0
	 */ 
	 

	function register_admin_settings() {
		
		$strSettingsGroup = $this->classPrefix . 'page';
		$strSettingsPage = $strSettingsGroup;
		$strGlobalSection = 'section_general';
		
		//new __debug( array( 'settings_group' => $strSettingsGroup, 'settings_page' => $strSettingsPage), 'cc2 custom styling: register_admin_settings fires');
		register_setting( $strSettingsGroup, $strSettingsGroup );
    
		// Settings fields and sections
		
		
		/*add_settings_section(
			'section_general', 
			'', 
			array( $this, 'admin_setting_general' ), // method callback 
			$strSettingsPage
		);*/
		
		
		add_settings_section(
			$strGlobalSection,
			'',
			'',
			$strSettingsPage
		);
		
			// add the reset confirmation to the TOP
			if( isset( $_POST['backup_action']) && $_POST['backup_action'] == 'reset' ) {
				
				if( empty( $_POST['settings_reset_confirm'] ) || $_POST['settings_reset_confirm'] != 'yes' ) {
					add_settings_field(
						$this->classPrefix . 'reset_confirm',
						'<strong class="delete">Confirm reset</strong>',
						array( $this, 'admin_setting_reset_confirm' ), /* method callback  */
						$strSettingsPage,
						'section_general'
					);
				}
			}
		
			add_settings_field(
				$this->classPrefix . 'export',
				'<strong>Export settings</strong>',
				array( $this, 'admin_setting_export' ), /** method callback */
				$strSettingsPage,
				$strGlobalSection
			);

			
			add_settings_field(
				$this->classPrefix . 'import',
				'<strong>Import settings</strong>',
				array( $this, 'admin_setting_import' ), /* method callback */
				$strSettingsPage,
				$strGlobalSection
			);
			
			
			add_settings_field(
				$this->classPrefix . 'reset',
				'<strong class="delete">Reset settings</strong>',
				array( $this, 'admin_setting_reset' ), /* method callback  */
				$strSettingsPage,
				'section_general'
			);
			

			/*add_settings_field(
				$this->classPrefix . 'bootstrap',
				'<strong>Bootstrap Features</strong>',
				array( $this, 'admin_setting_bootstrap_features' ), // method callback 
				$strSettingsPage,
				'section_general'
			);*/
	

	}
	
	/**
	 * Important notice on top of the screen
	 * 
	 * @author Sven Lehnert
	 * @package cc2  
	 * @since 2.0
	 */ 
	 
	
	function admin_setting_general() {
		
		//new __debug( $_POST['backup_action'], 'backup action' );
		
	}
	
	
	/**
	 * Resets all (!) settings
	 */
	function admin_setting_reset() {
		$do_reset = false;
		$data_items = $this->arrDataItems;
		
		//new __debug( $data_items, 'data items' );
		
		$strTemplatePath = get_template_directory() . '/includes/admin/templates/%s';
		$strLoadTemplate = 'settings-reset.php';
		
		
		if( !empty($_POST['backup_action'] ) && $_POST['backup_action'] == 'reset' ) {
			if( !empty($_POST['settings_reset_confirm'] ) && $_POST['settings_reset_confirm'] == 'yes' ) {
				$do_reset = true;
			}
		}
		
		if( $do_reset != false ) {
			// defaults
			$arrResetSettings = $data_items;
			
			// which settings?
			if( !empty( $_POST['reset_items'] ) && is_array( $_POST['reset_items'] ) ) {
				$strResetItems = implode(' || ', $_POST['reset_items'] );
				foreach( $this->arrDataItems as $strDataItemID => $arrItemAttributes ) {
					if( strpos( $strResetItems, $strDataItemID ) === false ) {
						unset( $arrResetSettings[ $strDataItemID ] );
					}
				}
			}			
			
			// do it
			if( !empty( $arrResetSettings ) ) {
				new __debug( $arrResetSettings, '_reset_settings() would fire ..' );
				//$reset_result = $this->_reset_settings( $arrResetSettings );
			}
			
		}
		
		include( sprintf( $strTemplatePath, $strLoadTemplate ) );
		
	}
	
	function admin_setting_reset_confirm() {
		$data_items = $this->arrDataItems;
		
		if( !empty( $_POST['reset_items'] ) ) {
			$reset_items = $_POST['reset_items'];
		}
		
	
		include ( get_template_directory() . '/includes/admin/templates/settings-reset-confirmation.php' );
	}
	
	protected function _reset_settings( $arrSettings = false ) {
		$return = false;
		if( empty( $arrSettings ) ) { // early abort
			return $return;
		}
		
		foreach( $arrSettings as $strDataItemID => $arrItemAttributes ) {
			
			$resetStatus = delete_option( $arrItemAttributes['option_name'] );
			
			$arrReturn[ $strDataItemID ] = array(
				'title' => $arrItemAttributes['title'],
				'status' => $resetStatus,
			);
			
			unset( $resetStatus );
		}
		
		if( !empty( $arrReturn ) ) {
			$return = $arrReturn;
		}
		
		
		return $return;
	}
	
	
	/**
	 * TODO: Stop automatic export, to avoid import data being overriden by the export data
	 */
	
	function admin_setting_export() {
		$available_formats = $this->arrKnownFormats;
		$data_items = $this->arrDataItems;
		$do_export = false;
		//$strExportFormat = 'json';
		
		
		//new __debug( $_POST['backup_action'], 'backup action ($_POST)' );
		if( isset( $_POST['backup_action'] ) && $_POST['backup_action'] == 'export' ) {
			$do_export = true;
		}
		
		
		if( isset($_POST['export_format'] ) && array_key_exists( $_POST['export_format'], $this->arrKnownFormats ) ) {
			$strExportFormat = $_POST['export_format'];
		}

		
		// do the export
		if( $do_export != false && isset( $strExportFormat ) && !empty( $_POST['export_items'] ) ) {
			
		
		
			// gather data
			// if items are set .. use em
			if( !empty( $_POST['export_items'] )  ) {
				//new __debug( $_POST['export_items'], 'export_items' );
				
				$export_data = $this->compile_export_data( $_POST['export_items'] );
			} else {
				$export_data = $this->compile_export_data();
			}
			
			// convert data to correct format
			$export_data = $this->prepare_data( $export_data, $strExportFormat );
			
		}

		
		if( !empty( $this->arrKnownFormats ) ) { // will change in 2.1 (ie. from empty to >= 1)
			if( sizeof( $this->arrKnownFormats ) > 1 ) {
				$strFormatOfChoice = 'format of your choice';
			} else {
				$strFormatOfChoice = ' '. reset( $this->arrKnownFormats )  . ' format';
			}
		}
		
		// load template
		include( get_template_directory() . '/includes/admin/templates/settings-export.php' );
	}
	
	function admin_setting_import() {
		// set up some variables
		$strImportFormat = 'json';
		$available_formats = $this->arrKnownFormats;
		
		$do_import = false;
		
		//new __debug( $_POST['backup_action'], 'backup action ($_POST)' );
		
		if( !empty( $this->arrKnownFormats ) ) { // will change in 2.1 (ie. from empty to >= 1)
			
			if( sizeof( $this->arrKnownFormats ) > 1 ) {
				$strFormatOfChoice = 'format of your choice';
			} else {
				$strFormatOfChoice = ' '. reset( $this->arrKnownFormats )  . ' format';
			}
		}
		
		//$strExportFormat = 'json';
		
		
		//new __debug( array( 'backup action ($_POST)' =>  $_POST['backup_action'], 'import_format' => $_POST['import_format'], 'import_data' => $_POST['import_data'] ) );
		if( isset( $_POST['backup_action'] ) && $_POST['backup_action'] == 'import' ) {
			$do_import = true;
		}
		
		
		
		if( !empty($_POST['import_data'] ) && $do_import != false ) {
			
			if( !empty( $_POST['import_format'] ) && isset( $this->arrKnownFormats[$_POST['import_format'] ] ) != false ) {
				$strImportFormat = $_POST['import_format'];
			}
			new __debug( array( 'import_format' => $strImportFormat, 'import_data' => $_POST['import_data'] ) );
			
			$result = $this->import_data( $_POST['import_data'], $strImportFormat );
			
			if( $result != false && is_array( $result ) ) {
				$import_result = $result;
			}
		}
		
		// fetch concerned tab / template
		include( get_template_directory() . '/includes/admin/templates/settings-import.php' );
	}
	

	/**
	 * Export settings
	 */ 
	
	
	/**
	 * Bundle all settings
	 */
	 
	public function compile_export_data( $arrDataItems = array() ) {
		$return = false;
		
		if( empty( $arrDataItems ) ) {
			$arrDataItems = array('settings', 'theme_mods', 'slideshows');
		}
		
		$strDataItems = implode('||', $arrDataItems ); // faster lookup using strings
		
		foreach( $this->arrDataItems as $strDataItemID => $arrItemAttributes ) {
			if( stripos( $strDataItems, $strDataItemID ) !== false ) {
				$arrReturn[ $strDataItemID ] = get_option( $arrItemAttributes['option_name'], false );
			}
		}
		
		/*
		// settings
		if( stripos( $strDataItems, 'settings') !== false ) { 
			$arrReturn['advanced_settings'] = get_option( 'cc2_advanced_settings', false );
		}
		
		// theme mods: options => theme_mods_cc2
		if( stripos( $strDataItems, 'theme_mods') !== false ) { 
			$arrReturn['theme_mods'] = get_option( 'theme_mods_cc2', false );
		}
		
		// slideshows
		if( stripos( $strDataItems, 'slideshows') !== false ) { 
			$arrReturn['slideshows'] = get_option('cc_slider_options', false );
		}*/
		
		if( !empty( $arrReturn ) ) {
			$return = $arrReturn;
		}
		
		return $return;
	}
	
	public function prepare_data( $data, $strFormat = 'json' ) {
		$return = false;
	
		if( !empty( $data ) && is_array( $data ) ) {
	
			switch( strtolower($strFormat) ) {
				case 'php': // simple serializing
					$return = maybe_serialize( $data );
				
				case 'xml':
					// fall-through for now
				case 'wpxml':
					// fall-through for now
				case 'csv':
					// fall-through for now
				case 'json':
				default:
					// convert to json
					if( defined('JSON_PRETTY_PRINT' ) ) {
						$return = json_encode( $data, JSON_PRETTY_PRINT );
					} else {
						$return = str_replace('\n', '', json_encode( $data ) );
					}
					break;
			}
		}
		
		return $return;
	}
	
	/**
	 * Import settings
	 */

	public function import_data( $data, $type = 'json' ) {
		$import_data = false;
		$return = false;
		
		if( !empty( $data ) ) {
		
			$cleaned_data = stripslashes( $data );
		
			switch( $type ) {
				case 'php':
					$import_data = maybe_unserialize( $cleaned_data );
					break;
				
				case 'json':
				default:
					$import_data = json_decode( $cleaned_data , true );
					break;
			}
		}
		
		// note: just for testing purposes
		//$opt_prefix = 'test_';
		
		//new __debug( $import_data, 'import_data' );
		
		if( !empty( $import_data ) && is_array( $import_data) ) {
			// settings
			if( isset( $import_data['advanced_settings'] ) && is_array( $import_data['advanced_settings']) ) {
				//$arrReturn['advanced_settings'] = get_option( 'cc2_advanced_settings', false );
				
				$import_result = update_option( 'cc2_advanced_settings', $import_data['advanced_settings'] );
				
				
				//$import_result = true;
				
				$arrReturn[] = array( 'title' => __('Advanced settings', 'cc2' ), 'number' => sizeof( $import_data['advanced_settings']), 'test_data_result' => get_option( 'advanced_settings', false ), 'status'=> $import_result );
				unset( $import_result );
			}
			
			// theme mods: options => theme_mods_cc2
			if( stripos( $strDataItems, 'theme_mods') !== false ) { 
				
				$import_result = update_option( 'theme_mods_cc2', $import_data['theme_mods'] );
				//$import_result = true;
				
				$arrReturn[] = array( 'title' => __('Customizer options', 'cc2' ), 'number' => sizeof( $import_data['theme_mods']), 'test_data_result' => get_option( 'theme_mods_cc2', false ), 'status' => $import_result );
				unset( $import_result );
			
			}
			
			// slideshows
			if( stripos( $strDataItems, 'slideshows') !== false ) { 
				//$arrReturn['slideshows'] = get_option('cc_slider_options', false );
				
				$import_result = update_option( 'cc_slider_options', $import_data['slideshows'] );
				//$import_result = true;
				
				$arrReturn[] = array( 'title' => __('Slideshows', 'cc2' ), 'number' => sizeof( $import_data['slideshows']), 'status' => $import_result, 'test_data_result' => get_option( 'cc_slider_options', false ) );
				unset( $import_result );
				
			}
			
		}
		
		if( !empty( $arrReturn ) ) {
			//new __debug( $arrReturn, 'return of import_data()' );
			
			$return = $arrReturn;
		}
		
		return $return;
	}
	
	
	
	/**
	 * Enqueue the needed JS _for the admin screen_
	 *
	 * FIXME: Needs to be loaded ONLY when showing the admin screen, but NOWHERE ELSE!
	 * TODO: Bundle into a seperate, independent call
	 * 
	 * @package cc2
	 * @since 2.0
	 */

	function init_admin_js($hook_suffix) {
		wp_enqueue_script('consoledummy');
		
		wp_enqueue_media();
		//wp_enqueue_
		
		// prepare localizations
		$cc2_admin_translations = array(
			'i10n' => array(
				/*'advanced_settings' => array(
					'error_save_data' => __('Error: Could not save settings!', 'cc2' ),
				),*/
			
				'settings_export' => array(
					'error_missing_fields' => __('No settings to export selected - please choose at least one!', 'cc2' ),
				),
				
				'settings_import' => array(
					'error_missing_data' => __('Error: No data to import added!', 'cc2' ),
				),
			),
		);
		
		
		wp_enqueue_script('jquery'); //load tabs
		wp_enqueue_script( 'custom-header' );
		wp_enqueue_script('jquery-ui-sortable'); //load sortable

		wp_enqueue_script('jquery-ui');
		wp_enqueue_script('jquery-ui-widget');
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_script('jquery-ui-button');
		wp_enqueue_script('jquery-ui-position');
		
		wp_enqueue_style( 'wp-jquery-ui-dialog' );		

		wp_enqueue_script('jquery-ui-tabs'); //load tabs
		
		wp_localize_script('cc-admin-js', 'cc2_admin_js', $cc2_admin_translations );
		wp_enqueue_script('cc-admin-js');
			
		/**
		 * FIXME: Post-load / Async-load themekraft zendesk support assets to avoid prolonged loading times
		 */
		
		//wp_enqueue_style( 'cc_tk_zendesk_css');
		//wp_enqueue_script( 'cc_tk_zendesk_js');

	}
}

add_action( 'after_setup_theme', array( 'cc2_Admin_ExportImport', 'get_instance'), 10 );
//$none = cc2_Admin_CustomStyling::get_instance();


// endif class_exists
endif;


?>
