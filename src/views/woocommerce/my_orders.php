  <div class="woocommerce-orders-table-wrapper">
        <table class="shop_table shop_table_responsive woocommerce-orders-table">
            <thead>
                <tr>
                    <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span class="nobr">Pedido</span></th>
                    <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr">Fecha</span></th>
                    <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status"><span class="nobr">Estado</span></th>
                    <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-total"><span class="nobr">Total</span></th>
                    <th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-actions"><span class="nobr">Acciones</span></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customer_orders as $order) : ?>
                    <tr class="woocommerce-orders-table__row order">
                        <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number" data-title="Pedido">
                            #<?php echo $order->get_order_number(); ?>
                        </td>
                        <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date" data-title="Fecha">
                            <time datetime="<?php echo esc_attr($order->get_date_created()->date('c')); ?>"><?php echo esc_html(wc_format_datetime($order->get_date_created())); ?></time>
                        </td>
                        <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status" data-title="Estado">
                            <?php echo esc_html(wc_get_order_status_name($order->get_status())); ?>
                        </td>
                        <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total" data-title="Total">
                            <?php echo wp_kses_post($order->get_formatted_order_total()); ?>
                        </td>
                        <td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-actions" data-title="Acciones">
                            <a href="<?php echo esc_url($order->get_view_order_url()); ?>" class="button wc-forward">
                                Ver
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>