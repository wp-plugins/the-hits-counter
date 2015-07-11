<?php

/*
  Plugin Name: The Hits Counter
  Plugin URI: http://gagan.pro
  Description: Checks and displays the number of hits for posts and pages
  Author: Gagan Deep Singh
  Version: 1.0
  Author URI: http://gagan.pro
  License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Runs at the init of every page and checks when to add the counters
 */
function thc_hits_logic() {
	if ( is_single() && get_the_ID() > 0 ) {
		thc_add_counter( get_the_ID() );
	}
}

add_action( 'wp', 'thc_hits_logic' );

/**
 * Adds the counter of the post
 * @param int|null $post_id The post ID of the post for which the counter needs to be incremented. Can omit if within the_loop.
 */
function thc_add_counter( $post_id = null ) {
	if ( $post_id == null || $post_id < 1 ) {
		return;
	}
	$counter = get_post_meta( $post_id, 'thc_hits_counter', true );
	if ( empty( $counter ) ) {
		$counter = 0;
	}
	$counter = intval( $counter );
	$counter++;
	update_post_meta( $post_id, 'thc_hits_counter', $counter );
}

/**
 * Displays the hits that are counted on the post
 * @param int|null $post_id The post ID of the post for which we need to display the counter. Can omit if within the_loop.
 */
function thc_display_count( $post_id = null ) {
	echo apply_filters( 'thc_display_count', thc_get_count( $post_id ) );
}

/**
 * Returns the count for the given post id
 * @param int|null $post_id The post ID of the post for which we need to get the count. Can omit if within the_loop.
 * @return int The number of times a post/page has been viewed
 */
function thc_get_count( $post_id = null ) {
	if ( $post_id == null ) {
		$post_id = get_the_ID();
	}
	$counter = get_post_meta( $post_id, 'thc_hits_counter', true );
	if ( empty( $counter ) ) {
		$counter = 0;
	}
	$counter = intval( $counter );
	return $counter;
}

add_shortcode( 'thc_hits_count', 'thc_get_count' );

/**
 * Returns the formatted html we need to display for the hits count on the post/page.
 * @param int $count number of hits for the post/page
 * @return string Formatted html we need to display for the hits count on the post/page.
 */
function thc_counter_pre_text( $count ) {
	return '<span class="hits-counter">Hits : ' . $count . '</span>';
}

add_filter( 'thc_display_count', 'thc_counter_pre_text' );

/**
 * Appends the hits counter output to the content of the post/page
 * @param string $content Orignal content of post/page
 * @return string Content appended with the hits counter output.
 */
function thc_content_filter( $content ) {
	$content .= apply_filters( 'thc_display_count', thc_get_count() );
	return $content;
}

add_filter( 'the_content', 'thc_content_filter' );
