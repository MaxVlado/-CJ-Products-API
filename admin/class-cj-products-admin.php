<?php
class CJ_Products_Admin {
    public function __construct() {

    }

    public function add_menu_pages() {
        add_menu_page(
            'CJ Products',
            'CJ Парсинг',
            'manage_options',
            'cj-products',
            array($this, 'sync_page'),
            'dashicons-products',
            30
        );

        add_submenu_page(
            'cj-products',
            'CJ Products View',
            'Просмотр',
            'manage_options',
            'cj-products-view',
            array($this, 'view_page')
        );

        add_submenu_page(
            'cj-products',
            'CJ Products Settings',
            'Настройки',
            'manage_options',
            'cj-products-settings',
            array($this, 'settings_page')
        );
        add_submenu_page(
            null, // Изменено на null для скрытия подменю
            'Edit Product',
            'Edit Product',
            'manage_options',
            'cj-product-edit',
            array($this, 'edit_product')
        );

        add_submenu_page(
            'cj-products',
            'Импорт CSV',
            'Импорт CSV',
            'manage_options',
            'cj-product-import',
            array($this, 'cj_product_import_csv_page')
        //  'cj_product_import_csv_page'

        );
    }

    public function sync_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cj_products';
        $total_products = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");

        $cj_products_sync = new CJ_Products_Sync();
        $total_count = $cj_products_sync->get_total_count_products();

        include CJ_PRODUCTS_API_PLUGIN_DIR . 'admin/partials/cj-products-sync-page.php';
    }

    public function view_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cj_products';

        $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
        $page = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $where = '';
        if (!empty($search)) {
            $where = "WHERE product_id LIKE '%$search%' OR title LIKE '%$search%' OR url LIKE '%$search%'";
        }

        $total_query = "SELECT COUNT(*) FROM $table_name $where";
        $total = $wpdb->get_var($total_query);

        $products_query = "SELECT * FROM $table_name $where LIMIT $offset, $limit";
        $products = $wpdb->get_results($products_query);

        include CJ_PRODUCTS_API_PLUGIN_DIR . 'admin/partials/cj-products-view-page.php';
    }

    public function edit_product(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'cj_products';

        if (isset($_GET['id'])) {

            $product_id = intval($_GET['id']);
            $this->cj_update_product($table_name, $product_id);

            $product = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $product_id));

            if ($product) {
                include CJ_PRODUCTS_API_PLUGIN_DIR . 'admin/partials/cj-product-edit-page.php';
            } else {
                echo '<div class="wrap"><p>Продукт не найден.</p></div>';
            }
        } else {
            echo '<div class="wrap"><p>Неверный запрос.</p></div>';
        }
    }

    /**
     * Обновление данных образца краски
     */
    function cj_update_product($table_name,$id) {
        global $wpdb;

        // Проверка наличия данных формы
        if (isset($_POST['submit'])) {

            // Проверка nonce для защиты от CSRF-атак
            if (!isset($_POST['cj_product_edit_nonce']) || !wp_verify_nonce($_POST['cj_product_edit_nonce'], 'cj_product_edit')) {
                wp_die('Неверный nonce. Попробуйте еще раз.');
            }

            // Получение данных из формы
            $product_id = isset($_POST['product_id']) ? sanitize_text_field($_POST['product_id']) : '';
            $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
            $url = isset($_POST['url']) ? esc_url_raw($_POST['url']) : '';
            $color_name = isset($_POST['color_name']) ? sanitize_text_field($_POST['color_name']) : null;
             $color_hex = isset($_POST['color_hex']) ? sanitize_text_field($_POST['color_hex']) : null;

            // Обновление данных образца краски в базе данных
            $result = $wpdb->update(
                $table_name,
                array(
                    'product_id' => $product_id,
                    'title' => $title,
                    'url' => $url,
                    'color_name' => $color_name,
                    'color_hex' => $color_hex,
                    'updated_at' => current_time('mysql')
                ),
                array('id' => $id),
                array('%s', '%s', '%s', '%s', '%s', '%s'),
                array('%d')
            );
            // Проверка на ошибку при обновлении данных
            if ($result === false) {
                $error_message = 'Ошибка при обновлении данных образца краски: ' . $wpdb->last_error;
                $error_message .= '<br>Последний выполненный запрос: ' . $wpdb->last_query;
                wp_die($error_message);
            }

            $redirect_url = admin_url('admin.php?page=cj-products-view');
            echo '<script>window.location.href = "' . esc_url($redirect_url) . '";</script>';
            exit;

        }
    }

    /** Отображение страницы импорта CSV-файла*/
    function cj_product_import_csv_page() {

        $import_result = get_transient('cj_product_import_result');
        delete_transient('cj_product_import_result');

        if (isset($_POST['submit']) && isset($_FILES['csv_file']) && wp_verify_nonce($_POST['cj_products_import_csv_nonce'], 'cj_products_import_csv')) {
          $this->cj_product_process_import_csv();
        }

        if (isset($_POST['sync']) && wp_verify_nonce($_POST['cj_products_sync_csv_nonce'], 'cj_products_sync_csv')) {
            $this->cj_product_process_sync_csv();
        }

        $cj_product_csv_total_count = $this->cj_product_get_count_table('cj_products_csv');
        $cj_product_total_count = $this->cj_product_get_count_table('cj_products');

        require_once plugin_dir_path(__FILE__) . 'partials/cj-products-import-csv.php';
    }


    function cj_product_process_import_csv() {
        if (isset($_FILES['csv_file'])) {
            $csv_file = $_FILES['csv_file'];

            if ($csv_file['error'] === UPLOAD_ERR_OK) {
                $file_name = $csv_file['tmp_name'];
                $file_handle = fopen($file_name, 'r');

                // Проверка успешности открытия файла
                if ($file_handle !== false) {
                    global $wpdb;
                    $table_name = $wpdb->prefix . 'cj_products_csv';

                    $success_count = 0;
                    $error_count = 0;
                    $row_count = 0;
                    $error_messages = array();

                    while (($data = fgetcsv($file_handle)) !== false) {
                        if ($row_count === 0) {
                            $row_count++;
                            continue; // Пропустить первую строку (заголовки)
                        }

                        $row_count++;

                        $product_id = isset($data[0]) ? sanitize_text_field($data[0]) : '';
                        $name = isset($data[1]) ? sanitize_text_field($data[1]) : '';
                        $hex = isset($data[2]) ? sanitize_text_field($data[2]) : '';
                        $color_identifier = isset($data[3]) ? sanitize_text_field($data[3]) : '';
                        $color_identifier_display = isset($data[4]) ? sanitize_text_field($data[4]) : '';
                        $url = isset($data[5]) ? esc_url_raw($data[5]) : '';
                        $is_dark = isset($data[6]) ? sanitize_text_field($data[6]) : '';
                        $brand_name = isset($data[7]) ? sanitize_text_field($data[7]) : '';

                        $wpdb->insert(
                            $table_name,
                            array(
                                'product_id' => $product_id,
                                'name' => $name,
                                'hex' => $hex,
                                'color_identifier' => $color_identifier,
                                'color_identifier_display' => $color_identifier_display,
                                'url' => $url,
                                'is_dark' => $is_dark,
                                'brand_name' => $brand_name
                            ),
                            array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
                        );

                        if ($wpdb->last_error) {
                            $error_count++;
                            $error_messages[] = sprintf('Ошибка в строке %d: %s', $row_count, $wpdb->last_error);
                        } else {
                            $success_count++;
                        }
                    }

                    fclose($file_handle);

                    $import_result = array(
                        'status' => $error_count > 0 ? 'warning' : 'success',
                        'message' => sprintf(
                            'Импорт завершен. Успешно импортировано: %d, Ошибок: %d.',
                            $success_count,
                            $error_count
                        ),
                        'error_messages' => $error_messages
                    );
                } else {
                    $import_result = array(
                        'status' => 'error',
                        'message' => 'Ошибка при открытии файла.'
                    );
                }
            } else {
                $import_result = array(
                    'status' => 'error',
                    'message' => 'Ошибка при загрузке файла.'
                );
            }

            set_transient('cj_product_import_result', $import_result, 60);
            wp_redirect(admin_url('admin.php?page=cj-product-import'));
            exit;
        }
    }

    function cj_product_process_sync_csv() {
        global $wpdb;
        $cj_products_table = $wpdb->prefix . 'cj_products';
        $cj_products_csv_table = $wpdb->prefix . 'cj_products_csv';

        // Очистить таблицу cj_products перед обновлением
       // $wpdb->query("TRUNCATE TABLE $cj_products_table");
        $wpdb->query("UPDATE $cj_products_table
            SET
                color_identifier = NULL,
                color_name = '',
                color_hex = ''
        ");


        // 1. Вычислить color_identifier в таблице 'cj_products'
        $rows = $wpdb->get_results("SELECT id, title, brand, url FROM $cj_products_table");
        foreach ($rows as $row) {
            $color_identifier = $this->extract_color_identifier($row->title, $row->url, $row->brand);
            if ($color_identifier) {
                $wpdb->update(
                    $cj_products_table,
                    array('color_identifier' => $color_identifier),
                    array('id' => $row->id),
                    array('%s'),
                    array('%d')
                );
            }
        }

        // 2. Обновить color_name и color_hex в таблице 'cj_products' на основе данных из 'cj_products_csv'
        $query = "
            UPDATE $cj_products_table AS p
            INNER JOIN $cj_products_csv_table AS csv ON  p.color_identifier = csv.color_identifier
            SET
                p.color_name = csv.name,
                p.color_hex = csv.hex
        ";
        $wpdb->query($query);

        // Сообщение об успешном обновлении
        $message = 'Синхронизация данных завершена.';
        set_transient('cj_product_sync_result', $message, 60);
        $redirect_url = admin_url('admin.php?page=cj-product-import');
        wp_redirect($redirect_url);
        echo '<script>window.location.href = "' . esc_url($redirect_url) . '";</script>';
        exit;
    }

    // Функция для извлечения color_identifier из строки
    function extract_color_identifier($title, $url, $brand) {
        // Поиск в title
        preg_match('/\(([^)]+)\)/', $title, $matches);
        if (!empty($matches[1])) {
            $color_identifier = trim($matches[1]);
            if ($brand == 'Sherwin Williams' or $brand == 'Sherwin-Williams') {
                $color_identifier = 'SW' . $color_identifier;
            }
            return $color_identifier;
        }

        // Поиск в URL
        parse_str(parse_url($url, PHP_URL_QUERY), $query_params);
        if (!empty($query_params['url'])) {
            $decoded_url = urldecode($query_params['url']);
            preg_match('/-([\w-]+)-/', $decoded_url, $matches);
            if (!empty($matches[1])) {
                $color_identifier = $matches[1];
                if ($brand == 'Sherwin Williams') {
                    $color_identifier = 'SW' . $color_identifier;
                }
                return $color_identifier;
            }
        }

        return null;
    }

// обработки AJAX-запроса на получение прогресса импорта
    function cj_product_get_import_progress() {
        $progress = get_option('cj_product_import_progress', 0);
        echo $progress;
        wp_die();
    }

//подсчет записей в таблице
    function cj_product_get_count_table($table) {
        global $wpdb;
        $table_name = $wpdb->prefix . $table;
        $total_query = "SELECT COUNT(*) FROM $table_name";
        return $wpdb->get_var($total_query);
    }

    public function settings_page() {
        include CJ_PRODUCTS_API_PLUGIN_DIR . 'admin/partials/cj-products-settings-page.php';
    }

    public function init() {
        add_action('admin_menu', array($this, 'add_menu_pages'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
    }

    public function enqueue_styles() {
        wp_enqueue_style('cj-products-admin', CJ_PRODUCTS_API_PLUGIN_URL . 'admin/css/cj-products-admin.css');
       // wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"');

    }
}