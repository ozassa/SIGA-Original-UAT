<?php
function arruma_cnpj($c) {
    if (strlen($c) == 14 && preg_match("/([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{2})/", $c, $v)) {
        return "$v[1].$v[2].$v[3]/$v[4]-$v[5]";
    }
    return null;
}

require_once("../../../navegacao.php");

$idEndosso = isset($_REQUEST['idEndosso']) ? (int) $_REQUEST['idEndosso'] : 0;
$idInform = isset($_REQUEST['idInform']) ? (int) $_REQUEST['idInform'] : 0;
$tipo = isset($_REQUEST['tipo']) ? (int) $_REQUEST['tipo'] : 0;

?>
<div class="conteudopagina">
    <a name=endosso></a>
  
<?php
// Consulta segura usando odbc_prepare e odbc_execute
$sql = "SELECT bornDate, state FROM Endosso WHERE id = ?";
$cur = odbc_prepare($db, $sql);
odbc_execute($cur, [$idEndosso]);

if ($cur && odbc_fetch_row($cur)) {
    if (odbc_result($cur, 2) >= 2) {
?>
        <li class="campo2colunas">
            <label>V&aacute;lido a partir de</label>
            <?php echo ymd2dmy(odbc_result($cur, 1)); ?>
        </li>
<?php
    }
}
?>
    <div style="clear:both">&nbsp;</div>
    <li class="campo2colunas">
        <label>
            <?php
            if ($tipo == 1) {
                echo "Endosso de Dados Cadastrais";
            } elseif ($tipo == 2) {
                echo "Endosso de Natureza de Operação";
            } elseif ($tipo == 4) {
                echo "Endosso de Prêmio Mínimo";
            }
            ?>
        <label>
    </li>

    <div style="clear:both">&nbsp;</div>
    <table>
       <thead>
          <tr>
            <th width="20%">&nbsp;</th>
            <th width="40%" align="left">Dados Antigos</th>
            <th width="40%" align="left">Dados Novos</th>
          </tr>
       </thead>
<?php
if ($tipo == 1) {
    $sql = "SELECT e.name, e.address, e.city, e.cep, r1.description, e.cnpj, e.nameOld,
                   e.addressOld, e.cityOld, e.cepOld, e.cnpjOld, r2.description, r1.id,
                   e.number, e.addresscomp, e.numberOld, e.addresscompOld 
            FROM EndossoDados e 
            JOIN Region r1 ON e.idRegion = r1.id 
            JOIN Region r2 ON e.idRegionOld = r2.id 
            WHERE e.idEndosso = ?";
    $c = odbc_prepare($db, $sql);
    odbc_execute($c, [$idEndosso]);

    if (odbc_fetch_row($c)) {
        $new_name = odbc_result($c, 1);
        $new_address = odbc_result($c, 2);
        $new_number = odbc_result($c, 14);
        $new_addresscomp = odbc_result($c, 15);
        $new_city = odbc_result($c, 3);
        $new_cep = odbc_result($c, 4);
        $new_region = odbc_result($c, 5);
        $new_cnpj = arruma_cnpj(odbc_result($c, 6));
        $name = odbc_result($c, 7);
        $address = odbc_result($c, 8);
        $number = odbc_result($c, 16);
        $addresscomp = odbc_result($c, 17);
        $city = odbc_result($c, 9);
        $cep = odbc_result($c, 10);
        $region = odbc_result($c, 12);
        $cnpj = arruma_cnpj(odbc_result($c, 11));
        $new_idRegion = odbc_result($c, 13);

        $dados = array(
            'name' => 'Razão Social',
            'cnpj' => 'CNPJ',
            'address' => 'Endereço',
            'number' => 'Numero',
            'addresscomp' => 'Complemento',
            'city' => 'Cidade',
            'cep' => 'CEP',
            'region' => 'Região'
        );

        foreach ($dados as $d => $label) {
            if (!empty(${"new_$d"})) {
?>
                <tr>
                    <td class="textoBold"><?php echo $label; ?></td>
                    <td class="texto"><?php echo ${$d}; ?></td>
                    <td class="texto"><?php echo ${"new_$d"}; ?></td>
                </tr>
<?php
            }
        }
    }
} elseif ($tipo == 2) {
    $sql = "SELECT s1.description, s2.description, e.natureza, e.naturezaOld 
            FROM EndossoNatureza e 
            JOIN Sector s1 ON (e.idSector = s1.id) 
            JOIN Sector s2 ON (e.idSectorOld = s2.id) 
            WHERE e.idEndosso = ?";
    $cur = odbc_prepare($db, $sql);
    odbc_execute($cur, [$idEndosso]);

    if ($cur && odbc_fetch_row($cur)) {
        $sector = odbc_result($cur, 1);
        $sectorOld = odbc_result($cur, 2);
        $natureza = odbc_result($cur, 3);
        $naturezaOld = odbc_result($cur, 4);
?>
        <tr>
            <td class="textoBold">Setor</td>
            <td class="texto"><?php echo $sectorOld; ?></td>
            <td class="texto"><?php echo $sector; ?></td>
        </tr>
        <tr>
            <td class="textoBold">Produtos</td>
            <td class="texto"><?php echo $naturezaOld; ?></td>
            <td class="texto"><?php echo $natureza; ?></td>
        </tr>
<?php
    }
} elseif ($tipo == 4) {
    $sql = "SELECT premio, premioOld, motivo, txMin, txMinOld FROM EndossoPremio WHERE idEndosso = ?";
    $cur = odbc_prepare($db, $sql);
    odbc_execute($cur, [$idEndosso]);

    if ($cur && odbc_fetch_row($cur)) {
        $premio = odbc_result($cur, 1);
        $premioOld = odbc_result($cur, 2);
        $motivo = odbc_result($cur, 3);
        $txMin = odbc_result($cur, 4);
        $txMinOld = odbc_result($cur, 5);
?>
        <tr>
            <td class="textoBold">Pr&eacute;mio</td>
            <td class="texto"><?php echo number_format($premioOld, 2, ',', '.'); ?></td>
            <td class="texto"><?php echo number_format($premio, 2, ',', '.'); ?></td>
        </tr>
        <tr>
            <td class="textoBold">Taxa</td>
            <td class="texto"><?php echo number_format($txMinOld, 4, ',', '.'); ?></td>
            <td class="texto"><?php echo number_format($txMin, 4, ',', '.'); ?></td>
        </tr>
        <tr>
            <td class="textoBold" valign="top">Motivo</td>
            <td class="texto" colspan="2"><?php echo $motivo; ?></td>
        </tr>
<?php
    }
}
?>
    </table>
    <div style="clear:both">&nbsp;</div> 
    <form action="<?php echo $root; ?>role/client/Client.php#endosso" method="get">
        <input type="hidden" name="idInform" value="<?php echo $idInform; ?>">
        <input type="hidden" name="comm">
        <div class="barrabotoes">
           <button class="botaoagm" onClick="this.form.comm.value='endosso';this.form.submit()">Voltar</button>
        </div>
    </form>
</div>
