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

require_once('class-html-element.php');




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
function featured_image( $post, $scAtts ) {
  $featImageAtts = array(
    'class' => 'ptd_col-bg',
    'style' => array(
      'background-image' =>  "url(" . get_the_post_thumbnail_url( $post->ID, $scAtts['thumbnail_size'] ) . ")",
      'height' => $scAtts['height'],
    ),
  );
  
  //Create and return featured image element
  $featuredImage = new HTMLElement( 'div', $featImageAtts, '' );
  return $featuredImage->get_element();
}


//Generate new grid column based on parameters
function create_grid_item( $post, $scAtts ) {
  $colDivAtts = array(
    'class' => "ptd_col-1-{$scAtts['cols']}",
  );
  $linkAtts = array(
    'href' => get_permalink( $post->ID ),
  );
  
  //Create featured image block wrapped in an anchor tag
  $featuredImage = new HTMLElement( 'a', $linkAtts, featured_image( $post, $scAtts ) );
  //Create the link to serve as the title under the image
  $postLink = new HTMLElement( 'a', $linkAtts, $post->post_title );
  //Combine elements
  $innerHTML = $featuredImage->get_element() . $postLink->get_element();
  //Create column element
  $column = new HTMLElement( 'div', $colDivAtts, $innerHTML );
  
  return $column->get_element();
}


// Generate HTML Grid, creates a row then fills with columns
function create_grid( $postArray, $scAtts ) {
  $counter = 0;
  $innerHTML = "";
  $columns = $scAtts['cols'];
  
  while( $counter < count( $postArray ) ) {
    //Loop through number of columns given
    for ( $x = 0; $x < $columns && $counter < count( $postArray ); $x++ ) {
      //Create new column
      $innerHTML .= create_grid_item( $postArray[$counter], $scAtts );
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
  
  // Overwrite defaults with passed attributes
  $scAtts = shortcode_atts( $defaults, $atts );
  // Get array of posts with given args
  $postsArray = get_posts( $scAtts );
  // Generate HTML grid of posts
  $html = create_grid( $postsArray, $scAtts );

  return $html;
}

add_shortcode( 'postgrid', 'postgrid_handler' );




?>