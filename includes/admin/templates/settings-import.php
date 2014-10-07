<?php
/**
 * cc2 Template: Export settings
 * @author Fabian Wolf
 * @package cc2
 * @since 2.0
 */

?>

	<div class="backup-settings settings-export">
		<p><?php echo sprintf( __('Import theme settings from the %s.', 'cc2'), $strFormatOfChoice ); ?></p>
		
	<?php if( $available_formats ) : ?>
		<p><label><?php _e('Select import format:', 'cc2'); ?>
			<select name="import_format">
		<?php foreach( $available_formats as $strFormat => $strFormatLabel ) : ?>
			<option value="<?php echo $strFormat; ?>"><?php echo $strFormatLabel; ?></option>
		<?php endforeach; ?>
			</select>
		</label></p>
		
	<?php endif; ?>
	
<?php if( empty($import_result) ) : ?>
		<p class="description"><label for="field-import-data"><?php _e('Copy + paste the data you want to import into the following text field:', 'cc2'); ?></label></p>
		
		<p><textarea class="large-text" rows="10" cols="50" name="import_data" id="field-import-data"></textarea>
	
		
	<?php 
	/**
	  * Set submit button with name = backup_action and value = import
	  * NOTE: When clicking this button, only THIS value is being sent, other submit button values are IGNORED.
	  */
	
	proper_submit_button( __('Start Import', 'cc2'), 'primary large', 'backup_action', true, array('value' => 'import', 'id' => 'init-settings-import')  ); ?>
<?php else : ?>

	<?php if( !empty( $import_result ) && is_array( $import_result ) ) : ?>
		<p><?php echo sprintf(__('%s the following data:', 'cc2'), 'Imported'); ?></p>
		
		<ul class="import-messages">
		<?php foreach( $import_result as $importedItem => $arrItemData ) : ?>
			<li><?php echo sprintf( ( is_int($importedItem) ? '#%d - ' : '%s: ' ), (is_int( $importedItem) ? $importedItem+1 : $importedItem ) ) . $arrItemData['title']; ?></li>
			<!-- <?php echo $importedItem; ?>: <?php print_r( $arrItemData); ?>  -->
		<?php endforeach; ?>
		</ul>
		
	<?php else: ?>
		
	<?php endif; ?>
		
<?php endif; ?>
			
	</div>
