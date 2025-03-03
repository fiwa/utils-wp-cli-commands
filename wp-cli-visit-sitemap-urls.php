<?php
if ( ! defined( 'WP_CLI' ) ) {
    return '';
}

/**
 * Visit all URLs listed in the sitemap.
 *
 * ## OPTIONS
 *
 * [--sitemap_url=<sitemap_url>]
 * : The URL of the sitemap. Default is '/sitemap.xml'.
 *
 * @when after_wp_load
 */
WP_CLI::add_command( 'visit_sitemap', function( $args, $assoc_args ) {
    $sitemap_url = isset( $assoc_args['sitemap_url'] ) ? $assoc_args['sitemap_url'] : home_url( '/sitemap.xml' );

    $response = wp_remote_get( $sitemap_url );

    if ( is_wp_error( $response ) ) {
        WP_CLI::error( "Failed to retrieve sitemap: " . $response->get_error_message() );
        return;
    }

    libxml_use_internal_errors(true); // Enable user error handling for libxml.
    $body = wp_remote_retrieve_body( $response );
    $xml = simplexml_load_string( $body );

    if ($xml === false) {
        // Handle errors
        $errors = libxml_get_errors();
        foreach ($errors as $error) {
            WP_CLI::error( $error->message );
        }
        libxml_clear_errors();
        return;
    }

    $urls = [];
    foreach ($xml->url as $url) {
        $urls[] = (string) $url->loc;
    }

    foreach ($urls as $url) {
        $response = wp_remote_get( $url );

        if ( is_wp_error( $response ) ) {
            WP_CLI::warning( "Failed to visit {$url}: " . $response->get_error_message() );
        } else {
            WP_CLI::success( "Visited {$url}" );
        }
    }
});
