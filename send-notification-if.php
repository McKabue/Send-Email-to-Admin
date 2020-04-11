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

add_filter(
    'the_posts', // (Required) The name of the filter to hook the $function_to_add callback to.
    'send_email_to_admin_if_no_search_results', // (Required) The callback to be run when the filter is applied.
    1, // (Optional) Used to specify the order in which the functions associated with a particular action are executed. Lower numbers correspond with earlier execution, and functions with the same priority are executed in the order in which they were added to the action.
    2 // (Optional) The number of arguments the function accepts.
);

/**
 * @param WP_Post[] $posts
 * @param WP_Query $query
 */
function send_email_to_admin_if_no_search_results($posts, WP_Query $query)
{
    if ($query->is_main_query() && count($posts) == 0) {
        send_email_to_admins();
    }

    return $posts;
}

function send_email_to_admins()
{
    $admin_emails = get_admin_emails();

    foreach ($admin_emails as $admin_email) {
        send_email($admin_email);
    }
}

function get_admin_emails()
{
    $wp_users = get_users('role=administrator');

    function get_email($wp_user)
    {
        return $wp_user->data->user_email;
    }

    return array_map("get_email", $wp_users);
}

/**
 * @param string $admin_email
 */
function send_email(string $admin_email)
{
    $search_query = get_search_query();

    $to = $admin_email;
    $subject = 'Search Result Not Found';
    $message = 'User searched  ' . $search_query;

    wp_mail($to, $subject, $message);
}
