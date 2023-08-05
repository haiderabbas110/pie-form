<?php

defined( 'ABSPATH' ) || exit;

/**
 * PFORM_Tools_PluginsThemes.
 */
class PFORM_Tools_PluginsThemes extends PFORM_Abstracts_Tools {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'plugins_and_themes';
		$this->label = esc_html__( 'Plugins And Themes', 'pie-forms' );
		parent::__construct();
		
	}

}