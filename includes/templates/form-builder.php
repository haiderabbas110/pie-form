<?php
  $form_id          = sanitize_key($_GET[ 'form_id' ]);
  // Get tabs for the builder panel.
  $tabs = apply_filters( 'pie_forms_builder_tabs_array', array() );
  //extract($tabs);

  
  
  // Get preview link.
  $preview_link = Pie_forms()->core()->pieforms_get_form_preview_url($form_id);

?>

<div id="pie-builder-main" class="pie-builder-main" data-form_id="<?php echo esc_attr($form_id); ?>">
  <form id="pie-forms-builder-form" name="pie-forms-builder" method="post" data-id="<?php echo esc_attr(absint( $form_id )); ?>">
      <input type="hidden" name="id" value="<?php echo esc_attr(absint( $form_id )); ?>">
      <input type="hidden" name="form_enabled" value="1">
      <input type="hidden" value="" name="form_field_id" id="pie-forms-field-id">
      <header class='pie-header'>
        <div class="header-left">
          <div class='logo'><img src="<?php  echo esc_url(Pie_Forms::$url) . 'assets/images/logo-white.png'; ?>" alt="pie-forms"></div>

            <ul class="pie-center-nav">
              <li class="shortcode"><input  type="text" id="shortcode-form" name="shortcode" value="[pie_form id='<?php echo esc_attr(absint($form_id)) ?>']" readonly="readonly">
              <span id="shortcode" class="copy-icon"></span>
              <div class="copied-wrap"><?php esc_html_e('Copied!', 'pie-forms')?></div>
            </li>
              <li class="preview"><a href="<?php echo esc_url( $preview_link ); ?>" target="_blank"><?php esc_html_e( __('Preview', 'pie-forms') ); ?></a></li>
              <li class="save"><button name="save_form" class="pie-forms-btn pie-forms-save-button" type="button" value="<?php esc_attr_e( __('Save', 'pie-forms') ); ?>"><?php esc_html_e('Save', 'pie-forms')?></button></li>
            </ul>
        </div>
        <div class="right">
          <ul class="pie-right-nav">
            <li class="form-fields active" data-id="fields"><a href="javascript:;">Form Fields</a></li>
            <li class="settings" data-id="settings"><a href="javascript:;">Form Settings</a></li>
            <li class="close"><a href="<?php echo esc_url(admin_url('admin.php?page=pie-forms'))?>"></a></li>
          </ul>
        </div>
      </header>
      <main>
        <section class="pie-section-main">
              <?php $fields     = 'fields';
                    $settings = 'settings' //foreach ( $tabs as $fields => $tab ) : ?>

                  <!--FORM FIELDS ACCORDIAN START -->   
                <div class="pie-form-main-wrapper" id="<?php echo esc_attr($fields);?>">
                    <div class="pie-form-left" >
                          <div class="pie-form-element" id="pie-<?php echo esc_attr($fields); ?>">
                                <?php do_action( 'pie_forms_builder_content_' . $fields ); ?>
                          </div>
                          <div class="pie-preview-save">
                            <div class="preview-button">
                            <a href="<?php echo esc_url( $preview_link ); ?>" target="_blank"></a>  
                            </div>
                            <div class="save-button-pie">
                              <button name="save_form" class="save-button pie-forms-btn pie-forms-save-button icon-save-button" type="button" value="<?php esc_attr_e( __('Save', 'pie-forms') ); ?>"></button>
                            </div>
                          </div>
                    </div>
                    <div class="pie-form-right">

                      <div class="tab-<?php echo esc_attr($fields); ?>" id="tab-<?php echo esc_attr($fields); ?>">
                              <div class="pie-tab-setting">
                                  <ul>
                                    <li data-id="tab-add-fields" class="active">Add Fields</li>    
                                    <li data-id="tab-field-options">Field Options</li>    
                                  </ul>
                              </div>
                              <div class="pie-form-accordian-<?php echo esc_attr($fields);?> ScrollBar" id="pie-form-accordian-<?php echo esc_attr($fields);?>">
                                    <?php do_action( 'pie_forms_builder_sidebar_' . $fields ); ?>
                              </div>
                      </div>
                       <!-- FIELDS ACCORDIAN START -->


                      <!-- SETTING ACCORDIAN START -->
                      <div class="accordian-<?php echo esc_attr($settings); ?> ScrollBar" id="tab-<?php echo esc_attr($settings); ?>">
                            <div class="pie-form-accordian-<?php echo esc_attr($settings);?>" id="pie-form-accordian-<?php echo esc_attr($settings);?>">
                                    <?php do_action( 'pie_forms_builder_sidebar_' . $settings ); ?>
                              </div>

                              <div class="pie-form-element" id="pie-<?php echo esc_attr($settings); ?>">
                                <?php do_action( 'pie_forms_builder_content_' . $settings ); ?>
                          </div>
                      </div>
                      <!-- SETTING ACCORDIAN END -->


                  </div>
                </div>  
                
            <?php //endforeach; ?>
        </section>
      </main>
    </form>  
</div>