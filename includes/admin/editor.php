<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'PFORM_Admin_Editor', false ) ) {
	return new PFORM_Admin_Editor();
}


class PFORM_Admin_Editor {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'media_buttons', array( $this, 'media_button' ), 15 );
	}

	/**
	 * Allow easy shortcode insertion via a custom media button.
	 */
	public function media_button( $editor_id ) {
		if ( ! apply_filters( 'pie_forms_show_media_button', is_admin(), $editor_id ) ) {
			return;
		}

		if ( get_post_type( ) == 'page' || get_post_type( ) == 'post' ) {

			// Setup the svg icon.
			$svg_icon = '
			<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="14pt" height="16pt" viewBox="0 0 14 16" version="1.1">
			<g id="surface1">
			<path style=" stroke:none;fill-rule:nonzero;fill:rgb(7.45098%,22.352941%,31.372549%);fill-opacity:1;" d="M 13.820312 3.957031 L 9.769531 0.0976562 C 9.707031 0.03125 9.613281 -0.00390625 9.519531 0 L 1.742188 0 C 0.820312 0 0.0742188 0.710938 0.0742188 1.589844 L 0.0742188 14.410156 C 0.0742188 15.289062 0.820312 16 1.742188 16 L 12.257812 16 C 13.179688 16 13.925781 15.289062 13.925781 14.410156 L 13.925781 4.195312 C 13.929688 4.105469 13.890625 4.019531 13.820312 3.957031 Z M 9.859375 1.113281 L 12.753906 3.871094 L 10.84375 3.871094 C 10.300781 3.867188 9.863281 3.449219 9.859375 2.933594 Z M 13.234375 14.410156 C 13.230469 14.925781 12.789062 15.34375 12.25 15.347656 L 1.742188 15.347656 C 1.207031 15.339844 0.769531 14.921875 0.765625 14.410156 L 0.765625 1.589844 C 0.773438 1.078125 1.207031 0.667969 1.742188 0.660156 L 9.175781 0.660156 L 9.175781 2.933594 C 9.179688 3.8125 9.921875 4.519531 10.84375 4.523438 L 13.234375 4.523438 L 13.234375 14.417969 Z M 13.234375 14.410156 "/>
			<path style=" stroke:none;fill-rule:nonzero;fill:rgb(7.45098%,22.352941%,31.372549%);fill-opacity:1;" d="M 9.660156 8.902344 C 9.660156 10.300781 8.46875 11.4375 7 11.4375 C 5.53125 11.4375 4.339844 10.300781 4.339844 8.902344 C 4.339844 7.503906 5.53125 6.371094 7 6.371094 C 8.46875 6.371094 9.660156 7.503906 9.660156 8.902344 Z M 9.660156 8.902344 "/>
			<path style=" stroke:none;fill-rule:nonzero;fill:rgb(100%,100%,100%);fill-opacity:1;" d="M 7.332031 7.914062 L 7.332031 8.613281 L 8.023438 8.613281 L 8.023438 9.234375 L 7.332031 9.234375 L 7.332031 9.894531 L 6.640625 9.894531 L 6.640625 9.234375 L 5.949219 9.234375 L 5.949219 8.632812 L 6.640625 8.632812 L 6.640625 7.914062 Z M 7.332031 7.914062 "/>
			</g>
			</svg>';
			printf(
				'<a href="#" class="button pf-insert-form-button" data-editor="%s" title="%s"><span class="wp-media-buttons-icon">%s</span> %s</a>',
				esc_attr( $editor_id ),
				esc_attr__( 'Add Pie Form', 'pie-forms' ),
				$svg_icon, 
				esc_html__( 'Add Form', 'pie-forms' )
			);
		}

		add_action( 'admin_footer', array( $this, 'shortcode_modal' ) );
	}

	/**
	 * Modal window for inserting the form shortcode into TinyMCE.
	 */
	public function shortcode_modal() {
		?>
		<div id="pf-modal-backdrop" style="display: none"></div>
		<div id="pf-modal-wrap" style="display: none">
			<form id="pf-modal" tabindex="-1">
				<div id="pf-modal-title">
					<?php esc_html_e( 'Insert Form', 'pie-forms' ); ?>
					<button type="button" id="pf-modal-close"><span class="screen-reader-text"><?php esc_html_e( 'Close', 'pie-forms' ); ?></span></button>
				</div>
				<div id="pf-modal-inner">
					<div id="pf-modal-options">
						<?php
						$forms = Pie_Forms()->core()->pform_get_all_forms();

						if ( ! empty( $forms ) ) {
							printf( '<p><label for="pf-modal-select-form">%s</label></p>', esc_html__( 'Select a form below to insert', 'pie-forms' ) );
							echo '<select id="pf-modal-select-form">';
							foreach ( $forms as $form_id => $form_value ) {
								printf( '<option value="%d">%s</option>', esc_attr( $form_id ), esc_html( $form_value ) );
							}
							echo '</select>';
						} else {
							echo '<p>';
							printf(
								wp_kses(
									/* translators: %s - Pie Builder page. */
									__( 'Whoops, you haven\'t created a form yet. Want to <a href="%s">give it a go</a>?', 'pie-forms' ),
									array(
										'a' => array(
											'href' => array(),
										),
									)
								),
								esc_url( admin_url( 'admin.php?page=pf-builder' ) )
							);
							echo '</p>';
						}
						?>
					</div>
				</div>
				<div class="submitbox">
					<div id="pf-modal-cancel">
						<a class="submitdelete deletion" href="#"><?php esc_html_e( 'Cancel', 'pie-forms' ); ?></a>
					</div>
					<?php if ( ! empty( $forms ) ) : ?>
						<div id="pf-modal-update">
							<button class="button button-primary" id="pf-modal-submit"><?php esc_html_e( 'Add Form', 'pie-forms' ); ?></button>
						</div>
					<?php endif; ?>
				</div>
			</form>
		</div>
		<?php
	}
}
