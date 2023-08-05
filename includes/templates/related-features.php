<?php 
$images_url = Pie_Forms::$url . 'assets/images/recommended/';
$related_data = array(
    array(
        'image'         => $images_url.esc_html('schedule-form.png' , 'pie-forms'),              
        'name'          => esc_html('Schedule Forms' , 'pie-forms'),
        'link'          => esc_url( "https://pieforms.com/schedule-form/?utm_source=admindashboard&utm_medium=recommendedsection&utm_campaign=freeuser" , 'pie-forms'),
        'description'   => esc_html( "Set up an opening and closing date on your forms for time-limited submissions" , 'pie-forms')

    ),
    array(
        'image'         => $images_url.esc_html('multipart-addon.png' , 'pie-forms'),              
        'name'          => esc_html('Multipage Addon' , 'pie-forms'),
        'link'          => esc_url( "https://pieforms.com/addons/multipage-forms-addon/?utm_source=admindashboard&utm_medium=recommendedsection&utm_campaign=freeuser" , 'pie-forms'),
        'description'   => esc_html( "Break your forms into small parts and multiple pages to provide a better user experience with the Multipage Forms Add-on" , 'pie-forms')         
    ),
    array(
        'image'         => $images_url.esc_html('drip-addon.png' , 'pie-forms'),              
        'name'          => esc_html('Drip Addon' , 'pie-forms'),
        'link'          => esc_url( "https://pieforms.com/addons/drip-addon/?utm_source=admindashboard&utm_medium=recommendedsection&utm_campaign=freeuser" , 'pie-forms'),
        'description'   => esc_html( "Design and schedule email campaigns to send automated response to your users with Pie Forms' Drip Add-on" , 'pie-forms')         
    ),     
    array(
        'image'         => $images_url.esc_html('file-upload.png' , 'pie-forms'),              
        'name'          => esc_html('File Upload' , 'pie-forms'),
        'link'          => esc_url( "https://pieforms.com/fileupload/?utm_source=admindashboard&utm_medium=recommendedsection&utm_campaign=freeuser" , 'pie-forms'),
        'description'   => esc_html( "The File Upload field in Pie Forms allows the user to upload documents, images, or videos on form submission" , 'pie-forms')          
    ),
    array(
        'image'         => $images_url.esc_html('limit-entries.png' , 'pie-forms'),              
        'name'          => esc_html('Limit Entries' , 'pie-forms'),
        'link'          => esc_url( "https://pieforms.com/limit-form-submissions?utm_source=admindashboard&utm_medium=recommendedsection&utm_campaign=freeuser" , 'pie-forms'),
        'description'   => esc_html( "Pie Forms allows you to limit the number of form submissions for a particular form" , 'pie-forms')              
    ),
    array(
        'image'         => $images_url.esc_html('block-user.png' , 'pie-forms'),              
        'name'          => esc_html('Block Users' , 'pie-forms'),
        'link'          => esc_url( "https://pieforms.com/block-users/?utm_source=admindashboard&utm_medium=recommendedsection&utm_campaign=freeuser" , 'pie-forms'),   
        'description'   => esc_html( "Block unwanted users and bots by their Username, Email address, or IP address to keep your website spam-free" , 'pie-forms')
    )     
)
?>


<div class="pie-form-related-data">
    <div class="pie-form-data-top">
        <h2><?php echo esc_html__('Recommended Features and Addons', 'pie-forms')?></h2>
    </div>
    <div class="pie-form-data-bottom">
    <?php 

    foreach($related_data as  $values){
        ?>
        <div class="pie-form-data">
            <div class="pie-form-data-img">
                <a href="<?php echo esc_url($values['link']) ?>" target="_blank"><img src="<?php echo esc_url($values['image'])?>"></a>
            </div>
            <div class="pie-form-data-info">
                <h3><a href="<?php echo esc_url($values['link']) ?>" target="_blank"><?php echo esc_html($values['name'])?></a></h3>
                <p><?php echo esc_html($values['description'])?></p>
            </div>
        </div>
        <?php
    }
        ?>
    </div>
</div>
