
<?php //MODIFICADO
  // Cálculo do prêmio - Tarifação rápida
  
  $warantyInterest = 0;
  $idSector = 0;
  $cur = odbc_exec(
    $db,
    "SELECT warantyInterest, idSector FROM Inform WHERE id = $idInform"
  );

  if (!odbc_fetch_row($cur)) $msg = "Problemas na obtenção dos dados do informe";
  else {

    $warantyInterest = odbc_result($cur,1);
    $idSector = odbc_result($cur,2);
	      
    $curSector = odbc_exec(
      $db,
      "SELECT nivel1, nivel2, nivel3, nivel4, nivel5, nivel6, nivel7 FROM Sector WHERE id = $idSector"
    );

    if (!odbc_fetch_row($curSector)) $msg = "Problemas na obtenção dos dados do setor";
    else {
      $nivel[] = odbc_result($curSector ,1);
      $nivel[] = odbc_result($curSector ,2);
      $nivel[] = odbc_result($curSector ,3);
      $nivel[] = odbc_result($curSector ,4);
      $nivel[] = odbc_result($curSector ,5);
      $nivel[] = odbc_result($curSector ,6);
      $nivel[] = odbc_result($curSector ,7);

      $cur = odbc_exec(
        $db,
        "SELECT valExp, score FROM VolumeSeg v Join Country c ON (idCountry = c.id) WHERE idInform = $idInform AND (c.score < 8)"  
      );
  
      $i      = 0;
      $premio = 0;
      $T1     = 0;
      $msg = "";
      $TOTASSEG = 0;
      while (odbc_fetch_row($cur)) {
        $i ++;
        if ((odbc_result($cur,2) != 8)) {             
          $TMP = odbc_result($cur,1) * $nivel[odbc_result($cur,2) - 1] / 1000;
          $T1 += $TMP;
          $TOTASSEG += odbc_result($cur,1);
        }
      }
      if ($i == 0) $msg = "Não foi feita a segmentação das exportações ou os países são de alto risco";
      else {
        $cur = odbc_exec(
          $db,
          "SELECT vol42, vol43, vol44, vol24, vol32, vol40 FROM Volume WHERE idInform = $idInform"
        );
//        $ABC = 0;
        $C58 = 0;
        $C66 = 0;
        $C74 = 0;
        if (odbc_fetch_row($cur)) {
//          $ABC = odbc_result($cur,1) + odbc_result($cur,2) + odbc_result($cur,3);
          $C58 = odbc_result($cur,4);
          $C66 = odbc_result($cur,5);
          $C74 = odbc_result($cur,6);
        }
//        if ($ABC == 0){ $msg = "Não foram preenchidos os campos A, B, C do Resumo do Volume de Vendas";
//        } else {
          $T1 = $T1 / $TOTASSEG;
        //  $T1 = $T1 / $ABC;
          $T2 = 0;
    
          // Calcular a taxa histórica
          $cur = odbc_exec(
            $db,
            "SELECT val1, val2, val3 FROM Lost WHERE idInform = $idInform"
          );
          if (!odbc_fetch_row($cur)){ $msg = "Problemas na obtenção das perdas registradas";
          } else {
            $C94 = odbc_result($cur, 1);
            $C95 = odbc_result($cur, 2);
            $C96 = odbc_result($cur, 3);
            $S1 = $C94 + $C95 + $C96;

            // $S2 = ($C94 != 0 ? $C58 : 0) + ($C95 != 0 ? $C66 : 0) + ($C96 != 0 ? $C74 : 0);

            $S2 = $C58 + $C66 + $C74;
            if ($S2 > 0)
              $T2 = ($S1 / $S2) *.7;
          }
          $T3 = ($T1 > $T2 ? $T1 : $T2);
    
          // Cálculo da taxa de risco final ajustada
          $T4 = (($C58 == 0 || $C66 == 0 || $C74 == 0) ? ($T3 * 1.2) : $T3);
         
          // Cálculo do custo fixo
          $CF = 0;
    
          // SE(H35/1000<1500, H35/1000*4.66+1000,
          // SE(H35/1000<5000,8000+(H35/1000-1500)*1.43,
          // SE(H35/1000<20000,13000+(H35/1000-5000)*0.33,
          // SE(H35/1000<50000,18000+(H35/1000-20000)*0.06,
          // SE(H35/1000>=50000,20000+(H35/1000-50000)*(5000/(H35/1000)),
    
          if ($TOTASSEG / 1000 < 1500)
            $CF = $TOTASSEG  / 1000  * 4.66 + 1000;
          else if ($TOTASSEG / 1000 < 5000)
            $CF = ($TOTASSEG  / 1000 - 1500) * 1.43 + 8000;
          else if ($TOTASSEG / 1000 < 20000)
            $CF = ($TOTASSEG  / 1000 - 5000) * 0.33 + 13000;
          else if ($TOTASSEG / 1000 < 50000)
            $CF = ($TOTASSEG  / 1000 - 20000) * 0.06 + 18000;
          else
            $CF = ($TOTASSEG  / 1000 - 50000) * (5000000 / $TOTASSEG) + 20000;

          if ($CF == 0) $msg = "Problemas no cálculo do custo fixo";
          else {
            // $T5 = (($T4 + ($CF / $TOTASSEG)) / (1 - .06 - .25));
            $T5 = (($T4 + ($CF / $TOTASSEG)) / (1 - .06 - .25));
	    // if ($warantyInterest)
            // $T5 = $T5 * 1.04;// * (($TOTASSEG < 1000000) ? 1.4 : 1.2); // acima de 1000000 gordura de 20%; abaixo gordura de 40%
    
            // Cálculo de Prêmio
//	      echo "
//		  <pre>
//		  t1  [$T1]
//		  t2  [$T2]
//		  t3  [$T3]
//		  t4  [$T4]
//		  CF  [$CF]
//		  TOTALSEG [$TOTASSEG]
//		  t5  [$T5]
//	      ";

            $PM = $T5 * $TOTASSEG * .8 * ($warantyInterest ? 1.04 : 1);
          }      
        //}
      } 
    }
  }
?>



<?php /**original
  // Cálculo do prêmio - Tarifação rápida
  
  $warantyInterest = 0;
  $idSector = 0;
  $cur = odbc_exec(
    $db,
    "SELECT warantyInterest, idSector FROM Inform WHERE id = $idInform"
  );

  if (!odbc_fetch_row($cur)) $msg = "Problemas na obtenção dos dados do informe";
  else {

    $warantyInterest = odbc_result($cur,1);
    $idSector = odbc_result($cur,2);

    $curSector = odbc_exec(
      $db,
      "SELECT nivel1, nivel2, nivel3, nivel4, nivel5, nivel6, nivel7 FROM Sector WHERE id = $idSector"
    );

    if (!odbc_fetch_row($curSector)) $msg = "Problemas na obtenção dos dados do setor";
    else {
      $nivel[] = odbc_result($curSector ,1);
      $nivel[] = odbc_result($curSector ,2);
      $nivel[] = odbc_result($curSector ,3);
      $nivel[] = odbc_result($curSector ,4);
      $nivel[] = odbc_result($curSector ,5);
      $nivel[] = odbc_result($curSector ,6);
      $nivel[] = odbc_result($curSector ,7);

      $cur = odbc_exec(
        $db,
        "SELECT valExp, score FROM VolumeSeg v Join Country c ON (idCountry = c.id) WHERE idInform = $idInform AND score < 7"
      );
  
      $i      = 0;
      $premio = 0;
      $T1     = 0;
      $msg = "";
      $TOTASSEG = 0;
      while (odbc_fetch_row($cur)) {
        $i ++;
        if (odbc_result($cur,2) != 8) {
          $TMP = odbc_result($cur,1) *  $nivel[odbc_result($cur,2) - 1] / 1000;
          $T1 += $TMP;
          $TOTASSEG += odbc_result($cur,1);
        }
      }
      if ($i == 0) $msg = "Não foi feita a segmentação das exportações ou os países são de alto risco";
      else {
        $cur = odbc_exec(
          $db,
          "SELECT vol2, vol3, vol4, vol24, vol32, vol40 FROM Volume WHERE idInform = $idInform"
        );
        $ABC = 0;
        $C58 = 0;
        $C66 = 0;
        $C74 = 0;
        if (odbc_fetch_row($cur)) {
          $ABC = odbc_result($cur,1) + odbc_result($cur,2) + odbc_result($cur,3);
          $C58 = odbc_result($cur,4);
          $C66 = odbc_result($cur,5);
          $C74 = odbc_result($cur,6);
        }
        if ($ABC == 0) $msg = "Não foram preenchidos os campos A, B, C do Resumo do Volume de Vendas";
        else {
          $T1 = $T1 / $ABC;
          $T2 = 0;
    
          // Calcular a taxa histórica
          $cur = odbc_exec(
            $db,
            "SELECT val1, val2, val3 FROM Lost WHERE idInform = $idInform"
          );
          if (!odbc_fetch_row($cur)) $msg = "Problemas na obtenção das perdas registradas";
          else {
            $C94 = odbc_result($cur, 1);
            $C95 = odbc_result($cur, 2);
            $C96 = odbc_result($cur, 3);
            $S1 = $C94 + $C95 + $C96;

            // $S2 = ($C94 != 0 ? $C58 : 0) + ($C95 != 0 ? $C66 : 0) + ($C96 != 0 ? $C74 : 0);

            $S2 = $C58 + $C66 + $C74;
            if ($S2 > 0)
              $T2 = ($S1 / $S2) *.7;
          }
          $T3 = ($T1 > $T2 ? $T1 : $T2);
    
          // Cálculo da taxa de risco final ajustada
          $T4 = (($C58 == 0 || $C66 == 0 || $C74 == 0) ? ($T3 * 1.2) : $T3);
         
          // Cálculo do custo fixo
          $CF = 0;
    
          // SE(H35/1000<1500, H35/1000*4.66+1000,
          // SE(H35/1000<5000,8000+(H35/1000-1500)*1.43,
          // SE(H35/1000<20000,13000+(H35/1000-5000)*0.33,
          // SE(H35/1000<50000,18000+(H35/1000-20000)*0.06,
          // SE(H35/1000>=50000,20000+(H35/1000-50000)*(5000/(H35/1000)),
    
          if ($TOTASSEG / 1000 < 1500)
            $CF = $TOTASSEG  / 1000  * 4.66 + 1000;
          else if ($TOTASSEG / 1000 < 5000)
            $CF = ($TOTASSEG  / 1000 - 1500) * 1.43 + 8000;
          else if ($TOTASSEG / 1000 < 20000)
            $CF = ($TOTASSEG  / 1000 - 5000) * 0.33 + 13000;
          else if ($TOTASSEG / 1000 < 50000)
            $CF = ($TOTASSEG  / 1000 - 20000) * 0.06 + 18000;
          else
            $CF = ($TOTASSEG  / 1000 - 50000) * (5000000 / $TOTASSEG) + 20000;

          if ($CF == 0) $msg = "Problemas no cálculo do custo fixo";
          else {
            // $T5 = (($T4 + ($CF / $TOTASSEG)) / (1 - .06 - .25));
            $T5 = (($T4 + ($CF / $TOTASSEG)) / (1 - .06 - .25));
            if ($warantyInterest=1)
              $T5 = $T5 * 1.04; // * (($TOTASSEG < 1000000) ? 1.4 : 1.2); // acima de 1000000 gordura de 20%; abaixo gordura de 40%
    
            // Cálculo de Prêmio
//	      echo "
//		  <pre>
//		  t1  [$T1]
//		  t2  [$T2]
//		  t3  [$T3]
//		  t4  [$T4]
//		  CF  [$CF]
//		  TOTALSEG [$TOTASSEG]
//		  t5  [$T5]
//	      ";

            $PM = $T5 * $TOTASSEG * .8;
          }      
        }
      }  
    }
  }
*/
?>
