<?php include_once("../../../navegacao.php");?>

<div class="conteudopagina">
	<p>Para iniciar o processo de assinatura digital, voc&ecirc; deve deve ter conectado ao seu computador um smartcard com um certificado digital v&aacute;lido (e-CPF ou e-CNPJ).</p>
	<style>
		.botaoagmpdf {
			border: 0;
	    font: bold 12px Arial,"Trebuchet MS",Verdana,Tahoma,Helvetica;
	    color: #ffffff;
	    text-transform: uppercase;
	    cursor: pointer;
	    width: 90px;
	    height: 31px;
	    background: url('../../../images/botao_a_gm.png');
	    text-decoration: none;
	    padding: 8px 12px;
		}
		.botaoagmpdf:hover {
			border: 0;
	    font: bold 12px Arial,"Trebuchet MS",Verdana,Tahoma,Helvetica;
	    color: #ffffff;
	    text-transform: uppercase;
	    cursor: pointer;
	    width: 90px;
	    height: 31px;
	    background: url('../../../images/botao_a_gm.png');
	    text-decoration: none;
	    padding: 8px 12px;
		}

		.botaoagmass {
			border: 0;
	    font: bold 12px Arial,"Trebuchet MS",Verdana,Tahoma,Helvetica;
	    color: #ffffff;
	    text-transform: uppercase;
	    cursor: pointer;
	    width: 90px;
	    height: 31px;
	    background: url('../../../images/botao_v_gg.png');
	    text-decoration: none;
	    padding: 8px 12px;
		}
		.botaoagmass:hover {
			border: 0;
	    font: bold 12px Arial,"Trebuchet MS",Verdana,Tahoma,Helvetica;
	    color: #ffffff;
	    text-transform: uppercase;
	    cursor: pointer;
	    width: 90px;
	    height: 31px;
	    background: url('../../../images/botao_v_gg.png');
	    text-decoration: none;
	        padding: 8px 12px;
		}

		#lista_arq li {
			display: inline-block;
			margin-right: 10px;
		}

		    
}
	</style>
	
	<script>
			function writeList(file1, p7s, file2){
		    var html = "<li><a class='botaoagmpdf' target='_blank' href='https://192.168.0.57/coface-siga/sign_cert/files/certs/" +file1+ "'>Original</a></li>";
		    html += "<li><a class='botaoagmpdf' target='_blank' href='https://192.168.0.57/coface-siga/sign_cert/files/certs/" +file2+ "'>Assinado</a></li>";
		    html += "<li><a class='botaoagmass' target='_blank' href='https://192.168.0.57/coface-siga/sign_cert/files/certs/" +p7s+ "'>Assinaturas</a></li>";
		    $("#lista_arq").html(html);
	    }

	</script>

		<object type="application/x-java-applet" name="previewersGraph" width="500" height="100">
      <param name="code" value="org.interaktiv.sign.DocSigner.class" />
      <param name="archive" value="signer.jar, bcprov-jdk15on-152.jar, bcmail-jdk15on-152.jar, commons-io-2.4.jar, bcpg-jdk15on-152.jar, bcpkix-jdk15on-152.jar, commons-logging-1.2.jar, commons-net-3.3.jar, pdfbox-1.8.9.jar, fontbox-1.8.9.jar, httpclient-4.5.jar, httpcore-4.4.1.jar, httpmime-4.5.jar" />
      <param name="scriptable" value="true" />
      <param name="mayscript" value="true" />
      <param name="paramFile" value="http://192.168.0.57/coface-siga/sign_cert/files/cond_esp.pdf"/>
      <param name="paramLogo" value="<?php echo "C:\\\\logo_mini_o.jpg"; ?>"/>
  	</object>

  	<!-- <param name="paramLogo" value="<?php //echo str_replace(array("view.php", "\\"), array("", "\\\\"), __FILE__)."logo_mini_o.jpg"; ?>"/> -->

		<h2>Arquivos</h2>
		<hr>
		<ul id="lista_arq">
		</ul>

</div>