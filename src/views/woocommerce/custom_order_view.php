<div class="wrap">
    <h1><?php printf( esc_html__( 'Order Details #%s', 'mi-plugin' ), $order->get_order_number() ); ?></h1>

    <style>
        .order-details-card { margin-bottom: 20px; border: 1px solid #ddd; padding: 15px; border-radius: 4px; background: #fdfdfd; }
        .order-details-card h3 { margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #eee; }
        .order-details-card p { margin: 5px 0; }
        .order-items table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .order-items th, .order-items td { border: 1px solid #eee; padding: 8px; text-align: left; }
        .order-items th { background-color: #f5f5f5; }
        .order-summary { text-align: right; margin-top: 20px; }
        .order-summary p { font-size: 1.1em; font-weight: bold; }
    </style>

    <div class="order-details-card">
        <h3><?php _e( 'General Information', 'mi-plugin' ); ?></h3>
        <p><strong><?php _e( 'Status:', 'mi-plugin' ); ?></strong> <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></p>
        <p><strong><?php _e( 'Order Date:', 'mi-plugin' ); ?></strong> <?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></p>
        <p><strong><?php _e( 'Payment Method:', 'mi-plugin' ); ?></strong> <?php echo esc_html( $order->get_payment_method_title() ); ?></p>
    </div>

    <div class="order-details-card">
        <h3><?php _e( 'Products', 'mi-plugin' ); ?></h3>
        <div class="order-items">
            <table>
                <thead>
                    <tr>
                        <th><?php _e( 'Product', 'mi-plugin' ); ?></th>
                        <th><?php _e( 'Quantity', 'mi-plugin' ); ?></th>
                        <th><?php _e( 'Unit Price', 'mi-plugin' ); ?></th>
                        <th><?php _e( 'Total', 'mi-plugin' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $order->get_items() as $item_id => $item ) : ?>
                        <tr>
                            <td><?php echo esc_html( $item->get_name() ); ?></td>
                            <td><?php echo esc_html( $item->get_quantity() ); ?></td>
                            <td><?php echo wc_price( $item->get_subtotal() / $item->get_quantity() ); ?></td>
                            <td><?php echo wc_price( $item->get_total() ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="order-summary">
            <p><?php _e( 'Subtotal:', 'mi-plugin' ); ?> <?php echo wc_price( $order->get_subtotal() ); ?></p>
            <?php if ( $order->get_shipping_total() > 0 ) : ?>
                <p><?php _e( 'Shipping:', 'mi-plugin' ); ?> <?php echo wc_price( $order->get_shipping_total() ); ?></p>
            <?php endif; ?>
            <?php if ( $order->get_total_tax() > 0 ) : ?>
                <p><?php _e( 'Taxes:', 'mi-plugin' ); ?> <?php echo wc_price( $order->get_total_tax() ); ?></p>
            <?php endif; ?>
            <p><?php _e( 'Total:', 'mi-plugin' ); ?> <?php echo wc_price( $order->get_total() ); ?></p>
        </div>
    </div>

    <div class="order-details-card">
        <h3><?php _e( 'Billing Address', 'mi-plugin' ); ?></h3>
        <p><strong><?php _e( 'Name:', 'mi-plugin' ); ?></strong> <?php echo esc_html( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ); ?></p>
        <p><strong><?php _e( 'Company:', 'mi-plugin' ); ?></strong> <?php echo esc_html( $order->get_billing_company() ); ?></p>
        <p><strong><?php _e( 'Address 1:', 'mi-plugin' ); ?></strong> <?php echo esc_html( $order->get_billing_address_1() ); ?></p>
        <?php if ( $order->get_billing_address_2() ) : ?>
            <p><strong><?php _e( 'Address 2:', 'mi-plugin' ); ?></strong> <?php echo esc_html( $order->get_billing_address_2() ); ?></p>
        <?php endif; ?>
        <p><strong><?php _e( 'City:', 'mi-plugin' ); ?></strong> <?php echo esc_html( $order->get_billing_city() ); ?></p>
        <p><strong><?php _e( 'State/Province:', 'mi-plugin' ); ?></strong> <?php echo esc_html( $order->get_billing_state() ); ?></p>
        <p><strong><?php _e( 'Postal Code:', 'mi-plugin' ); ?></strong> <?php echo esc_html( $order->get_billing_postcode() ); ?></p>
        <p><strong><?php _e( 'Country:', 'mi-plugin' ); ?></strong> <?php echo esc_html( $order->get_billing_country() ); ?></p>
        <p><strong><?php _e( 'Email:', 'mi-plugin' ); ?></strong> <?php echo esc_html( $order->get_billing_email() ); ?></p>
        <p><strong><?php _e( 'Phone:', 'mi-plugin' ); ?></strong> <?php echo esc_html( $order->get_billing_phone() ); ?></p>
    </div>
</div>