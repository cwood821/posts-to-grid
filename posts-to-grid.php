<?php
/*
 * Plugin Name: Posts to Grid
 * Version: 1.0
 * Plugin URI: http://www.christianwood.net/
 * Description: Show Recent Posts in a Grid via [postgrid ...] shortcode. Takes all get_post parameters.
 * Author: Christian Wood
 * Author URI: http://www.christianwood.net/
 * Requires at least: 4.7
 * Tested up to: 4.7
 *
*/


/**************************************************************
  Security
***************************************************************/

if ( ! defined( 'ABSPATH' ) ) exit;




/**************************************************************
  Enqueue plugin CSS file
***************************************************************/

function prefix_add_my_stylesheet() {
    // Respects SSL, Style.css is relative to the current file
    wp_register_style( 'prefix-style', plugins_url('style.css', __FILE__) );
    wp_enqueue_style( 'prefix-style' );
}
// Register with hook 'wp_enqueue_scripts', which can be used for front end CSS and JavaScript
add_action( 'wp_enqueue_scripts', 'prefix_add_my_stylesheet' );




/**************************************************************
  Generate HTML & CSS for the Post grid
***************************************************************/

//Generate CSS code for a background image if a post has a featured image
function featured_image_css( $postID ) {
  $css = "";
  
  if ( has_post_thumbnail( $postID ) ) {
    $css = $css . "background: url(" . get_the_post_thumbnail_url( $postID, 'large' ) . ") center center; background-size: cover;";
  }
  
  return $css;
}


//Generate HTML for featured image block in grid
function featured_image_block( $postID, $height ) {
  
  $html = "<div class='ptd_col-bg' style='" . featured_image_css( $postID ) . " height: " . $height . ";'>"; 
  $html .= "</div>";
  
  return $html;
}


//Generate new grid column based on parameters
function create_column( $columns, $post, $height ) {
  $html = "";
  //Open a new column div with appropriate class
  $html .= "<div class='ptd_col-1-{$columns}'>";
  //Create a link around the featured image container
  $html .= "<a href='" . post_permalink( $post->ID ) . "'>";
  //Create container for post featured image
  $html .= featured_image_block( $post->ID, $height ) . "</a>";
  //Create permalink with post title 
  $html .= "<a href='" . post_permalink( $post->ID ) . "'>" . $post->post_title . "</a>";
  //Close the column div
  $html .= "</div>";
  
  return $html;
}


//Generate HTML code based on an array of posts and columns
function create_grid( $post_array, $columns, $height ) {
  $counter = 0;
  $html = "";
  
  while( $counter < count( $post_array ) ) {
    //Open new grid row
    $html .= "<div class='ptd_grid'>";
    
    //Loop through number of columns given
    for ( $x = 0; $x < $columns && $counter < count( $post_array ); $x++ ) {
      
      //Create new column
      $html .= create_column( $columns, $post_array[$counter], $height );

      //Increment counter
      $counter++;
    }
    
    //Close grid row
    $html .= "</div>";
  } //End While

  return $html;
}




/**************************************************************
  Register & Return Shortcode
***************************************************************/

// Return HTML shortcode
function postgrid_handler( $atts ) {
    //Short code default parameters
    $defaults = array(
      // Short Code specific args
      'cols' => '3',
      'height' => '15em',
      // These are all the get_post() params with sensible defaults
      // https://developer.wordpress.org/reference/functions/get_posts/
      'posts_per_page'   => 6,  
      'offset'           => 0,
      'category'         => '',
      'category_name'    => '',
      'orderby'          => 'date',
      'order'            => 'DESC',
      'include'          => '',
      'exclude'          => '',
      'meta_key'         => '',
      'meta_value'       => '',
      'post_type'        => 'post',
      'post_mime_type'   => '',
      'post_parent'      => '',
      'author'	   => '',
      'author_name'	   => '',
      'post_status'      => 'publish',
      'suppress_filters' => true 
    );
    
    //Store passed short code attributes, add deafults
    $scAtts = shortcode_atts( $defaults, $atts );
  
    //Set up posts posts arguments array
    $postargs = array(
      'posts_per_page'   => $scAtts['posts_per_page'],
      'offset'           => $scAtts['offset'],
      'category'         => $scAtts['category'],
      'category_name'    => $scAtts['category_name'],
      'orderby'          => $scAtts['orderby'],
      'order'            => $scAtts['order'],
      'include'          => $scAtts['include'],
      'exclude'          => $scAtts['exclude'],
      'meta_key'         => $scAtts['meta_key'],
      'meta_value'       => $scAtts['meta_value'],
      'post_type'        => $scAtts['post_type'],
      'post_mime_type'   => $scAtts['post_mime_type'],
      'post_parent'      => $scAtts['post_parent'],
      'author'	         => $scAtts['author'],
      'author_name'	     => $scAtts['author_name'],
      'post_status'      => $scAtts['post_status'],
      'suppress_filters' => $scAtts['suppress_filters']
    );
  
    //Get array of posts with given args
    $posts_array = get_posts( $postargs );
  
    //Generate HTML of given post data
    $html = create_grid( $posts_array, $scAtts['cols'], $scAtts['height'] );

    return $html;
}

add_shortcode( 'postgrid', 'postgrid_handler' );





?>