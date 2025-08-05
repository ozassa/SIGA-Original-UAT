<?php
require_once("../../../navegacao.php");
?>
<div class="conteudopagina">
<form action="../policy/Policy.php" name="form" method="post">
<?php 
$idInform = $_REQUEST['idInform'];
$nomeEmp = $_REQUEST['nomeEmp'];
$idNotification = $_REQUEST['idNotification'];

$qry = "SELECT dateAceit FROM Inform WHERE id = ?";
$stmt = odbc_prepare($db, $qry);
odbc_execute($stmt, [$idInform]);

$dateAceit = odbc_result($stmt, 1);

list($anoA, $mesA, $diaA) = explode('-', $dateAceit);

$dAceit_ini = date("Y-m-d", mktime(0, 0, 0, $mesA, $diaA, date("Y")));
$dAceit_fim = date("Y-m-d", mktime(0, 0, 0, $mesA, $diaA + 45, date("Y")));

$dia = date('d');
$mes = date('m');
$dHoje = date("Y-m-d", mktime(0, 0, 0, $mes, $dia, date("Y")));

if ($dHoje > $dAceit_fim) {
    $comm = "emitec";
    $msgEmite = "J&aacute; se passaram mais de 45 dias da data de aceita&ccedil;&atilde;o. Deseja continuar com a emiss&atilde;o da Ap&oacute;lice de " . htmlspecialchars($nomeEmp) . "?";
} else {
    $comm = "emite";
    $msgEmite = "Para emitir a Ap&oacute;lice de " . htmlspecialchars($nomeEmp) . " agora, clique em 'Emitir Ap&oacute;lice'";
}
?>
<input type="hidden" name="comm" value="<?php echo htmlspecialchars($comm); ?>">
<input type="hidden" name="nomeEmp" value="<?php echo htmlspecialchars($nomeEmp); ?>">
<input type="hidden" name="nome" value="<?php echo htmlspecialchars($nomeEmp); ?>">
<input type="hidden" name="idInform" value="<?php echo htmlspecialchars($idInform); ?>">
<input type="hidden" name="idNotification" value="<?php echo htmlspecialchars($idNotification); ?>">

<div style="clear:both">&nbsp;</div>
<li id="clear" class="campo2colunas" style="width:600px;">
    <label><?php echo $msgEmite; ?></label>
</li>
<div style="clear:both">&nbsp;</div>

<?php 
$btv = "N&atilde;o";
$btok = "Sim";

if ($par == "password") {  
?>
    <li class="campo2colunas">
        <label>Usu&aacute;rio:</label>
        <input type="text" name="usuario" id="usuario">
    </li>  
    <div style="clear:both">&nbsp;</div>
    <li class="campo2colunas">
        <label>Senha:</label>
        <input type="password" name="password" id="password">
    </li>
<?php       
    $btv = "Voltar";
    $btok = "Ok";
}

if ($msg) {   
?>
    <div style="clear:both">&nbsp;</div>
    <li class="campo2colunas">
        <label style="color:#F00"><?php echo htmlspecialchars($msg); ?></label>
    </li>
    <div style="clear:both">&nbsp;</div>
<?php
}
?>
<li id="clear" class="campo2colunas" style="width:600px;">
    <?php if ($comm == "emite") { ?>
        <button class="botaoagm" type="button" onClick="this.form.comm.value='view';this.form.submit()">Voltar</button>
        <button class="botaoagg" type="button" onClick="form.submit();">Emitir Ap&oacute;lice</button>
    <?php } ?>
    <?php if ($comm == "emitec") { ?>
        <button class="botaoagm" type="button" onClick="this.form.comm.value='view';this.form.submit()"><?php echo $btv; ?></button>
        <input class="botaoagm" type="submit" value="<?php echo $btok; ?>">
    <?php } ?>
</li>
<div style="clear:both">&nbsp;</div>
</form>
</div>
