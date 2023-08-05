
<?php
defined( 'ABSPATH' ) || exit;

global $current_section, $current_tab, $errors;

?>
<div class="smarttranslation-page-heading-icon">
	<h1 class="smarttranslation-page-heading"><?php echo esc_html( 'Smart Auto Translation' ); ?></h1>
</div>

<?php if(is_plugin_active('pie-forms-for-wp-smart-translation/pie-forms-for-wp-smart-translation.php') ){?>
<div class="wrap pie-smarttranslation">
	<form method="<?php echo esc_attr( apply_filters( 'pie_forms_smarttranslation_form_method_tab_' . $current_tab, 'post' ) ); ?>" id="mainform" action="" enctype="multipart/form-data">
		
		<?php 
        	?>
			
			<div class="pie-forms-smarttranslation" id='<?php echo esc_attr($current_tab); ?>'>
				<?php
					do_action( 'pie_forms_sections_' . $current_tab );

					PFORM_Admin_Settings::show_messages();
					
					do_action( 'pie_forms_settings_' . $current_tab );

				?>

				<p class="submit">
						<button name="save" class="pie-forms-btn pie-forms-btn-primary pie-forms-save-button" type="submit" value="<?php esc_attr_e( 'save settings', 'pie-forms' ); ?>"><?php esc_html_e( 'Save Settings', 'pie-forms' ); ?></button>
					<?php  ?>
					<?php wp_nonce_field( 'pie-forms-settings' ); ?>
				</p>	
			</div>
		<?php 
		 ?>	
	<?php  ?>
	</form>
	<?php  ?>
	<?php }else{
	echo "<img src='".esc_url(Pie_forms::$url)."assets/images/smart-translation-addon.png' alt='Smart Translation Addon' > ";
	$plugins = 'https://store.genetech.co/checkout/?add-to-cart=10205';
	echo sprintf(__('<div class="pf-activate-plugin-notice">Get Smart Translation Addon for free. <a href="%s" target="_blank">Click here</a></div>'), esc_url($plugins));

} ?>

</div>
