<?php 
$value = 	 ! empty( $_REQUEST['s'] ) ? sanitize_text_field($_REQUEST['s']) : '';  
?>
<p class="search-box">
	<input id="search_id-search-input" type="text" name="s" value="<?php echo esc_attr($value) ?> " /> 
	<input id="search-submit" class="button" type="submit" name="" value="Search Entry" />
</p>