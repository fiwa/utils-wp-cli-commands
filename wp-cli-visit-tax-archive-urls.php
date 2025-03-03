<?php
if ( ! defined( 'WP_CLI' ) ) {
    return '';
}

/**
 * Visit all archive pages for a specified taxonomy on the site.
 *
 * ## OPTIONS
 *
 * [--taxonomy=<taxonomy>]
 * : The taxonomy to visit. Default is 'category'.
 *
 * @when after_wp_load
 */
WP_CLI::add_command( 'visit_all_tax_archives', function( $args, $assoc_args ) {
    $taxonomy = isset( $assoc_args['taxonomy'] ) ? $assoc_args['taxonomy'] : 'category';

    $terms = get_terms( array(
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
    ) );

    if ( is_wp_error( $terms ) ) {
        WP_CLI::error( "Failed to retrieve terms for taxonomy {$taxonomy}: " . $terms->get_error_message() );
        return;
    }

    foreach ( $terms as $term ) {
        $url = get_term_link( $term );
        $response = wp_remote_get( $url, [
            'timeout' => 20,
        ] );

        if ( is_wp_error( $response ) ) {
            WP_CLI::warning( "Failed to visit {$url}: " . $response->get_error_message() );
        } else {
            WP_CLI::success( "Visited {$url}" );
        }
    }
});
