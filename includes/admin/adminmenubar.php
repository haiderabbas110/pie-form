<?php

/**
 * Pieforms admin bar menu.
 */
class PFORM_Admin_Adminmenubar extends PFORM_Abstracts_Adminmenubar {
    
    /**
	 * Construct.
	 */
    public function __construct()
    {
        $this->hooks();
    }

	/**
	 * Register hooks.
	 */
	public function hooks() {
		parent::hooks();
	}

	
	/**
	 * Render primary top-
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar WordPress Admin Bar object.
	 */
	public function main_menu( \WP_Admin_Bar $wp_admin_bar ) {
		
		$wp_admin_bar->add_menu(
			[
				'id'    => 'pieform-menu',
				'title'  => __( 'Pie Forms' , 'pie-forms' ),
				'href'  => admin_url( 'admin.php?page=pie-forms' ),
			]
		);
	}
   
	/**
	 * Render Add New 
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar WordPress Admin Bar object.
	 */
	public function addnew_menu( \WP_Admin_Bar $wp_admin_bar ) {

		$wp_admin_bar->add_menu(
			[
				'parent' => 'pieform-menu',
				'id'     => 'pieform-addnew',
				'title'  => __( 'Add New', 'pie-forms' ),
				'href'   => admin_url( 'admin.php?page=pie-addnew' ),
			]
		);
	}
	
	/**
	 * Render Global Settings 
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar WordPress Admin Bar object.
	 */
	public function global_settings_menu( \WP_Admin_Bar $wp_admin_bar ) {

		$wp_admin_bar->add_menu(
			[
				'parent' => 'pieform-menu',
				'id'     => 'pieform-settings',
				'title'  => __( 'Global Settings', 'pie-forms' ),
				'href'   => admin_url( 'admin.php?page=pf-settings' ),
			]
		);
	}

	/**
	 * Render Entries 
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar WordPress Admin Bar object.
	 */
	public function entries_menu( \WP_Admin_Bar $wp_admin_bar ) {

		$wp_admin_bar->add_menu(
			[
				'parent' => 'pieform-menu',
				'id'     => 'pieform-entries',
				'title'  => __( 'Entries', 'pie-forms' ),
				'href'   => admin_url( 'admin.php?page=pf-entries' ),
			]
		);
	}

	/**
	 * Render Tools 
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar WordPress Admin Bar object.
	 */
	public function tools_menu( \WP_Admin_Bar $wp_admin_bar ) {

		$wp_admin_bar->add_menu(
			[
				'parent' => 'pieform-menu',
				'id'     => 'pieform-tools',
				'title'  => __( 'Tools', 'pie-forms' ),
				'href'   => admin_url( 'admin.php?page=pf-tools' ),
			]
		);
	}

	/**
	 * Render Marketing 
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar WordPress Admin Bar object.
	 */
	public function marketing_menu( \WP_Admin_Bar $wp_admin_bar ) {

		$wp_admin_bar->add_menu(
			[
				'parent' => 'pieform-menu',
				'id'     => 'pieform-marketing',
				'title'  => __( 'Marketing', 'pie-forms' ),
				'href'   => admin_url( 'admin.php?page=pf-marketing' ),
			]
		);
	}

	/**
	 * Render Block User 
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar WordPress Admin Bar object.
	 */
	public function blockusers_menu( \WP_Admin_Bar $wp_admin_bar ) {

		$wp_admin_bar->add_menu(
			[
				'parent' => 'pieform-menu',
				'id'     => 'pieform-blockuser',
				'title'  => __( 'Block User', 'pie-forms' ),
				'href'   => admin_url( 'admin.php?page=pf-blockuser' ),
			]
		);
	}

 	/**
	 * Render About Us 
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar WordPress Admin Bar object.
	 */
	public function aboutus_menu( \WP_Admin_Bar $wp_admin_bar ) {

		$wp_admin_bar->add_menu(
			[
				'parent' => 'pieform-menu',
				'id'     => 'pieform-aboutus',
				'title'  => __( 'About Us', 'pie-forms' ) . '',
				'href'   => admin_url( 'admin.php?page=pf-aboutus' ),
			]
		);
	}
}
