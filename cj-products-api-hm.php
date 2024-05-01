<?php
/**
 * Plugin Name: CJ Products API
 * Plugin URI: http://housemagik.com/plugins/cj-product-api-hm
 * Description: Retrieves product data from the CJ.com API.
 * Version: 1.1
 * Author: V. Kirillov HousemagiK
 * Author URI: hhttp://housemagik.com/author
 *
 * Copyright 2024  V. Kirillov  (email: netdesopgame@gmail.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
if (!defined('ABSPATH')) {
    exit;
}

define('CJ_PRODUCTS_API_VERSION', '1.0');
define('CJ_PRODUCTS_API_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CJ_PRODUCTS_API_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once CJ_PRODUCTS_API_PLUGIN_DIR . 'includes/class-cj-products-api.php';

function cj_products_api_init() {
    $cj_products_api = new CJ_Products_API();
    $cj_products_api->init();
}
add_action('plugins_loaded', 'cj_products_api_init');

register_activation_hook(__FILE__, array('CJ_Products_API', 'activate'));
register_deactivation_hook(__FILE__, array('CJ_Products_API', 'deactivate'));
register_uninstall_hook(__FILE__, array('CJ_Products_API', 'uninstall'));
