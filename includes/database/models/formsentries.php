<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class PFORM_Database_Models_FormsEntries
 */
final class PFORM_Database_Models_FormsEntries
{
   

    public function __construct( )
    {
        add_action( 'admin_init', array( $this, 'actions' ) );
        add_action( 'admin_init', array( $this, 'process_bulk_action' ) );
    }

	/**
	 * Entries admin actions.
	 */
	public function actions() {
		if ( $this->is_entries_page() ) {
			// Trash entry.
			if ( isset( $_GET['trash'] ) ) { 
				$this->trash_entry();
			}

			// Untrash entry.
			if ( isset( $_GET['untrash'] ) ) { 
				$this->untrash_entry();
			}

			// Delete entry.
			if ( isset( $_GET['delete'] ) ) { 
				$this->delete_entry();
			}

			
			/* // Empty Trash.
			if ( isset( $_REQUEST['delete_all'] ) || isset( $_REQUEST['delete_all2'] ) ) { 
				$this->empty_trash();
			} */
		}
	}

	/**
	 * Check if is entries page.
	 */
	private function is_entries_page() {
		return isset( $_GET['page'] ) && 'pf-entries' === $_GET['page']; 
	}

	/**
	 * Trash entry.
	 */
	private function trash_entry() {
		check_admin_referer( 'trash-entry' );

		$form_id = isset( $_GET['form_id'] ) ? absint( sanitize_key($_GET['form_id'] )) : '';

		if ( isset( $_GET['trash'] ) ) { 
			$entry_id = absint( sanitize_key($_GET['trash']) ); 
			if ( $entry_id ) {
				$this->update_status( $entry_id, 'trash' );
			}
		}
		
		wp_safe_redirect(
			esc_url_raw(
				add_query_arg(
					array(
						'form_id' => $form_id,
						'trashed' => 1,
					),
					admin_url( 'admin.php?page=pf-entries' )
				)
			)
		);
		exit();
	}

	/**
	 * Remove entry.
	 */
	public static function remove_entry( $entry_id ) {
		global $wpdb;

		$delete = $wpdb->delete( $wpdb->prefix . 'pf_entries', array( 'id' => $entry_id ), array( '%d' ) );

		if ( apply_filters( 'pie_forms_delete_entrymeta', true ) ) {
			$wpdb->delete( $wpdb->prefix . 'pf_entrymeta', array( 'id' => $entry_id ), array( '%d' ) );
		}

		return $delete;
	}

	/**
	 * Delete entry.
	 */
	private function delete_entry() {
		check_admin_referer( 'delete-entry' );

		$form_id = isset( $_GET['form_id'] ) ? absint( sanitize_key($_GET['form_id']) ) : '';

		if ( isset( $_GET['delete'] ) ) { 
			$entry_id = absint( sanitize_key($_GET['delete']) ); 

			if ( $entry_id ) {
				self::remove_entry( $entry_id );
			}
		}

		wp_safe_redirect(
			esc_url_raw(
				add_query_arg(
					array(
						'form_id' => $form_id,
						'deleted' => 1,
					),
					admin_url( 'admin.php?page=pf-entries' )
				)
			)
		);
		exit();
	}

	/**
	 * Trash entry.
	 */
	private function untrash_entry() {
		check_admin_referer( 'untrash-entry' );

		$form_id = isset( $_GET['form_id'] ) ? absint( sanitize_key($_GET['form_id'] )) : '';

		if ( isset( $_GET['untrash'] ) ) { 
			$entry_id = absint( sanitize_key($_GET['untrash']) ); 

			if ( $entry_id ) {
				$this->update_status( $entry_id, 'publish' );
			}
		}

		wp_safe_redirect(
			esc_url_raw(
				add_query_arg(
					array(
						'form_id'   => $form_id,
						'untrashed' => 1,
					),
					admin_url( 'admin.php?page=pf-entries' )
				)
			)
		);
		exit();
	}

	/**
	 * Set entry status.
	 */
	public function update_status( $entry_id, $status = 'publish' ) {
		global $wpdb;
		
		$entry = $this->pform_get_entry( $entry_id );

		// Preseve entry status.
		if ( 'trash' === $status ) {
			$wpdb->insert(
				$wpdb->prefix . 'pf_entrymeta',
				array(
					'entry_id'   => $entry_id,
					'meta_key'   => '_pf_trash_entry_status',
					'meta_value' => sanitize_text_field( $entry->status ), 
				)
			);
		} elseif ( 'publish' === $status ) {
			$status = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM {$wpdb->prefix}pf_entrymeta WHERE entry_id = %d AND meta_key = '_pf_trash_entry_status'", $entry_id ) );
			$wpdb->delete(
				$wpdb->prefix . 'pf_entrymeta',
				array(
					'entry_id' => $entry_id,
					'meta_key' => '_pf_trash_entry_status',
				)
			);
		}
		$update = $wpdb->update(
			$wpdb->prefix . 'pf_entries',
			array( 'status' => $status ),
			array( 'id' => $entry_id ),
			array( '%s' ),
			array( '%d' )
		);

		return $update;
	}

	 /**
        * Get entry.
    */
    function pform_get_entry( $id, $with_fields = false ) {
        global $wpdb;

        $entry = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}pf_entries WHERE id = %d LIMIT 1;", $id ) ); 
        
        $fields = Pie_Forms()->core()->pform_decode( $entry->fields );
            
        if ( $with_fields && ! empty( $fields ) ) {
            foreach ( $fields as $field ) {
                if ( isset( $field['meta_key'], $field['value'] ) ) {
                    $entry->meta[ $field['meta_key'] ] = maybe_serialize( $field['value'] );
                }
            }
        } elseif ( apply_filters( 'pie_forms_get_entry_metadata', true ) ) {
            $results     = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key,meta_value FROM {$wpdb->prefix}pf_entrymeta WHERE entry_id = %d", $id ), ARRAY_A );
            $entry->meta = wp_list_pluck( $results, 'meta_value', 'meta_key' );
        }
        
        return 0 !== $entry ? $entry : null;
	}
	/**
	 * Get the current action selected from the bulk actions dropdown.
	 */
	public function current_action() {
		
		if ( isset( $_REQUEST['delete_all'] ) || isset( $_REQUEST['delete_all2'] ) ) { 
			return 'delete_all';
		}

	}

	/**
	 * Entries Process bulk actions.
	 */
	public function process_bulk_action() {
		$entry_ids 	= isset( $_REQUEST['entry'] ) ? array_map( 'sanitize_key',$_REQUEST['entry'] ) : array(); 
		$count     	= 0;
		$form_id 	= isset( $_GET['form_id'] ) ? absint( sanitize_key($_GET['form_id']) ) : '';
		$sendback 	= get_admin_url( null, 'admin.php?page=pf-entries&form_id="'.$form_id.'"&status=trash' );

		if(!isset($_REQUEST['page']) || $_REQUEST['page'] != 'pf-entries'){
			return false;
		}
		
		// If the trash bulk action is triggered
		if ( ( isset( $_GET['action'] ) && $_GET['action'] == 'trash' )
		|| ( isset( $_GET['action2'] ) && $_GET['action2'] == 'trash' )
		) {
		
			foreach ( $entry_ids as $entry_id ) {
				if ( $this->update_status( $entry_id, 'trash' ) ) {
					$count ++;
				}
			}
		
			$redirect_url =  $sendback;

			wp_safe_redirect( $redirect_url );
			exit;
		}


		// If the trash bulk action is triggered
		if ( ( isset( $_GET['action'] ) && $_GET['action'] == 'untrash' )
		|| ( isset( $_GET['action2'] ) && $_GET['action2'] == 'untrash' )
		) {
		
			foreach ( $entry_ids as $entry_id ) {
				if ( $this->update_status( $entry_id, 'publish' ) ) {
					$count ++;
				}
			}
		
			$redirect_url =  $sendback;

			wp_safe_redirect( $redirect_url );
			exit;
		}

		// If the trash bulk action is triggered
		if ( ( isset( $_GET['action'] ) && $_GET['action'] == 'delete' )
		|| ( isset( $_GET['action2'] ) && $_GET['action2'] == 'delete' )
		) {
		
			foreach ( $entry_ids as $entry_id ) {
				if ( $this->remove_entry( $entry_id ) ) {
					$count ++;
				}
			}
		
			$redirect_url =  $sendback;

			wp_safe_redirect( $redirect_url );
			exit;
		}
		
	}

} // End PFORM_Database_Models_FormsEntries
