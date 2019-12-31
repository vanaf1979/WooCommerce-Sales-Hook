<?php
/**
 * WcSalesHook
 *
 * @package             Since1979\WcSalesHook
 * @author              Stephan Nijman <vanaf1979@gmail.com>
 * @copyright           2020 Stephan Nijman
 * @license             GPL-2.0-or-later
 * @version             1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:         WooCommerce Sales Hook
 * Plugin URI:          https://since1979.dev
 * Description:         Send order details to an external api endpoint.
 * Version:             1.0.0
 * Requires at least:   5.1
 * Requires PHP:        7.0
 * Author:              Stephan Nijman
 * Author URI:          https://since1979.dev
 * Text Domain:         wc-sales-hook
 * License:             GPL v2 or later
 * License URI:         http://www.gnu.org/licenses/gpl-2.0.txt
 */

namespace Since1979;

/**
 * Check WordPress context.
 */
if (!defined('ABSPATH')) exit;

/**
 * WcSalesHook
 *
 * Main WcSalesHook plugin class.
 *
 * @package Since1979\WcSalesHook
 */
final class WcSalesHook {

    /**
     * instance.
     *
     * @var WcSalesHook $instance instance.
     *
     * @access private static
     */
    private static $instance = null;


    /**
     * instance.
     *
     * Return a instance of this class.
     *
     * @access public static
     * @return WcSalesHook
     */
    public static function instance(): WcSalesHook
    {
        if (!isset(self::$instance) && !(self::$instance instanceof \Since1979\WcSalesHook)) {
            self::$instance = new Self();
        }
        return self::$instance;
    }


    /**
     * __clone
     *
     * Throw error on object clone.
     *
     * @uses   _doing_it_wrong https://developer.wordpress.org/reference/functions/_doing_it_wrong/
     * @uses   esc_html__ https://developer.wordpress.org/reference/functions/esc_html__/
     *
     * @access public
     * @return void
     */
    public function __clone()
    {
        \_doing_it_wrong(__METHOD__, \esc_html__('Cheating huh?', '_microbe'), '1.0');
    }


    /**
     * __wakeup
     *
     * Disable unserializing of the class.
     *
     * @uses   _doing_it_wrong https://developer.wordpress.org/reference/functions/_doing_it_wrong/
     * @uses   esc_html__ https://developer.wordpress.org/reference/functions/esc_html__/
     *
     * @access public
     * @return void
     */
    public function __wakeup()
    {
        \_doing_it_wrong(__METHOD__, \esc_html__('Cheating huh?', '_microbe'), '1.0');
    }


    /**
     * init
     *
     * initialize the plugin.
     *
     * @uses   add_action https://developer.wordpress.org/reference/functions/add_action/
     * @uses   woocommerce_payment_complete http://hookr.io/actions/woocommerce_payment_complete/
     *
     * @access public
     * @return void
     */
    public function init(): void
    {
        $this->maybeSetupAdminSettings();

        \add_action('woocommerce_payment_complete', array($this, 'paymentComplete'), 10, 1);
    }


    /**
     * paymentComplete
     *
     * Callback for woocommerce_payment_complete hook.
     * Gets order details and sends it to api enpoint.
     *
     * @param Int $orderId
     *
     * @access public
     * @return void
     */
    public function paymentComplete(Int $orderId): void
    {
        $this->sendOrderToEndPoint($this->getOrder($orderId));
    }


    /**
     * getOrder
     *
     * Get order details from WooCommerce.
     *
     * @uses   wc_get_order https://docs.woocommerce.com/wc-apidocs/function-wc_get_order.html
     *
     * @param Int $orderId
     *
     * @access private
     * @return array
     */
    private function getOrder($orderId): array
    {
        $order['order'] = wc_get_order($order_id);
        $order['products'] = $order['order']->get_items();

        return $order;
    }


    /**
     * sendOrderToEndPoint
     *
     * Send the order to given endpoint.
     *
     * @uses   wp_remote_post https://developer.wordpress.org/reference/functions/wp_remote_post/
     * @uses   json_encode https://www.php.net/manual/en/function.json-encode.php
     *
     * @param Int $orderId
     *
     * @access private
     * @return Array |  \WP_Error
     */
    private function sendOrderToEndPoint(Array $order): Array
    {
        $url = 'http://requestb.in/15gbo981';

        return \wp_remote_post($url, array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'body' => array('order' => json_encode($order)),
            'cookies' => array()
        ));
    }


    /**
     * maybeSetupAdminSettings
     *
     * Check if we are in the admin and setup the admin settings.
     *
     * @return void
     * @uses   is_admin https://developer.wordpress.org/reference/functions/is_admin/
     *
     * @access private
     * @uses   AdminSettings
     */
    private function maybeSetupAdminSettings(): void
    {
        if (\is_admin()) {
            require_once 'AdminSettings.php';
            $adminSettings = new AdminSettings();
            $adminSettings->init();
        }
    }

}

/**
 * runWcSalesHook.
 *
 * Initialize the Wc Sales Hook plugin.
 *
 * @return void
 * @uses WcSalesHook
 *
 */
function runWcSalesHook(): void
{
    \Since1979\WcSalesHook::instance()->init();
}

runWcSalesHook();
?>