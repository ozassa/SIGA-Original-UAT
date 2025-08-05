<?php

class UserView {
  var $roles;
  var $id;
  var $name;
  var $email;
  var $per;

  function _construct ($id, $name, $r, $email, $per) {
   
	$this->id = $id;
    $this->name = $name;
    $this->roles = $r;
    $this->email = $email;
    $this->per = $per;
	
	
  }

  function hasRole ($r) {
    if (!is_null($this->roles[$r]))
        return true;
    else 
        return false;
  }

  function dump () {
    echo "<br>id[$this->id]<br>name[$this->name]";
    echo "<ul>";
    foreach ($this->roles as $r) {
      echo "<li>$r</li>";
    }
    echo "</ul>";
  }
}
?>
