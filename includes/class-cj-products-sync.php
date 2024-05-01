<?php
class CJ_Products_Sync {
    public function __construct() {
    }

    public function init() {
        add_action('wp_ajax_cj_init_sync', array($this, 'init_sync'));
        add_action('wp_ajax_cj_sync_products', array($this, 'sync_products'));
    }

    public function init_sync() {
        $options = get_option('cj_products_settings');
        $total_count = isset($options['limit']) ? intval($options['limit']) : $this->get_total_count_products();
        wp_send_json_success(array('total_count' => $total_count));
    }
// общее количество продуктов, суммируя значения "productCount" для каждого фида продуктов.
    public function get_total_count_products() {
        $options = get_option('cj_products_settings');
        $company_id = isset($options['company_id']) ? $options['company_id'] : '';
        $access_token = isset($options['access_token']) ? $options['access_token'] : '';
        $partner_ids = isset($options['partner_ids']) ? $options['partner_ids'] : '';

        $query = '{
        productFeeds(companyId: "' . $company_id . '", partnerIds: [' . $partner_ids . ']) {
            resultList {
                productCount
            }
        }
    }';

        $response = wp_remote_post('https://ads.api.cj.com/query', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode(array('query' => $query))
        ));

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return 0;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        $product_feeds = isset($data['data']['productFeeds']['resultList']) ? $data['data']['productFeeds']['resultList'] : array();

        $total_count = 0;

        foreach ($product_feeds as $feed) {
            $total_count += isset($feed['productCount']) ? intval($feed['productCount']) : 0;
        }

        return $total_count;
    }

    public function sync_products() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cj_products';

        //  $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
        //   $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;

        $options = get_option('cj_products_settings');
        $company_id = isset($options['company_id']) ? $options['company_id'] : '';
        $access_token = isset($options['access_token']) ? $options['access_token'] : '';
        $partner_ids = isset($options['partner_ids']) ? $options['partner_ids'] : '';
        $pid = isset($options['pid']) ? $options['pid'] : '';
        $limit = isset($options['limit']) ? intval($options['limit']) : 10;
        //  $total_count = ($limit == 0) ? $this->get_total_count_products() : $limit;

        $offset = isset($options['offset']) ? intval($options['offset']) : 0;

        $products = isset($_POST['products']) ? $_POST['products'] : array();

        $query = '{
            products(companyId: "' . $company_id . '", partnerIds: [' . $partner_ids . '], limit: ' . $limit . ', offset: ' . $offset . ') {
                resultList {
                    advertiserId,
                    catalogId,
                    id,
                    title,
                    price {
                        amount,
                        currency
                    },
                    linkCode(pid: "' . $pid . '") {
                        clickUrl
                    },
                    brand
                }
            }
        }';

        $response = wp_remote_post('https://ads.api.cj.com/query', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode(array('query' => $query))
        ));

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            wp_send_json_error();
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        $products = isset($data['data']['products']['resultList']) ? $data['data']['products']['resultList'] : array();

        $synced_count = 0;

        foreach ($products as $product) {
            $advertiserId = isset($product['advertiserId']) ? $product['advertiserId'] : '';
            $catalogId = isset($product['catalogId']) ? $product['catalogId'] : '';
            $id = isset($product['id']) ? $product['id'] : '';
            $title = isset($product['title']) ? $product['title'] : '';
            $price_amount = isset($product['price']['amount']) ? $product['price']['amount'] : '';
            $price_currency = isset($product['price']['currency']) ? $product['price']['currency'] : '';
            $url = isset($product['linkCode']['clickUrl']) ? $product['linkCode']['clickUrl'] : '';
            $brand = isset($product['brand']) ? $product['brand'] : '';

            $result = $wpdb->replace(
                $table_name,
                array(
                    'advertiser_id' => $advertiserId,
                    'catalog_id' => $catalogId,
                    'product_id' => $id,
                    'title' => $title,
                    'price_amount' => $price_amount,
                    'price_currency' => $price_currency,
                    'url' => $url,
                    'brand' => $brand,
                ),
                array('%s', '%s', '%s', '%s', '%f', '%s', '%s', '%s')
            );

            if ($result === false) {
                wp_send_json_error();
            }
            if ($result !== false) {
                $synced_count++;

            }
        }

        $offset += $synced_count;

        $options['offset'] = $offset;
        update_option('cj_products_settings', $options);

        wp_send_json_success(array('synced_count' => $synced_count));
    }
}