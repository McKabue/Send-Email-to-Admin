<?php

add_filter(
    'the_posts', // (Required) The name of the filter to hook the $function_to_add callback to.
    'send_notifications_if_no_search_results', // (Required) The callback to be run when the filter is applied.
    1, // (Optional) Used to specify the order in which the functions associated with a particular action are executed. Lower numbers correspond with earlier execution, and functions with the same priority are executed in the order in which they were added to the action.
    2 // (Optional) The number of arguments the function accepts.
);

/**
 * @param WP_Post[] $posts
 * @param WP_Query $query
 */
function send_notifications_if_no_search_results($posts, WP_Query $query)
{
    if ($query->is_main_query() && count($posts) == 0) {
        send_notifications();
    }

    return $posts;
}

function send_notifications()
{
    $notification = get_notification_data("no-search-results");

    $users = $notification->users;

    foreach ($users as $user) {
        $channels = $user->channels;

        foreach ($channels as $channel) {
            if ($channel->name == 'email') {
                $emails = $channel->value;

                foreach ($emails as $email) {
                    send_email($email);
                }
            }
        }
    }
}

/**
 * @param string $email
 */
function send_email(string $email)
{
    $search_query = get_search_query();

    $to = $email;
    $subject = 'Search Result Not Found';
    $message = 'User searched  ' . $search_query;

    echo json_encode(array($to, $subject, $message));

    wp_mail($to, $subject, $message);
}
