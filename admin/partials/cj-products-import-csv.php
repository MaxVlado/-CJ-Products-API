<?php
/**
 * Шаблон страницы импорта CSV-файла
 */
?>
<div class="wrap">
    <h1>Импорт образцов красок из CSV-файла и синхронизация с таблицей </h1>

    <div id="import-progress" style="display: none;">
        <p>Импорт в процессе...</p>
        <progress id="import-progress-bar" max="100" value="0"></progress>
    </div>


    <div class="row">
        <div class="col">
            <form method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('cj_products_import_csv', 'cj_products_import_csv_nonce'); ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title text-center">
                            Шаг 1.
                            Импорт из  CSV-файла
                        </h5>
                    </div>

                    <div class="card-body">
                        <p class="card-text">Выберите CSV-файл</p>
                        <input type="file" name="csv_file" id="csv_file" accept=".csv" required>

                    </div>

                    <div class="card-footer text-center">
                            <input type="submit" name="submit" id="submit" class="button button-primary" value="Импортировать">
                     </div>
                </div>
            </form>
        </div>
        <div class="col">
            <form method="post" enctype="">
                <?php wp_nonce_field('cj_products_sync_csv', 'cj_products_sync_csv_nonce'); ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title text-center">Шаг 2.
                            Синхронизация  импорта с данными в таблице </h5>
                    </div>

                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <td>Произведенный импорт:</td>
                                <td><?php echo $cj_product_csv_total_count ?? 0; ?>  записей</td>
                            </tr>
                            <tr>
                                <td>Данные в таблице:</td>
                                <td><?php echo $cj_product_total_count ?? 0; ?> записей</td>
                            </tr>
                        </table>

                    </div>

                    <div class="card-footer text-center">
                        <p>
                            Синхронизируются поля:
                        </p>
                        <ol>
                            <li>Color name</li>
                            <li>HEX code</li>
                        </ol>
                            <input type="submit" name="sync" id="submit" class="button button-primary" value="Синхронизировать">

                    </div>
                </div>
            </form>
        </div>
    </div>


</div>