
<?php
defined( 'ABSPATH' ) || exit;

global $current_section, $current_tab;

$tabs = apply_filters( 'pie_forms_settings_tabs_array', array() );


$tab_exists        = isset( $tabs[ $current_tab ] ) || has_action( 'pie_forms_sections_' . $current_tab ) || has_action( 'pie_forms_settings_' . $current_tab );
$current_tab_label = isset( $tabs[ $current_tab ] ) ? $tabs[ $current_tab ] : '';
if ( ! $tab_exists ) {
	wp_safe_redirect( admin_url( 'admin.php?page=pf-settings' ) );
	exit;
}
do_action( 'pie_forms_admin_pages_before_content' ); 
?>
<div class="wrap pie-forms">
	<form method="<?php echo esc_attr( apply_filters( 'pie_forms_settings_form_method_tab_' . $current_tab, 'post' ) ); ?>" id="mainform" action="" enctype="multipart/form-data">
		<nav class="nav-tab-wrapper pf-nav-tab-wrapper">
			<?php
			foreach ( $tabs as $slug => $label ) {
				echo '<a href="' . esc_url( admin_url( 'admin.php?page=pf-settings&tab=' . esc_attr( $slug ) ) ) . '" class="nav-tab ' . ( $current_tab === $slug ? 'nav-tab-active' : '' ) .' tab_'.esc_attr($slug).'"><span class="pf-nav-icon ' . esc_attr( $slug ) . '"></span>' . esc_html( $label ) . '</a>';
			}
			do_action( 'pie_forms_settings_tabs' );
			?>
		</nav>
		<?php 
			?>
			<div class="pie-forms-settings" id='<?php echo esc_attr($current_tab); ?>'>
			<h1 class="setting-page-heading"><?php echo esc_html( $current_tab_label ); ?></h1>
				<?php
					do_action( 'pie_forms_sections_' . $current_tab );

					PFORM_Admin_Settings::show_messages();
					
					do_action( 'pie_forms_settings_' . $current_tab );
				
				if($current_tab != "licence" && $current_tab != "integrations" ) {
				?>
				<p class="submit">
						<button name="save" class="pie-forms-btn pie-forms-btn-primary pie-forms-save-button" type="submit" value="<?php esc_attr_e( 'Save Settings', 'pie-forms' ); ?>"><?php _e( 'Save Settings', 'pie-forms' ); ?></button>
					<?php  ?>
					<?php wp_nonce_field( 'pie-forms-settings' ); ?>
				</p>
				<?php
				}	?>
			</div>
		<?php 
		 ?>	
	<?php  ?>
	</form>
	<?php  ?>
</div>
