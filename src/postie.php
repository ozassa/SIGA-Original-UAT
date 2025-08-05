<?php
function sendPostie($to, $from, $subject, $msg){
  $tmp = time(). '.txt';
  $sarq = "d:\\temp\\". $tmp;
  echo "<pre>arquivo:$sarq</pre>";
  $arq = fopen ($sarq,"wt");
  fwrite ($arq, $msg);
  fclose ($arq);
  $sperl = "perl c:\\projects\\sbce\\siex\\src\\postie.pl \"$to\" \"$from\" \"$subject\" \"$sarq\"";

/*   $sperl="d:\\postie\\postie -cgi:-post&-host:192.168.0.3&-s:". */
/*     urlencode($subject). */
/*     "&-to:$to&-file:$tmp -config"; */

  echo "<pre>$sperl</pre>";
  system($sperl);
}
?>
