<div class="wrap">
    <h1>CJ Products Sync</h1>
    <div class="row">
        <div class="col-12">Количество продуктов в базе данных: <?php echo $total_products; ?></div>
        <div class="col-12">Общее количество продуктов в API CJ: <?php echo $total_count; ?></div>
    </div>
    <div id="import-status" class="mt-2"></div>
    <p>
        <button id="start-sync" class="button button-primary">Начать синхронизацию</button>
    </p>
    <div id="sync-status"></div>
    <div id="loading-gif" style="display: none; text-align: center;">
        <img src="<?php echo plugin_dir_url(__FILE__) ?>../img/uploading.gif" width="150" alt="Uploading...">
    </div>
</div>

<script> jQuery(document).ready(function ($) {
        var importStatus = $('#import-status');
        var startSyncButton = $('#start-sync');
        var loadingGif = $('#loading-gif');
        startSyncButton.on('click', function () {
            startSyncButton.prop('disabled', true);
            importStatus.empty();
            loadingGif.show();
            initSync();
        });

        function initSync() {
            $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                action: 'cj_init_sync'
            }, function (response) {
                if (response.success) {
                    var totalCount = response.data.total_count;
                    syncProducts(0, totalCount);
                } else {
                    importStatus.html('<div class="alert alert-danger">Ошибка при инициализации синхронизации.</div>');
                    startSyncButton.prop('disabled', false);
                    loadingGif.hide();
                }
            });
        }

        function syncProducts(offset, totalCount) {
            $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                action: 'cj_sync_products',
                offset: offset
            }, function (response) {
                if (response.success) {
                    var syncedCount = response.data.synced_count;
                    offset += syncedCount;

                    if (offset < totalCount) {
                        syncProducts(offset, totalCount);
                    } else {
                        importStatus.html('<div class="alert alert-success">Синхронизация завершена. Всего синхронизировано ' + totalCount + ' продуктов.</div>');
                        startSyncButton.prop('disabled', false);
                        loadingGif.hide();
                        setTimeout(function() {
                            window.location.href = "<?php echo admin_url('admin.php?page=cj-products') ?>";
                        }, 5000);
                    }
                } else {
                    importStatus.html('<div class="alert alert-danger">Ошибка при синхронизации продуктов.</div>');
                    startSyncButton.prop('disabled', false);
                    loadingGif.hide();
                    setTimeout(function() {
                        window.location.href = "<?php echo admin_url('admin.php?page=cj-products') ?>";
                    }, 5000);
                }
            });
        }
    });
</script>