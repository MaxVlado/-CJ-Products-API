<div class="wrap">
    <h1>CJ Products</h1>
    <form method="get" style="width: 100%">
        <input type="hidden" name="page" value="cj-products-view">
        <p class="search-box" style="float: left;width: 100%">
            <label class="screen-reader-text" for="post-search-input">Search Products:</label>
            <input type="search" style="width: 100%" id="post-search-input" name="search" value="<?php echo esc_attr($search); ?>">
            <input type="submit" class="button" style="margin-top: 12px" value="Search Products">
        </p>
    </form>
    <div class="tablenav top">
        <div class="tablenav-pages">
            <span class="displaying-num"><?php echo $total; ?> items</span>
            <?php echo paginate_links(array(
                'base' => add_query_arg('paged', '%#%'),
                'format' => '',
                'prev_text' => __('&laquo;', 'text-domain'),
                'next_text' => __('&raquo;', 'text-domain'),
                'total' => ceil($total / $limit),
                'current' => $page,
            )); ?>
        </div>
    </div>
    <table class="wp-list-table widefat striped">
        <thead>
        <tr>
            <th>№</th>
            <th>Color identifier</th>
            <th>Имя цвета</th>
            <th>HEX code</th>
            <th>Brand</th>
            <th>Title</th>
            <th>URL</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $count = 1 + (($_GET['paged'] == 1 ? 0 : $_GET['paged']) * 10);
        foreach ($products as $product) : ?>
            <tr>
                <td><?php echo $count ++; ?></td>
                <td><?php echo $product->color_identifier; ?></td>
                <td><?php echo $product->color_name ?></td>
                <td><?php echo $product->color_hex ?></td>
                <td><?php echo $product->brand; ?></td>
                <td>
                    <a href="<?php echo admin_url('admin.php?page=cj-product-edit&id=' . $product->id); ?>">
                        <?php echo $product->title; ?>
                    </a>
                </td>


                <td>
                    <a href="<?php echo $product->url; ?>" target="_blank">Click URL</a>
                </td>
                <!--                <td>
                    <a href="<?php echo admin_url('admin.php?page=cj-product-edit&id=' . $product->id); ?>">Edit</a>

                    <a href="<?php //echo admin_url('admin.php?page=cj-delete-product&id=' . $product->id); ?>
                        " onclick="return confirm('Вы уверены, что хотите удалить этот образец?');">Del</a>
               </td> -->
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="tablenav bottom">
        <div class="tablenav-pages">
            <span class="displaying-num"><?php echo $total; ?> items</span>
            <?php echo paginate_links(array(
                'base' => add_query_arg('paged', '%#%'),
                'format' => '',
                'prev_text' => __('&laquo;', 'text-domain'),
                'next_text' => __('&raquo;', 'text-domain'),
                'total' => ceil($total / $limit),
                'current' => $page,
            )); ?>
        </div>
    </div>
</div>