<?php
if ( ! defined( 'WP_CLI' ) ) {
    return '';
}

/**
 * Visit posts of a specified post type on the site.
 *
 * ## OPTIONS
 *
 * [--post_type=<post_type>]
 * : The post type to visit. Default is 'page'.
 *
 * @when after_wp_load
 */
WP_CLI::add_command( 'visit_posts', function( $args, $assoc_args ) {
    $post_type = isset( $assoc_args['post_type'] ) ? $assoc_args['post_type'] : 'page';

    $args = array(
        'post_type' => $post_type,
        'post_status' => 'publish',
        'posts_per_page' => -1,
    );

    $posts = get_posts( $args );

    foreach ( $posts as $post ) {
        $url = get_permalink( $post->ID );
        $response = wp_remote_get( $url );

        if ( is_wp_error( $response ) ) {
            WP_CLI::warning( "Failed to visit {$url}: " . $response->get_error_message() );
        } else {
            WP_CLI::success( "Visited {$url}" );
        }
    }
});
