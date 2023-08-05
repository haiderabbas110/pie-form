
<?php
defined( 'ABSPATH' ) || exit;

global $current_section, $current_tab;

$tabs = apply_filters( 'pie_forms_blockuser_tabs_array', array() );


$tab_exists        = isset( $tabs[ $current_tab ] ) || has_action( 'pie_forms_sections_' . $current_tab ) || has_action( 'pie_forms_settings_' . $current_tab );
$current_tab_label = isset( $tabs[ $current_tab ] ) ? $tabs[ $current_tab ] : '';


?>
<div class="wrap pie-forms">
	<form method="<?php echo esc_attr( apply_filters( 'pie_forms_settings_form_method_tab_' . $current_tab, 'post' ) ); ?>" id="mainform" action="" enctype="multipart/form-data">
		<nav class="nav-tab-wrapper pf-nav-tab-wrapper">
			<?php
			foreach ( $tabs as $slug => $label ) {
				echo '<a href="' . esc_url( admin_url( 'admin.php?page=pf-blockuser&tab=' . esc_attr( $slug ) ) ) . '" class="nav-tab ' . ( $current_tab === $slug ? 'nav-tab-active' : '' ) .' tab_'.esc_attr($slug).'"><span class="pf-nav-icon ' . esc_attr( $slug ) . '"></span>' . esc_html( $label ) . '</a>';
			}
			do_action( 'pie_forms_blockuser_tabs' );
			?>
		</nav>
		<?php 
			?>
			<div class="pie-forms-blockuser" id='<?php echo esc_attr($current_tab); ?>'>
			<?php do_action( 'pie_forms_upgrade_to_premium' ); ?>
			<h1 class="blockuser-page-heading"><?php echo esc_html( $current_tab_label ); ?></h1>
				<?php
					do_action( 'pie_forms_sections_' . $current_tab );

					PFORM_Admin_Settings::show_messages();
					
					do_action( 'pie_forms_settings_' . $current_tab );
				?>	
				<div class="upgrade-to-pro-banner"><a href="https://pieforms.com/plan-and-pricing/?utm_source=admindashboard&utm_medium=adminfeatures&utm_campaign=blockusers" target="_blank"><img src="<?php echo esc_url(plugins_url( 'assets/images/builder/upgrade-to-premium.jpg', dirname(__DIR__) ))  ?>" alt="upgrade-to-premium"></a></div>
			</div>
			

		<?php 
		 ?>	
	<?php  ?>
	</form>
	<?php  ?>
</div>
