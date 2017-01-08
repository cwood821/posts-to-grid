<?php
/*
  HTML Generator Class
  Pass attributes as an array, pass inline style as an array, also;
*/

/**************************************************************
  Class Definition
***************************************************************/
class HTMLElement {
    private $name;
    private $height;

  
    public function __construct( $tag, $atts, $innerHTML )
    {
        $this->tag = $tag;
        $this->atts = $atts;
        $this->innerHTML = $innerHTML;
    }

  
    public function get_tag() {
        return $this->tag;
    }
  
  
    public function get_atts() {
      return $this->atts;
    }

  
    // Returns HTML tag with attributes and innerHTML as string
    public function get_element(){
      //Open tag
      $element = "<" .  $this->tag . " ";
      //Add attributes
      $element .= $this->format_attributes() . ">";
      //Add innerHTML and close tag if not a void element
      if ( !$this->is_void() ) {
        $element .= $this->innerHTML;
        $element .= "</" .  $this->tag . ">";
      }
        
      return $element;
    }
  
     
    // Returns attributes formatted in attribute='value' form;  
    private function format_attributes() {
      $attList = "";
      $attKeys = array_keys( $this->atts );
      $attValues = array_values( $this->atts );
      
      for ( $i = 0; $i < count( $this->atts ); $i++ ) {
        // 'style' value is formatted as inline CSS, all others as plain string
        if( $attKeys[$i] === 'style' ) {
          $attList .= $attKeys[$i] . "='" . $this->format_css( $attValues[$i] ) . "' ";
        } else {
          $attList .= $attKeys[$i] . "='" . $attValues[$i] . "' ";
        }
      }
      
      return $attList;
    }
  
    
    // Returns CSS properties formatted for an inline style
    private function format_css( $css ) {
      $propList = "";
      $propKeys = array_keys( $css );
      $propValues = array_values( $css );
      
      for ( $i = 0; $i < count( $css ); $i++ ) {
          $propList .= $propKeys[$i] . ":" . $propValues[$i] . "; ";
      }
      
      return $propList;
    }
  
  
    // Returns attributes formatted in attribute='value' form
    private function is_void() {
      $voidElements = array( 
        "area", "base", "br", "col", "embed", 
        "hr", "img", "input", "keygen", "link", 
        "meta", "param", "source", "track", "wbr"
      );
      
      return in_array($this->tag, $voidElements);
    }
}




?>