<?php

/**
 * Plugin Name: Send Notification If
 * Plugin URI: https://github.com/McKabue/send-notification-if
 * Description: Send Emails to Admins whenever certain conditions are meet. Version 1.0 sends emails to all admins when the search is not found. Future versions will have more configuration options.
 * Version: 1.0
 * Author: Kabue Charles
 * Author URI: https://mckabue.com/
 * License: MIT
 **/
require 'send-notification-if.settings.php';
require 'send-notification-if.no-search-results.php';

function get_notification_data($notification_name)
{
    $stringFileContents = file_get_contents(__DIR__ . "/send-notification-if.json");
    $jsonFileContents = json_decode($stringFileContents);
    $notifications = $jsonFileContents->notifications;

    foreach ($notifications as $notification) {
        if ($notification->name == $notification_name) {
            $user_map = function ($user) {
                if ($user->type == 'role') {
                    $channel_map = function ($channel) use ($user) {
                        if ($channel == 'email') {
                            return (object) ['name' => $channel, 'value' => get_role_emails($user->value)];
                        }
                    };

                    $user->channels = array_map($channel_map, $user->channels);
                }

                return $user;
            };

            $notification->users = array_map($user_map, $notification->users);

            return $notification;
        }
    }

    return null;
}

function get_role_emails($role)
{
    $wp_users = get_users('role=' + $role);

    $get_email = function ($wp_user) {
        return $wp_user->data->user_email;
    };

    return array_map($get_email, $wp_users);
}
