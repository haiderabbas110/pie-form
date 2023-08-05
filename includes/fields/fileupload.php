<?php if ( ! defined( 'ABSPATH' ) ) exit;

class PFORM_Fields_Fileupload extends PFORM_Abstracts_Fields
{   

	/**
	 * Replaceable (either in PHP or JS) template for a maximum file number.
	 *
	 * @var string
	 */
	const TEMPLATE_MAXFILENUM = '{maxFileNumber}';

	/**
	 * File extensions that are now allowed.
	 *
	 * @var array
	 */
	private $denylist = array( 'ade', 'adp', 'app', 'asp', 'bas', 'bat', 'cer', 'cgi', 'chm', 'cmd', 'com', 'cpl', 'crt', 'csh', 'csr', 'dll', 'drv', 'exe', 'fxp', 'flv', 'hlp', 'hta', 'htaccess', 'htm', 'html', 'htpasswd', 'inf', 'ins', 'isp', 'jar', 'js', 'jse', 'jsp', 'ksh', 'lnk', 'mdb', 'mde', 'mdt', 'mdw', 'msc', 'msi', 'msp', 'mst', 'ops', 'pcd', 'php', 'pif', 'pl', 'prg', 'ps1', 'ps2', 'py', 'rb', 'reg', 'scr', 'sct', 'sh', 'shb', 'shs', 'sys', 'swf', 'tmp', 'torrent', 'url', 'vb', 'vbe', 'vbs', 'vbscript', 'wsc', 'wsf', 'wsf', 'wsh', 'dfxp', 'onetmp' );

    public function __construct()
    {
        $this->name     = esc_html__( 'File Upload', 'pie-forms' );
		$this->type     = 'fileupload';
		$this->icon     = 'fileupload';
		$this->order    = 210;
        $this->group    = 'advanced';
        $this->settings = array(
			'basic-options'    => array(
				'field_options' => array(
					'meta',
					'label',
					'description',
					'style',
					'required',
					'required_field_message',
					'ajax_notice',
					'features_notice'
				),
			),
			'advanced-options' => array(
				'field_options' => array(
					'max_files_size',
					'allowed_file_extension',
					'label_hide',
					'css'
				),
			),
		);

		// Define additional field properties.
		add_filter( 'pie_forms_field_properties_' . $this->type, array( $this, 'field_properties' ), 5, 3 );

		// Customize value format for HTML emails.
		add_filter( 'pie_forms_html_field_value', array( $this, 'html_field_value' ), 10, 4 );

        parent::__construct();
    }

    /**
	 * Allowed file extension.
	 */
	public function allowed_file_extension($field){

		$label = $this->field_element(
			'label',
			$field,
			array(
				'slug'    => 'extensions',
				'value'   => esc_html__( 'Allowed File Extensions', 'pie-forms' ),
				'tooltip' => esc_html__( 'Enter the extensions you would like to allow, comma separated.', 'pie-forms' ),
			),
			false
		);
		$input_field = $this->field_element(
			'text',
			$field,
			array(
				'slug'  => 'extensions',
				'value' => ! empty( $field['extensions'] ) ? $field['extensions'] : '',
			),
			false
		);
		$this->field_element(
			'row',
			$field,
			array(
				'slug'    => 'extensions',
				'content' => $label . $input_field,
			)
		);
	}

	/**
	 * Max file size.
	 */
	public function max_files_size($field){

		$label = $this->field_element(
			'label',
			$field,
			array(
				'slug'    => 'max_size',
				'value'   => esc_html__( 'Max File Size', 'pie-forms' ),
				/* translators: %s - max upload size. */
				'tooltip' => sprintf( esc_html__( 'Enter the max size of each file, in megabytes, to allow. If left blank, the value defaults to the maximum size the server allows which is %s.', 'pie-forms' ), Pie_forms()->core()->pform_max_upload() ),
			),
			false
		);
		$input_field = $this->field_element(
			'text',
			$field,
			array(
				'slug'  => 'max_size',
				'type'  => 'number',
				'attrs' => array(
					'min'     => 1,
					'max'     => 512,
					'step'    => 1,
					'pattern' => '[0-9]',
				),
				'value' => ! empty( $field['max_size'] ) ? abs( $field['max_size'] ) : '',
			),
			false
		);
		$this->field_element(
			'row',
			$field,
			array(
				'slug'    => 'max_size',
				'content' => $label . $input_field,
			)
		);
	}

	/**
	 * Feature Notice
	 */
	public function features_notice($field){

		$input_field = $this->field_element(
			'notice',
			$field,
			array(
				'notice' => __('<i>The‌ ‌Premium‌ ‌version‌ ‌allows‌ ‌multiple‌ ‌uploads‌ ‌through‌ ‌the‌ ‌Modern‌ ‌method,‌ ‌conditional‌ ‌logic,‌ ‌
				and‌ ‌storing‌ ‌files‌ ‌in‌ ‌the‌ ‌WP‌ ‌media‌ ‌library.‌</i>' , 'pie-forms'),
			),
			false
		);
		$this->field_element(
			'row',
			$field,
			array(
				'slug'    => 'max_size',
				'content' => $input_field,
			)
		);
	}

	/**
	 * Ajax Notice
	 */
	public function ajax_notice($field){

		$input_field = $this->field_element(
			'notice',
			$field,
			array(
				'notice' 		=> esc_html('File upload will not work with the ajax form submission‌' , 'pie-forms'),
				'starting_key' 	=> 'Please Note:'
			),
			false
		);
		$this->field_element(
			'row',
			$field,
			array(
				'slug'    => 'max_size',
				'content' => $input_field,
			)
		);
	}

    /**
	 * Define additional field properties.
	 *
	 * @param array $properties Field properties.
	 * @param array $field      Field data and settings.
	 * @param array $form_data  Form data and settings.
	 *
	 * @return array
	 */
	public function field_properties( $properties, $field, $form_data ) {

		$this->form_data  = $form_data;
		$this->form_id    = absint( $form_data['id']);
		$this->field_id   =  $field['id'] ;
		$this->field_data = $this->form_data['form_fields'][ $this->field_id ];
		
		// Input Primary: adjust name.
		$properties['inputs']['primary']['attr']['name'] = "pie_forms_{$this->form_id}_{$this->field_id}";

		// Input Primary: filter files in classic uploader style in files selection window.
	
		$properties['inputs']['primary']['attr']['accept'] = rtrim( '.' . implode( ',.', $this->get_extensions() ), ',.' );
	
		// Input Primary: allowed file extensions.
		$properties['inputs']['primary']['data']['rule-extension'] = implode( ',', $this->get_extensions() );

		// Input Primary: max file size.
		$properties['inputs']['primary']['data']['rule-maxsize'] = $this->max_file_size();

		return $properties;
	}
	/**
	 * Customize format for HTML email notifications. Added different link generation for classic and modern uploader.
	 *
	 * @param string $val       Field value.
	 * @param array  $field     Field settings.
	 * @param array  $form_data Form data and settings.
	 * @param string $context   Value display context.
	 *
	 * @return string
	 */
	public function html_field_value( $val, $field, $form_data = array(), $context = '' ) {

		if ( empty( $field['value'] ) || $field['type'] !== $this->type ) {
			return $val;
		}
        // Process classic uploader.
        return sprintf(
            '<a href="%s" rel="noopener" target="_blank">%s</a>',
            esc_url( $field['value'] ),
            esc_html( $field['file_original'] )
        );
	
	}

	/**
	 * File Upload field specific strings.
	 *
	 * @return array Field specific strings.
	 */
	public function get_strings() {

		return array(
			'preview_title_single' => esc_html__( 'Drop your file here or click here to upload.', 'pie-forms' ),
			'preview_title_plural' => esc_html__( 'Drop your files here or click here to upload.', 'pie-forms' ),
			'preview_hint'         => sprintf( /* translators: % - max number of files as a template string (not a number), replaced by a number later. */
				esc_html__( 'You can upload up to %s files.', 'pie-forms' ),
				self::TEMPLATE_MAXFILENUM
			),
		);
	}
    
    /**
	 * Field preview inside the builder.
	 */
	public function field_preview( $field , $form_data ) {
		// Label.
		$this->field_preview_option( 'label', $field );
        
		$classic_classes = array( 'pie-forms-file-upload-builder-classic' );
	
		$strings         = $this->get_strings();
		$classic_classes = implode( ' ', $classic_classes );

		echo '<div class="widefat '.esc_attr( $classic_classes ).'">';
			echo '<div class="pie-fileupload">';
				echo  '<input type="file" class="primary-input" disabled>';
			echo '</div>';
		echo '</div>';

		// Description.
		$this->field_preview_option( 'description', $field );
	}

	/**
	 * Field display on the form front-end. Added modern style uploader logic.
	 *
	 * @param array $field      Field data and settings.
	 * @param array $deprecated Deprecated field attributes. Use field properties.
	 * @param array $form_data  Form data and settings.
	 */
	public function field_display( $field, $deprecated, $form_data ) {
        // Define data. 
		$primary = $field['properties']['inputs']['primary'];

        printf(
            '<input type="file" %s %s>',
            Pie_Forms()->core()->pie_html_attributes( $primary['id'], $primary['class'], $primary['data'], $primary['attr'] ),
            $primary['required'] 
        );
	}
    
	/**
	 * Input name.
	 *
	 * The input name is name in which the data is expected to be sent in from the client.
	 *
	 * @return string
	 */
	public function get_input_name() {
		return sprintf( 'pie_forms_%d_%s', $this->form_id , $this->field_id );
	}

	/**
	 * Validate field for various errors on form submit. Added modern style uploader logic.
	 *
	 * @param int   $field_id     Field ID.
	 * @param array $field_submit Submitted field value.
	 * @param array $form_data    Form data and settings.
	 */
	public function validate( $field_id, $field_submit, $form_data ) {

		$this->form_data  = (array) $form_data;
		$this->form_id    = absint( $this->form_data['id'] );
		$this->field_id   = $field_id; 
		$this->field_data = $this->form_data['form_fields'][ $this->field_id ];

		$input_name = $this->get_input_name();

		$this->validate_classic( $input_name);
		
	}
    
	/**
	 * Validate classic file uploader field data.
	 *
	 * @param string $input_name Input name inside the form on front-end.
	 */
	protected function validate_classic( $input_name ) {

		if ( empty( $_FILES[ $input_name ] ) ) {
			return;
		}

		/*
		 * If nothing is uploaded and it is not required, don't process.
		 */
		if ( $_FILES[ $input_name ]['error'] === 4 && ! $this->is_required() ) {
			return;
		}

		/*
		 * Basic file upload validation.
		 */
		$validated_basic = $this->validate_basic( (int) $_FILES[ $input_name ]['error'] );
		if ( ! empty( $validated_basic ) ) {
			Pie_Forms()->task->errors[ $this->form_id ][  $this->field_id ] = $validated_basic;

			return;
		}

		/*
		 * Validate if file is required and provided.
		 */
		if (
			( empty( $_FILES[ $input_name ]['tmp_name'] ) || 4 === $_FILES[ $input_name ]['error'] ) &&
		     $this->is_required()
		) {
			Pie_Forms()->task->errors[ $this->form_id ][  $this->field_id ] = Pie_Forms()->core()->pform_get_required_label();

			return;
		}

		/*
		 * Validate file size.
		 */
		$validated_size = $this->validate_size();
		if ( ! empty( $validated_size ) ) {
			Pie_Forms()->task->errors[ $this->form_id ][  $this->field_id ] = $validated_size;

			return;
		}

		/*
		 * Validate file extension.
		 */
		$ext = strtolower( pathinfo( sanitize_file_name ( $_FILES[ $input_name ]['name'] ), PATHINFO_EXTENSION ) );

		$validated_ext = $this->validate_extension( $ext );
		if ( ! empty( $validated_ext ) ) {
			// var_dump($validated_ext);die;
			Pie_Forms()->task->errors[ $this->form_id ][  $this->field_id ] = $validated_ext;

			return;
		}

		/*
		 * Validate file against what WordPress is set to allow.
		 * At the end of the day, if you try to upload a file that WordPress
		 * doesn't allow, we won't allow it either. Users can use a plugin to
		 * filter the allowed mime types in WordPress if this is an issue.
		 */
		$validated_filetype = $this->validate_wp_filetype_and_ext( sanitize_file_name($_FILES[ $input_name ]['tmp_name']), sanitize_file_name( wp_unslash( $_FILES[ $input_name ]['name'] ) ) ); 
		if ( ! empty( $validated_filetype ) ) {
			Pie_Forms()->task->errors[ $this->form_id ][  $this->field_id ] = $validated_filetype;

			return;
		}
	}

    /**
	 * Format and sanitize field.
	 *
	 * @param int   $field_id     Field ID.
	 * @param array $field_submit Submitted field value.
	 * @param array $form_data    Form data and settings.
	 */
	public function format( $field_id, $field_submit, $form_data , $meta_key ) {

		// Setup class properties to reuse everywhere.
		$this->form_data  = $form_data;
		$this->form_id    = absint( $this->form_data['id'] );
		$this->field_id   = $field_id; 
		$this->field_data = $this->form_data['form_fields'][ $this->field_id ];
		
		$field_label = ! empty( $this->form_data['form_fields'][ $this->field_id ]['label'] ) ? $this->form_data['form_fields'][ $this->field_id ]['label'] : '';
		$input_name  = sprintf( 'pie_forms_%d_%s',$this->form_id , $this->field_id );

        $this->format_classic( $field_label, $input_name ,  $meta_key  );

	}

	/**
	 * Format and sanitize classic style of file upload field.
	 *
	 * @param string $field_label Field label.
	 * @param string $input_name  Input name inside the form on front-end.
	 */
	protected function format_classic( $field_label, $input_name , $meta_key ) {
		$file = ! empty( $_FILES[ $input_name ] ) ? $_FILES[ $input_name ] : false; 

		// Preserve field CL visibility state before processing the field.
		$visible = isset( Pie_Forms()->task->form_fields[ $this->field_id ]['visible'] ) ? Pie_Forms()->task->form_fields[ $this->field_id ]['visible'] : false;
		
		// Define data.
		$file_name     = sanitize_file_name( $file['name'] );
		$file_ext      = pathinfo( $file_name, PATHINFO_EXTENSION );
		$file_base     = wp_basename( $file_name, '.' . $file_ext );
		$file_name_new = sprintf( '%s-%s.%s', $file_base, wp_hash( wp_rand() . microtime() . $this->form_id . $this->field_id ), strtolower( $file_ext ) );
		$upload_dir    = Pie_forms()->core()->pform_upload_dir();
		$upload_path   = $upload_dir['path'];

		// Old dir.
		$form_directory   = absint( $this->form_id ) . '-' . md5( $this->form_id . $this->form_data['created'] );
		$upload_path_form = trailingslashit( $upload_path ) . $form_directory;

		// Check for form upload directory destination.
		if ( ! file_exists( $upload_path_form ) ) {

			// New one.
			$form_directory   = absint( $this->form_id ) . '-' . wp_hash( $this->form_data['created'] . $this->form_id );
			$upload_path_form = trailingslashit( $upload_path ) . $form_directory;

			// Check once again and make directory if it's not exists.
			if ( ! file_exists( $upload_path_form ) ) {
				wp_mkdir_p( $upload_path_form );
			}
		}
		$file_new      = trailingslashit( $upload_path_form ) . $file_name_new;
		$file_name_new = wp_basename( trailingslashit( dirname( $file_new ) ) . $file_name_new );
		$file_new      = trailingslashit( dirname( $file_new ) ) . $file_name_new;
		$file_url      = trailingslashit( $upload_dir['url'] ) . trailingslashit( $form_directory ) . $file_name_new;
		$attachment_id = '0';

		// Check if the .htaccess exists in the upload directory, if not - create it.
        Pie_forms()->core()->pform_create_upload_dir_htaccess_file();

		// Check if the index.html exists in the directories, if not - create it.
		Pie_forms()->core()->pform_create_index_html_file( $upload_path );
		Pie_forms()->core()->pform_create_index_html_file( $upload_path_form );

		// Move the file to the uploads dir - similar to _wp_handle_upload().
		$move_new_file = @move_uploaded_file( $file['tmp_name'], $file_new ); 
		if ( false === $move_new_file ) {

			return;
		}

		$this->set_file_fs_permissions( $file_new );

		// Maybe move file to the WordPress media library.
		if ( $this->is_media_integrated() ) {

			// Include necessary code from core.
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';

			// Copy our file into WordPress uploads.
			$file_args = array(
				'error'    => '',
				'tmp_name' => $file_new,
				'name'     => $file_name_new,
				'type'     => $file['type'],
				'size'     => $file['size'],
			);
			$upload    = wp_handle_sideload( $file_args, array( 'test_form' => false ) );

			if ( ! empty( $upload['file'] ) ) {
				// Create a Media attachment for the file.
				$attachment_id = wp_insert_attachment(
					array(
						'post_title'     => $this->get_wp_media_file_title( $file ),
						'post_content'   => $this->get_wp_media_file_desc( $file ),
						'post_status'    => 'publish',
						'post_mime_type' => $file['type'],
					),
					$upload['file']
				);

				if ( ! empty( $attachment_id ) ) {
					// Generate attachment meta.
					wp_update_attachment_metadata(
						$attachment_id,
						wp_generate_attachment_metadata( $attachment_id, $upload['file'] )
					);

					// Update file url/name.
					$file_url      = wp_get_attachment_url( $attachment_id );
					$file_name_new = wp_basename( $file_url );
				}
			}
		}

		// Set final field details.
		Pie_Forms()->task->form_fields[ $this->field_id ] = array(
			'name'           => sanitize_text_field( $field_label ),
			'value'          => esc_url_raw( $file_url ),
			'file'           => $file_name_new,
			'file_original'  => $file_name,
			'file_user_name' => sanitize_text_field( $file['name'] ),
			'ext'            => $file_ext,
			'attachment_id'  => absint( $attachment_id ),
			'id'             =>  $this->field_id ,
			'type'           => $this->type,
			'meta_key'       => $meta_key,
		);

		// Save field CL visibility state after field processing.
		if ( $visible ) {
			Pie_Forms()->task->form_fields[ $this->field_id ]['visible'] = $visible;
		}
	}

	/**
	 * Generate an attachment title used in WP Media library for an uploaded file.
	 *
	 * @param array $file File data.
	 *
	 * @return string
	 */
	private function get_wp_media_file_title( $file ) {

		$title = apply_filters(
			'pie_forms_field_' . $this->type . '_media_file_title',
			sprintf(
				'%s: %s',
				$this->field_data['label'],
				$file['name']
			),
			$file,
			$this->field_data
		);

		return Pie_forms()->core()->pform_sanitize_textarea_field( $title );
	}

	/**
	 * Generate an attachment description used in WP Media library for an uploaded file.
	 *
	 * @param array $file File data.
	 *
	 * @return string
	 */
	private function get_wp_media_file_desc( $file ) {

		$desc = apply_filters(
			'pie_forms_field_' . $this->type . '_media_file_desc',
			$this->field_data['description'],
			$file,
			$this->field_data
		);

		return wp_kses_post_deep( $desc );
	}

	/**
	 * Generate a ready for DB data for each file.
	 *
	 * @param array $file File to generate data for.
	 *
	 * @return array
	 */
	protected function generate_file_data( $file ) {

		return array(
			'name'           => sanitize_text_field( $file['name'] ),
			'value'          => esc_url_raw( $file['file_url'] ),
			'file'           => $file['file_name_new'],
			'file_original'  => $file['name'],
			'file_user_name' => sanitize_text_field( $file['file_user_name'] ),
			'ext'            => Pie_forms()->core()->pform_chain( $file['file'] )->explode( '.' )->pop()->value(),
			'attachment_id'  => isset( $file['attachment_id'] ) ? absint( $file['attachment_id'] ) : 0,
			'id'             => $this->field_id,
			'type'           => $file['type']
		);
	}


	/**
	 * Determine the max allowed file size in bytes as per field options.
	 *
	 * @return int Number of bytes allowed.
	 */
	public function max_file_size() {

		if ( ! empty( $this->field_data['max_size'] ) ) {

			// Strip any suffix provided (eg M, MB etc), which leaves us with the raw MB value.
			$max_size = preg_replace( '/[^0-9.]/', '', $this->field_data['max_size'] );
			$max_size = Pie_forms()->core()->pform_size_to_bytes( $max_size . 'M' );

		} else {
			$max_size = Pie_forms()->core()->pform_max_upload( true );
		}

		return $max_size;
	}

	/* *
	 * Basic file upload validation.
	 *
	 * @param int $error Error ID provided by PHP.
	 *
	 * @return false|string False if no errors found, error text otherwise.
	 */
	protected function validate_basic( $error ) {

		if ( 0 === $error || 4 === $error ) {
			return false;
		}

		$errors = array(
			false,
			esc_html__( 'The uploaded file exceeds the upload_max_filesize directive in php.ini.', 'pie-forms' ),
			esc_html__( 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.', 'pie-forms' ),
			esc_html__( 'The uploaded file was only partially uploaded.', 'pie-forms' ),
			esc_html__( 'No file was uploaded.', 'pie-forms' ),
			'',
			esc_html__( 'Missing a temporary folder.', 'pie-forms' ),
			esc_html__( 'Failed to write file to disk.', 'pie-forms' ),
			esc_html__( 'File upload stopped by extension.', 'pie-forms' ),
		);

		if ( array_key_exists( $error, $errors ) ) {
			/* translators: %s - error text. */
			return sprintf( esc_html__( 'File upload error. %s', 'pie-forms' ), $errors[ $error ] );
		}

		return false;
	}

	/**
	 * Validate file size.
	 *
	 * @param array $sizes Array with all file sizes in bytes.
	 *
	 * @return false|string False if no errors found, error text otherwise.
	 */
	protected function validate_size( $sizes = null ) {

		if (
			null === $sizes &&
			! empty( $_FILES )
		) {
			$sizes = [];
			foreach ( $_FILES as $file ) {
				$sizes[] = $file['size'];
			}
		}

		if ( ! is_array( $sizes ) ) {
			return false;
		}

		$max_size = min( wp_max_upload_size(), $this->max_file_size() );

		foreach ( $sizes as $size ) {
			if ( $size > $max_size ) {
				return sprintf( /* translators: $s - allowed file size in Mb. */
					esc_html__( 'File exceeds max size allowed (%s).', 'pie-forms' ),
					size_format( $max_size )
				);
			}
		}

		return false;
	}

	/**
	 * Validate extension against denylist and admin-provided list.
	 * There are certain extensions we do not allow under any circumstances,
	 * with no exceptions, for security purposes.
	 *
	 * @param string $ext Extension.
	 *
	 * @return false|string False if no errors found, error text otherwise.
	 */
	protected function validate_extension( $ext ) {

		// Make sure file has an extension first.
		if ( empty( $ext ) ) {
			return esc_html__( 'File must have an extension.', 'pie-forms' );
		}

		// Validate extension against all allowed values.
		if ( ! in_array( $ext, $this->get_extensions(), true ) ) {
			return esc_html__( 'File type is not allowed.', 'pie-forms' );
		}

		return false;
	}

	/**
	 * Validate file against what WordPress is set to allow.
	 * At the end of the day, if you try to upload a file that WordPress
	 * doesn't allow, we won't allow it either. Users can use a plugin to
	 * filter the allowed mime types in WordPress if this is an issue.
	 *
	 * @param string $path Path to a newly uploaded file.
	 * @param string $name Name of a newly uploaded file.
	 *
	 * @return false|string False if no errors found, error text otherwise.
	 */
	protected function validate_wp_filetype_and_ext( $path, $name ) {

		$wp_filetype = wp_check_filetype_and_ext( $path, $name );

		$ext             = empty( $wp_filetype['ext'] ) ? '' : $wp_filetype['ext'];
		$type            = empty( $wp_filetype['type'] ) ? '' : $wp_filetype['type'];
		$proper_filename = empty( $wp_filetype['proper_filename'] ) ? '' : $wp_filetype['proper_filename'];

		if ( $proper_filename || ! $ext || ! $type ) {
			return esc_html__( 'File type is not allowed.', 'pie-forms' );
		}

		return false;
	}

	/**
	 * Create both the directory and index.html file in it if any of them doesn't exist.
	 *
	 * @param string $path Path to the directory.
	 *
	 * @return string Path to the newly created directory.
	 */
	protected function create_dir( $path ) {

		if ( ! file_exists( $path ) ) {
			wp_mkdir_p( $path );
		}

		// Check if the index.html exists in the path, if not - create it.
		Pie_forms()->core()->pform_create_index_html_file( $path );

		return $path;
	}



	/**
	 * Set correct file permissions in the file system.
	 *
	 * @param string $path File to set permissions for.
	 */
	protected function set_file_fs_permissions( $path ) {

		// Set correct file permissions.
		$stat = stat( dirname( $path ) );

		@chmod( $path, $stat['mode'] & 0000666 ); 
	}

	/**
	 * Get all allowed extensions.
	 * Check against user-entered extensions.
	 *
	 * @return array
	 */
	protected function get_extensions() {

		// Allowed file extensions by default.
		$default_extensions = $this->get_default_extensions();

		// Allowed file extensions.
		$extensions = ! empty( $this->field_data['extensions'] ) ? explode( ',', $this->field_data['extensions'] ) : $default_extensions;

		return Pie_forms()->core()->pform_chain( $extensions )
			->map(
				static function ( $ext ) {

					return strtolower( preg_replace( '/[^A-Za-z0-9]/', '', $ext ) );
				}
			)
			->array_filter()
			->array_intersect( $default_extensions )
			->value();
	}

	/**
	 * Get default extensions supported by WordPress
	 * without those that we manually denylist.
	 *
	 * @return array
	 */
	protected function get_default_extensions() {

		return Pie_forms()->core()->pform_chain( get_allowed_mime_types() )
			->array_keys()
			->implode( '|' )
			->explode( '|' )
			->array_diff( $this->denylist )
			->value();
	}

	/**
	 * Whether field is required or not.
	 *
	 * @uses $this->field_data
	 *
	 * @return bool
	 */
	protected function is_required() {

		return ! empty( $this->field_data['required'] );
	}

	/**
	 * Whether field is integrated with WordPress Media Library.
	 *
	 * @uses $this->field_data
	 *
	 * @return bool
	 */
	protected function is_media_integrated() {

		return ! empty( $this->field_data['media_library'] ) && '1' === $this->field_data['media_library'];
	}

}