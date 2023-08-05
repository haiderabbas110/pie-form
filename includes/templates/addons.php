<?php
/**
 * Admin View: Page - Addons
 */

defined( 'ABSPATH' ) || exit;
$addons          = array();
$category = isset( $_GET['section'] ) ? sanitize_text_field( wp_unslash( $_GET['section'] ) ) : null;
$addons   = PFORM_Admin_Addons::get_extension_data();

?>
<div class="wrap pf_addons_wrap">
	<h1 class="pf-heading-inline"><?php esc_html_e( 'Add-ons', 'pie-forms' ); ?></h1>
	<p class="pf-heading-inline"></p>
	<?php // if ( $sections ) : ?>
	<?php
		$action =	"";
		$active	= 'class="selected"';
		if(isset($_GET['tab']))
			$action	= sanitize_text_field($_GET['tab']);
		
	
		$images_url = Pie_Forms::$url . 'assets/images/addons/';
		$all_plugins = get_plugins();
		?>
		<div class="sib-products-container">
			<div class="sib-products">
		<?php			
		$genetech_products = PFORM_Admin_Addons::get_extension_data();
		if (isset($genetech_products)){

			foreach ( $genetech_products as $plugin => $details ) :
				$plugin_data = Pie_Forms()->core()->get_addons_plugin_data( $plugin, $details, $all_plugins );
				?>
				<div class="sib-product-container">
					<div class="sib-product">
						<div class="sib-product-detail">
							<img src="<?php echo esc_url($images_url.esc_html( $plugin_data['details']->icon) )?>">
								<h3><?php echo esc_html( $plugin_data['details']->name )?></h3>
							<p><?php echo wp_kses_post( $plugin_data['details']->desc )?></p>
						</div>	
						<div class="sib-product-action">
							<?php
							if($plugin_data['status_class'] !== 'status-download'){
							?>
							<div class="product-status">
								<strong>Status : 
								<span class="status-label <?php echo esc_attr( $plugin_data['status_class'] ) ?>"><?php echo wp_kses_post( $plugin_data['status_text'] ) ?></span>
								</span> 
								</strong>
							</div>	
							<?php
							}
							?>
							<div class="product-action">
							<?php
							if($plugin_data['status_class'] !== 'status-download'){
								?>
								<button class="<?php echo esc_attr( $plugin_data['action_class'] ) ?>" data-plugin="<?php echo esc_attr( $plugin_data['plugin_src'] ) ?>" data-type="plugin">
									<?php echo wp_kses_post( $plugin_data['action_text'] ); ?>
								</button>
							<?php
								}else{
									?>
									<a class="<?php echo esc_attr( $plugin_data['action_class'] ) ?>" target="_blank" href="<?php echo esc_url( $plugin_data['details']->plugin_src ) ?>" data-type="plugin">
										<?php echo wp_kses_post( $plugin_data['action_text'] ); ?>
									</a>
									<?php
								}
								?>
							</div>
							<?php
							if($plugin_data['status_class'] === 'status-download'){
							?>
							<div class="product-status">
								<strong> 
								<span class="status-label <?php echo esc_attr( $plugin_data['status_class'] ) ?>"><?php echo wp_kses_post(  $plugin_data['details']->price ) ?></span>
								</span> 
								</strong>
							</div>	
							<?php
							}
							?>
						</div>	
					</div>	
				</div>	
				<?php
			endforeach;
		}
			?>
		</div>	
	</div>	
</div>
