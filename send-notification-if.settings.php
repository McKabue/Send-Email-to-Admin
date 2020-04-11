<?php

function notification_if_settings_page()
{
    add_menu_page(
        "Notification If",
        "Notification If",
        "manage_options",
        "notification-if",
        "notification_if_settings",
        "dashicons-archive",
        4
    );
}

add_action('admin_menu', 'notification_if_settings_page');
