<?php
/*
  Plugin Name: Posts to Grid
  Description: Show Recent Posts in a Grid via [postgrid] shortcode.
  Author: Utopian Slingshot
  Version: 0.1
  Author URI: http://utopianslingshot.com
*/






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
  Generate HTML & CSS for the post grid
***************************************************************/

//Generate CSS code for a background image if a post has a featured image
function background_image_css( $postID ) {
  $css = "";
  
  if ( has_post_thumbnail( $postID ) ) {
    $css = $css . "background: url(" . get_the_post_thumbnail_url( $postID, 'large' ) . ") center center; background-size: cover;";
  }
  
  return $css;
}

//Generate HTML code based on an array of posts and columns
function postarray_to_html( $post_array, $columns, $height ) {
  $counter = 0;
  $theHTML = "";
  
  while( $counter < count( $post_array ) ) {
    //Open new grid row
    $theHTML .= "<div class='grid'>";
    
    //Loop through number of columns
    for ( $x = 0; $x < $columns && $counter < count( $post_array ); $x++ ) {
      //Open a new column div with appropriate class
      $theHTML .= "<div class='col-1-{$columns}'>";
      //Create a link around the featured image container
      $theHTML .= "<a href='" . post_permalink( $post_array[$counter]->ID ) . "'>";
      //Create container for post featured image
      $theHTML .= "<div class='col-bg' style='" . background_image_css( $post_array[$counter]->ID ) . " height: " . $height . ";'></div></a>";
      //Create permalink for the post
      $theHTML .= "<a href='" . post_permalink( $post_array[$counter]->ID ) . "'>" . $post_array[$counter]->post_title . "</a>";
      //Close the column div
      $theHTML .= "</div>";

      //Increment counter
      $counter++;
    }
    
    //Close grid row
    $theHTML .= "</div>";
  } //End While

  return $theHTML;
}



/**************************************************************
  Register & Return Shortcode
***************************************************************/

// Return HTML shortcode
function postgrid_func( $atts ) {
    
    //Store Short code attributes
    $a = shortcode_atts( array(
        'cols' => '3',
        'posttype' => 'post',
        'max' => '3',
        'height' => '15em',
    ), $atts );
  
    //Set up posts posts arguments array
    $postargs = array(
      'posts_per_page'   => $a['max'],
      'offset'           => 0,
      'orderby'          => 'date',
      'order'            => 'DESC',
      'post_type'        => $a['posttype'],
      'post_status'      => 'publish',
      'suppress_filters' => true 
    );
  
    //Get array of posts with given args
    $posts_array = get_posts( $postargs );
  
    //Generate HTML of given post data
    $html = postarray_to_html( $posts_array, $a['cols'],  $a['height']);

    return $html;
}

add_shortcode( 'postgrid', 'postgrid_func' );





?>