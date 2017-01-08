<?php
/*
 * Plugin Name: Posts to Grid
 * Version: 1.1
 * Plugin URI: http://www.christianwood.net/
 * Description: Show Recent Posts in a Grid via [postgrid ...] shortcode. Takes get_post parameters and more.
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
  Includes
***************************************************************/
require_once('html-generator.php');




/**************************************************************
  Enqueue plugin CSS file
***************************************************************/

function prefix_add_my_stylesheet() {
    // Respects SSL, Style.css is relative to the current file
    wp_register_style( 'prefix-style', plugins_url('style.css', __FILE__) );
    wp_enqueue_style( 'prefix-style' );
}

add_action( 'wp_enqueue_scripts', 'prefix_add_my_stylesheet' );




/**************************************************************
  Generate HTML & CSS for the post grid
***************************************************************/

//Generates HTML for featured image block in grid
function featured_image( $postID, $height, $thumbSize ) {
  $featImageAtts = array(
    'class' => 'ptd_col-bg',
    'style' => array(
      'background-image' =>  "url(" . get_the_post_thumbnail_url( $postID, $thumbSize ) . ")",
      'height' => $height,
    ),
  );
  
  //Create and return featured image element
  $featuredImage = new HTMLElement( 'div', $featImageAtts, '' );
  return $featuredImage->get_element();
}


//Generate new grid column based on parameters
function create_column( $columns, $post, $height, $thumbSize ) {
  $colDivAtts = array(
    'class' => "ptd_col-1-{$columns}",
  );
  $linkAtts = array(
    'href' => get_permalink( $post->ID ),
  );
  
  //Create featured image block wrapped in an anchor tag
  $featuredImage = new HTMLElement( 'a', $linkAtts, featured_image( $post->ID, $height, $thumbSize ) );
  //Create the link to serve as the title under the image
  $postLink = new HTMLElement( 'a', $linkAtts, $post->post_title );
  //Combine elements
  $innerHTML = $featuredImage->get_element() . $postLink->get_element();
  //Create column element
  $column = new HTMLElement( 'div', $colDivAtts, $innerHTML );
  
  return $column->get_element();
}


//Generate HTML code based on an array of posts and columns
function create_grid( $postArray, $columns, $height, $thumbSize ) {
  $counter = 0;
  $innerHTML = "";
  
  while( $counter < count( $postArray ) ) {
    //Loop through number of columns given
    for ( $x = 0; $x < $columns && $counter < count( $postArray ); $x++ ) {
      //Create new column
      $innerHTML .= create_column( $columns, $postArray[$counter], $height, $thumbSize );
      //Increment counter
      $counter++;
    }
  } //End While
  
  $grid = new HTMLElement( 'div', array('class' => 'ptd_grid'), $innerHTML);

  return $grid->get_element();
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
      'thumbnail_size' => 'large',
      // These are all the get_post() args with sensible defaults
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
    $html = create_grid( $posts_array, $scAtts['cols'], $scAtts['height'], $scAtts['thumbnail_size'] );

    return $html;
}

add_shortcode( 'postgrid', 'postgrid_handler' );




?>