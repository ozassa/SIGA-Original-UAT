<html>
<head>
<STYLE type=text/css>TD {
	COLOR: #000066; FONT-FAMILY: Arial,Helvetica; FONT-SIZE: 10pt; FONT-WEIGHT: bold
}
BODY {
	COLOR: #000066; FONT-FAMILY: Arial,Helvetica; FONT-SIZE: 10pt; FONT-WEIGHT: bold
}
P {
	COLOR: #000066; FONT-FAMILY: Arial,Helvetica; FONT-SIZE: 10pt; FONT-WEIGHT: bold
}
A {
	COLOR: #000066; FONT-FAMILY: Arial,Helvetica; FONT-SIZE: 10pt; FONT-WEIGHT: bold
}
A:active {
	COLOR: #ff0000; FONT-FAMILY: Arial,Helvetica; FONT-SIZE: 10pt; FONT-WEIGHT: bold
}
</STYLE>
<title><?php echo $title; ?></title>
</head>
<body bgColor=#ffffcc leftMargin="0" topMargin="0" marginheight="0" marginwidth="0">
<table width="100%" cellspacing=0 cellpadding=5>
  <tr>
    <td width=100 bgColor="#000066">
      <a href="http://www.coface.com.br"><IMG src="../../../images/logo.gif" name="IMG1" width="227" height="112" border=0 id=IMG1 ></a>
    </td>
    <td align=center width=100%><h3><?php echo $title; ?></h3></td> 
  </tr>
  <tr>
    <td bgColor="#000066" valign=top><?php require_once ("menu.php"); ?></td>
    <td valign=top><?php require_once ($content); ?></td>
  </tr>
</table>
</body>
</html>
