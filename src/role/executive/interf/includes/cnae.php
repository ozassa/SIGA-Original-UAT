<li class="barrabotoes" style="list-style:none;*margin-left:-15px;">  
   <label><h2>Classifica&ccedil;&atilde;o Nacional de Atividades Econ&ocirc;micas</h2></label>
</li>
<div class="divisoria01"> &nbsp;</div>

<!-- Modal -->
<div class="modal-ext" style="display:none">
<div class="bg-black"></div>

<div class='modal-int'>
  <h1>Selecionar CNAE</h1>
  <div class="divisoriaamarelo"></div>

  <table class="tbl_opts_cnae">
	<tr class="tr_um">
	  <td width="10%" ><input type="radio" name="opt_cnae" value="1" checked></td>
	  <td >
		<p>Busque a classe por seus n&iacute;veis</p>
		<ul>
		  <li class="">
			<label>Se&ccedil;&atilde;o:</label>
			<select name="" id="list_secoes">
			  <option value="">Selecione</option>
			  <?php for ($i=0; $i < count($lista_secoes_cnae); $i++) { ?>
				<option value="<?php echo $lista_secoes_cnae[$i]["id"] ?>"><?php echo $lista_secoes_cnae[$i]["nome"] ?></option>
			  <?php } ?>
			</select>
		  </li>
		  <li class="">
			<label>Divis&atilde;o:</label>
			<select name="" id="list_divisoes">
			  <option value="">Selecione uma se&ccedil;&atilde;o</option>
			</select>
		  </li>
		  <li class="">
			<label>Grupo:</label>
			<select name="" id="list_grupos">
			  <option value="">Selecione uma divis&atilde;o</option>
			</select>
		  </li>
		  <li class="">
			<label>Classe:</label>
			<select name="" id="list_classes">
			  <option value="">Selecione um grupo</option>
			</select>
		  </li>
		</ul>
	  </td>
	</tr>
	<tr class="tr_dois">
	  <td width="10%" style="background-color:#f5f5f5;"><input type="radio" name="opt_cnae" value="2"></td>
	  <td style="background-color:#f5f5f5;">
		<ul>
		  <li>
			<p>Ou busque-a pelo nome</p>
		  </li>
		  <li>
			<input type="text" name="busca_cnae" id="txt_busca_cnae" disabled>
			<input type="hidden" id="id_class_ac">
		  </li>
		</ul>
	  </td>
	</tr>
  </table>

  <button style="margin: 10px 0" type="button" class="botaoagg" id="seleciona_cnae">OK</button>
</div>
</div>

<!-- Fim modal -->

<ul>
	<li class="campo3colunas">
	  <label>CNAE:</label>
		<p id="nome_classe_cnae"><?php echo trim($desc_cnae) != "" ? $desc_cnae : "Nenhum cadastrado"; ?></p>
		<input type="hidden" id="id_sel_classe_cnae" name="sel_classe_cnae" value="<?php echo $i_CNAE; ?>">
		<button type="button" class="botaoagg" id="abre_modal_cnae">Selecionar</button>
	</li>
</ul>