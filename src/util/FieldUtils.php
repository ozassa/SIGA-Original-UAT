<?php

class FieldUtils {
  var $post;  // post method
  var $get;   // get method
  var $cur;
   
    // Add $num articles of $artnr to the cart
 
 
 
	
  function __construct ($p, $g, $c) {
		$this->post = $p;
		$this->get  = $g;
		$this->cur  = $c;
     
  }

 
 
  function setDB ($c) {
    $this->cur = $c;
  }

//  function getDBField ($name, $index) {
//    if ($this->post[$name] != "") {
//      return $this->post[$name];
//    } else if ($this->get[$name] != "") {
//      return $this->get[$name];
//    } else {
//      return odbc_result ($this->cur, $index);
//    }
//  }

  function getDBField ($name, $index) {
    $v = $this->getField($name);
    if ($v != "") {
      return $v;
    } else {
      return odbc_result ($this->cur, $index);
    }
  }

  function getDBNumField ($name, $index) {
    $v = $this->getNumField ($name);
    if ($v != "0") {
      return $v;
    } else {
      return odbc_result ($this->cur, $index);
    }
  }
   
  function getField ($name) {
    if(!isset($this->post[$name]) AND !isset($this->get[$name])){
      return "";
    }
    if (isset($this->post[$name])) {
      return $this->post[$name];
    } else if ($this->get[$name] != "") {
      return $this->get[$name];
    } else {
      return "";
    }
  }

  function getNumField ($name) {
    $ret = "0";
    if (isset($this->post[$name])) {
      $ret = $this->post[$name];
    } else if (isset($this->get[$name])) {
      $ret = $this->get[$name];
    }
    $v = "";
    $len = strlen($ret);
    for ($i = 0; $i < $len; $i++) {
      $v .= (($ret[$i] == ",") ? "." : ((is_numeric($ret[$i])) ? $ret[$i] : ""));
    }
    return $v;
  }
}
?>
