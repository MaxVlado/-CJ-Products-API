<?php
class CJ_Products_Public
{
    public function __construct()
    {
        // Конструктор класса
    }
    public function init() {
        add_shortcode('paint_sample', array($this, 'cj_products_shortcode'));
        add_action('wp_enqueue_scripts', array($this, 'cj_products_enqueue_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'cj_products_enqueue_styles'));
    }

    /** Обработка шорткода [paint_sample] */
    function cj_products_shortcode($atts, $content = null)
    {
        $color_identifier = $atts['id'];

        global $wpdb;
        $table_name = $wpdb->prefix . 'cj_products';

     //   $sample = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE color_identifier = %s", $color_identifier));
        $sample = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE color_identifier = %s AND color_hex != ''", $color_identifier));

        if ($sample) {
            $samplize_link = esc_html($sample->url);
            $samplize_hex = esc_html($sample->color_hex);
            $color_name = esc_html($sample->color_name);
            $color_identifier = esc_html($sample->color_identifier);
            $output = '<a href="#" class="samplize-button" data-identifier="' . $color_identifier . '" data-name="' . $color_name . '"  data-hex="' . $samplize_hex . '"  data-url="' . $samplize_link . '">(CHECK A SAMPLE)</a>';
            return $output;
        }

        return '';
    }

    // Enqueue the JavaScript file
    function cj_products_enqueue_scripts() {
        wp_enqueue_script('cj-products-script', plugin_dir_url(__FILE__) . '../js/cj-products-public.js', array('jquery'), '1.0', true);

        // Локализация скрипта и передача значений popup_text_1 и popup_text_2
        $options = get_option('cj_products_settings');
        $popup_text_1 = isset($options['popup_text_1']) ? $options['popup_text_1'] : "Don't forget to get";
        $popup_text_2 = isset($options['popup_text_2']) ? $options['popup_text_2'] : "2 FREE SAMPLES!";
        $popup_button_text = isset($options['popup_button_text']) ? $options['popup_button_text'] : "CHECK A SAMPLE!";
        wp_localize_script('cj-products-script', 'cjProductsSettings', array(
            'popupText1' => $popup_text_1,
            'popupText2' => $popup_text_2,
            'popupButtonText' => $popup_button_text,
        ));
    }

    /** Регистрация и подключение стилей CSS */
    function cj_products_enqueue_styles()
    {
        wp_enqueue_style('cj-products-style', plugin_dir_url(__FILE__) . '../css/cj-products-public.css');
    }

}