<?php
function latest_posts_render_callback($attributes)
{
    // Allow non-authenticated users to access the block
    if (!current_user_can('read')) {
        return '<p>You do not have permission to view this block.</p>';
    }

    $query_args = [
        'posts_per_page' => 5,
        'post_status'    => 'publish',
    ];
    $query = new WP_Query($query_args);

    if (!$query->have_posts()) {
        return '<p>No posts found.</p>';
    }

    $output = '<ul class="latest-posts-block">';
    while ($query->have_posts()) {
        $query->the_post();
        $output .= '<li>';
        $output .= '<a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a>';
        $output .= '</li>';
    }
    $output .= '</ul>';

    wp_reset_postdata();

    return $output;
}
