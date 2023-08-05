<?php

 $templates = Pie_Forms()->templateView()->page_output(); 
?>
<div class="dashboard-wrapper">
  <ul class="button-group filters-button-group">
    <li> <button class="button is-checked" data-filter="*"><?php esc_html_e( 'All', 'pie-forms' ); ?></button> </li>
    <li> <button class="button" data-filter=".free"><?php esc_html_e( 'Free', 'pie-forms' ); ?></button> </li>
    <li> <button class="button" data-filter=".premium , .quiz"><?php esc_html_e( 'Premium', 'pie-forms' ); ?></button> </li>
    <?php 
      // if(is_plugin_active('pie-forms-for-wp-quiz/pie-forms-for-wp-quiz.php') || get_option('pieforms_manager_addon_quiz_activated') == 'Activated'){              
        ?>
        <!-- <li> <button class="button" data-filter=".quiz"><?php _e( 'Quiz Forms', 'pie-forms' ); ?></button> </li> -->
        <?php
      // }
    ?>
  </ul>
  <div class="pf-content-area">
      <div class="pie-forms-logo">
        <img src="<?php  echo esc_url(Pie_Forms::$url) . 'assets/images/pie-forms.png'; ?>" alt="pie-forms">
      </div>
      <div class="element-grid">
      <?php
          if ( empty( $templates ) ) {
            echo '<div id="message" class="error"><p>' . esc_html__( 'Something went wrong. Please refresh your templates.', 'pie-forms' ) . '</p></div>';
          } else {
            foreach ( $templates as $template ) :
                $plan = $template->plan;
                if( $plan === 'premium'){
                  $upgrade_class = 'upgrade-modal upgrade';
                } 
                elseif( $plan === 'quiz' ){
                  if(! is_plugin_active('pie-forms-for-wp-quiz/pie-forms-for-wp-quiz.php')){
                    $upgrade_class = 'upgrade-modal upgrade';
                  }
                  else{
                    $upgrade_class = '';
                    $quiz_active = 'quiz_active';
                  }
                }
                else{
                  $upgrade_class = '';
                  $quiz_active = '';
                }
                // $upgrade_class = ( $plan === 'premium' || $plan === 'quiz') ? 'upgrade-modal upgrade' : '';
            
            ?>
              <div class="element-item <?php echo esc_html($plan); ?>" id="<?php echo esc_html($quiz_active);?>" data-template="<?php echo esc_html($template->slug); ?>">
                <div class="element-image">
                  <img src="<?php echo esc_url(Pie_Forms::$url) . 'assets/images/templates/'.esc_attr($template->image); ?>" alt="blank">
                  <div class="getting-start">
                    <a class = "<?php echo esc_attr($upgrade_class) ?>" href="javascript:;">Get Started</a>
                </div>
                </div>
                <div class="name">
                  <h3><?php echo esc_html($template->title); ?></h3>
                </div>
                <!-- <div class="description">
                  <p><?php echo esc_html($template->description); ?></p>
                </div> -->
              </div>
            <?php endforeach;
            
            }?>
        </div>
  </div>   
</div>