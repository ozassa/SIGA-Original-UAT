<?php require_once ("../rolePrefix.php");
$title = "Notificações";

if ($comm == "InformReview"){
  $content = "../notification/TEMPinterf/InformReview.php";

} else if ($comm == "DeniedInformReview"){
  $content = "../notification/TEMPinterf/DeniedInformReview.php"; 

} else if ($comm == "HighTaxInformReview"){
  $content = "../notification/TEMPinterf/HighTaxInformReview.php"; 

} else if ($comm == "ChangeReview"){
  $content = "../notification/TEMPinterf/ChangeReview.php"; 

} else if ($comm == "EndAnalysis"){
  $content = "../notification/TEMPinterf/EndAnalysis.php"; 

} else if ($comm == "TariffStart"){
  $content = "../notification/TEMPinterf/TariffStart.php"; 

} else if ($comm == "IssueOffer"){
  $content = "../notification/TEMPinterf/IssueOffer.php"; 

} else if ($comm == "LockPassword"){
  $content = "../notification/TEMPinterf/LockPassword.php"; 

} else if ($comm == "OfferAssigned"){
  $content = "../notification/TEMPinterf/OfferAssigned.php"; 
}
 
require_once ("../functionary/interf/main.php");
 
?>
