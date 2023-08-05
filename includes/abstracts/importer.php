<?php
/**
 * PFORM_Abstracts_Importer
 *
 */
abstract class PFORM_Abstracts_Importer implements PFORM_Admin_Importers_Interface {

	/**
	 * Importer name.
	 *
	 */
	public $name;

	/**
	 * Importer name in slug format.
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * Importer plugin path.
	 *
	 * @var string
	 */
	public $path;

	/**
	 * Primary class constructor.
	 *
	 */
	public function __construct() {

		$this->init();

		add_filter( 'pieforms_importers', array( $this, 'register' ), 10, 1 );

		add_filter( "pieforms_importer_forms_{$this->slug}", array( $this, 'get_forms' ), 10, 1 );

		add_action( "wp_ajax_pieforms_import_form_{$this->slug}", array( $this, 'import_form' ) );
	}

	/**
	 * Add to list of registered importers.
	 *
	 * @param array $importers List of supported importers.
	 *
	 * @return array
	 */
	public function register( $importers = array() ) {

		$importers[ $this->slug ] = array(
			'name'      => $this->name,
			'slug'      => $this->slug,
			'path'      => $this->path,
			'installed' => file_exists( trailingslashit( WP_PLUGIN_DIR ) . $this->path ),
			'active'    => $this->is_active(),
		);

		return $importers;
	}

	/**
	 * If the importer source is available.
	 *
	 * @return bool
	 */
	protected function is_active() {
		return is_plugin_active( $this->path );
	}

	/**
	 * Add the new form to the database and return AJAX data.
	 */
	public function add_form( $form, $unsupported = array(), $upgrade_plain = array(), $upgrade_omit = array() ) {
		// Create empty form so we have an ID to work with.
		$pie_form_name = $form['settings']['form_title'];	
		$form_id = Pie_Forms()->form()->insert_into_pf_forms( $pie_form_name );
		
		Pie_Forms()->form()->insert_into_pf_fields($form_id, $pie_form_name,$form);
		
		if ( empty( $form_id ) || is_wp_error( $form_id ) ) {
			wp_send_json_success( array(
				'error' => true,
				'name'  => sanitize_text_field( $pie_form_name ),
				'msg'   => esc_html__( 'There was an error while creating a new form.', 'pie-forms' ),
			) );
		}

		$form['id']       = $form_id;
		$form['field_id'] = count( $form['form_fields'] ) + 1;

		// Update the form with all our compiled data.

		//pieforms()->form->update( $form_id, $form );
		
		Pie_Forms()->form()->update( $form_id, $form );
		// Make note that this form has been imported.
		$this->track_import( $form['settings']['import_form_id'], $form_id );
		
		
		// Build and send final AJAX response!
		wp_send_json_success( array(
			'name'          => $form['settings']['form_title'],
			'edit'          => esc_url_raw( admin_url( 'admin.php?page=pie-forms&form_id=' . $form_id ) ),
			'preview'       => Pie_forms()->core()->pieforms_get_form_preview_url($form_id),
			'unsupported'   => $unsupported,
			'upgrade_plain' => $upgrade_plain,
			'upgrade_omit'  => $upgrade_omit,
		) );

	}

	/**
	 * After a form has been successfully imported we track it, so that in the
	 * future we can alert users if they try to import a form that has already
	 * been imported.
	 *
	 * @param int $source_id Imported plugin form ID.
	 * @param int $pieforms_id pieforms form ID.
	 */
	public function track_import( $source_id, $pieforms_id ) {

		$imported = get_option( 'pieforms_imported', array() );

		$imported[ $this->slug ][ $pieforms_id ] = $source_id;

		update_option( 'pieforms_imported', $imported, false );
	}
}
