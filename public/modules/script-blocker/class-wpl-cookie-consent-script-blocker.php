<?php
/**
 * The cookie script blocker functionality of the plugin.
 *
 * @link       https://club.wpeka.com/
 * @since      3.0.0
 *
 * @package    Gdpr_Cookie_Consent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * The frontend-specific functionality for cookie script blocker.
 *
 * @package    Gdpr_Cookie_Consent
 * @subpackage Gdpr_Cookie_Consent/public/modules
 * @author     wpeka <https://club.wpeka.com>
 */
class Gdpr_Cookie_Consent_Script_Blocker {
	/**
	 * Main scripts table.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string $main_table Main scripts table.
	 */
	public $main_table = 'wpl_cookie_scripts';

	/**
	 * Gdpr_Cookie_Consent_Script_Blocker constructor.
	 */
	public function __construct() {
		require plugin_dir_path( __FILE__ ) . 'classes/class-wpl-cookie-consent-script-blocker-ajax.php';
		require plugin_dir_path( __FILE__ ) . 'classes/class-wpl-cookie-consent-script-blocker-frontend.php';
		// Creating necessary tables for script blocker.
		register_activation_hook( GDPR_COOKIE_CONSENT_PLUGIN_FILENAME, array( $this, 'wpl_activator' ) );
		add_action( 'admin_init', array( $this, 'wpl_activator' ) );

		if ( Gdpr_Cookie_Consent::is_request( 'admin' ) ) {
			add_filter( 'gdprcookieconsent_script_blocker_sub_tabs', array( $this, 'wpl_script_blocker_sub_tabs' ) );
			add_action( 'gdpr_settings_script_blocker_tab', array( $this, 'wpl_script_blocker_advanced_form' ) );
			add_action( 'gdpr_settings_script_blocker_tab', array( $this, 'wpl_script_blocker_advanced_tab' ) );
			add_filter( 'gdpr_settings_script_blocker_values', array( $this, 'wpl_script_blocker_values' ) );
		}
		add_action( 'wp_ajax_wpl_script_add', array( $this, 'wpl_ajax_script_add' ), 10, 1 );
		add_action( 'wp_ajax_wpl_script_save', array( $this, 'wpl_ajax_script_save' ), 10, 1 );
	}

	/**
	 * Run during the plugin's activation to install required tables in database.
	 *
	 * @since 3.0.0
	 */
	public function wpl_activator() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		if ( is_multisite() ) {
			// Get all blogs in the network and activate plugin on each one.
			$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" ); // db call ok; no-cache ok.
			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				$this->wpl_install_tables();
				restore_current_blog();
			}
		} else {
			$this->wpl_install_tables();
		}
	}

	/**
	 * Installs necessary tables.
	 *
	 * @since 3.0.0
	 */
	public function wpl_install_tables() {
		global $wpdb;

		$wild = '%';
		// Creating main table.
		$table_name = $wpdb->prefix . $this->main_table;
		$find       = $table_name;
		$like       = $wild . $wpdb->esc_like( $find ) . $wild;

		$result = $wpdb->get_results( $wpdb->prepare( 'SHOW TABLES LIKE %s', array( $like ) ), ARRAY_N ); // db call ok; no-cache ok.
		if ( ! $result ) {
			$create_table_sql = "CREATE TABLE `$table_name`(
			    `id` INT NOT NULL AUTO_INCREMENT,
                `script_title` TEXT NOT NULL,
                `script_category` INT NOT NULL,
                `script_status` BOOL NOT NULL,
                `script_description` LONGTEXT NOT NULL,
                `script_key` VARCHAR(100) NOT NULL,
                PRIMARY KEY(`id`)
			);";
			dbDelta( $create_table_sql );
		}
		$total_data = $wpdb->get_row( 'SELECT COUNT(id) AS ttnum FROM ' . $wpdb->prefix . 'wpl_cookie_scripts', ARRAY_A ); // db call ok; no-cache ok.
		if ( '0' === $total_data['ttnum'] || $total_data['ttnum'] ) {
			// Get category id for Unclassified.
			$data_arr = $this->get_category_by_slug( 'unclassified' );
			if ( isset( $data_arr ) && ! empty( $data_arr ) ) {
				foreach ( $data_arr as $data ) {
					if ( isset( $data->id_gdpr_cookie_category ) ) {
						$category = $data->id_gdpr_cookie_category;
					} else {
						$category = 5;
					}
				}
			} else {
				$category = 5;
			}
			// Insert records into Script blocker table.
			$records = array(
				array(
					'script_key'         => 'googleanalytics',
					'script_title'       => 'GA4 Analytics',
					'script_category'    => $category,
					'script_status'      => 1,
					'script_description' => 'GA4 scripts',
				),
				array(
					'script_key'         => 'facebook_pixel',
					'script_title'       => 'Meta Pixel',
					'script_category'    => $category,
					'script_status'      => 1,
					'script_description' => 'Meta pixel scripts',
				),
				array(
					'script_key'         => 'google_tag_manager',
					'script_title'       => 'Google Tag Manager',
					'script_category'    => $category,
					'script_status'      => 1,
					'script_description' => 'Google tag manager Scripts',
				),
				array(
					'script_key'         => 'hotjar',
					'script_title'       => 'Hotjar Analytics',
					'script_category'    => $category,
					'script_status'      => 1,
					'script_description' => 'Hotjar analytics scripts',
				),
				array(
					'script_key'         => 'google_publisher_tag',
					'script_title'       => 'Google Publisher Tag',
					'script_category'    => $category,
					'script_status'      => 1,
					'script_description' => 'Google publisher tag (Google Ad manager)',
				),
				array(
					'script_key'         => 'youtube_embed',
					'script_title'       => 'Youtube Embed',
					'script_category'    => $category,
					'script_status'      => 1,
					'script_description' => 'Youtube player embed',
				),
				array(
					'script_key'         => 'vimeo_embed',
					'script_title'       => 'Vimeo Embed',
					'script_category'    => $category,
					'script_status'      => 1,
					'script_description' => 'Vimeo player embed',
				),
				array(
					'script_key'         => 'google_maps',
					'script_title'       => 'Google Maps',
					'script_category'    => $category,
					'script_status'      => 1,
					'script_description' => 'Google maps embed',
				),
				array(
					'script_key'         => 'addthis_widget',
					'script_title'       => 'Addthis Widget',
					'script_category'    => $category,
					'script_status'      => 1,
					'script_description' => 'Addthis social widget',
				),
				array(
					'script_key'         => 'sharethis_widget',
					'script_title'       => 'Sharethis Widget',
					'script_category'    => $category,
					'script_status'      => 1,
					'script_description' => 'Sharethis social widget',
				),
				array(
					'script_key'         => 'twitter_widget',
					'script_title'       => 'X Widget',
					'script_category'    => $category,
					'script_status'      => 1,
					'script_description' => 'X social widget',
				),
				array(
					'script_key'         => 'soundcloud_embed',
					'script_title'       => 'Soundcloud Embed',
					'script_category'    => $category,
					'script_status'      => 1,
					'script_description' => 'Soundcloud player embed',
				),
				array(
					'script_key'         => 'slideshare_embed',
					'script_title'       => 'Slideshare Embed',
					'script_category'    => $category,
					'script_status'      => 1,
					'script_description' => 'Slideshare embed',
				),
				array(
					'script_key'         => 'linkedin_widget',
					'script_title'       => 'Linkedin Widget',
					'script_category'    => $category,
					'script_status'      => 1,
					'script_description' => 'Linkedin social widget',
				),
				array(
					'script_key'         => 'instagram_embed',
					'script_title'       => 'Instagram Embed',
					'script_category'    => $category,
					'script_status'      => 1,
					'script_description' => 'Instagram embed',
				),
				array(
					'script_key'         => 'pinterest',
					'script_title'       => 'Pinterest Widget',
					'script_category'    => $category,
					'script_status'      => 1,
					'script_description' => 'Pinterest widget',
				),
				array(
					'script_key'         => 'tawk',
					'script_title'       => 'Tawk Widget',
					'script_category'    => $category,
					'script_status'      => 1,
					'script_description' => 'Chat widget',
				),
				array(
					'script_key'         => 'hubspot',
					'script_title'       => 'Hubspot Analytics',
					'script_category'    => $category,
					'script_status'      => 1,
					'script_description' => 'Hubspot Analytics',
				),
				array(
					'script_key'         => 'recaptcha',
					'script_title'       => 'Google Recaptcha',
					'script_category'    => $category,
					'script_status'      => 1,
					'script_description' => 'Google Recaptcha',
				),
				array(
					'script_key'         => 'adsense',
					'script_title'       => 'Google Adsense',
					'script_category'    => $category,
					'script_status'      => 1,
					'script_description' => 'Google Adsense',
				),
				array(
					'script_key'         => 'matomo',
					'script_title'       => 'Matomo Analytics',
					'script_category'    => $category,
					'script_status'      => 1,
					'script_description' => 'Matomo Analytics',
				),
			);
			foreach ( $records as $key => $value ) {
				$data_exists = $wpdb->get_row( $wpdb->prepare( 'SELECT id FROM ' . $wpdb->prefix . 'wpl_cookie_scripts WHERE `script_key`=%s', array( $value['script_key'] ) ), ARRAY_A ); // db call ok; no-cache ok.
				if ( $data_exists ) {
					// Update the script_title and script_description if the entry exists.
					$wpdb->update(
						$table_name,
						array(
							'script_title'       => $value['script_title'], // Replace with the new title.
							'script_description' => $value['script_description'], // Replace with the new description.
						),
						array( 'id' => $data_exists['id'] ),
						array(
							'%s', // Format for script_title.
							'%s', // Format for script_description.
						),
						array( '%d' ) // Format for WHERE clause.
					);
				} else {
					// Insert a new entry if it doesn't exist.
					$wpdb->insert( $table_name, $value ); // db call ok; no-cache ok.
				}
			}
		}
	}

	/**
	 * Add script blocker advanced tab.
	 *
	 * @param array $tabs Tabs array.
	 * @return mixed
	 */
	public function wpl_script_blocker_sub_tabs( $tabs ) {
		$tabs['script-blocker-advanced'] = __( 'Advanced', 'gdpr-cookie-consent' );
		return $tabs;
	}

	/**
	 * Script blocker settings form.
	 *
	 * @since 3.0.0
	 */
	public function wpl_script_blocker_advanced_form() {
		$data_arr      = $this->get_categories();
		$category_list = array();
		foreach ( $data_arr as $category ) {
			$category_list[ $category->id_gdpr_cookie_category ] = $category->gdpr_cookie_category_name;
		}
		$scripts_list = $this->get_cookie_scripts();
		wp_enqueue_script( 'wplcookieconsent_script_blocker', plugin_dir_url( __FILE__ ) . 'assets/js/script-blocker' . GDPR_CC_SUFFIX . '.js', array(), GDPR_COOKIE_CONSENT_VERSION, true );
		$params = array(
			'nonces'        => array(
				'wpl_script_blocker' => wp_create_nonce( 'wpl_script_blocker' ),
			),
			'ajax_url'      => admin_url( 'admin-ajax.php' ),
			'scripts_list'  => $scripts_list,
			'category_list' => $category_list,
		);
		wp_localize_script( 'wplcookieconsent_script_blocker', 'wplcookieconsent_script_blocker', $params );

		$view_file     = 'scripts.php';
		$error_message = '';

		$view_file = plugin_dir_path( __FILE__ ) . 'views/' . $view_file;

		Gdpr_Cookie_Consent::gdpr_envelope_settings_tabcontent( 'gdpr_sub_tab_content', 'script-blocker-advanced', $view_file, '', $params, 2, $error_message );
	}

	/***
	 * Script blocker localization values filter callback.
	 *
	 * @since 3.0.0
	 */
	public function wpl_script_blocker_values() {
		$data_arr      = $this->get_categories();
		$category_list = array();
		$index         = 0;
		foreach ( $data_arr as $category ) {
			$category_list[ $index ] = array(
				'label' => $category->gdpr_cookie_category_name,
				'code'  => $category->id_gdpr_cookie_category,
			);
			++$index;
		}
		$scripts_list = $this->get_cookie_scripts();
		$params       = array(
			'nonces'        => array(
				'wpl_script_blocker' => wp_create_nonce( 'wpl_script_blocker' ),
			),
			'ajax_url'      => admin_url( 'admin-ajax.php' ),
			'scripts_list'  => $scripts_list,
			'category_list' => $category_list,
		);
		return $params;
	}

	/**
	 * Script blocker settings popup form.
	 *
	 * @since 3.0.0
	 */
	public function wpl_script_blocker_advanced_tab() {
		?>
		<c-tab v-show="show_revoke_card" title="<?php esc_attr_e( 'Script Blocker', 'gdpr-cookie-consent' ); ?>" href="#cookie_settings#script_blocker" id="gdpr-cookie-consent-script-blocker">
			<c-card class="script-blocker-card">
				<c-row>
					<c-col class="col-sm-32">
						<div id="gdpr-cookie-consent-settings-configure-cookie-bar-top"><?php esc_html_e( 'Script Blocker Settings', 'gdpr-cookie-consent' ); ?></div>
					</c-col>
				</c-row>
				<c-card-body style="position:relative;">
					<div :class="{ 'overlay-script-style': enable_safe}" v-show="enable_safe">
						<div :class="{ 'overlay-script-message': enable_safe}">
						<img id="safe-mode-activate-img"src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/safe-mode-lock.png'; ?>" alt="WP Cookie Consent Logo">
						<?php
						esc_attr_e(
							'Safe Mode enabled. Disable it in Compliance settings to configure Script Blocker settings.',
							'gdpr-cookie-consent'
						);
						?>
						</div>
					</div>
					<c-row>
						<c-col class="col-sm-4"><label><?php esc_attr_e( 'Script Blocker', 'gdpr-cookie-consent' ); ?></label></c-col>
						<c-col class="col-sm-8">
							<c-switch v-bind="labelIcon" v-model="is_script_blocker_on" id="gdpr-cookie-consent-script-blocker-on" variant="3d"  color="success" :checked="!enable_safe && is_script_blocker_on" v-on:update:checked="onSwitchingScriptBlocker" :disabled="enable_safe"></c-switch>
							<input type="hidden" name="gcc-script-blocker-on" v-model="is_script_blocker_on" :disabled="enable_safe">
						</c-col>
					</c-row>
					<!-- Added Header,Body,Footer ScriptSection -->
					<c-row>
						<c-col class="col-sm-4"><label><?php esc_attr_e( 'Custom Scripts', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Enter non functional cookies javascript code here to be used after the consent is accepted.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
						<c-col class="col-sm-8">
							<div role="group" class="form-group">
							<span class="gdpr-cookie-consent-description"><?php esc_attr_e( 'Enter non functional cookies javascript code here (for e.g. Google Analytics) to be used after the consent is accepted.', 'gdpr-cookie-consent' ); ?></span>
							</div>
						</c-col>
					</c-row>
					<c-row>
						<c-col class="col-sm-4"><label><?php esc_attr_e( 'Header Scripts', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Add scripts in the header location. Upon acceptance these scripts will run in the visitor\'s browser.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
						<c-col class="col-sm-8">
							<c-textarea :rows="4" name="gcc-header-scripts" v-model="header_scripts" :disabled="enable_safe"></c-textarea>
						</c-col>
					</c-row>
					<c-row>
						<c-col class="col-sm-4"><label><?php esc_attr_e( 'Body Scripts', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Add scripts in the body location. Upon acceptance these scripts will run in the visitor\'s browser.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
						<c-col class="col-sm-8">
							<c-textarea :rows="4" name="gcc-body-scripts" v-model="body_scripts" :disabled="enable_safe"></c-textarea>
						</c-col>
					</c-row>
					<c-row>
						<c-col class="col-sm-4"><label><?php esc_attr_e( 'Footer Scripts', 'gdpr-cookie-consent' ); ?> <tooltip text="<?php esc_html_e( 'Add scripts in the footer location. Upon acceptance these scripts will run in the visitor\'s browser.', 'gdpr-cookie-consent' ); ?>"></tooltip></label></c-col>
						<c-col class="col-sm-8">
							<c-textarea :rows="4" name="gcc-footer-scripts" v-model="footer_scripts" :disabled="enable_safe"></c-textarea>
						</c-col>
					</c-row>
					<c-row v-show="is_gdpr">
						<c-col class="col-sm-4"><label><?php esc_attr_e( 'Click here to manually select the cookie categories', 'gdpr-cookie-consent' ); ?></label></c-col>
						<c-col class="col-sm-8">
							<c-button id="script-blocker-advanced-settings-btn" @click="showScriptBlockerForm" :disabled="enable_safe"><span>Advanced Settings</span></c-button>
						</c-col>
					</c-row>
					<v-modal :append-to="appendField" :based-on="show_script_blocker" @click="showScriptBlockerForm">
						<div class="advanced-settings-wrapper">
							<div class="advances-settings-tittle-bar">
								<div class="advances-setting-tittle" slot="header"><?php esc_attr_e('Advanced Settings', 'gdpr-cookie-consent'); ?></div>
								<img  @click="showScriptBlockerForm" class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/cancel.svg'; ?>" alt="Add new entry logo">
							</div>
							<c-card>
							<c-card-body class="gdpr-script-blocker-table" v-if="scripts_list_total > 0">
								<table class="advanced-settings-table-container">
								<thead>
									<tr scope="col" class="gdpr-script-blocker-header">
									<th class="gdpr-cookie-consent-script-left"style="text-align: center;"><?php esc_attr_e('Enabled', 'gdpr-cookie-consent'); ?></th>
									<th class="gdpr-cookie-consent-script-left"><?php esc_attr_e('Name', 'gdpr-cookie-consent'); ?></th>
									<th class="gdpr-cookie-consent-script-left"><?php esc_attr_e('Description', 'gdpr-cookie-consent'); ?></th>
									<th class="gdpr-cookie-consent-script-left"><?php esc_attr_e('Category', 'gdpr-cookie-consent'); ?></th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="script in scripts_list_data" :key="script['id']" :class="{'gdpr-script-blocker-data': true, 'gdpr-script-blocker-data-even': script['id'] % 2 === 0, 'gdpr-cookie-consent-script-blocker-row': true}">
									<td class="col-sm-2" style="text-align: center;">
										<c-switch
										v-bind="labelIcon"
										v-model="script['script_status']"
										id="gdpr-cookie-consent-script-status"
										variant="3d"
										color="success"
										:checked="script['script_status']"
										@update:checked="onSwitchScriptBlocker(script['id'])"
										></c-switch>
										<input type="hidden" name="script_status" v-model="script['script_status']">
									</td>
									<td class="col-sm-3 gdpr-cookie-consent-script-left">{{ script['script_title'] }}</td>
									<td class="col-sm-4 gdpr-cookie-consent-script-left">{{ script['script_description'] }}</td>
									<td class="col-sm-3">
										<v-select
										class="form-group"
										id="gdpr-cookie-consent-script-category"
										:reduce="label => label.code + ',' + script['id']"
										:options="category_list_options"
										v-model="script['script_category_label']"
										@input="onScriptCategorySelect"
										></v-select>
										<input type="hidden" name="script_category" v-model="script['script_category']">
									</td>
									</tr>
								</tbody>
								</table>
							</c-card-body>
							</c-card>
						</div>
					</v-modal>
				</c-card-body>
					<c-row>
						<c-col class="col-sm-32"><div id="gdpr-cookie-consent-settings-cookie-notice"><?php esc_html_e( 'Whitelist Scripts', 'gdpr-cookie-consent' ); ?></div></c-col>
					</c-row>
					<c-card-body  style="position:relative;">
						<!-- Whitelist Scripts Card -->
						<c-row :class="{ 'overlay-whitelistscript-style': enable_safe}"v-show="enable_safe">
						<img id="safe-mode-activate-img"src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/safe-mode-lock.png'; ?>" alt="WP Cookie Consent Logo">
							<?php
							esc_attr_e(
								'Safe Mode enabled. Disable it in Compliance settings to configure Script Blocker settings.',
								'gdpr-cookie-consent'
							);
							?>
						</c-row>
						<?php $this->whitelist_script(); ?>
					</c-card-body>
			</c-card>
		</c-tab>
		<?php
	}

	/**
	 * Field for whitelisting scripts.
	 */
	public function whitelist_script() {
		// latest save.

		$fieldname = 'whitelist_script';
		$options   = get_option( 'wpl_options_custom-scripts' );
		$values    = isset( $options[ $fieldname ] ) ? $options[ $fieldname ] : false;

		if ( empty( $values ) ) {
			$values = array(
				array(
					'name'   => __( 'Example', 'gdpr-cookie-consent' ),
					'urls'   => array( 'https://www.example.com' ),
					'enable' => '0',
				),
			);
		}

		foreach ( $values as $key => $value ) {
			// All the value getting in the $value is already escaped so it not needed to do here.
			echo $this->get_whitelist_script_html( $value, $key ); // phpcs:ignore
		}

		?>
		<div class="wpl-whitelist-add-new-container">
			<button id="wpl-whitelist-add-new" type="button" class="button wpl_script_add" data-type="whitelist_script"><?php esc_attr_e( 'Add New Entry', 'gdpr-cookie-consent' ); ?><img class="add-new-entry-img" src="<?php echo esc_url( GDPR_COOKIE_CONSENT_PLUGIN_URL ) . 'admin/images/add_new_entry.svg'; ?>" alt="Add new entry logo"></button>
		</div>
		<?php
	}

	/**
	 * Get HTML for whitelisting scripts.
	 *
	 * @param mixed $value The value for whitelisting.
	 * @param int   $i An integer parameter.
	 * @param bool  $open A boolean parameter indicating whether it should be open or not (default is false).
	 */
	public function get_whitelist_script_html( $value, $i, $open = false ) {
			$default_index = array(
				'name'   => __( 'New entry', 'gdpr-cookie-consent' ) . ' ' . $i,
				'urls'   => array( '' ),
				'enable' => '1',
			);

			$value   = wp_parse_args( $value, $default_index );
			$enabled = $value['enable'] ? 'checked="checked"' : '';
			$action  = $value['enable'] ? 'disable' : 'enable';
			$size    = 15;
			$color   = '';

			$html            = '
            <div class="multiple-field">
                <div>
                    <label>' . __( 'Name', 'gdpr-cookie-consent' ) . '</label>
                </div>
                <div>
                    <input type="text"
                    		data-name="name"
                           class="wpl_name wpl-whitelist-name-field"
                           name="wpl_whitelist_script[' . $i . '][name]"
                           value="' . esc_html( $value['name'] ) . '">
                </div>
                <div>
                    <label>' . __( 'URLs that should be whitelisted.', 'gdpr-cookie-consent' ) . '' . '</label>
                </div>
                      <div>
                      <div class="wpl-hidden wpl-url-template">
                      	<div><input type="text"
							   data-name="urls"
							   name="wpl_whitelist_script[' . $i . '][urls][]"
							   value=""><button type="button" class="wpl_remove_url">' . '<svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
							   height="' . $size . '" >
							   <path fill="' . $color . '" d="M400 288h-352c-17.69 0-32-14.32-32-32.01s14.31-31.99 32-31.99h352c17.69 0 32 14.3 32 31.99S417.7 288 400 288z"/>
						   </svg>' . '</button></div></div>
                      ';
					$counter = 0;
			if ( empty( $value['urls'] ) ) {
				$value['urls'] = array( ' ' );
			}
			foreach ( $value['urls'] as $url ) {
				++$counter;
				$html .= '<div class="wpl-whitelist-plus-minus"><input type="text"
							data-name="urls"
							class="wpl-whitelist-plus-script-field"
							name="wpl_whitelist_script[' . $i . '][urls][]"
							value="' . esc_html( $url ) . '">';
				if ( $counter == 1 ) {
					$html .= '<button type="button" class="wpl_add_url">' . '<svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
							height="' . $size . '" >
							<path fill="' . $color . '" d="M432 256c0 17.69-14.33 32.01-32 32.01H256v144c0 17.69-14.33 31.99-32 31.99s-32-14.3-32-31.99v-144H48c-17.67 0-32-14.32-32-32.01s14.33-31.99 32-31.99H192v-144c0-17.69 14.33-32.01 32-32.01s32 14.32 32 32.01v144h144C417.7 224 432 238.3 432 256z"/>
						</svg>' . '</button>';
				} else {
					$html .= '<button type="button" class="wpl_remove_url">' . '<svg aria-hidden="true" focusable="false" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
							height="' . $size . '" >
							<path fill="' . $color . '" d="M400 288h-352c-17.69 0-32-14.32-32-32.01s14.31-31.99 32-31.99h352c17.69 0 32 14.3 32 31.99S417.7 288 400 288z"/>
						</svg>' . '</button>';
				}
				$html .= '</div>';
			}

			$html         .= '</div>
                <div class="wpl-multiple-field-button-footer">
                    <button id="wpl-whitelist-save-btn" class="button button-primary wpl_script_save" type="button" data-id="' . $i . '" data-type="whitelist_script" data-action="save">' . __( 'Save', 'gdpr-cookie-consent' ) . '</button>
					<button id="wpl-whitelist-remove-btn" class="button button-primary button-red wpl_script_save" type="button" data-id="' . $i . '" data-type="whitelist_script" data-action="remove">' . __( 'Remove', 'gdpr-cookie-consent' ) . '</button>
                </div>
            </div>';
			$title         = esc_html( $value['name'] ) !== '' ? esc_html( $value['name'] ) : __( 'New entry', 'gdpr-cookie-consent' );
			$custom_button = '<div class="wpl-checkbox wpl_script_save" data-action="' . $action . '" data-type="whitelist_script" data-id="' . $i . '">
								<input type="hidden"
									   value="0"
										name="wpl_whitelist_script[' . $i . '][enable]">
								<input type="checkbox"
									   name="wpl_whitelist_script[' . $i . '][enable]"
									   class="wpl-checkbox wpl-enable"
									   size="40"
									   value="1"
									   data-name="enable"
									   ' . $enabled . '/>
								<label class="wpl-label" for="wpl-enable" tabindex="0"></label>
							</div>';

			return $this->wpl_panel( $title, $html, $custom_button, '', false, $open );
	}

	/**
	 * Whitelist Script Panel.
	 *
	 * @param string $title       The title for the script panel.
	 * @param string $html        The HTML content for the script panel.
	 * @param string $custom_btn  Optional custom button for the panel.
	 * @param string $validate    Validation information for the panel.
	 * @param bool   $echo        Whether to echo the panel content (default is true).
	 * @param bool   $open        Whether the panel should be open or closed (default is false).
	 *
	 * @return string|void The panel content if $echo is false, otherwise void.
	 */
	public function wpl_panel( $title, $html, $custom_btn = '', $validate = '', $echo = true, $open = false ) {
		if ( $title == '' ) {
			return '';
		}
		$open_class = $open ? 'open' : '';

		$output = '
        <details class="wpl-panel wpl-slide-panel wpl-toggle-active" ' . $open_class . '>
        	<summary>
				<div class="wpl-panel-title">

					<span class="wpl-title">' . $title . '</span>

					<span>' . $validate . '</span>

					<span class="wpl-custom-btns">' . $custom_btn . '</span>

					<div class="wpl-icon wpl-open"></div>

				</div>
            </summary>
            <div class="wpl-panel-content">
                ' . $html . '
            </div>
        </details>';

		if ( $echo ) {
			echo $output;
		} else {
			return $output;
		}
	}

	/**
	 * Add a script.
	 */
	public function wpl_ajax_script_add() {

		$html  = '';
		$error = false;

		if ( ! isset( $_POST['type'] ) || ( $_POST['type'] !== 'whitelist_script' ) ) {
			$error = true;
		}

		if ( ! $error ) {
			// clear cache.
			delete_transient( 'wpl_blocked_scripts' );
			$scripts = get_option( 'wpl_options_custom-scripts' );

			if ( ! is_array( $scripts ) ) {
				$scripts = array(
					'whitelist_script' => array(),
				);
			}

			if ( $_POST['type'] === 'whitelist_script' ) {
				if ( ! is_array( $scripts['whitelist_script'] ) ) {
					$scripts['whitelist_script'] = array();
				}
				$new_id                                 = ! empty( $scripts['whitelist_script'] ) ? max( array_keys( $scripts['whitelist_script'] ) ) + 1 : 1;
				$scripts['whitelist_script'][ $new_id ] = array(
					'name'   => '',
					'urls'   => array(),
					'enable' => '1',
				);
				$html                                   = $this->get_whitelist_script_html( array(), $new_id, true );
			}
			update_option( 'wpl_options_custom-scripts', $scripts );
		}

		$data = array(
			'success' => ! $error,
			'html'    => $html,
		);

		$response = wp_json_encode( $data );
		header( 'Content-Type: application/json' );
		// The content of the response variable is already escaped.
		echo $response; //phpcs:ignore 
		exit;
	}

	/**
	 * Save script center data.
	 */
	public function wpl_ajax_script_save() {
		$error = false;

		if ( ! isset( $_POST['data'] ) ) {
			$error = true;
		}
		if ( ! isset( $_POST['id'] ) ) {
			$error = true;
		}
		if ( ! isset( $_POST['type'] ) ) {
			$error = true;
		}

		// clear transients when updating script.
		delete_transient( 'wpl_blocked_scripts' );
		if ( $_POST['type'] !== 'whitelist_script' ) {
			$error = true;
		}
		if ( ! isset( $_POST['button_action'] ) ) {
			$error = true;
		}
		if ( $_POST['button_action'] !== 'save' && $_POST['button_action'] !== 'enable' && $_POST['button_action'] !== 'disable' && $_POST['button_action'] !== 'remove' ) {
			$error = true;
		}
		if ( ! $error ) {
			$id      = intval( $_POST['id'] );
			$type    = sanitize_text_field( $_POST['type'] );
			$action  = sanitize_title( $_POST['button_action'] );
			$data    = json_decode( stripslashes( $_POST['data'] ), true );
			$scripts = get_option( 'wpl_options_custom-scripts', array() );
			if ( ! $error ) {
				if ( $action === 'remove' ) {
					unset( $scripts[ $type ][ $id ] );
				} else {
					$scripts[ $type ][ $id ] = $this->sanitize_custom_scripts( $data );
				}
				update_option( 'wpl_options_custom-scripts', $scripts );
			}
		}

		$data = array(
			'success' => ! $error,
		);

		$response = wp_json_encode( $data );
		header( 'Content-Type: application/json' );
		// The content of the response variable is already escaped.
		echo $response; //phpcs:ignore 
		exit;
	}

	/**
	 * Sanitize a custom script structure.
	 *
	 * @param array $arr The array to be sanitized.
	 *
	 * @return array|mixed The sanitized array.
	 */
	public function sanitize_custom_scripts( $arr ) {
		if ( isset( $arr['name'] ) ) {
			$arr['name'] = sanitize_text_field( $arr['name'] );
		}
		if ( isset( $arr['async'] ) ) {
			$arr['async'] = intval( $arr['async'] );
		}
		if ( isset( $arr['category'] ) ) {
			$arr['category'] = sanitize_title( $arr['category'] );
		}
		if ( isset( $arr['category'] ) ) {
			$arr['category'] = sanitize_title( $arr['category'] );
		}
		if ( isset( $arr['enable_placeholder'] ) ) {
			$arr['enable_placeholder'] = intval( $arr['enable_placeholder'] );
		}
		if ( isset( $arr['iframe'] ) ) {
			$arr['iframe'] = intval( $arr['iframe'] );
		}
		if ( isset( $arr['placeholder_class'] ) ) {
			$arr['placeholder_class'] = sanitize_text_field( $arr['placeholder_class'] );
		}
		if ( isset( $arr['placeholder'] ) ) {
			$arr['placeholder'] = sanitize_title( $arr['placeholder'] );
		}
		if ( isset( $arr['enable_dependency'] ) ) {
			$arr['enable_dependency'] = intval( $arr['enable_dependency'] );
		}
		if ( isset( $arr['dependency'] ) ) {
			// maybe split array from ajax save.
			if ( is_array( $arr['dependency'] ) ) {
				foreach ( $arr['dependency'] as $key => $value ) {
					if ( strpos( $value, '|:|' ) !== false ) {
						$result = explode( '|:|', $value );
						unset( $arr['dependency'][ $key ] );
						$arr['dependency'][ $result[0] ] = $result[1];
					}
				}
			}
			// don't have to be valid URLs, so don't sanitize as such.
			$arr['dependency'] = array_map( 'sanitize_text_field', $arr['dependency'] );
			$arr['dependency'] = array_filter( array_map( 'trim', $arr['dependency'] ) );
		}

		if ( isset( $arr['enable'] ) ) {
			$arr['enable'] = intval( $arr['enable'] );
		}

		if ( isset( $arr['urls'] ) ) {
			// don't have to be valid URLs, so don't sanitize as such.
			$arr['urls'] = array_map( 'sanitize_text_field', $arr['urls'] );
			$arr['urls'] = array_filter( array_map( 'trim', $arr['urls'] ) );
		}
		return $arr;
	}

	/**
	 * Return Scripts.
	 *
	 * @since 3.0.0
	 * @param int $offset Offset.
	 * @param int $limit Limit.
	 * @return array
	 */
	public function get_cookie_scripts( $offset = 0, $limit = 100 ) {
		global $wpdb;
		$out = array(
			'total' => 0,
			'data'  => array(),
		);

		$count_arr = $wpdb->get_row( 'SELECT COUNT(id) AS ttnum FROM ' . $wpdb->prefix . 'wpl_cookie_scripts', ARRAY_A ); // db call ok; no-cache ok.
		if ( $count_arr ) {
			$out['total'] = $count_arr['ttnum'];
		}

		$data_arr = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'wpl_cookie_scripts ORDER BY id ASC LIMIT %d, %d', array( $offset, $limit ) ), ARRAY_A ); // db call ok; no-cache ok.
		if ( $data_arr ) {
			$out['data'] = $data_arr;
		}
		return $out;
	}

	/**
	 * Returns script categories.
	 *
	 * @since 3.0.0
	 * @return array|null|object
	 */
	public function get_categories() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'gdpr_cookie_scan_categories';
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) {
			$data_arr = $wpdb->get_results( 'SELECT `id_gdpr_cookie_category`, `gdpr_cookie_category_slug`, `gdpr_cookie_category_name` FROM ' . $wpdb->prefix . 'gdpr_cookie_scan_categories' );
		} // db call ok; no-cache ok.
		return $data_arr;
	}

	/**
	 * Returns script category by ID.
	 *
	 * @since 3.0.0
	 * @param string $id ID.
	 * @return array|null|object
	 */
	public function get_category_by_id( $id = '' ) {
		global $wpdb;
		$data_arr   = array();
		$table_name = $wpdb->prefix . 'gdpr_cookie_scan_categories';
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) {
			if ( isset( $id ) && ! empty( $id ) ) {
				$data_arr = $wpdb->get_results( $wpdb->prepare( 'SELECT `id_gdpr_cookie_category`, `gdpr_cookie_category_slug`, `gdpr_cookie_category_name` FROM ' . $wpdb->prefix . 'gdpr_cookie_scan_categories WHERE id_gdpr_cookie_category = %d', array( $id ) ) );  // db call ok; no-cache ok.
			}
		}
		return $data_arr;
	}

	/**
	 * Returns script category by slug.
	 *
	 * @since 3.0.0
	 * @param string $slug Slug.
	 * @return array|null|object
	 */
	public function get_category_by_slug( $slug = '' ) {
		global $wpdb;
		$data_arr   = array();
		$table_name = $wpdb->prefix . 'gdpr_cookie_scan_categories';
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) {
			if ( isset( $slug ) && ! empty( $slug ) ) {
				$data_arr = $wpdb->get_results( $wpdb->prepare( 'SELECT `id_gdpr_cookie_category`, `gdpr_cookie_category_slug`, `gdpr_cookie_category_name` FROM ' . $wpdb->prefix . 'gdpr_cookie_scan_categories WHERE gdpr_cookie_category_slug = %s', array( $slug ) ) );  // db call ok; no-cache ok.
			}
		}
		return $data_arr;
	}

	/**
	 * Returns blocking script patterns.
	 *
	 * @since 3.0.0
	 * @return mixed|void
	 */
	public function wpl_get_script_patterns() {
		$script_patterns = apply_filters(
			'wpl_script_patterns',
			array(
				'googleanalytics'      => array(
					'label'     => __( 'GA4', 'gdpr-cookie-consent' ),
					'src'       => 'google-analytics.com',
					'js'        => 'www.google-analytics.com/analytics.js',
					'js_needle' => array(
						'www.google-analytics.com/analytics.js',
						'google-analytics.com/ga.js',
						'stats.g.doubleclick.net/dc.js',
						'window.ga=window.ga',
						'_getTracker',
						'__gaTracker',
						'GoogleAnalyticsObject',
					),
					'cc'        => 'analytics',
				),
				'facebook_pixel'       => array(
					'label'     => __( 'Meta Pixel', 'gdpr-cookie-consent' ),
					'js'        => 'connect.facebook.net/en_US/fbevents.js',
					'js_needle' => array(
						'connect.facebook.net/en_US/fbevents.js',
						'fbq',
						'fjs',
						'facebook-jssdk',
					),
					'cc'        => 'analytics',
					'html_elem' => array(
						array(
							'name' => 'img',
							'attr' => 'src:facebook.com/tr',
						),
					),
				),
				'google_tag_manager'   => array(
					'label'     => __( 'Google Tag Manager', 'gdpr-cookie-consent' ),
					'src'       => 'www.googletagmanager.com/ns.html?id=GTM-',
					'js'        => 'googletagmanager.com/gtag/js',
					'js_needle' => array(
						'www.googletagmanager.com/gtm',
					),
					'cc'        => 'analytics',
				),
				'hotjar'               => array(
					'label'     => __( 'Hotjar', 'gdpr-cookie-consent' ),
					'js_needle' => array(
						'static.hotjar.com/c/hotjar-',
					),
					'cc'        => 'analytics',
				),
				'google_publisher_tag' => array(
					'label'     => __( 'Google Publisher Tag', 'gdpr-cookie-consent' ),
					'js'        => array(
						'www.googletagservices.com/tag/js/gpt.js',
						'www.googleadservices.com/pagead/conversion.js',
					),
					'js_needle' => array(
						'googletag.pubads',
						'googletag.enableServices',
						'googletag.display',
						'www.googletagservices.com/tag/js/gpt.js',
						'www.googleadservices.com/pagead/conversion.js',
					),
					'cc'        => 'marketing',
					'html_elem' => array(
						array(
							'name' => 'img',
							'attr' => 'src:pubads.g.doubleclick.net/gampad',
						),
						array(
							'name' => 'img',
							'attr' => 'src:googleads.g.doubleclick.net/pagead',
						),
					),
				),
				'youtube_embed'        => array(
					'label'     => __( 'Youtube embed', 'gdpr-cookie-consent' ),
					'js'        => 'www.youtube.com/player_api',
					'js_needle' => array(
						'www.youtube.com/player_api',
						'onYouTubePlayerAPIReady',
						'YT.Player',
						'onYouTubeIframeAPIReady',
						'www.youtube.com/iframe_api',
					),
					'cc'        => 'preferences',
					'html_elem' => array(
						array(
							'name' => 'iframe',
							'attr' => 'src:www.youtube.com/embed',
						),
						array(
							'name' => 'iframe',
							'attr' => 'src:youtu.be',
						),
						array(
							'name' => 'object',
							'attr' => 'data:www.youtube.com/embed',
						),
						array(
							'name' => 'embed',
							'attr' => 'src:www.youtube.com/embed',
						),
						array(
							'name' => 'img',
							'attr' => 'src:www.youtube.com/embed',
						),
					),
				),
				'vimeo_embed'          => array(
					'label'     => __( 'Vimeo embed', 'gdpr-cookie-consent' ),
					'js'        => 'player.vimeo.com/api/player.js',
					'js_needle' => array(
						'www.vimeo.com/api/oembed',
						'player.vimeo.com/api/player.js',
						'Vimeo.Player',
						'new Player',
					),
					'cc'        => 'preferences',
					'html_elem' => array(
						array(
							'name' => 'iframe',
							'attr' => 'src:player.vimeo.com/video',
						),
					),
				),
				'google_maps'          => array(
					'label'     => __( 'Google maps', 'gdpr-cookie-consent' ),
					'js'        => 'maps.googleapis.com/maps/api',
					'js_needle' => array(
						'maps.googleapis.com/maps/api',
						'google.map',
						'initMap',
					),
					'cc'        => 'preferences',
					'html_elem' => array(
						array(
							'name' => 'iframe',
							'attr' => 'src:www.google.com/maps/embed',
						),
						array(
							'name' => 'iframe',
							'attr' => 'src:maps.google.com/maps',
						),
					),
				),
				'addthis_widget'       => array(
					'label'     => __( 'Addthis widget', 'gdpr-cookie-consent' ),
					'js'        => 's7.addthis.com/js',
					'js_needle' => array(
						'addthis_widget',
					),
					'cc'        => 'marketing',
				),
				'sharethis_widget'     => array(
					'label'     => __( 'Sharethis widget', 'gdpr-cookie-consent' ),
					'js'        => 'platform-api.sharethis.com/js/sharethis.js',
					'js_needle' => array(
						'sharethis.js',
					),
					'cc'        => 'marketing',
				),
				'twitter_widget'       => array(
					'label'     => __( 'Twitter widget', 'gdpr-cookie-consent' ),
					'js'        => 'platform.twitter.com/widgets.js',
					'js_needle' => array(
						'platform.twitter.com/widgets.js',
						'twitter-wjs',
						'twttr.widgets',
						'twttr.events',
						'twttr.ready',
						'window.twttr',
					),
					'cc'        => 'marketing',
				),
				'soundcloud_embed'     => array(
					'label'     => __( 'Soundcloud embed', 'gdpr-cookie-consent' ),
					'js'        => 'connect.soundcloud.com',
					'js_needle' => array(
						'SC.initialize',
						'SC.get',
						'SC.connectCallback',
						'SC.connect',
						'SC.put',
						'SC.stream',
						'SC.Recorder',
						'SC.upload',
						'SC.oEmbed',
						'soundcloud.com',
					),
					'cc'        => 'preferences',
					'html_elem' => array(
						array(
							'name' => 'iframe',
							'attr' => 'src:w.soundcloud.com/player',
						),
						array(
							'name' => 'iframe',
							'attr' => 'src:api.soundcloud.com',
						),
					),
				),
				'slideshare_embed'     => array(
					'label'     => __( 'Slideshare embed', 'gdpr-cookie-consent' ),
					'js'        => 'www.slideshare.net/api/oembed',
					'js_needle' => array(
						'www.slideshare.net/api/oembed',
					),
					'cc'        => 'preferences',
					'html_elem' => array(
						array(
							'name' => 'iframe',
							'attr' => 'src:www.slideshare.net/slideshow',
						),
					),
				),
				'linkedin_widget'      => array(
					'label'     => __( 'Linkedin widget/Analytics', 'gdpr-cookie-consent' ),
					'js'        => 'platform.linkedin.com/in.js',
					'js_needle' => array(
						'platform.linkedin.com/in.js',
						'snap.licdn.com/li.lms-analytics/insight.min.js',
						'_linkedin_partner_id',
					),
					'cc'        => 'analytics',
					'html_elem' => array(
						array(
							'name' => 'img',
							'attr' => 'src:dc.ads.linkedin.com/collect/',
						),
					),
				),
				'instagram_embed'      => array(
					'label'     => __( 'Instagram embed', 'gdpr-cookie-consent' ),
					'js'        => 'www.instagram.com/embed.js',
					'js_needle' => array(
						'www.instagram.com/embed.js',
						'api.instagram.com/oembed',
					),
					'cc'        => 'preferences',
					'html_elem' => array(
						array(
							'name' => 'iframe',
							'attr' => 'src:www.instagram.com/p',
						),
					),
				),
				'pinterest'            => array(
					'label'     => __( 'Pinterest widget', 'gdpr-cookie-consent' ),
					'js'        => 'assets.pinterest.com/js/pinit.js',
					'js_needle' => array(
						'assets.pinterest.com/js/pinit.js',
					),
					'cc'        => 'marketing',
				),
				'tawk'                 => array(
					'label'     => __( 'Tawk widget', 'gdpr-cookie-consent' ),
					'js_needle' => array(
						'embed.tawk.to',
					),
					'cc'        => 'preferences',
				),
				'hubspot'              => array(
					'label'     => __( 'Hubspot Analytics', 'gdpr-cookie-consent' ),
					'js'        => 'js.hs-scripts.com',
					'js_needle' => array(
						'js.hsforms.net',
						'js.hs-scripts.com',
						'static.hsappstatic.net',
					),
					'cc'        => 'analytics',
				),
				'recaptcha'            => array(
					'label' => __( 'Google Recaptcha', 'gdpr-cookie-consent' ),
					'js'    => array(
						'www.google.com/recaptcha/api.js',
						'recaptcha.js',
						'recaptcha/api',
					),
					'cc'    => 'analytics',
				),
				'adsense'              => array(
					'label'     => __( 'Google Adsense', 'gdpr-cookie-consent' ),
					'js'        => 'pagead2.googlesyndication.com/pagead/js/adsbygoogle.js',
					'js_needle' => array( 'adsbygoogle.js' ),
					'cc'        => 'analytics',
				),
				'matomo'               => array(
					'label'     => __( 'Matomo Analytics', 'gdpr-cookie-consent' ),
					'js'        => 'matomo.js',
					'js_needle' => array(
						'_paq.push',
						'_mtm.push',
					),
					'cc'        => 'analytics',
				),
			)
		);
		foreach ( $script_patterns as $key => $value ) {
			$script_patterns[ $key ]['has_src']       = false;
			$script_patterns[ $key ]['has_js']        = false;
			$script_patterns[ $key ]['has_js_needle'] = false;
			$script_patterns[ $key ]['has_cc']        = false;
			$script_patterns[ $key ]['has_html_elem'] = false;
			$script_patterns[ $key ]['has_url']       = false;
			$script_patterns[ $key ]['internal_cb']   = false;

			if ( isset( $value['src'] ) ) {
				$script_patterns[ $key ]['has_src'] = true;
			} else {
				$script_patterns[ $key ]['src'] = '';
			}
			if ( isset( $value['js'] ) ) {
				$script_patterns[ $key ]['has_js'] = true;
			} else {
				$script_patterns[ $key ]['js'] = '';
			}
			if ( isset( $value['js_needle'] ) ) {
				$script_patterns[ $key ]['has_js_needle'] = true;
			} else {
				$script_patterns[ $key ]['js_needle'] = '';
			}
			if ( isset( $value['cc'] ) ) {
				$script_patterns[ $key ]['has_cc'] = true;
			} else {
				$script_patterns[ $key ]['cc'] = '';
			}
			if ( isset( $value['html_elem'] ) ) {
				$script_patterns[ $key ]['has_html_elem'] = true;
			} else {
				$script_patterns[ $key ]['html_elem'] = '';
			}
			if ( isset( $value['url'] ) ) {
				$script_patterns[ $key ]['has_url'] = true;
			} else {
				$script_patterns[ $key ]['url'] = '';
			}
			$cb = 'wpl_automate_default';
			if ( ! isset( $value['callback'] ) ) {
				$script_patterns[ $key ]['callback'] = $cb;
			} else {
				$cb = $script_patterns[ $key ]['callback'];
			}
			if ( method_exists( $this, $cb ) ) {
				$script_patterns[ $key ]['internal_cb'] = true;
			}
		}
		return $script_patterns;
	}

	/**
	 * Returns regex patterns.
	 *
	 * @since 3.0.0
	 * @return mixed|void
	 */
	public function wpl_get_regex_patterns() {
		$regex_patterns = apply_filters(
			'wpl_regex_patterns',
			array(
				'_regexParts'                 => array(
					'-lookbehindImg'       => '(?<!src=")',
					'-lookbehindLink'      => '(?<!href=")',
					'-lookbehindLinkImg'   => '(?<!href=")(?<!src=")',
					'-lookbehindShortcode' => '(?<!])',
					'-lookbehindAfterBody' => '(?<=\<body\>)',
					'-lookaheadBodyEnd'    => '(?=.*\</body\>)',
					'-lookaheadHeadEnd'    => '(?=.*\</head\>)',
					'randomChars'          => '[^\s\["\']+',
					'srcSchemeWww'         => '(?:https?://|//)?(?:[www\.]{4})?',
				),
				'_regexScriptBasic'           => '\<script.+?\</script\>',
				'_regexScriptTagOpen'         => '\<script[^\>]*?\>',
				'_regexScriptTagClose'        => '\</script\>',
				'_regexScriptAllAdvanced'     => '\<script[^>]*?\>((?!\</script\>).*?)?\</script\>',
				'_regexScriptHasNeedle'       => '\<script[^>]*?\>(?!\</script>)[^<]*%s[^<]*\</script\>',
				'_regexScriptSrc'             => '\<script[^>]+?src=("|\')((https?:)?//(?:[www\.]{4})?%s%s[^\s"\']*?)("|\')[^>]*\>[^<]*\</script\>',
				'_regexIframeBasic'           => '\<iframe.+?\</iframe\>',
				'_regexIframe'                => '\<iframe[^>]+?src=("|\')((https?://|//)?(?:[www\.]{4})?%s%s[^"\']*?)("|\')[^>]*\>(?:(?!\<iframe).*?)\</iframe\>',
				'_regexHtmlElemWithAttr'      => '\<%s[^>]+?%s=(?:"|\')(?:%s%s[^"\']*?)(?:"|\')[^>]*(?:\>((?!\<%s).*?)\</%s\>|/\>)',
				'_regexHtmlElemWithAttrTypeA' => '\<%s[^>]+?%s= (?:"|\')(?:%s%s[^"\']*?)(?:"|\')[^>]*(?:\>)',
			)
		);
		return $regex_patterns;
	}

	/**
	 * Parse url for subdomain.
	 *
	 * @since 3.0.0
	 * @param string $url URL.
	 * @param string $subdomain Sub Domain.
	 * @return null|string|string[]
	 */
	public function wpl_get_url_without_schema_subdomain( $url = '', $subdomain = 'www' ) {

		$url = preg_replace( "#(https?://|//|$subdomain\.)#", '', $url );
		return ( null === $url ) ? '' : $url;
	}

	/**
	 * Escapes string for regex chars.
	 *
	 * @since 3.0.0
	 * @param string $str String to be escaped.
	 * @return null|string|string[]
	 */
	public function wpl_escape_regex_chars( $str = '' ) {
		$chars = array( '^', '$', '(', ')', '<', '>', '.', '*', '+', '?', '[', '{', '\\', '|' );
		foreach ( $chars as $k => $char ) {
			$chars[ $k ] = '\\' . $char;
		}
		$replaced = preg_replace( '#(' . join( '|', $chars ) . ')#', '\\\${1}', $str );
		return ( null !== $replaced ) ? $replaced : $str;
	}

	/**
	 * Returns clean url.
	 *
	 * @since 3.0.0
	 * @param string $url url.
	 * @param bool   $strip_subdomain Sub domain strip.
	 * @param string $subdomain Sub domain.
	 * @return null|string|string[]
	 */
	public function wpl_get_clean_url( $url = '', $strip_subdomain = false, $subdomain = 'www' ) {
		if ( ! is_string( $url ) ) {
			return '';
		}

		$regex_subdomain = '';
		if ( $strip_subdomain && is_string( $subdomain ) ) {
			$subdomain = trim( $subdomain );
			if ( '' !== $subdomain ) {
				$regex_subdomain = $this->wpl_escape_regex_chars( "$subdomain." );
			}
		}

		$regex = '^' .
			'https?://' .
			$regex_subdomain .
			'([^/?]+)' .
			'(.*)' .
			'$';

		$url = preg_replace( "#$regex#", '${1}', $url );
		return ( null === $url ) ? '' : $url;
	}
}
new Gdpr_Cookie_Consent_Script_Blocker();
