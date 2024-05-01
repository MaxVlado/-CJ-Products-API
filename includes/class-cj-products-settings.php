<?php
class CJ_Products_Settings {
    public function __construct() {
    }
    public function init() {
        add_action('admin_init', array($this, 'register_settings'));
    }
    public function register_settings() {
        register_setting('cj_products_settings', 'cj_products_settings');

        add_settings_section(
            'cj_products_section',
            'API Settings',
            null,
            'cj-products-settings'
        );

        add_settings_field(
            'company_id',
            'Company ID',
            array($this, 'company_id_callback'),
            'cj-products-settings',
            'cj_products_section'
        );

        add_settings_field(
            'partner_ids',
            'Partner IDs',
            array($this, 'partner_ids_callback'),
            'cj-products-settings',
            'cj_products_section'
        );

        add_settings_field(
            'access_token',
            'Access Token',
            array($this, 'access_token_callback'),
            'cj-products-settings',
            'cj_products_section'
        );

        add_settings_field(
            'pid',
            'PID',
            array($this, 'pid_callback'),
            'cj-products-settings',
            'cj_products_section'
        );

        add_settings_field(
            'limit',
            'Limit',
            array($this, 'limit_callback'),
            'cj-products-settings',
            'cj_products_section'
        );

        add_settings_field(
            'total_count',
            'Total Count',
            array($this, 'total_count_callback'),
            'cj-products-settings',
            'cj_products_section'
        );

        add_settings_field(
            'popup_text_1',
            'Popup Text 1',
            array($this, 'popup_text_1_callback'),
            'cj-products-settings',
            'cj_products_section'
        );

        add_settings_field(
            'popup_text_2',
            'Popup Text 2',
            array($this, 'popup_text_2_callback'),
            'cj-products-settings',
            'cj_products_section'
        );
        add_settings_field(
            'Text on button',
            'Text on button',
            array($this, 'popup_button_text_callback'),
            'cj-products-settings',
            'cj_products_section'
        );
    }

    public function company_id_callback() {
        $options = get_option('cj_products_settings');
        $company_id = isset($options['company_id']) ? $options['company_id'] : '';
        echo '<input type="text" name="cj_products_settings[company_id]" value="' . esc_attr($company_id) . '">';
    }

    public function partner_ids_callback() {
        $options = get_option('cj_products_settings');
        $partner_ids = isset($options['partner_ids']) ? $options['partner_ids'] : '';
        echo '<input type="text" name="cj_products_settings[partner_ids]" value="' . esc_attr($partner_ids) . '">';
        echo '<p class="description">Введите идентификаторы партнеров через запятую, например: 111,222,333</p>';
    }

    public function access_token_callback() {
        $options = get_option('cj_products_settings');
        $access_token = isset($options['access_token']) ? $options['access_token'] : '';
        echo '<input type="text" name="cj_products_settings[access_token]" value="' . esc_attr($access_token) . '">';
    }

    public function pid_callback() {
        $options = get_option('cj_products_settings');
        $pid = isset($options['pid']) ? $options['pid'] : '';
        echo '<input type="text" name="cj_products_settings[pid]" value="' . esc_attr($pid) . '">';
    }

    public function limit_callback() {
        $options = get_option('cj_products_settings');
        $limit = isset($options['limit']) ? $options['limit'] : 10;
        echo '<input type="number" name="cj_products_settings[limit]" value="' . esc_attr($limit) . '">';
    }

    public function total_count_callback() {
        $options = get_option('cj_products_settings');
        $total_count = isset($options['total_count']) ? $options['total_count'] : '';
        echo '<input type="number" name="cj_products_settings[total_count]" value="' . esc_attr($total_count) . '">';
    }

    public function popup_text_1_callback() {
        $options = get_option('cj_products_settings');
        $popup_text_1 = isset($options['popup_text_1']) ? $options['popup_text_1'] : "Don't forget to get";
        echo '<input type="text" name="cj_products_settings[popup_text_1]" value="' . esc_attr($popup_text_1) . '">';
    }

    public function popup_text_2_callback() {
        $options = get_option('cj_products_settings');
        $popup_text_2 = isset($options['popup_text_2']) ? $options['popup_text_2'] : "2 FREE SAMPLES!";
        echo '<input type="text" name="cj_products_settings[popup_text_2]" value="' . esc_attr($popup_text_2) . '">';
    }

    public function popup_button_text_callback() {
        $options = get_option('cj_products_settings');
        $popup_button_text = isset($options['popup_button_text']) ? $options['popup_button_text'] : "CHECK A SAMPLE!";
        echo '<input type="text" name="cj_products_settings[popup_button_text]" value="' . esc_attr($popup_button_text) . '">';
    }
}