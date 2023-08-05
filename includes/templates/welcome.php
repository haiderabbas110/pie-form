<?php

defined( 'ABSPATH' ) || exit;

/**
 * Welcome page class.
 *
 * This page is shown when the plugin is activated.
 *
 */
class PForm_Welcome_ {

	/**
	 * Hidden welcome page slug.
	 *
	 */
	const SLUG = 'pieforms-getting-started';

	/**
	 * Primary class constructor.
	 *
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'hooks' ) );
    
    }

	/**
	 * Register all WP hooks.
	 *
	 */
	public function hooks() {

		// // If user is in admin ajax or doing cron, return.
		if ( wp_doing_ajax() || wp_doing_cron() ) {
			return;
		}

		// // If user cannot manage_options, return.
		if ( !current_user_can('manage_options') ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'register' ) );
		add_action( 'admin_head', array( $this, 'hide_menu' ) );
		add_action( 'admin_init', array( $this, 'redirect' ), 9999 );
		add_action( 'admin_init', array( $this, 'pieform_welcome' ) );
	}

	/**
	 * Register the pages to be used for the Welcome screen (and tabs).
	 *
	 * These pages will be removed from the Dashboard menu, so they will
	 * not actually show. Sneaky, sneaky.
	 *
	 */
	public function register() {
		// Getting started - shows after installation.
		add_dashboard_page(
			esc_html__( 'Welcome to PForms', 'pie-forms' ),
			esc_html__( 'Welcome to pieform', 'pie-forms' ),
			apply_filters( 'pieforms_welcome_cap', 'manage_options' ),
			self::SLUG,
			array( $this, 'output' )
		);
	}

	/**
	 * Removed the dashboard pages from the admin menu.
	 *
	 * This means the pages are still available to us, but hidden.
	 *
	 */
	public function hide_menu() {
		remove_submenu_page( 'index.php', self::SLUG );
	}

	/**
	 * Welcome screen redirect.
	 *
	 * This function checks if a new install or update has just occurred. If so,
	 * then we redirect the user to the appropriate page.
	 *
	 */
	public function redirect() {

		// Check if we should consider redirection.
		if ( ! get_transient( 'pieforms_activation_redirect' ) ) {
			return;
		}

		// If we are redirecting, clear the transient so it only happens once.
		delete_transient( 'pieforms_activation_redirect' );

		// Check option to disable welcome redirect.
		if ( get_option( 'pieforms_activation_redirect', false ) ) {
			return;
		}

		wp_safe_redirect( get_admin_url(null, 'admin.php?page='.self::SLUG) );
		exit;
	}

	/**
	 * Enqueue CSS for activation page.
	 *
	 */
	function pieform_welcome()
	{
		wp_enqueue_style( 'welcome' );
		wp_register_style('welcome', Pie_Forms::$url . 'assets/css/welcome.css', array(), Pie_Forms::VERSION );
 
	}

	/**
	 * Getting Started screen. Shows after first install.
	 *
	 */
	public function output() {

		$images_url = Pie_Forms::$url . 'assets/images/';

		?>

		<div id="pieforms-welcome">
			<div class="container">
				<div class="intro">
					<div class="pie-forms-logo">
						<img src="<?php echo esc_url($images_url.'pie-forms.png')?>" alt="<?php esc_attr_e( 'Pie Forms Logo', 'pie-forms' ); ?>">
					</div>
					<div class="block">
						<h6 class="welcome-msg"><?php esc_html_e( 'How to create your first form', 'pie-forms' ); ?></h6>
					</div>
					<div class="block">
						<div class="welcome-pr-video">
						<iframe width="833" height="441" src=<?php echo esc_url("https://www.youtube.com/embed/VylejkqOY04") ?> title="How to create your first form" frameborder="0" rel="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>						
						</div>
					</div>
					<div class="block block-white get-started-btns">
						<div class="button-wrap pieforms-clear">
							<div class="left">
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=pie-forms' ) ); ?>" class="pieforms-btn pieforms-btn-block pieforms-btn-lg pieforms-btn-red">
									<?php esc_html_e( 'Get Started', 'pie-forms' ); ?>
								</a>
							</div>
							<div class="left">
								<a href=<?php echo esc_url("https://pieforms.com/documentation/") ?>
									class="pieforms-btn pieforms-btn-block pieforms-btn-lg pieforms-btn-white" target="_blank" rel="noopener noreferrer">
									<?php esc_html_e( 'Read the Full Guide', 'pie-forms' ); ?>
								</a>
							</div>
						</div>
					</div>
				</div><!-- /.intro -->

				<div class="features">
					<div class="block-features">
						<h1><?php esc_html_e( 'Frequently Used Features', 'pie-forms' ); ?></h1>
						<div class="feature-list">
							<a href="<?php echo esc_url(get_admin_url(null, 'admin.php?page=pf-entries')) ?>" target="_blank">
								<div class="feature-block first">
									<div class="feature-block-icon">
										<img src="<?php echo esc_url($images_url.'welcome/view_submissions.png')?>">
									</div>
									<div class="feature-block-content">
										<h5><?php esc_html_e( 'View Submissions', 'pie-forms' ); ?></h5>
									</div>
								</div>
							</a>

							<a href="<?php echo esc_url(get_admin_url(null, 'admin.php?page=pf-settings&tab=recaptcha')) ?>" target="_blank">
								<div class="feature-block last">
									<div class="feature-block-icon">
										<img src="<?php echo esc_url($images_url.'welcome/configure_recaptcha.png')?>">
									</div>
									<div class="feature-block-content">
										<h5><?php esc_html_e( 'Configure reCaptcha', 'pie-forms' ); ?></h5>
									</div>
								</div>
							</a>

							<a href="<?php echo esc_url(get_admin_url(null, 'admin.php?page=pf-settings&tab=email')) ?>" target="_blank">
								<div class="feature-block first">
									<div class="feature-block-icon">
										<img src="<?php echo esc_url($images_url.'welcome/email_settings.png') ?>">
									</div>
									<div class="feature-block-content">
										<h5><?php esc_html_e( 'Email Settings', 'pie-forms' ); ?></h5>
									</div>
								</div>
							</a>

							<a href="<?php echo esc_url(get_admin_url(null, 'admin.php?page=pf-tools&tab=import')) ?>" target="_blank">
								<div class="feature-block last">
									<div class="feature-block-icon">
										<img src="<?php echo esc_url($images_url.'welcome/import_contact_form7.png')?>">
									</div>
									<div class="feature-block-content">
										<h5><?php esc_html_e( 'Import Contact Form 7', 'pie-forms' ); ?></h5>
									</div>
								</div>
							</a>

							<a href="<?php echo esc_url(get_admin_url(null, 'admin.php?page=pf-blockuser')) ?>" target="_blank">
								<div class="feature-block first">
									<div class="feature-block-icon">
										<img src="<?php echo esc_url($images_url.'welcome/block_users.png')?>">
									</div>
									<div class="feature-block-content">
										<h5><?php esc_html_e( 'Block Users', 'pie-forms' ); ?></h5>
									</div>
								</div>
							</a>

							<a href="<?php echo esc_url(get_admin_url(null, 'admin.php?page=pf-smarttranslation')) ?>" target="_blank">
								<div class="feature-block last">
									<div class="feature-block-icon">
										<img src="<?php echo esc_url($images_url.'welcome/translate_forms.png')?>">
									</div>
									<div class="feature-block-content">
										<h5><?php esc_html_e( 'Translate Forms', 'pie-forms' ); ?></h5>
									</div>
								</div>
							</a>
						</div>
					</div>
				</div><!-- /.features -->

				<div class="resourceful-links">
					<div class="block block-white">
						<h1><?php esc_html_e( 'Resourceful Links', 'pie-forms' ); ?></h1>
						<div class="resourceful-links-list">
							<div class="resourceful-link">
								<a href=<?php echo esc_url("https://pieforms.com/docs-category/getting-started/") ?> target="_blank">
									<div class="resourceful-link-image">
										<img src="<?php echo esc_url($images_url.'welcome/getting-started.png') ?>">
									</div>
									<h6><?php esc_html_e( 'Getting Started', 'pie-forms' ); ?></h6>
								</a>
							</div>

							<div class="resourceful-link">
								<a href=<?php echo esc_url("https://pieforms.com/addon/") ?> target="_blank">
									<div class="resourceful-link-image">
										<img src="<?php echo esc_url($images_url.'welcome/addons.png') ?>">
									</div>
									<h6><?php esc_html_e( 'Add-ons', 'pie-forms' ); ?></h6>
								</a>
							</div>

							<div class="resourceful-link">
								<a href=<?php echo esc_url("https://pieforms.com/docs-category/features/") ?> target="_blank">
									<div class="resourceful-link-image">
										<img src="<?php echo esc_url($images_url.'welcome/features.png') ?>">
									</div>
									<h6><?php esc_html_e( 'Features', 'pie-forms' ); ?></h6>
								</a>
							</div>

							<div class="resourceful-link">
								<a href=<?php echo esc_url("https://pieforms.com/docs-category/how-to-articles/") ?> target="_blank">
									<div class="resourceful-link-image">
										<img src="<?php echo esc_url($images_url .'welcome/how-to-articles.png') ?>">
									</div>
									<h6><?php esc_html_e( 'How-to', 'pie-forms' ); ?></h6>
								</a>
							</div>
						</div>
					</div>
				</div><!-- /.resourceful links -->			
			</div><!-- /.container -->
		</div><!-- /#pieform-welcome -->
		<?php
	}
}

new PForm_Welcome_();

?>