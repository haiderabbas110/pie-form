<?php

defined( 'ABSPATH' ) || exit;

$current_tab     = empty( $_GET['tab'] ) ? 'aboutus' : sanitize_title( wp_unslash( $_GET['tab'] ) ); 

$tabs = apply_filters( 'pie_forms_aboutus_tabs_array', array() );

$tab_exists        = isset( $tabs[ $current_tab ] ) || has_action( 'pie_forms_sections_' . $current_tab ) || has_action( 'pie_forms_aboutus_' . $current_tab );
$current_tab_label = isset( $tabs[ $current_tab ] ) ? $tabs[ $current_tab ] : '';

do_action( 'pie_forms_admin_pages_before_content' ); 
?>

<div class="wrap pie-forms">
	
		<nav class="nav-tab-wrapper pf-nav-tab-wrapper">
			<?php
			foreach ( $tabs as $slug => $label ) {
				echo '<a href="' . esc_url( admin_url( 'admin.php?page=pf-aboutus&tab=' . esc_attr( $slug ) ) ) . '" class="nav-tab ' . ( $current_tab === $slug ? 'nav-tab-active' : '' ) .' tab_'.esc_attr($slug).'"><span class="pf-nav-icon ' . esc_attr( $slug ) . '"></span>' . esc_html( $label ) . '</a>';
			}
			?>
		</nav>
		<?php 
			?>
			<div class="pie-forms-aboutus" id='<?php echo esc_attr($current_tab); ?>'>
				<?php
					do_action( 'pie_forms_sections_' . $current_tab );

					do_action( 'pie_forms_aboutus_' . $current_tab );
				?>
			</div>
		<?php 
		 ?>	
	<?php  ?>
</div>
