<?php

// Configuração
include_once("../../../config.php");

// Pasta onde o arquivo será salvo
$pasta = realpath($pdfDir); // Garante que é um caminho absoluto

// Tamanho máximo do arquivo (2MB)
$tamanhoMaximo = 1024 * 1024 * 2;

// Extensões permitidas
$extensoesPermitidas = ['pdf'];

// Verifica se houve erro no upload
$erros = [
    'Não houve erro',
    'O arquivo no upload é maior do que o limite do PHP.',
    'O arquivo ultrapassa o limite de tamanho especificado no HTML.',
    'O upload do arquivo foi feito parcialmente.',
    'Não foi feito o upload do arquivo.'
];

if ($_FILES['arquivo']['error'] != 0) {
    exit("Erro no upload: " . $erros[$_FILES['arquivo']['error']]);
}

// Obtém a extensão do arquivo
$extensao = strtolower(pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION));
if (!in_array($extensao, $extensoesPermitidas)) {
    exit("Erro: Apenas arquivos PDF são permitidos.");
}

// Obtém o nome original do arquivo sem caminho relativo
$nomeArquivo = basename($_FILES['arquivo']['name']);

// Caminho completo do arquivo
$caminhoCompleto = $pasta . DIRECTORY_SEPARATOR . $nomeArquivo;

// Verifica se o arquivo está dentro da pasta esperada (evita Path Traversal)
if (strpos(realpath($caminhoCompleto), $pasta) !== 0) {
    exit("Erro: Nome de arquivo inválido.");
}

// Exclui o documento original se existir
if (file_exists($caminhoCompleto)) {
    if (!unlink($caminhoCompleto)) {
        exit("Erro ao excluir o arquivo existente.");
    }
}

// Verifica o tamanho do arquivo
if ($_FILES['arquivo']['size'] > $tamanhoMaximo) {
    exit("Erro: O arquivo excede o tamanho permitido de 2MB.");
}

// Move o arquivo para o diretório seguro
if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $caminhoCompleto)) {
    echo "Upload efetuado com sucesso!";
} else {
    exit("Erro: Não foi possível mover o arquivo.");
}

?>
