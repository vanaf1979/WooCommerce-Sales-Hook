<?php

namespace Since1979;

class AdminSettings {

    /**
     * init
     *
     * Register hooks with WordPress.
     *
     * @uses   add_action https://developer.wordpress.org/reference/functions/add_action/
     * @uses   admin_menu https://developer.wordpress.org/reference/hooks/admin_menu/
     * @uses   admin_init https://developer.wordpress.org/reference/hooks/admin_init/
     *
     * @access public
     * @return void
     */
    public function init(): void
    {
        \add_action('admin_menu', array($this, 'addAdminSubPage'), 10, 0);
        \add_action('admin_init', array($this, 'registerSettingsSections'), 10, 0);
        \add_action('admin_init', array($this, 'addSettingsFields'), 11, 0);
    }


    /**
     * addAdminSubPage
     *
     * Add admin subpage under WooCommerce page.
     *
     * @uses   add_submenu_page https://developer.wordpress.org/reference/functions/add_submenu_page/
     *
     * @access public
     * @return void
     */
    public function addAdminSubPage(): void
    {
        \add_submenu_page(
            'woocommerce',
            'Sales hook',
            'Sales hook',
            'manage_options',
            'saleshook',
            array($this, 'menuPage'),
            4
        );
    }


    /**
     * menuPage
     *
     * Setup the menu page html.
     *
     * @uses   settings_fields https://developer.wordpress.org/reference/functions/settings_fields/
     * @uses   do_settings_sections https://developer.wordpress.org/reference/functions/do_settings_sections/
     * @uses   submit_button https://developer.wordpress.org/reference/functions/submit_button/
     *
     * @access public
     * @return void
     */
    public function menuPage()
    {
        echo '<form action="options.php" method="post">';

        echo '<h1>Woocommerce Sales Hook</h1>';

        \settings_fields('salesHook');
        \do_settings_sections('saleshook');

        \submit_button();

        echo '</form>';
    }


    /**
     * registerSettingsSections
     *
     * Register the settings sections.
     *
     * @uses   add_settings_section https://developer.wordpress.org/reference/functions/add_settings_section/
     *
     * @access public
     * @return void
     */
    public function registerSettingsSections(): void
    {
        \add_settings_section(
            'salesHookEndPoint',
            'Setup the enpoint',
            array($this, 'salesHookEndPointCallback'),
            'saleshook'
        );

        \add_settings_section(
            'salesHookData',
            'Data',
            array($this, 'salesHookDataCallback'),
            'saleshook'
        );
    }


    /**
     * salesHookEndPointCallback
     *
     * Provide description for the salesHookEndPoint sectioo.
     *
     * @access public
     * @return void
     */
    public function salesHookEndPointCallback()
    {
        echo '<p>Setup the remote api that needs to be called when a sale is processed.</p>';
    }


    /**
     * salesHookEndPointCallback
     *
     * Provide description for the salesHookData sectioo.
     *
     * @access public
     * @return void
     */
    public function salesHookDataCallback()
    {
        echo '<p>Select what data you want to send to the andpoint</p>';
    }


    /**
     * addSettingsFields
     *
     * Register settings and fields for the page.
     *
     * @access public
     * @return void
     */
    public function addSettingsFields(): void
    {
        $this->registerSalesHookEndPointUrl();
        $this->registerSalesHookDataBillingAddress();
    }


    /**
     * registerSalesHookEndPointUrl
     *
     * Register the SalesHookEndPointUrl field.
     *
     * @uses   register_setting https://developer.wordpress.org/reference/functions/register_setting/
     * @uses   add_settings_field https://developer.wordpress.org/reference/functions/add_settings_field/
     *
     * @access private
     * @return void
     */
    private function registerSalesHookEndPointUrl()
    {
        \register_setting(
            'salesHook',
            'salesHookEndPointUrl',
            $this->getFieldArguments()
        );

        \add_settings_field(
            'salesHookEndPointUrl',
            'Endpoint Url',
            array($this, 'enableHookCallback'),
            'saleshook',
            'salesHookEndPoint',
            array(
                'name' => 'sales-hook-setting',
                'label_for' => 'salesHookEndPointUrl',
            )
        );
    }


    /**
     * enableHookCallback
     *
     * Input for enableHook setting.
     *
     * @access public
     * @return void
     */
    public function enableHookCallback($args): void
    {
        echo '<input id="salesHookEndPointUrl" name="salesHookEndPointUrl" type="text" value="' . \get_option('salesHookEndPointUrl') . '"/>';
    }


    /**
     * registerSalesHookDataBillingAddress
     *
     * Register the SalesHookDataBillingAddress field.
     *
     * @uses   register_setting https://developer.wordpress.org/reference/functions/register_setting/
     * @uses   add_settings_field https://developer.wordpress.org/reference/functions/add_settings_field/
     *
     * @access private
     * @return void
     */
    private function registerSalesHookDataBillingAddress(): void
    {
        \register_setting(
            'salesHook',
            'salesHookDataBillingAddress',
            $this->getFieldArguments()
        );

        \add_settings_field(
            'salesHookDataBillingAddress',
            'Billing address',
            array($this, 'billingAddressCallback'),
            'saleshook',
            'salesHookData',
            array(
                'class' => 'sales-hook-setting',
                'label_for' => 'salesHookDataBillingAddress',
            )
        );
    }


    /**
     * salesHookDataBillingAddress
     *
     * Input for salesHookDataBillingAddress setting.
     *
     * @access public
     * @return void
     */
    public function billingAddressCallback()
    {
        echo '<input id="salesHookDataBillingAddress" name="salesHookDataBillingAddress" type="checkbox" value="yes" ' . $this->checked('salesHookDataBillingAddress') . ' />';
    }


    /**
     * checked
     *
     * Set checked on checkbox input.
     *
     * @uses   checked https://developer.wordpress.org/reference/functions/checked/
     * @uses   get_option https://developer.wordpress.org/reference/functions/get_option/
     *
     * @param string $option Id of the option.
     *
     * @access private
     * @return string
     */
    private function checked(string $option): string
    {
        return \checked(\get_option($option), 'yes', false);
    }


    /**
     * getFieldArguments
     *
     * Rerun an array with fields arguments.
     *
     * @access private
     * @return string
     */
    private function getFieldArguments(): array
    {
        return array(
            'type' => 'string',
            'group' => 'seobox-settings',
            'description' => null,
            'sanitize_callback' => null,
            'show_in_rest' => false
        );
    }

}