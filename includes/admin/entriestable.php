<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Entries table list class.
 */
class PFORM_Admin_EntriesTable extends WP_List_Table {

	/**
	 * Form ID.
	 */
	public $form_id;

	/**
	 * Forms object.
	 */
	public $form;

	/**
	 * Forms object.
	 */
	public $forms;

	/**
	 * Form data as an array.
	 */
	public $form_data;

	/**
	 * Initialize the log table list.
	 */
	public function __construct() {
		// Fetch all forms.
		$this->forms = Pie_Forms()->core()->pform_get_all_forms( );
		add_action( 'admin_init', array( $this, 'actions' ) );

		// Check that the user has created at least one form.
		if ( ! empty( $this->forms ) ) {
			$this->form_id   = ! empty( $_REQUEST['form_id'] ) ? absint( sanitize_key($_REQUEST['form_id']) ) : apply_filters( 'pie_forms_entry_list_default_form_id', key( $this->forms ) ); 
			$form_fields      = Pie_Forms()->core()->get_form_fields( $this->form_id );
            $this->form_data = ! empty( $form_fields ) ? $form_fields : '';
            
		}

		parent::__construct(
			array(
				'singular' => 'entry',
				'plural'   => 'entries',
				'ajax'     => false,
			)
        );
        ?>
        <form id="form-list" method="get">
		<input type="hidden" name="page" value="pf-entries" />
		<?php 
			do_action( 'pie_forms_admin_pages_before_content' ); 
            $this->prepare_items();
            $this->display();
        ?> 
        </form>
        <?php
	}

	/**
	 * Get the current action selected from the bulk actions dropdown.
	 */
	public function current_action() {
		
		if ( isset( $_REQUEST['delete_all'] ) || isset( $_REQUEST['delete_all2'] ) ) { 
			return 'delete_all';
		}

		return parent::current_action();
	}

	/**
	 * No items found text.
	 */
	public function no_items() {
		esc_html_e( 'Whoops, it appears you do not have any form entries yet.', 'pie-forms' );
	}

	/**
	 * Get list columns.
	 */
	public function get_columns() {
		$columns            = array();
		$columns['cb']      = '<input type="checkbox" />';
		$columns            = apply_filters( 'pie_forms_entries_table_form_fields_columns', $this->get_columns_form_fields( $columns ), $this->form_id, $this->form_data );
		$columns['date']    = esc_html__( 'Date Created', 'pie-forms' );
		$columns['actions'] = esc_html__( 'Actions', 'pie-forms' );

		return apply_filters( 'pie_forms_entries_table_columns', $columns, $this->form_data );
	}

	/**
	 * Get a list of sortable columns.
	 */
	protected function get_sortable_columns() {
		$sortable_columns = array();

		if ( isset( $_GET['form_id'] ) ) { 
			$sortable_columns = array(
				'date' => array( 'created_at', false ),
			);
		}

		return array_merge(
			array(
				'id' => array( 'title', false ),
			),
			$sortable_columns
		);
	}

	/**
	 * Generates content for a single row of the table.
	 */
	public function single_row( $entry ) {
		if ( empty( $_GET['status'] ) || ( isset( $_GET['status'] ) && 'trash' !== $_GET['status'] ) ) { 
			echo '<tr class="' . ( "" ? 'read' : 'unread' ) . '">';
			$this->single_row_columns( $entry );
			echo '</tr>';
		} else {
			parent::single_row( $entry );
		}
	}

	/**
	 * get disallow fields
	 */
	public static function get_columns_form_disallowed_fields() {
		return (array) apply_filters( 'pie_forms_entries_table_fields_disallow', array( 'html', 'title', 'captcha' ) );
	}

	/**
	 * Logic to determine which fields are displayed in the table columns.
	 */
	public function get_columns_form_fields( $columns = array(), $display = 3 ) {
		// $entry_columns = pf()->form->get_meta( $this->form_id, 'entry_columns' );
        
		if (  ! empty( $this->form_data['form_fields'] ) ) {
			$x = 0;
			foreach ( $this->form_data['form_fields'] as $id => $field ) {
				if($field['type'] !== 'signature' && $field['type'] !== 'multipart'){
					if ( ! in_array( $field['type'], self::get_columns_form_disallowed_fields(), true ) && $x < $display ) {
						$columns[ 'pf_field_' . $id ] = ! empty( $field['label'] ) ? wp_strip_all_tags( $field['label'] ) : esc_html__( 'Field', 'pie-forms' );
						$x++;
					}
				}
			}
		} 

		return $columns;
	}

	/**
	 * Column cb.
	 */
	public function column_cb( $entry ) {
		return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $entry->id );
	}


	public function column_form_field( $entry, $column_name ) {
		$field_id = str_replace( 'pf_field_', '', $column_name );
		$meta_key = isset( $this->form_data['form_fields'][ $field_id ]['meta-key'] ) ? $this->form_data['form_fields'][ $field_id ]['meta-key'] : $field_id;
		$field_data = isset( $this->form_data['form_fields'][ $field_id ] ) ? $this->form_data['form_fields'][ $field_id ] : $field_id;

		$entry_fields = Pie_forms()->core()->pform_decode( $entry->fields );
		if ( ! empty( $entry_fields[ $field_id ] ) ) { 
			if($entry_fields[ $field_id ]['type'] == 'checkbox'){
				$value = implode( ',' , $entry_fields[ $field_id ]["value"]['label']);
			}
			else{
				$value = $entry_fields[ $field_id ]["value"] ;
			}
			
			if ( is_serialized( $value ) ) {
				$field_html  = array();
				$field_value = maybe_unserialize( $value );
				$field_label = ! empty( $field_value['label'] ) ? $field_value['label'] : $field_value;

				if ( is_array( $field_label ) ) {
					foreach ( $field_label as $value ) {
						$field_html[] = esc_html( $value );
					}

					$value = implode( ' | ', $field_html );
				} else {
					$value = esc_html( $field_label );
				}
			}

			return apply_filters( 'pie_forms_html_field_value', $value, $entry_fields[ $field_id ], $entry, 'entry-table' );
		} else {
			return '<span class="na">&mdash;</span>';
		}
	}

	/**
	 * Renders the columns.
	 */
	public function column_default( $entry, $column_name ) {
		switch ( $column_name ) {
			case 'id':
				$value = absint( $entry->entry_id );
				break;

			case 'date':
				$value = date_i18n( get_option( 'date_format' ), strtotime( $entry->created_at ) + ( get_option( 'gmt_offset' ) * 3600 ) );
				break;

			default:
				if ( false !== strpos( $column_name, 'pf_field_' ) ) {
					$value = $this->column_form_field( $entry, $column_name );
				} else {
					$value = '';
				}
				break;
		}

		return apply_filters( 'pie_forms_entry_table_column_value', $value, $entry, $column_name );
	}


	/**
	 * Get the status label for entries.
	 */
	private function get_status_label( $status_name, $amount ) {
		$statuses = $this->pform_get_entry_statuses( $this->form_data );
        
		if ( isset( $statuses[ $status_name ] ) ) {
			return array(
				'singular' => sprintf( '%s <span class="count">(<span class="%s-count">%s</span>)</span>', esc_html( $statuses[ $status_name ] ), $status_name, $amount ),
				'plural'   => sprintf( '%s <span class="count">(<span class="%s-count">%s</span>)</span>', esc_html( $statuses[ $status_name ] ), $status_name, $amount ),
				'context'  => '',
				'domain'   => 'pie-forms',
			);
		}


		return array(
			'singular' => sprintf( '%s <span class="count">(<span class="%s-count">%s</span>)</span>', esc_html( $statuses[ $status_name ] ), $status_name, $amount ),
			'plural'   => sprintf( '%s <span class="count">(%s)</span>', esc_html( $status_name ), $amount ),
			'context'  => '',
			'domain'   => 'pie-forms',
		);
	}

	/**
	 * Table list views.
	 */
	protected function get_views() {
		$status_links  = array();
		$num_entries   = $this->pform_get_count_entries_by_status( $this->form_id );
		$total_entries = apply_filters( 'pie_forms_total_entries_count', (int) $num_entries['publish'], $num_entries, $this->form_id );
		$statuses      = array_keys( $this->pform_get_entry_statuses( $this->form_data ) );
		$class         = empty( $_REQUEST['status'] ) ? ' class="current"' : ''; 

		/* translators: %s: count */
		$status_links['all'] = "<a href='admin.php?page=pf-entries&amp;form_id=$this->form_id'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_entries, 'entries', 'pie-forms' ), number_format_i18n( $total_entries ) ) . '</a>';

		foreach ( $statuses as $status_name ) {
			$class = '';

			if ( 'publish' === $status_name ) {
				continue;
			}

			if ( isset( $_REQUEST['status'] ) && sanitize_key( wp_unslash( $_REQUEST['status'] ) ) === $status_name ) { 
				$class = ' class="current"';
			}

			$label = $this->get_status_label( $status_name, $num_entries[ $status_name ] );

			$status_links[ $status_name ] = "<a href='admin.php?page=pf-entries&amp;form_id=$this->form_id&amp;status=$status_name'$class>" . sprintf( translate_nooped_plural( $label, $num_entries[ $status_name ] ), number_format_i18n( $num_entries[ $status_name ] ) ) . '</a>';
		}

		return apply_filters( 'pie_forms_entries_table_views', $status_links, $num_entries, $this->form_data );
	}

    /**
     * Get total entries counts by status.
     */
    function pform_get_count_entries_by_status( $form_id ) {
        $form_data = Pie_forms()->form()->get( $form_id, array( 'content_only' => true ) );
        $statuses  = array_keys( $this->pform_get_entry_statuses( $form_data ) );
		$counts    = array();
		
        foreach ( $statuses as $status ) {
            $count = count(
                $this->pform_search_entries(
                    array(
                        'limit'   => -1,
                        'status'  => $status,
                        'form_id' => $form_id,
                    )
                )
            );

            $counts[ $status ] = $count;
        }
        return $counts;
    }

    /**
     * Get entry statuses.
     */
    function pform_get_entry_statuses( $form_data = array() ) {
        return apply_filters(
            'pie_forms_entry_statuses',
            array(
                'publish' => esc_html__( 'Published', 'pie-forms' ),
                'trash'   => esc_html__( 'Trash', 'pie-forms' ),
				'unsubscribe'   => esc_html__( 'Unsubscribed', 'pie-forms' ),
            ),
            $form_data
        );
    }
	/**
	 * Get bulk actions.
	 */
	protected function get_bulk_actions() {
		if ( isset( $_GET['status'] ) && 'trash' === $_GET['status'] ) { 
			$actions = array(
				'untrash' => __( 'Restore', 'pie-forms' ),
				'delete'  => __( 'Delete Permanently', 'pie-forms' ),
			);
		} else {
			$actions = array(
				'trash' => __( 'Move to Trash', 'pie-forms' ),
			);
		}

		return apply_filters( 'pie_forms_entry_bulk_actions', $actions );
	}

	
	/**
	 * Extra controls to be displayed between bulk actions and pagination.
	 */
	protected function extra_tablenav( $which ) {
		?>
		<div class="alignleft actions">
		<?php
		if ( ! empty( $this->forms ) && 'top' === $which ) {
			ob_start();
			$this->forms_dropdown();
			$output = ob_get_clean();
			if ( ! empty( $output ) ) {
				echo wp_kses($output, Pie_Forms()->core()->pform_get_allowed_tags()); 
				submit_button( __( 'Filter', 'pie-forms' ), '', 'filter_action', false, array( 'id' => 'post-query-submit' ) );
				
			}
		} ?>
		</div>
		<?php
	}
	/**
	 * Display a form dropdown for filtering entries.
	 */
	public function forms_dropdown() {
		$forms   = Pie_Forms()->core()->pform_get_all_forms( );
		$form_id = isset( $_REQUEST['form_id'] ) ? absint( sanitize_key($_REQUEST['form_id']) ) : $this->form_id; 
		?>
		<label for="filter-by-form" class="screen-reader-text"><?php esc_html_e( 'Filter by form', 'pie-forms' ); ?></label>
		<select name="form_id" id="filter-by-form">
			<?php foreach ( $forms as $id => $form ) : ?>
				<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $form_id, $id ); ?>><?php echo esc_html( $form ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Prepare table list items.
	 */
	public function prepare_items() {
		$per_page     = $this->get_items_per_page( 'pf_entries_per_page' );
		$current_page = $this->get_pagenum();
        
		// Query args.
		$args = array(
			'status'  => 'publish',
			'form_id' => $this->form_id,
			'limit'   => $per_page,
			'offset'  => $per_page * ( $current_page - 1 ),
		);

		// Handle the status query.
		if ( ! empty( $_REQUEST['status'] ) ) { 
			$args['status'] = sanitize_key( wp_unslash( $_REQUEST['status'] ) ); 
		}

		// Handle the search query.
		if ( ! empty( $_REQUEST['s'] ) ) { 
			$args['search'] = sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ); 
		}

        $entries     = $this->pform_search_entries( $args );
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
		$PFORM_Database_Models_FormsEntries = new PFORM_Database_Models_FormsEntries();
        $this->items = array_map( array($PFORM_Database_Models_FormsEntries, 'pform_get_entry'), $entries );

		Pie_Forms::template( 'entries-search.php' );
		
        $this->views();
		// Get total items.
		$args['limit']  = -1;
		$args['offset'] = 0;
		$total_items    = count( $this->pform_search_entries( $args ) );
        
		// Set the pagination.
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
    }


   
    
    /**
     * Search entries.
     */
    static function pform_search_entries( $args ) {
        global $wpdb;
        $args = wp_parse_args(
            $args,
            array(
                'limit'   => 10,
                'offset'  => 0,
                'order'   => 'DESC',
                'orderby' => 'entry_id',
            )
        );
        
        // Check if form ID is valid for entries.
        if ( ! array_key_exists( $args['form_id'], Pie_Forms()->core()->pform_get_all_forms() ) ) {
            return array();
        }

        $query   = array();
        $query[] = "SELECT DISTINCT {$wpdb->prefix}pf_entries.id FROM {$wpdb->prefix}pf_entries INNER JOIN {$wpdb->prefix}pf_entrymeta WHERE {$wpdb->prefix}pf_entries.id = {$wpdb->prefix}pf_entrymeta.entry_id";
        
        if ( ! empty( $args['search'] ) ) {
            $like    = '%' . $wpdb->esc_like( $args['search'] ) . '%';
            $query[] = $wpdb->prepare( 'AND meta_value LIKE %s', $like );
        }
        
        if ( ! empty( $args['form_id'] ) ) {
            $query[] = $wpdb->prepare( 'AND form_id = %d', absint( $args['form_id'] ) );
		}
		
		
		if ( ! empty( $args['status'] ) ) {
			if($args['status'] == "unsubscribe"){
				$query[] = $wpdb->prepare( 'AND `subscription` = %s', $args['status'] );
			}else{
				$query[] = $wpdb->prepare( 'AND `status` = %s', $args['status'] );

			}
		
		}

        $valid_fields = array( 'date', 'form_id', 'title', 'status' );
        $orderby      = in_array( $args['orderby'], $valid_fields, true ) ? $args['orderby'] : 'entry_id';
        $order        = 'DESC' === strtoupper( $args['order'] ) ? 'DESC' : 'ASC';
        $orderby_sql  = sanitize_sql_orderby( "{$orderby} {$order}" );
        $query[]      = "ORDER BY {$orderby_sql}";

        if ( -1 < $args['limit'] ) {
            $query[] = $wpdb->prepare( 'LIMIT %d', absint( $args['limit'] ) );
        }
        
        if ( 0 < $args['offset'] ) {
            $query[] = $wpdb->prepare( 'OFFSET %d', absint( $args['offset'] ) );
        }
        
        $results = $wpdb->get_results( implode( ' ', $query ), ARRAY_A );
        
        $ids = wp_list_pluck( $results, 'id' );
        return $ids;
	}

	/**
	 * Render the actions column.
	 */
	public function column_actions( $entry ) {
		if ( 'trash' !== $entry->status ) {
			$actions = array(
				//'view'  => '<a href="' . esc_url( admin_url( 'admin.php?page=pf-entries&amp;form_id=' . $entry->form_id . '&amp;view-entry=' . $entry->id ) ) . '">' . esc_html__( 'View', 'pie-forms' ) . '</a>',
				/* translators: %s: entry name */
				'trash' => '<a class="submitdelete" aria-label="' . esc_attr__( 'Trash form entry', 'pie-forms' ) . '" href="' . esc_url(
					wp_nonce_url(
						add_query_arg(
							array(
								'trash'   => $entry->id,
								'form_id' => $this->form_id,
							),
							admin_url( 'admin.php?page=pf-entries' )
						),
						'trash-entry'
					)
				) . '">' . esc_html__( 'Trash', 'pie-forms' ) . '</a>',
			);
		} else {
			$actions = array(
				'untrash' => '<a aria-label="' . esc_attr__( 'Restore form entry from trash', 'pie-forms' ) . '" href="' . esc_url(
					wp_nonce_url(
						add_query_arg(
							array(
								'untrash' => $entry->id,
								'form_id' => $this->form_id,
							),
							admin_url( 'admin.php?page=pf-entries' )
						),
						'untrash-entry'
					)
				) . '">' . esc_html__( 'Restore', 'pie-forms' ) . '</a>',
				'delete'  => '<a class="submitdelete" aria-label="' . esc_attr__( 'Delete form entry permanently', 'pie-forms' ) . '" href="' . esc_url(
					wp_nonce_url(
						add_query_arg(
							array(
								'delete'  => $entry->id,
								'form_id' => $this->form_id,
							),
							admin_url( 'admin.php?page=pf-entries' )
						),
						'delete-entry'
					)
				) . '">' . esc_html__( 'Delete Permanently', 'pie-forms' ) . '</a>',
			);
		}

		return implode( ' <span class="sep">|</span> ', apply_filters( 'pie_forms_entry_table_actions', $actions, $entry ) );
	}
	
}