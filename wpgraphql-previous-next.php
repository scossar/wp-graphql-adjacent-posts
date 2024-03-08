<?php
/**
 * Plugin Name: WPGraphQL Previous Next
 * Description: Adds previousPost and nextPost fields to the data returned by WPGraphQL Post queries
 * Version: 0.1
 * Author: scossar
 */

/**
 * Based on https://github.com/kellenmace/pagination-station/blob/main/pagination-fields.php
 */

// Probably not required, but deactivate the plugin if WPGraphQL isn't activated
add_action( 'admin_init', 'check_wpgraphql_dependency' );

function check_wpgraphql_dependency() {
	if ( ! class_exists( 'WPGraphQL' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices', 'wpgraphql_missing_notice' );
	}
}

function wpgraphql_missing_notice() {
	?>
    <div class="error">
        <p>The WPGraphQL Previous Next plugin requires the WPGraphQL plugin to be active.</p>
    </div>
	<?php
}

add_action( 'graphql_register_types', 'add_previous_and_next_post_to_wpgraphql_schema' );

function add_previous_and_next_post_to_wpgraphql_schema() {
	register_graphql_fields( 'Post', [
		'previousPost' => [
			'type'        => 'Post',
			'description' => 'Previous post',
			'resolve'     => function ( WPGraphQL\Model\Post $resolving_post ) {
				if ( is_post_type_hierarchical( $resolving_post->post_type ) ) {
					return null;
				}
				$post            = get_post( $resolving_post->postId );
				$GLOBALS['post'] = $post;
				setup_postdata( $post );
				$previous_post = get_previous_post();
				wp_reset_postdata();

				return $previous_post ? new WPGraphQL\Model\Post( $previous_post ) : null;
			}
		],
		'nextPost'     => [
			'type'        => 'Post',
			'description' => 'Next post',
			'resolve'     => function ( WPGraphQL\Model\Post $resolving_post ) {
				if ( is_post_type_hierarchical( $resolving_post->post_type ) ) {
					return null;
				}
				$post            = get_post( $resolving_post->postId );
				$GLOBALS['post'] = $post;
				setup_postdata( $post );
				$next_post = get_next_post();
				wp_reset_postdata();

				return $next_post ? new WPGraphQL\Model\Post( $next_post ) : null;
			}
		]
	] );
}
