<?php
/*
  HTML Element Generator Class
  Note: Pass attributes as an array, pass inline style as an array, also.
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
  
    
    public function get_innerHTML() {
      return $this->innerHTML;
    }

  
    // Returns HTML tag with attributes and innerHTML as string
    public function get_element(){
      //Open tag
      $element = "<" .  $this->tag . " ";
      //Add attributes
      $element .= $this->format_string( $this->atts, "=", "'", "'" ) . ">";
      //Add innerHTML and close tag if not a void element
      if ( !$this->is_void() ) {
        $element .= $this->innerHTML;
        $element .= "</" .  $this->tag . ">";
      }
        
      return $element;
    }
  
       
    // Returns attributes formatted in attribute='value' form;  
    private function format_string($values, $divider, $start, $end) {
      $str = "";
      $valKeys = array_keys( $values );
      $valValues = array_values( $values );
      
      for ( $i = 0; $i < count( $values ); $i++ ) {
        // 'style' value is formatted as inline CSS
        if( $valKeys[$i] === 'style' ) {
          $str .= $valKeys[$i] . $divider . $start . $this->format_string( $valValues[$i], ":", "", ";"  ) . $end;
        } else {
          $str .= $valKeys[$i] . $divider . $start . $valValues[$i] . $end;
        }
      }
      
      return $str;
    }
  
  
    // Returns true/false if the tag is a void HTML element
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