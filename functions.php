<?php
/* #01 Load Parent Theme style.css file
=============================== */
function extra_enqueue_styles() {
	wp_enqueue_style( 'extra-parent', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'extra-parent' ) );
}
add_action( 'wp_enqueue_scripts', 'extra_enqueue_styles' );

function wpc_extra_display_single_post_meta() {
    $post_meta_options = et_get_option( 'extra_postinfo2', array(
        'author',
        'date',
        'categories',
        'comments',
        'rating_stars',
    ) );
 
    $meta_args = array(
        'author_link'    => in_array( 'author', $post_meta_options ),
        'author_link_by' => et_get_safe_localization( __( 'Posted by %s', 'extra' ) ),
        'post_date'      => in_array( 'date', $post_meta_options ),
        'categories'     => in_array( 'categories', $post_meta_options ),
        'comment_count'  => in_array( 'comments', $post_meta_options ),
        'rating_stars'   => in_array( 'rating_stars', $post_meta_options ),
    );
 
    return wpc_et_extra_display_post_meta( $meta_args );
}
 
function wpc_et_extra_display_post_meta( $args = array() ) {
    $default_args = array(
        'post_id'        => get_the_ID(),
        'author_link'    => true,
        'author_link_by' => et_get_safe_localization( __( 'by %s', 'extra' ) ),
        'post_date'      => true,
        'date_format'    => et_get_option( 'extra_date_format', '' ),
        'categories'     => true,
        'comment_count'  => true,
        'rating_stars'   => true,
    );
 
    $args = wp_parse_args( $args, $default_args );
 
    $meta_pieces = array();
 
    if ( $args['author_link'] ) {
        $meta_pieces[] = sprintf( $args['author_link_by'], wpc_extra_get_post_author_link( $args['post_id'] ) );
    }
 
    if ( $args['post_date'] ) {
        $meta_pieces[] = extra_get_the_post_date( $args['post_id'], $args['date_format'] );
    }
 
    if ( $args['categories'] ) {
        $meta_piece_categories = extra_get_the_post_categories( $args['post_id'] );
        if ( !empty( $meta_piece_categories ) ) {
            $meta_pieces[] = $meta_piece_categories;
        }
    }
 
    if ( $args['comment_count'] ) {
        $meta_piece_comments = extra_get_the_post_comments_link( $args['post_id'] );
        if ( !empty( $meta_piece_comments ) ) {
            $meta_pieces[] = $meta_piece_comments;
        }
    }
 
    if ( $args['rating_stars'] && extra_is_post_rating_enabled( $args['post_id'] ) ) {
        $meta_piece_rating_stars = extra_get_post_rating_stars( $args['post_id'] );
        if ( !empty( $meta_piece_rating_stars ) ) {
            $meta_pieces[] = $meta_piece_rating_stars;
        }
    }
 
    $output = implode( ' | ', $meta_pieces );
 
    return $output;
}
 
function wpc_extra_get_post_author_link( $post_id = 0 ) {
    $post_id = empty( $post_id ) ? get_the_ID() : $post_id;
    $post_author_id = get_post( $post_id )->post_author;
    $author = get_user_by( 'id', $post_author_id );
 
    if ( function_exists( 'coauthors_posts_links' ) ) {
        $link = sprintf(
            '<a href="%1$s" class="url fn" title="%2$s" rel="author">%3$s</a>',
            esc_url( get_author_posts_url( $author->ID, $author->user_nicename ) ),
            esc_attr( sprintf( __( 'Posts by %s' ), $author->display_name ) ),
            coauthors_posts_links( null, null, null, null, false )
        );
    } else {
        $link = sprintf(
            '<a href="%1$s" class="url fn" title="%2$s" rel="author">%3$s</a>',
            esc_url( get_author_posts_url( $author->ID, $author->user_nicename ) ),
            esc_attr( sprintf( __( 'Posts by %s' ), $author->display_name ) ),
            esc_html( $author->display_name )
        );
    }
    return $link;
}