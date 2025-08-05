<table>
	<thead>	
      <tr>
            <th>&nbsp;</th>
            <th>T&oacute;picos</th>
            <th>Status</th>
      </tr>
    </thead>
    <tbody>
              <tr>
                <td>a</td>
                <td><a href="<?php   echo $root;?>role/executive/Executive.php?comm=generalInformation&idInform=<?php   echo $idInform;?>&idNotification=<?php   echo $idNotification;?>">Informa&ccedil;&otilde;es Gerais</a></td>
                <td><?php  $dataState = odbc_result($cur,1); require_once("status.php"); ?></td>
              </tr>
              <tr>
                <td>b</td>
                <td><a href="<?php   echo $root;?>role/executive/Executive.php?comm=volVendExt&idInform=<?php   echo $idInform;?>&idNotification=<?php   echo $idNotification;?>">Distribuição de Vendas por Tipo de Pagamento</a></td>
                <td><?php  $dataState = odbc_result($cur,2); require_once("status.php"); ?></td>
              </tr>
              <tr>
                <td>c</td>
                <td><a href="<?php   echo $root;?>role/executive/Executive.php?comm=segVendExt&idInform=<?php   echo $idInform;?>&idNotification=<?php   echo $idNotification;?>">Segmenta&ccedil;&atilde;o de Previs&atilde;o Vendas Externas</a></td>
                <td><?php  $dataState = odbc_result($cur,3); require_once("status.php"); ?></td>
              </tr>
              <tr>
                <td>d</td>
                <td><a href="<?php   echo $root;?>role/executive/Executive.php?comm=buyers&idInform=<?php   echo $idInform;?>&idNotification=<?php   echo $idNotification;?>"><?php   echo $idAnt ? '' : 'Principais' ?> Compradores</a></td>
                <td><?php  $dataState = odbc_result($cur,5); require_once("status.php"); ?></td>
              </tr>
              <tr>
                <td>e</td>
                <td><a href="<?php   echo $root;?>role/executive/Executive.php?comm=lost&idInform=<?php   echo $idInform;?>&idNotification=<?php   echo $idNotification;?>">Hist&oacute;rico de Perdas</a></td>
                <td><?php  $dataState = odbc_result($cur,6); require_once("status.php"); ?></td>
              </tr>
              <?php  if ($ok) { ?>
              <tr>
                <td>f</td>
                <td><a href="<?php   echo $root;?>/role/executive/Executive.php?comm=simul&idInform=<?php   echo $idInform;?>&idNotification=<?php   echo $idNotification;?>">Simula&ccedil;&atilde;o de Pr&ecirc;mio</a></td>
                <td>OK</td>
              </tr>
              <?php  }  ?>
     </tbody>
</table>
