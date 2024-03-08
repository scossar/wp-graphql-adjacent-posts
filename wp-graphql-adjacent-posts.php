<?php
/**
 * Plugin Name: WPGraphQL Adjacent Posts
 * Description: Extends the WPGraphQL plugin to add fields for adjacent posts.
 * Version: 0.1
 * Author: scossar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'graphql_register_types', 'add_adjacent_post_fields_to_wpgraphql_schema' );

/**
 * Registers adjacent post fields.
 *
 * @return void
 */
function add_adjacent_post_fields_to_wpgraphql_schema() {
	register_graphql_fields( 'Post', [
		'previousPost'           => [
			'type'        => 'Post',
			'description' => __( 'Previous post', 'zalgorithm' ),
			'resolve'     => function ( WPGraphQL\Model\Post $resolving_post ) {
				return resolve_adjacent_post( $resolving_post, 'previous' );
			}
		],
		'nextPost'               => [
			'type'        => 'Post',
			'description' => __( 'Next post', 'zalgorithm' ),
			'resolve'     => function ( WPGraphQL\Model\Post $resolving_post ) {
				return resolve_adjacent_post( $resolving_post, 'next' );
			}
		],
		'previousPostInCategory' => [
			'type'        => 'Post',
			'description' => __( 'Previous post in same category', 'zalgorithm' ),
			'resolve'     => function ( WPGraphQL\Model\Post $resolving_post ) {
				return resolve_adjacent_post( $resolving_post, 'previous', true );
			}
		],
		'nextPostInCategory'     => [
			'type'        => 'Post',
			'description' => __( 'Next post in same category', 'zalgorithm' ),
			'resolve'     => function ( WPGraphQL\Model\Post $resolving_post ) {
				return resolve_adjacent_post( $resolving_post, 'next', true );
			}
		]
	] );
}

/**
 * Returns the next or previous post. Returns null if no post is found.
 *
 * @param \WPGraphQL\Model\Post $resolving_post The post to find the adjacent post for.
 * @param string $direction Whether to return the 'next' or 'previous' post.
 * @param boolean $in_same_term Whether to limit the next post to the post's category.
 *
 * @return \WPGraphQL\Model\Post|null
 */
function resolve_adjacent_post( WPGraphQL\Model\Post $resolving_post, $direction, $in_same_term = false ) {
	// Currently not setup to handle hierarchical posts.
	if ( is_post_type_hierarchical( $resolving_post->post_type ) ) {
		return null;
	}

	$current_post    = get_post( $resolving_post->postId );
	$GLOBALS['post'] = $current_post;
	setup_postdata( $current_post );

	// Any string other than 'next' will result in querying for the previous post.
	$adjacent_post = $direction === 'next' ? get_next_post( $in_same_term ) : get_previous_post( $in_same_term );
	wp_reset_postdata();

	return $adjacent_post ? new WPGraphQL\Model\Post( $adjacent_post ) : null;
}
