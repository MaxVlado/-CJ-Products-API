<?php
/**
 * Шаблон страницы редактирования образца краски
 * admin/partials/cj-product-edit-page.php
 */
?>
<div class="wrap">
    <style>
        .regular-text {
            width: 100%;
        }
    </style>
    <h1>Редактировать образец краски</h1>
    <form method="post" action="">
        <?php wp_nonce_field('cj_product_edit', 'cj_product_edit_nonce'); ?>
        <input type="hidden" name="product_id" value="<?php echo esc_attr($product->id); ?>">
        <table class="form-table table " style="width: 100%">
            <tr>
                <th scope="row"><label for="product_id">Идентификатор цвета</label></th>
                <td><input type="text" name="product_id" id="product_id" class="regular-text"
                           value="<?php echo esc_attr($product->product_id); ?>" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="title">Название бренда</label></th>
                <td><input type="text" name="title" id="title" class="regular-text"
                           value="<?php echo esc_attr($product->title); ?>" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="url">Ссылка на Samplize</label></th>
                <td><input type="text" name="url" id="url" class="regular-text"
                           value="<?php echo esc_attr($product->url); ?>" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="color_name">Название цвета</label></th>
                <td><input type="text" name="color_name" id="color_name" class="regular-text"
                           value="<?php echo esc_attr($product->color_name); ?>"></td>
            </tr>


            <tr>
                <th scope="row"><label for="color_hex">HEX-код цвета</label></th>
                <td><input type="text" name="color_hex" id="color_hex" class="regular-text"
                           value="<?php echo esc_attr($product->color_hex); ?>"></td>
            </tr>

        </table>
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Обновить образец">
        </p>
    </form>
</div>
