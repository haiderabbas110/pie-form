<?php

defined( 'ABSPATH' ) || exit;

/**
 * PFORM_Aboutus_Aboutus.
 */
class PFORM_Aboutus_Aboutus extends PFORM_Abstracts_Aboutus {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'aboutus';
		$this->label = esc_html__( 'About Us', 'pie-forms' );
		parent::__construct();
		
	}

	public function get_output() {
		$action =	"";
		$active	= 'class="selected"';
		if(isset($_GET['tab']))
			$action	= sanitize_text_field($_GET['tab']);
		
		$can_install_plugins = true;
		if ( ! current_user_can( 'install_plugins' ) ) {
			$can_install_plugins = false;
		}
		$images_url = Pie_Forms::$url . 'assets/images/aboutus/';
		$all_plugins = get_plugins();
	
		$genetech_products = array(
	
			'vc-addons-by-bit14/bit14-vc-addons.php' => array(
				'icon'  => $images_url . 'vc-addons-by-bit14.png',
				'name'  => esc_html__( 'PB Add-ons for WP Bakery', 'pie-forms' ),
				'desc'  => esc_html__( 'Build your website with premium quality All-in-One Web elements for WPBakery Page Builder.', 'pie-forms' ),
				'wporg' => 'https://wordpress.org/plugins/vc-addons-by-bit14/',
				'url'   => 'https://downloads.wordpress.org/plugin/vc-addons-by-bit14.zip',
			),
	
			'pie-register/pie-register.php' => array(
				'icon'  => $images_url . 'pie-register.png',
				'name'  => esc_html__( 'Pie Register', 'pie-forms' ),
				'desc'  => esc_html__( 'Your custom registration form builder plugin for WordPress websites to help you create simple to most robust registration forms in minutes, without a single line of code!', 'pie-forms' ),
				'wporg' => 'https://wordpress.org/plugins/pie-register/',
				'url'   => 'https://downloads.wordpress.org/plugin/pie-register.zip',
			),
		); 
		$data = "<div id='container'  class='pf-admin aboutus-page-admin'>";
			$data .= "<div class='pane'>";
				$data .= "<div id='tab2' class='tab-content'>";
					$data .= "<div class='addons-container-section'>";
						$data .= "<div class='content-row'>";
							$data .= "<div class='about-content'>";
								$data .= "<h3 class='welcome-to-pr'>Welcome to Pie Forms, the Easiest Drag and Drop WordPress Form Plugin. Your custom Drag and Drop Form Builder with a user-friendly interface, built-in ready-to-use templates, and various Form Field options to Create Advanced Forms without a single line of Code!</h3>";
								$data .= " <p class='about-us-p'>Pie Forms are fast, flexible, and 100% responsive. The Basic and Advanced Fields in Pie Forms have everything you need for your User Data Collection. It is fully optimized to maximize your website’s efficiency and reduces the loading time. Customize and create dynamic forms using the exclusive features and add-ons in Pie Forms.</p>";
								$data .= " <h3 class='welcome-to-pr'>Resourceful links:</h3>";
								$data .= "<ul class='resourceful-links'>";
									$data .= "<li><a href='https://pieforms.com/docs-category/getting-started/?utm_source=PFplugindashboard&utm_medium=pfabouttab&utm_campaign=links' target='_blank'>Getting Started</a></li>";
									$data .= "<li><a href='https://pieforms.com/docs-category/how-to-articles/?utm_source=PFplugindashboard&utm_medium=pfabouttab&utm_campaign=links' target='_blank'>How To Article</a></li>";
									$data .= "<li><a href='https://pieforms.com/limit-and-schedule/?utm_source=PFplugindashboard&utm_medium=pfabouttab&utm_campaign=links' target='_blank'>Limit and Schedule</a></li>";
									$data .= "<li><a href='https://pieforms.com/block-users/?utm_source=PFplugindashboard&utm_medium=pfabouttab&utm_campaign=links' target='_blank'>Block Users</a></li>";
								$data .= "</ul>";
								$data .= "<p class='about-us-p genetech-resource'>Pie Forms is a product of <a class='red-anchor' href='https://www.genetechsolutions.com/?utm_source=PFplugindashboard&utm_medium=pfabouttab&utm_campaign=Genetech' target='_blank' rel='noopener noreferrer'>Genetech Solutions</a>.</p>";
								$data .= "<p class='about-us-p'>Other products by the Team include:</p>";
								$data .= "<p class='about-us-p'><a class='red-anchor' href='http://pieregister.com/?utm_source=PFplugindashboard&utm_medium=pfabouttab&utm_campaign=prfrompf' target='_blank'>Pie Register</a>, a WordPress Registration plugin to create custom registration forms with the simplest drag and drop builder.</p>";
								$data .= "<p class='about-us-p'><a class='red-anchor' href='https://pagebuilderaddons.com/?utm_source=PFplugindashboard&utm_medium=pfabouttab&utm_campaign=pbfrompf' target='_blank' rel='noopener noreferrer'>PB Add-ons for WP Bakery</a>, a collection of free and premium add-ons to build your website using WP Bakery.</p>";
							$data .= "</div>";
							$data .= "<div class='about-links'>";
								$data .= "<div class='about-pr-docs'>";
									$data .= "<a href='https://pieforms.com/limit-and-schedule/?utm_source=plugindashboard&amp;utm_medium=abouttab&amp;utm_campaign=documentlink' target='_blank'>
                                    <img src=".esc_html($images_url)."limit-and-schedule.png alt='Limit and Schedule'></a>";
									$data .= "<a href='https://pieforms.com/block-users/?utm_source=plugindashboard&amp;utm_medium=abouttab&amp;utm_campaign=documentlink' target='_blank'>
                                    <img src=".esc_html($images_url)."block-users.png alt='Block Users'></a>";
								$data .= '</div>';	
							$data .= '</div>';	
						$data .= '</div>';	
					$data .= '</div>';	
				$data .= '</div>';					
			$data .= '</div>';			
			$data .= '<div class="pieform-sib-products">';			
				$data .= '<div class="sib-products-container">';
					$data .= '<div class="sib-products">';		
					foreach ( $genetech_products as $plugin => $details ) :
						$plugin_data = Pie_Forms()->core()->get_about_plugin_data( $plugin, $details, $all_plugins );
						$data .= '<div class="sib-product-container">';
							$data .= '<div class="sib-product">';
								$data .= '<div class="sib-product-detail">';
									$data .= '<img src="'.esc_url( $plugin_data['details']['icon'] ).'">';
										$data .= '<h5>'.esc_html( $plugin_data['details']['name'] ).'</h5>';
									$data .= '<p>'.wp_kses_post( $plugin_data['details']['desc'] ).'</p>';
								$data .= '</div>';	
								$data .= '<div class="sib-product-action">';
									$data .= '<div class="product-status">';
										$data .= '<strong>Status : ';
										$data .= '<span class="status-label ' . esc_attr( $plugin_data['status_class'] ) . '">' . wp_kses_post( $plugin_data['status_text'] ) . '</span>';
										$data .= '</span>';
									
										$data .= '</strong>';
									$data .= '</div>';	
									$data .= '<div class="product-action">';
									if ( $can_install_plugins ){
										$data .= '<button class="'.esc_attr( $plugin_data['action_class'] ).'" data-plugin="'. esc_attr( $plugin_data['plugin_src'] ).'" data-type="plugin">';
											$data .= wp_kses_post( $plugin_data['action_text'] );
										$data .= '</button>';
									}else{
										$data .= '<a href="'.esc_url( $details['wporg'] ).'?>" target="_blank" rel="noopener noreferrer">';
										$data .= esc_html_e( 'WordPress.org', 'pie-forms' );
										$data .= '<span aria-hidden="true" class="dashicons dashicons-external"></span>';
										$data .= '</a>';
									}
									$data .= '</div>';
								$data .= '</div>';	
							$data .= '</div>';	
						$data .= '</div>';	
					endforeach;
					$data .= '</div>';	
				$data .= '</div>';	
			$data .= '</div>';	
		$data .= '</div>';

		return apply_filters( 'pie_forms_get_aboutus_output_' . $this->id, $data );
		
	}

}