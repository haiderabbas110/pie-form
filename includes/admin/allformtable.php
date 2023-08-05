<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'WP_List_Table' ) ){

    if( file_exists( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' ) ) {

        require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
    } else {

        //TODO: Load local wp-list-table-class.php
    }
}

class PFORM_Admin_AllFormTable extends WP_List_Table
{
    /** Class constructor */
    public function __construct() {
        parent::__construct( array(
            'singular' => esc_html__( 'Form', 'pie-forms' ), //singular name of the listed records
            'plural'   => esc_html__( 'Forms', 'pie-forms' ), //plural name of the listed records
            'ajax'     => false //should this table support ajax?
        ) );
        ?>
        <form id="form-list" method="post">
        <?php 
            $this->prepare_items();
            $this->display();
        ?> 
        </form>
        <?php
        
    }

    function get_columns() {
        $columns = array(
            'cb'            => '<input type="checkbox" />',
            'enabled'   => '',
            'form_name'     => __('Form Name','pie-forms'),
            'shortcode'     => __('Shortcodes','pie-forms'),            
            'date'          => esc_html__( 'Date Created', 'pie-forms' )   
                     
        );
        return $columns;
    }

    function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="form_id[]" value="%s" />', $item['id']
        );    
    }

    function column_default( $item, $column_name ) {
        switch( $column_name ) {
            case 'cb':
            //case 'id':
            case 'form_name':
            case 'shortcode':
            case 'date':

                return $item[ $column_name ];
            default:
                return print_r( $item, true ) ;
        }
    }


    protected function get_views() { 
        //$views = array();
        $all_args     = array( 'page' => 'pie-forms' );
        $class = '';
        
        $all_forms = Pie_Forms()->form()->get_result();
        $all_forms_except_trash = Pie_Forms()->form()->get_status_except_trash();

        $total_forms = count($all_forms_except_trash);

      
        
         if ( empty( $class ) && empty( $_REQUEST['status'] ) ) {
			$class = 'current';
        }
        
         $all_inner_html = sprintf(
			_nx(
				'All <span class="count">(%s)</span>',
				'All <span class="count">(%s)</span>',
				$total_forms,
				'pie-forms'
			),
			number_format_i18n( $total_forms )
        );
     
        $status_links['all'] = $this->get_edit_link( $all_args, $all_inner_html, $class );
        
        foreach ( $all_forms as $status ) {
            
			$class       = '';
            $status_name = $status->post_status;

            $status_count = Pie_Forms()->form()->get_status($status_name);
            

            if ( isset( $_REQUEST['status'] ) && $status_name === $_REQUEST['status'] ) { 
				$class = 'current';
            }

            $status_args = array(
				'page'   => 'pie-forms',
				'status' => $status_name,
            );
            
            
            $status_label = sprintf(
                _nx(
                    $status_name.'<span class="count">(%s)</span>',
                    $status_name.'<span class="count">(%s)</span>',
                    $status_count,
                    'pie-forms'
                ),
                number_format_i18n( $status_count )
            );
            
            $status_links[ $status_name ] = $this->get_edit_link( $status_args, $status_label, $class );
        
        }
   
         return $status_links;
      }

      /**
	 * Helper to create links to admin.php with params.
	 */
	protected function get_edit_link( $args, $label, $class = '' ) {
		$url = add_query_arg( $args, 'admin.php' );

		$class_html   = '';
		$aria_current = '';

		if ( ! empty( $class ) ) {
			$class_html = sprintf(
				' class="%s"',
				esc_attr( $class )
			);

			if ( 'current' === $class ) {
				$aria_current = ' aria-current="page"';
			}
		}

		return sprintf(
			'<a href="%s"%s%s>%s</a>',
			esc_url( $url ),
			$class_html,
			$aria_current,
			$label
		);
	}

    /**
	 * Column enabled.
	 */
	public function column_enabled( $posts ) {
	
        $form_fields =  Pie_Forms()->core()->get_form_fields($posts['id']);

		$form_enabled = isset( $form_fields['form_enabled'] ) ? $form_fields['form_enabled'] : 1;
		return '<label class="pie-forms-toggle-form form-enabled"><input type="checkbox" data-form_id="' . absint( $posts['id'] ) . '" value="1" ' . checked( 1, $form_enabled, false ) . '/><span class="slider round"></span></label>';
	}
    
    /**
     * Get the table data
     */
    private function table_data($status)
    {
        $data = array();

        $forms = Pie_Forms()->form()->get_data_by_status($status);

        foreach( $forms as $form ){
             $data[] = array(
                 'id'               => $form->id,
                 'form_name'        => $form->form_title,
                 'shortcode'        => apply_filters ( 'pie_forms_form_list_shortcode','[pie_form id='."'$form->id'".']', $form->id ),
                 'date'             => $form->created_at,
                );
                
        }
        return $data;
    }

    public function get_sortable_columns() {

        return array(
            'form_name' => array( 'form_name', false ),
            'shortcode' => array( 'shortcode', false ),
            'date' => array( 'date', false ),
        );

    }
    public function usort_reorder( $a, $b ) {
        // If no sort, default to title
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? sanitize_sql_orderby($_GET['orderby']) : 'form_name';
        // If no order, default to asc
        $order = ( ! empty($_GET['order'] ) ) ? sanitize_sql_orderby($_GET['order']) : 'asc';
        // Determine sort order
        $result = strcmp( $a[$orderby], $b[$orderby] );
        // Send final sort direction to usort
        return ( $order === 'asc' ) ? $result : -$result;
      }
      
    /**
     * Returns an associative array containing the bulk action

     */
    public function get_bulk_actions() {
        if ( isset( $_GET['status'] ) && 'trash' === $_GET['status'] ) {
			return array(
				'bulk-restore' => __( 'Restore', 'pie-forms' ),
				'bulk-delete'  => __( 'Delete permanently', 'pie-forms' ),
			);
		}

		return array(
			'trash' => __( 'Move to trash', 'pie-forms' ),
		);
    }
      
    function prepare_items() {
            
        $status = (isset($_REQUEST['status']) && $_REQUEST['status']) ? sanitize_key($_REQUEST['status']) : 'published'; 
        
        
        $table_data = $this->table_data($status);
      

        /** Process bulk action */
        //$this->process_bulk_action();

        

        
        Pie_Forms::template( 'search.php' );

        $this->views();

        // FETCH THE TABLE DATA
        $user_search_key = isset( $_POST['s'] ) ? sanitize_title(wp_unslash( trim( $_POST['s'] )) ) : '';

        // FILTER THE DATA IN CASE OF A SEARCH
        if( $user_search_key ) {
            $table_data = $this->filter_table_data( $table_data, $user_search_key );
        }   


        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        usort(  $table_data, array( &$this, 'usort_reorder' ) );
        $this->items = $table_data;

        // PAGINATION
        $per_page = 5;
        $current_page = $this->get_pagenum();
        $total_items = count($table_data);

        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page                     //WE have to determine how many items to show on a page
        ) );
        $this->items = array_slice($table_data,(($current_page-1)*$per_page), $per_page);


    }
    
    // FILTER THE TABLE DATA BASED ON THE SEARCH KEY
    public function filter_table_data( $table_data, $search_key ) {
        $filtered_table_data = array_values( array_filter( $table_data, function( $row ) use( $search_key ) {
            foreach( $row as $row_val ) {
                if( stripos( $row_val, $search_key ) !== false ) {
                    return true;
                }               
            }           
        } ) );

        return $filtered_table_data;

    }
    function column_form_name($item) {
        
    
        // create a nonce
         $delete_nonce = wp_create_nonce( 'pf_delete_form' );
         $trash_nonce = wp_create_nonce( 'pf_trash_form' );
         $restore_nonce = wp_create_nonce( 'pf_restore_form' );

         $trash = admin_url( sprintf( 'admin.php?page=pie-forms&action=trash&id=%s&_wpnonce=%s',absint( $item['id']),$trash_nonce ) );
        
         $restore = admin_url(  sprintf( 'admin.php?page=pie-forms&action=restore&id=%s&_wpnonce=%s',absint( $item['id'] ) ,$restore_nonce ));
        
       
        $edit_link        = admin_url( 'admin.php?page=pie-forms&form_id=' . $item['id'] );
        
        $title            = $item['form_name'];

		// Title.
		$output = '<strong>';
			$output .= '<a href="' . esc_url( $edit_link ) . '" class="row-title">' . esc_html( $title ) . '</a>';
		$output .= '</strong>';

           
            if(isset($_REQUEST['status']) && $_REQUEST['status'] == "trash"){

                $actions['delete'] = sprintf('<a href="?page=%s&amp;action=%s&amp;form=%s&amp;_wpnonce=%s">Delete Permanently</a>', sanitize_text_field( $_REQUEST['page'] ),'delete',absint( $item['id'] ), $delete_nonce);

                $actions['restore'] = '<a class="submitdelete" aria-label="' . esc_attr__( 'Move this item to the restore', 'pie-forms' ) . '" href="' . esc_url( $restore ) . '">' . esc_html__( 'Restore', 'pie-forms' ) . '</a>';
            }  
            
			$preview_link   = add_query_arg(
                array(
                    'form_id'     => absint( $item['id'] ),
					'pf_preview' => 'true',
				),
				home_url()
			);
            
            $duplicate_link = wp_nonce_url(admin_url( 'admin.php?page=pie-forms&action=duplicate_form&id=' . absint( $item['id'] ) ), 'pie-forms-duplicate-form_' . $item['id'] );
                
            if(!isset($_REQUEST['status']) && isset($_REQUEST['status']) != "trash" || $_GET['status'] == "published"){
                $actions['edit']    = '<a href="' . esc_url( $edit_link ) . '">' . __( 'Edit', 'pie-forms' ) . '</a>';

                $actions['view'] = '<a href="' . esc_url( $preview_link ) . '" rel="bookmark" target="_blank">' . __( 'Preview', 'pie-forms' ) . '</a>';
                
                $actions['duplicate'] = '<a href="' . esc_url( $duplicate_link ) . '">' . __( 'Duplicate', 'pie-forms' ) . '</a>';
            }

            if( !isset($_REQUEST['status']) || $_GET['status'] == 'published'){
                $actions['trash'] = '<a class="submitdelete" aria-label="' . esc_attr__( 'Move this item to the Trash', 'pie-forms' ) . '" href="' . esc_url( $trash ) . '">' . esc_html__( 'Trash', 'pie-forms' ) . '</a>';
            }
            

                
                
		$row_actions = array();
        
        if(!empty($actions)){

            foreach ( $actions as $action => $link ) {
                $row_actions[] = '<span class="' . esc_attr( $action ) . '">' . $link . '</span>';
            }
        }

		$output .= '<div class="row-actions">' . implode( ' | ', $row_actions ) . '</div>';

		return $output;
    }

  
    

    
    
} // END CLASS NF_Admin_AllFormsTable
