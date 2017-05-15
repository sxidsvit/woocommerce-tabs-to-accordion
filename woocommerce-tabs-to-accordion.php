<?php
/**
 * Plugin Name: WooCommerce Tabs to Accordion
 * Plugin URI: https://dtweb.uk/
 * Description: Changes the default WooCommerce tabs to an Accordion when on mobile devices
 * Version: 1.0.0
 * Author: Dan Tomlinson
 * Author URI: https://dtweb.uk
 *
 * Text Domain: wctta
 *
 * @author dtwebuk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WooCommerceTabsToAccordion
{
	public function __construct()
	{
		add_filter( 'woocommerce_get_sections_products', array($this, 'add_settings_section') );
		add_filter( 'woocommerce_get_settings_products', array($this, 'register_plugin_settings'), 10, 2 );

		add_action( 'wp_head', array($this, 'remove_default_wc_tabs_action'));
		add_action( 'wp_enqueue_scripts', array($this, 'register_scripts_and_styles'));

		add_action( 'woocommerce_after_single_product_summary', array($this, 'custom_tab_template'), 30 );
	}

	public function add_settings_section( $sections )
	{
		$sections['wctta'] = __('WC Tabs to Accordion', 'wctta');
		return $sections;
	}

	public function register_plugin_settings( $settings, $current_section )
	{
		if ( $current_section == 'wctta' ) {

			$settings_wctta = array();

			$settings_wctta[] = array(

				'name' 	=> __('WC Tabs to Accordion', 'wctta'),
				'type' 	=> 'title',
				'desc' 	=> __('The following options are used to configure the WC Tabs to Accordion plugin', 'wctta'),
				'id' 	=> 'wctta'

			);

			$settings_wctta[] = array(

				'name' => __('Breakpoint', 'wctta'),
				'desc_tip' => __('Set the pixel width which you would like the tabs to change to an accordion', 'wctta'),
				'id' => 'wctta_breakpoint',
				'type' => 'text',
				'default' => '480',
				'css' => 'text-align:right;',
				'desc' => __('px', 'wctta')

			);

			$settings_wctta[] = array('type' => 'sectionend', 'id' => 'wctta');

			return $settings_wctta;

		} else {

			return $settings;

		}
	}

	public function register_scripts_and_styles()
	{
		wp_register_style('woocommerce-tabs-to-accordion-css', plugins_url('/css/woocommerce-tabs-to-accordion.css', __FILE__));
		wp_register_script('easy-responsive-tabs-js', plugins_url('/js/easyResponsiveTabs.js', __FILE__), array('jquery'));
		wp_enqueue_style('woocommerce-tabs-to-accordion-css');
		wp_enqueue_script('easy-responsive-tabs-js');
	}

	public function remove_default_wc_tabs_action()
	{
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
	}

	public function custom_tab_template()
	{
		$tabs = apply_filters( 'woocommerce_product_tabs', array() );

		if ( ! empty( $tabs ) ) : ?>

			<div class="woocommerce-tabs wc-tabs-wrapper">
				<ul class="tabs wc-tabs resp-tabs-list" role="tablist">
					<?php foreach ( $tabs as $key => $tab ) : ?>
						<li class="<?php echo esc_attr( $key ); ?>_tab" id="tab-title-<?php echo esc_attr( $key ); ?>" role="tab" aria-controls="tab-<?php echo esc_attr( $key ); ?>">
							<a href="#tab-<?php echo esc_attr( $key ); ?>"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
				<div class="resp-tabs-container">
					<?php foreach ( $tabs as $key => $tab ) : ?>
						<div class="woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr( $key ); ?> panel entry-content wc-tab" id="tab-<?php echo esc_attr( $key ); ?>" role="tabpanel" aria-labelledby="tab-title-<?php echo esc_attr( $key ); ?>">
							<?php call_user_func( $tab['callback'], $key, $tab ); ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery('.woocommerce-tabs').easyResponsiveTabs();
				});
			</script>

		<?php endif;
	}
}

new WooCommerceTabsToAccordion;