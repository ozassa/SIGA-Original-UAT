<?php  $h = new Java ('java.util.HashMap');

//extract($_SESSION);

$h->put('dir', $pdfDir);
$h->put('key', $keyProp);
$h->put('msg1', $msg1);
$h->put('msg2', $msg2);
$h->put('msg3', $msg3);
$h->put('msg4', $msg4);
$h->put('msg5', $msg5);
$h->put('msg6', $msg6);
$h->put('msg7', $msg7);
$h->put('msg8', $msg8);
$h->put('msg9', $msg9);
$h->put('msg10', $msg10);
$h->put('msg11a', $msg11a);
$h->put('msg11b', $msg11b);
$h->put('msg11c', $msg11c);
$h->put('msg11e', $msg11e);
$h->put('msg12', $msg12);
$h->put('msg13', $msg13);
$h->put('msg14', $msg14);
$h->put('msg15', $msg15);
$h->put('msg16', $msg16);
$h->put('msg17', $msg17);
$h->put('msg18a', $msg18a);
$h->put('msg18b', $msg18b);
$h->put('msg19', $msg19);
$h->put('msg20', $msg20);
$h->put('totasseg', $totasseg);
$h->put('contract', $contract);
if($segundavia){
  $h->put('segundavia', '1');
}else{
  $h->put('segundavia', '0');
}

$prop = new Java ('JavaProp', $h);
//if ($prop == null) die("<h1>prop null</h1>");
$prop->generate();

?>
