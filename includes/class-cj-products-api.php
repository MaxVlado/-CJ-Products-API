<?php
class CJ_Products_API {
    public function __construct() {
        // Конструктор класса
    }

    public function init() {
        // Инициализация плагина
        require_once CJ_PRODUCTS_API_PLUGIN_DIR . 'includes/class-cj-products-settings.php';
        require_once CJ_PRODUCTS_API_PLUGIN_DIR . 'includes/class-cj-products-sync.php';
        require_once CJ_PRODUCTS_API_PLUGIN_DIR . 'admin/class-cj-products-admin.php';
        require_once CJ_PRODUCTS_API_PLUGIN_DIR . 'includes/class-cj-products-public.php';

        $cj_products_admin = new CJ_Products_Admin();
        $cj_products_admin->init();

        $cj_products_settings = new CJ_Products_Settings();
        $cj_products_settings->init();

        $cj_products_sync = new CJ_Products_Sync();
        $cj_products_sync->init();

        $cj_products_public = new CJ_Products_Public();
        $cj_products_public->init();
    }

    public static function activate() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cj_products';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        `advertiser_id` varchar(255) NOT NULL,
        `catalog_id` varchar(255) NOT NULL,
        `product_id` varchar(255) NOT NULL,
        `title` text NOT NULL,
        `price_amount` decimal(10,2) NOT NULL,
        `price_currency` varchar(10) NOT NULL,
        `url` text NOT NULL,
        `brand` varchar(255) DEFAULT NULL,
        `color_identifier` varchar(255) DEFAULT NULL,        
        `color_name` varchar(255) DEFAULT NULL,
        `color_hex` varchar(255) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_product_id` (`product_id`)
    ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

// temp table
        $table_name = $wpdb->prefix . 'cj_products_csv';

        $sql_csv = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        `product_id` varchar(255) NOT NULL,
        name varchar(255) NOT NULL,
        hex varchar(255) NOT NULL,
        color_identifier varchar(255) NOT NULL,
        color_identifier_display varchar(255) NOT NULL,
        url varchar(255) NOT NULL,
        is_dark varchar(255) NOT NULL,
        brand_name varchar(255) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_csv);
    }

    public static function deactivate() {
        // Действия при деактивации плагина
        // Например, очистка кеша или удаление временных данных
    }

    public static function uninstall() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cj_products';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");

        delete_option('cj_products_settings');
    }
}