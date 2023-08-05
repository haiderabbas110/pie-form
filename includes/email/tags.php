<?php

defined( 'ABSPATH' ) || exit;

class PFORM_Email_Tags {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'pie_forms_process_smart_tags', array( $this, 'process' ), 10, 4 );
	}

	/**
	 * Other smart tags.
	 *
	 * @return string|array
	 */
	public static function other_smart_tags() {
		$smart_tags = apply_filters(
			'pie_forms_smart_tags',
			array(
				'admin_email'     => esc_html__( 'Site Admin Email', 'pie-forms' ),
				'site_name'       => esc_html__( 'Site Name', 'pie-forms' ),
				'site_url'        => esc_html__( 'Site URL', 'pie-forms' ),
				'page_title'      => esc_html__( 'Page/Post Title', 'pie-forms' ),
				'page_url'        => esc_html__( 'Page/Post URL', 'pie-forms' ),
				'page_id'         => esc_html__( 'Page ID', 'pie-forms' ),
				'form_name'       => esc_html__( 'Form Name', 'pie-forms' ),
				'user_id'         => esc_html__( 'User ID', 'pie-forms' ),
				'user_name'       => esc_html__( 'User Name', 'pie-forms' ),
				'user_email'      => esc_html__( 'User Email', 'pie-forms' ),
				'referrer_url'    => esc_html__( 'Referrer URL', 'pie-forms' ),
			)
		);

		return $smart_tags;
	}

	/**
	 * Process and parse smart tags.
	 */
	public function process( $content, $form_data, $fields = '', $entry_id = '' ) {
		// Field smart tags (settings, etc).
		preg_match_all( '/\{field_id="(.+?)"\}/', $content, $ids );

		// We can only process field smart tags if we have $fields.
		if ( ! empty( $ids[1] ) && ! empty( $fields ) ) {

			foreach ( $ids[1] as $key => $field_id ) {
				$mixed_field_id = explode( '_', $field_id );
				if( $fields[ $mixed_field_id[1] ]['type'] !== 'multipart' && $fields[ $mixed_field_id[1] ]['type'] !== 'payment-single' ){
					if ( 'fullname' !== $field_id && 'email' !== $field_id && 'subject' !== $field_id && 'message' !== $field_id ) {
						$value          = ! empty( $fields[ $mixed_field_id[1] ]['value'] ) ? Pie_Forms()->Core()->pform_sanitize_textarea_field( $fields[ $mixed_field_id[1] ]['value'] ) : '';
					} else {
						$value = ! empty( $fields[ $field_id ]['value'] ) ? Pie_Forms()->Core()->pform_sanitize_textarea_field( $fields[ $field_id ]['value'] ) : '';
					}
					if(isset($value['label']) && is_array($value['label'])){
						$value = implode(PHP_EOL,$value['label']);
					}
					$content = str_replace( '{field_id="' . $field_id . '"}', $value, $content );
				}
			}
		}

		// Other Smart tags.
		preg_match_all( '/\{(.+?)\}/', $content, $other_tags );

		if ( ! empty( $other_tags[1] ) ) {

			foreach ( $other_tags[1] as $key => $other_tag ) {

				switch ( $other_tag ) {
					case 'admin_email':
						$admin_email = sanitize_email( get_option( 'admin_email' ) );
						$content     = str_replace( '{' . $other_tag . '}', $admin_email, $content );
						break;

					case 'site_name':
						$site_name = get_option( 'blogname' );
						$content   = str_replace( '{' . $other_tag . '}', $site_name, $content );
						break;

					case 'site_url':
						$site_url = get_option( 'siteurl' );
						$content  = str_replace( '{' . $other_tag . '}', $site_url, $content );
						break;

					case 'page_title':
						$page_title = get_the_ID() ? get_the_title( get_the_ID() ) : '';
						$content    = str_replace( '{' . $other_tag . '}', $page_title, $content );
						break;

					case 'page_url':
						$page_url = get_the_ID() ? get_permalink( get_the_ID() ) : '';
						$content  = str_replace( '{' . $other_tag . '}', $page_url, $content );
						break;

					case 'page_id':
						$page_id = get_the_ID() ? get_the_ID() : '';
						$content = str_replace( '{' . $other_tag . '}', $page_id, $content );
						break;

					case 'form_name':
						if ( isset( $form_data['settings']['form_title'] ) && ! empty( $form_data['settings']['form_title'] ) ) {
							$form_name = $form_data['settings']['form_title'];
						} else {
							$form_name = '';
						}
						$content = str_replace( '{' . $other_tag . '}', $form_name, $content );
						break;

					case 'user_id':
						$user_id = is_user_logged_in() ? get_current_user_id() : '';
						$content = str_replace( '{' . $other_tag . '}', $user_id, $content );
						break;

					case 'user_email':
						if(!empty($form_data['form_fields'])){

							foreach ($form_data['form_fields'] as $key => $value) {
								if($value['type'] == "email"){
									$user_emails = $fields[$key];
								}
							}
						}
						$user_emails = !empty($user_emails) ? $user_emails['value'] : "";
						$content = str_replace( '{' . $other_tag . '}', $user_emails, $content );
						break;

					case 'user_name':
						if ( is_user_logged_in() ) {
							$user = wp_get_current_user();
							$name = sanitize_text_field( $user->user_login );
						} else {
							$name = '';
						}
						$content = str_replace( '{' . $other_tag . '}', $name, $content );
						break;

					case 'referrer_url':
						$referer = ! empty( $_SERVER['HTTP_REFERER'] ) ? sanitize_url($_SERVER['HTTP_REFERER']) : '';
						$content = str_replace( '{' . $other_tag . '}', sanitize_text_field( $referer ), $content );
						break;

					case 'quiz_table':
						$case = 'table';
						$referer = apply_filters('pie_forms_email_results' , $case);
						$content = str_replace( '{' . $other_tag . '}', $referer , $content );
						break;

					case 'quiz_score':
						$case = 'score';
						$referer = apply_filters('pie_forms_email_results' , $case);
						$content = str_replace( '{' . $other_tag . '}', $referer , $content );
						break;

					case 'quiz_percentage':
						$case = 'percentage';
						$referer = apply_filters('pie_forms_email_results' , $case);
						$content = str_replace( '{' . $other_tag . '}', $referer , $content );
						break;

					case 'quiz_result':
						$case = 'result';
						$referer = apply_filters('pie_forms_email_results' , $case);
						$content = str_replace( '{' . $other_tag . '}', $referer , $content );
						break;
	
					case 'unsubscribe_link':
						$encodedQuery = base64_encode('unsubscribelink-'. $entry_id. '-user');
						$link = get_site_url().'/?unsub='.$encodedQuery;
						$content = str_replace( '{' . $other_tag . '}', "<a href=".sanitize_text_field( $link ).">Unsubscribe</a>", $content );
						break;
					case 'display_name':
						$entry = Pie_Forms()->entry()->pform_get_entry( $entry_id );
						
						$entry_fields = Pie_forms()->core()->pform_decode( $entry->fields );
						foreach ($entry_fields as $key => $value) {
							if($value['type'] == "name"){
								$content = str_replace( '{' . $other_tag . '}', $value['value'], $content );
							}
							
						}
						break;
					case 'email':
						$entry = Pie_Forms()->entry()->pform_get_entry( $entry_id );
						$entry_fields = Pie_forms()->core()->pform_decode( $entry->fields );
						foreach ($entry_fields as $key => $value) {
							if($value['type'] == "email"){
								$content = str_replace( '{' . $other_tag . '}', $value['value'], $content );
							}
							
						}
						break;

				}
			}
		}

		return $content;
	}
}
