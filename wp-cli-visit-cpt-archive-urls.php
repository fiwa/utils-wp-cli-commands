<?php
if ( ! defined( 'WP_CLI' ) ) {
    return '';
}

/**
 * Visit all archive pages for a specified custom post type on the site.
 *
 * ## OPTIONS
 *
 * [--post_type=<post_type>]
 * : The custom post type to visit. Default is 'post'.
 *
 * @when after_wp_load
 */
WP_CLI::add_command( 'visit_all_cpt_archives', function( $args, $assoc_args ) {
    $post_type = isset( $assoc_args['post_type'] ) ? $assoc_args['post_type'] : 'post';

    $post_type_object = get_post_type_object( $post_type );

    if ( ! $post_type_object || is_wp_error( $post_type_object ) ) {
        WP_CLI::error( "Invalid post type: {$post_type}" );
        return;
    }

    if ( ! $post_type_object->has_archive ) {
        WP_CLI::error( "Post type {$post_type} does not have an archive." );
        return;
    }

    $archive_url = get_post_type_archive_link( $post_type );

    if ( ! $archive_url ) {
        WP_CLI::error( "Failed to retrieve archive link for post type {$post_type}." );
        return;
    }

    $response = wp_remote_get( $archive_url, [
        'timeout' => 20,
    ] );

    if ( is_wp_error( $response ) ) {
        WP_CLI::warning( "Failed to visit {$archive_url}: " . $response->get_error_message() );
    } else {
        WP_CLI::success( "Visited {$archive_url}" );
    }
});
