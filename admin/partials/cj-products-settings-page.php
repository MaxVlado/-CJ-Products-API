<div class="wrap">
    <h1>CJ Products API Settings</h1>
    <form method="post" action="options.php">
        <?php
        settings_fields('cj_products_settings');
        do_settings_sections('cj-products-settings');
        submit_button();
        ?>
    </form>
</div>