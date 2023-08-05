<?php

/**
 * Pieforms admin 
 */
class PFORM_Abstracts_Adminmenubar {

	/**
	 * Initialize class.

	 */
	public function init() {

		$this->hooks();
	}

	/**
	 * Register hooks.
	 *
	 * 
	 */
	public function hooks() {

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueues' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueues' ] );

		add_action( 'admin_bar_menu', [ $this, 'register' ], 999 );
	}

	/**
	 * Check if current 
	 *
	 * @return bool
	 */
	public function has_access() {

		$access = false;

		if (
			is_user_logged_in() 
		) {
			$access = true;
		}

	}

	/**
	 * Enqueue styles.
	 *
	 * 
	 */
	public function enqueues() {

		if ( ! $this->has_access() ) {
			return;
		}
	}

	/**
	 * Register and render 
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar WordPress Admin Bar object.
	 */
	public function register( \WP_Admin_Bar $wp_admin_bar ) {

		$items = apply_filters(
			'pieforms_admin_adminbarmenu_register',
			[
				'main_menu',
				'addnew_menu',
				'global_settings_menu',
				'entries_menu',
				'tools_menu',
				'marketing_menu',
				'blockusers_menu',
				'aboutus_menu'
			],
			$wp_admin_bar
		);
	
		foreach ( $items as $item ) {

			$this->{ $item }( $wp_admin_bar );
			do_action( "pieforms_admin_adminbarmenu_register_{$item}_after", $wp_admin_bar );
		}
	}

}
