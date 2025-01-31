<?php

/**
 * Plugin Name:       My Latest Posts
 * Description:       Example block scaffolded with Create Block tool.
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       my-latest-posts
 *
 * @package CreateBlock
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Register the block dynamically based on `block.json` location.
 */
function create_block_my_latest_posts_block_init()
{
	// Detect block.json path inside `build/`
	$block_path = glob(__DIR__ . '/build/*/block.json');

	if (!empty($block_path[0])) {
		$block_dir = dirname($block_path[0]);
		register_block_type($block_dir, [
			'render_callback' => 'latest_posts_render_callback',
			'args' => [
				'context' => [
					'type' => 'string',
					'default' => 'view',
					'enum' => ['view', 'edit'],
				],
			],
			'permission_callback' => '__return_true' // ✅ Allow all users
		]);
	} else {
		register_block_type(__DIR__ . '/build', [
			'render_callback' => 'latest_posts_render_callback',
			'permission_callback' => function () {
				return current_user_can('read') || !is_admin(); // ✅ Allow non-logged-in users
			}
		]);
	}
}
add_action('init', 'create_block_my_latest_posts_block_init');

/**
 * Server-side rendering for the block.
 */
function latest_posts_render_callback($attributes)
{
	$query_args = [
		'posts_per_page' => isset($attributes['postsToShow']) ? intval($attributes['postsToShow']) : 5,
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
