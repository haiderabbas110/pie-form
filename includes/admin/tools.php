<?php
defined( 'ABSPATH' ) || exit;

	class PFORM_Admin_Tools {
		public static $importers = array();
		public static $importer_forms = array();
		/**
		 * Display Environment details.
		 */
		public static function environment() {
			?>
			<?php
			/**
			 * Pie forms Version
			 */
			
			$pie_form_ver = get_plugins();
			$pie_form_ver = $pie_form_ver['pie-forms-for-wp/pie-forms-for-wp.php'];

			if(!empty($pie_form_ver)):
				?>
				<div class="environment-info">
					<label class="info-label"><?php echo esc_html("Pie Forms Version",'pie-forms') ?></label>
					<span class="info-status"><?php echo esc_html($pie_form_ver['Name'].' '. $pie_form_ver['Version'])?></span>
				</div>
				<?php
			endif;

			/**
			 * PhP Version
			 */
			
			?>
			<div class="environment-info">
			<label class="info-label"><?php echo esc_html("PHP Version",'pie-forms') ?></label>
				<span class="info-status"><?php echo  esc_html(phpversion()) ?></span>
			</div>
			
			<?php
			/**
			 *  MySQL Version
			 */
			?>

			<div class="environment-info">
			<label class="info-label"><?php echo esc_html("MySQL Version",'pie-forms') ?></label>
			<?php
				global $wpdb;
				$mysqli_version = $wpdb->db_version();
				?>
					<span class="info-status"><?php echo esc_html($mysqli_version) ?></span>
			</div>

			<?php
			/**
			 * Wordpress Version 
			 */
			?>
			
			<div class="environment-info">
            <label class="info-label"><?php echo esc_html("Wordpress Version",'pie-forms') ?></label>
				<span class="info-status"><?php echo esc_html( get_bloginfo('version')) ?></span>
				
			</div>
			
			<?php
			/**
			 * Curl
			 */
			?>

 			<div class="environment-info">
				<label class="info-label"><?php echo esc_html("Curl",'pie-forms') ?></label>
				<?php
				if(function_exists('curl_version')):
				?>
					<span class="info-status"><?php echo esc_html("Enable","pie-forms")?></span>
				<?php 
				else:
				?>
					<span class="info-status"><?php echo esc_html("Disable","pie-forms")?></span>
				<?php endif; ?>
			</div>
			
			<?php 
			/**
			 * File Get Contents
			 */
			?>

			 <div class="environment-info">
				<label class="info-label"><?php echo esc_html("File Get Contents",'pie-forms') ?></label>
				<?php 
				if(function_exists('file_get_contents')):
				?>
					<span class="info-status"><?php echo esc_html("Enable","pie-forms")?></span>
				<?php 
				else:
				?>
					<span class="info-status"><?php echo esc_html("Disable","pie-forms")?></span>
				<?php
				endif;
				?>
			</div>

			<?php 
			/**
			 * MB String
			 */
			?>

			<div class="environment-info">
				<label class="info-label"><?php _e("MB String",'pie-forms') ?></label>
				<?php if (extension_loaded('mbstring')):
				?>
					<span class="info-status"><?php echo esc_html("Enable","pie-forms") ?></span>
				<?php				
				else:
				?>
					<span class="info-status">'<?php echo esc_html("Disable","pie-forms") ?></span>
				<?php
				endif;
				?>
			</div>

			<?php 
			/**
			 * PHP Post Max Size
			 * PHP Time Limit
			 */

			?>
			<div class="environment-info">
				<label class="info-label"><?php echo esc_html("PHP Post Max Size",'pie-forms') ?></label>
				<span class="info-status"><?php echo esc_html(ini_get('post_max_size')) ?></span>
			</div>
			<div class="environment-info">
				<label class="info-label"><?php echo esc_html("PHP Time Limit",'pie-forms') ?></label>
				<span class="info-status"><?php echo esc_html(ini_get('max_execution_time')) ?></span>
			</div>				

			<?php 
			 
			/**
			 * WP Memory Limit
			 */
			
			?>
			<div class="environment-info">
                <label class="info-label"><?php echo esc_html("WP Memory Limit",'pie-forms') ?></label>
				<span class="info-status"><?php echo esc_html(WP_MEMORY_LIMIT) ?></span>
            </div>
					
			<?php
			/**
			 * WP Debug Mode
			 */
			?>

			<div class="environment-info">
				<label class="info-label"><?php echo esc_html("WP Debug Mode",'pie-forms') ?></label>
				<?php
				if ( defined('WP_DEBUG') && WP_DEBUG ):
				?>
					<span class="info-status"><?php echo esc_html( 'Yes', 'pie-forms' ) ?></span> 
				<?php
					else:
				?>
				<span class="info-status">'<?php echo esc_html( 'No', 'pie-forms' )?></span>
				<?php 
				endif;
				?>
			</div>
			
			<?php
			/**
			 * WP Language
			 */
			?>

			<div class="environment-info">
				<label class="info-label"><?php echo esc_html("WP Language",'pie-forms') ?></label>
				<span class="info-status"><?php echo esc_html(get_locale())?></span>
			</div>

			
			<?php
			/**
			 * WP Language
			 */
			?>

			<div class="environment-info">
				<label class="info-label"><?php echo esc_html('WP Max Upload Size','pie-forms') ?></label>
				<span class="info-status"><?php echo esc_html(size_format( wp_max_upload_size() ))?></span>
			</div>
		<?php	
		}

		/**
		 * Display Plugins and Themes details.
		 */
		public static function plugins_and_themes() {
		?>
			<textarea id="pie-forms-plugins-and-themes" name="pie-forms-plugins-and-themes" readonly="readonly">
			<?php

				/**
				 * For themes 
				 */
				$themes 		= wp_get_themes();
				
				#$current_theme = get_current_theme(); get_current_theme() is deprecated since version 3.4!
				$current_theme 	= wp_get_theme();

				echo "\r\n=== Themes ===\r\n\r\n";
				foreach($themes as $theme){
					if( $current_theme == $theme['Name'] )
						echo esc_html($theme['Name'])." [ACTIVATED]\r\n";
					else
						echo esc_html($theme['Name'])." [DEACTIVATED]\r\n";
				}
				
				$activate_plugins 	= get_option('active_plugins');
				$all_plugins 		= get_plugins();
				
				echo "\r\n\r\n=== Plugins (".esc_html(count($activate_plugins))."/".esc_html(count($all_plugins)).") ===\r\n\r\n";
				foreach($all_plugins as $key=>$plugin){
					if( in_array($key,$activate_plugins) )
						echo esc_html($plugin['Name'])." [ACTIVATED]\r\n";
					else
						echo esc_html($plugin['Name'])." [DEACTIVATED]\r\n";
				}
			?>
			</textarea>	
		<?php
		}

		public static function import(){
			self::$importers = apply_filters( 'pieforms_importers', self::$importers );
			?>
			<div class="pieforms-setting-row tools" id="pieforms-importers">
				<h4><?php esc_html_e( 'Import from Other Form Plugins', 'pie-forms' ); ?></h4>

				<div class="pieforms-importers-wrap">
					<?php if ( empty( self::$importers ) ) { ?>
						<p><?php esc_html_e( 'No form importers are currently enabled.', 'pie-forms' ); ?> </p>
					<?php } else { ?>
						<form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>?page=pf-tools&tab=import">
							<span class="choicesjs-select-wrap">
							<input type="hidden" name="page" value="pf-tools">
							<input type="hidden" name="tab" value="import">
								<select class="choicesjs-select" name="provider" required>
									<option value="" placeholder><?php esc_html_e( 'Select previous contact form plugin...', 'pie-forms' ); ?></option>
									<?php
									foreach ( self::$importers as $importer ) {
										$status = '';
										if ( empty( $importer['installed'] ) ) {
											$status = esc_html__( 'Not Installed', 'pie-forms' );
										} elseif ( empty( $importer['active'] ) ) {
											$status = esc_html__( 'Not Active', 'pie-forms' );
										}
										printf(
											'<option value="%s" %s>%s %s</option>',
											esc_attr( $importer['slug'] ),
											! empty( $status ) ? 'disabled' : '',
											esc_html( $importer['name'] ),
											! empty( $status ) ? '(' . $status . ')' : ''
										);
									}
									?>
								</select>
							</span>
							<br />
							<br />
							<button type="submit" class="pie-forms-btn"><?php esc_html_e( 'Import', 'pie-forms' ); ?></button>
						</form>
					<?php } ?>
				</div>
			</div>
		<?php }

		/**
		 * Importer tab contents.
		 */
		public static function importer_tab() {

			$slug     = ! empty( $_GET['provider'] ) ? sanitize_key( $_GET['provider'] ) : '';
			$provider = self::$importers[ $slug ];
			?>

			<div class="pieforms-setting-row tools pieforms-clear section-heading no-desc">
				<div class="pieforms-setting-field">
					<h1 class='admin-main-heading'><?php esc_html_e( 'Form Import', 'pie-forms' ); ?></h1>
				</div>
			</div>

			<div id="pieforms-importer-forms">
				<div class="pieforms-setting-row tools">
					<p><?php esc_html_e( 'Select the forms you would like to import.', 'pie-forms' ); ?></p>

					<div class="checkbox-multiselect-columns">
						<div class="first-column">
							<h5 class="header"><?php esc_html_e( 'Available Forms', 'pie-forms' ); ?></h5>

							<ul>
								<?php
								if ( empty( self::$importer_forms ) ) {
									echo '<li>' . esc_html__( 'No forms found.', 'pie-forms' ) . '</li>';
								} else {
									foreach ( self::$importer_forms as $id => $form ) {
										printf(
											'<li><label><input type="checkbox" name="forms[]" value="%s">%s</label></li>',
											esc_attr( $id ),
											esc_html( $form )
										);
									}
								}
								?>
							</ul>

							<?php if ( ! empty( self::$importer_forms ) ) : ?>
								<a href="#" class="all"><?php esc_html_e( 'Select All', 'pie-forms' ); ?></a>
							<?php endif; ?>

						</div>
						<div class="second-column">
							<h5 class="header"><?php esc_html_e( 'Forms to Import', 'pie-forms' ); ?></h5>
							<ul></ul>
						</div>
					</div>
				</div>

				<?php if ( ! empty( self::$importer_forms ) ) : ?>
					<p class="submit">
						<button class="pie-forms-btn" id="pieforms-importer-forms-submit"><?php esc_html_e( 'Import', 'pie-forms' ); ?></button>
					</p>
				<?php endif; ?>
			</div>

			<div id="pieforms-importer-analyze" style='display:none'>
				<p class="process-analyze">
					<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
					<?php
					printf(
						/* translators: %1$s - current forms counter; %2$s - total forms counter; %3$s - provider name. */
						esc_html__( 'Analyzing %1$s of %2$s forms from %3$s.', 'pie-forms' ),
						'<span class="form-current">1</span>',
						'<span class="form-total">0</span>',
						esc_html( $provider['name'] )
					);
					?>
				</p>
				<div class="upgrade">
				<h5><?php esc_html_e( 'Heads Up!', 'pie-forms' ); ?></h5>
				<p><?php esc_html_e( 'One or more of your forms contain fields that are not available in Pie Forms Free', 'pie-forms' ); ?></p>
				<p>
					<a href="https://pieforms.com/plan-and-pricing/" target="_blank" rel="noopener noreferrer" class="pieforms-btn pieforms-btn-md pieforms-btn-orange pieforms-upgrade-modal"><?php esc_html_e( 'Upgrade to Pie Forms Pro', 'pie-forms' ); ?></a>
					<a href="#" class="pieforms-btn pieforms-btn-md pieforms-btn-light-grey" id="pieforms-importer-continue-submit"><?php esc_html_e( 'Continue Import without Upgrading', 'pie-forms' ); ?></a>
				</p>
				<hr>
				<p><?php esc_html_e( 'Below is the list of form fields that may be impacted:', 'pie-forms' ); ?></p>
			</div>
			</div>

			<div id="pieforms-importer-process">

				<p class="process-count">
					<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
					<?php
					printf(
						/* translators: %1$s - current forms counter; %2$s - total forms counter; %3$s - provider name. */
						esc_html__( 'Importing %1$s of %2$s forms from %3$s.', 'pie-forms' ),
						'<span class="form-current">1</span>',
						'<span class="form-total">0</span>',
						esc_html( $provider['name'] )
					);
					?>
				</p>

				<p class="process-completed">
					<?php
					printf(
						/* translators: %s - number of imported forms. */
						esc_html__( 'Congrats, the import process has finished! We have successfully imported %s forms. You can review the results below.', 'pie-forms' ),
						'<span class="forms-completed"></span>'
					);
					?>
				</p>

				<div class="status"></div>

			</div>
			<?php
		}

		/**
	 * Controller for Tools -> Import tab.
	 *
	 */
	public static function import_controller() {
		
		self::$importers = apply_filters( 'pieforms_importers', self::$importers );
		
		// Get all forms for the previous form provider.
		if ( ! empty( $_GET['provider'] ) ) { 
			$provider             = sanitize_key( $_GET['provider'] ); 
			self::$importer_forms = apply_filters( "pieforms_importer_forms_{$provider}", self::$importer_forms );
		}

		// Load the Underscores templates for importers.
		if(!empty(self::importer_templates())){

			add_action( 'admin_print_scripts', self::importer_templates() );
		}
	}


	/**
	 * Various Underscores templates for form importing.
	 */
	public static function importer_templates() {

		?>

		<script type="text/html" id="tmpl-pieforms-importer-upgrade">
			<# _.each( data, function( item, key ) { #>
				<ul>
					<li class="form">{{ item.name }}</li>
					<# _.each( item.fields, function( val, key ) { #>
						<li>{{ val }}</li>
					<# }) #>
				</ul>
			<# }) #>
		</script>
		<script type="text/html" id="tmpl-pieforms-importer-status-error">
			<div class="item">
				<div class="pieforms-clear">
					<span class="name">
						<i class="status-icon fa fa-times" aria-hidden="true"></i>
						{{ data.name }}
					</span>
				</div>
				<p>{{ data.msg }}</p>
			</div>
		</script>
		<script type="text/html" id="tmpl-pieforms-importer-status-update">
			<div class="item">
				<div class="pieforms-clear">
					<span class="name">
						<# if ( ! _.isEmpty( data.upgrade_omit ) ) { #>
							<i class="status-icon fa fa-exclamation-circle" aria-hidden="true"></i>
						<# } else if ( ! _.isEmpty( data.upgrade_plain ) ) { #>
							<i class="status-icon fa fa-exclamation-triangle" aria-hidden="true"></i>
						<# } else if ( ! _.isEmpty( data.unsupported ) ) { #>
							<i class="status-icon fa fa-info-circle" aria-hidden="true"></i>
						<# } else { #>
							<i class="status-icon fa fa-check" aria-hidden="true"></i>
						<# } #>
						{{ data.name }}
					</span>
					<span class="actions">
						<a href="{{ data.edit }}" target="_blank"><?php esc_html_e( 'Edit', 'pie-forms' ); ?></a>
						<span class="sep">|</span>
						<a href="{{ data.preview }}" target="_blank"><?php esc_html_e( 'Preview', 'pie-forms' ); ?></a>
					</span>
				</div>
				<# if ( ! _.isEmpty( data.upgrade_omit ) ) { #>
					<p><?php esc_html_e( 'The following fields are available in PRO and were not imported:', 'pie-forms' ); ?></p>
					<ul>
						<# _.each( data.upgrade_omit, function( val, key ) { #>
							<li>{{ val }}</li>
						<# }) #>
					</ul>
				<# } #>
				<# if ( ! _.isEmpty( data.upgrade_plain ) ) { #>
					<p><?php esc_html_e( 'The following fields are available in PRO and were imported as text fields:', 'pie-forms' ); ?></p>
					<ul>
						<# _.each( data.upgrade_plain, function( val, key ) { #>
							<li>{{ val }}</li>
						<# }) #>
					</ul>
				<# } #>
				<# if ( ! _.isEmpty( data.unsupported ) ) { #>
					<p><?php esc_html_e( 'The following fields are not supported and were not imported:', 'pie-forms' ); ?></p>
					<ul>
						<# _.each( data.unsupported, function( val, key ) { #>
							<li>{{ val }}</li>
						<# }) #>
					</ul>
				<# } #>
				<# if ( ! _.isEmpty( data.upgrade_plain ) || ! _.isEmpty( data.upgrade_omit ) ) { #>
				<p>
					<?php esc_html_e( 'Upgrade to the PRO plan to import these fields.' ); ?><br><br>
					
				</p>
				<# } #>
			</div>
		</script>
		<?php
	}



	}