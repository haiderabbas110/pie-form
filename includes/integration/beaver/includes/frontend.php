<?php
$data = Pie_Forms()->form()->get( $settings->form_id);

$form =array_shift($data);

if(! empty( $settings->show_title ) && 'on' === $settings->show_title)
	$show_title = true;
else
	$show_title = false;

if(! empty( $settings->show_desc ) && 'on' === $settings->show_desc)
	$show_desc = true;
else
	$show_desc = false;

$text = (
	sprintf(
		'[pie_form id="%1$s" title="%2$s" description="%3$s"]',
		absint( isset($form->form_id) ? $form->form_id : 0 ),
		(bool) apply_filters( 'pieforms_beaver_builder_form_title', $show_title, absint(isset($form->form_id ) ? $form->form_id : 0) ),
		(bool) apply_filters( 'pieforms_beaver_builder_form_desc', $show_desc, absint( isset($form->form_id ) ? $form->form_id : 0 ) )
	)
	);

	printf(
		'<p class="wpforms-shortcode-amp-text">%s</p>',
		wp_kses_post( $text )
	);
?>