<div class="pie-form-related-docx">
    <div class="pie-form-docx-top">
        <h2><?php echo esc_html__('How to use', 'pie-forms')?></h2>
    </div>
    <div class="pie-form-docx-bottom">
    <?php 

    foreach($docx as  $values){
        ?>
        <div class="pie-form-docx">
            <div class="pie-form-docx-img">
                <a href="<?php echo esc_url($values['link']) ?>" target="_blank"><img src="<?php echo esc_url($values['image'])?>"></a>
            </div>
            <div class="pie-form-docx-info">
                <h3><a href="<?php echo esc_url($values['link']) ?>" target="_blank"><?php echo esc_html($values['name'])?></a></h3>
                <p><?php echo esc_html($values['description'])?></p>
            </div>
        </div>
        <?php
    }
        ?>
    </div>
</div>
