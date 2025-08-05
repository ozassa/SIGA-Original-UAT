<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test Applet</title>
</head>
<body>
    <h1>Coface</h1>
    <object type="application/x-java-applet" name="previewersGraph" width="500" height="100">
      <param name="code" value="org.interaktiv.sign.DocSigner.class" />
      <param name="archive" value="signer.jar, bcmail-jdk15on-152.jar, bcpg-jdk15on-152.jar, bcpkix-jdk15on-152.jar, bcprov-jdk15on-152.jar, commons-logging-1.2.jar, commons-net-3.3.jar, pdfbox-1.8.9.jar, fontbox-1.8.9.jar, httpclient-4.5.jar, httpcore-4.4.1.jar, httpmime-4.5.jar" />
      <param name="scriptable" value="true" />
      <param name="mayscript" value="true" />
      <param name="paramFile" value="http://192.168.0.21/coface-siga/sign_cert/files/inter_doc.pdf"/>
  	</object>
    <!-- <a href="#" onclick="window.open('https://192.168.0.21/coface-siga/sign_cert/window_sign.html', '', 'width=500, height=500');">Assinar Documento</a> -->
</body>
</html>