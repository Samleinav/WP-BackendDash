<?php

add_action('admin_bar_menu', function ($wp_admin_bar) {
    $args = [
        'id'    => 'custom_rooms_link',
        'title' => '<span>Rooms</span>', // O HTML completo si se necesita
        'href'  =>  wberoute("center.rooms"),
        'meta'  => [
            'target' => '_blank',
            'class'  => 'adminify-top-menu-item adminify-top-menu-new-content', // Clases personalizadas
        ]
    ];
    $wp_admin_bar->add_node($args);
}, 100);