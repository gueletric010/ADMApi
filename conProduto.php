<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclui a conexão existente
require_once 'conexao.php';
$con->set_charset("utf8");

// SQL para selecionar todos os produtos baseados na sua nova tabela
$sql = "SELECT idProduto, nmProduto, nmUnidadeMedida, nrNCM FROM produto";

$result = $con->query($sql);

$response = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $response[] = $row;
    }
} else {
    // Retorno padrão caso a tabela esteja vazia
    $response[] = [
        "idProduto" => 0,
        "nmProduto" => "",
        "nmUnidadeMedida" => "",
        "nrNCM" => 0
    ];
}

// Define o cabeçalho para JSON
header('Content-Type: application/json; charset=utf-8');

// Exibe o JSON formatado
echo json_encode($response, JSON_UNESCAPED_UNICODE);

$con->close();
?>