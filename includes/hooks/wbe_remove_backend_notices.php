<?php

function hide_admin_notices_for_non_admins() {
    if ( ! current_user_can( 'manage_options' ) ) {
        remove_all_actions( 'admin_notices' );
        remove_all_actions( 'all_admin_notices' ); // Algunos plugins usan este hook
        remove_all_actions( 'user_admin_notices' );
    }
}
add_action( 'admin_head', 'hide_admin_notices_for_non_admins', 99 );