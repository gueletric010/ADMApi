<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclui a conexão existente
require_once 'conexao.php';

// Crucial para que o 'ç' de lançamento e acentos dos produtos funcionem
$con->set_charset("utf8");

// SQL com JOIN para trazer detalhes do produto
$sql = "SELECT 
            l.idLançamento, 
            p.nmProduto, 
            p.nmUnidadeMedida,
            p.nrNCM, 
            l.dtEmissao, 
            l.vlNota, 
            l.nrAliquotaICMS, 
            l.nrAliquotaIPI 
        FROM `lançamento` l
        JOIN produto p ON l.idProduto = p.idProduto";

$result = $con->query($sql);

$response = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $response[] = $row;
    }
} else {
    // Retorno padrão caso a consulta venha vazia
    $response[] = [
        "idLançamento"    => 0,
        "nmProduto"       => "Nenhum registro encontrado",
        "nmUnidadeMedida" => "",
        "nrNCM"           => "",
        "dtEmissao"       => "",
        "vlNota"          => "0.00",
        "nrAliquotaICMS"  => "0.00",
        "nrAliquotaIPI"   => "0.00"
    ];
}

// Configuração do cabeçalho JSON
header('Content-Type: application/json; charset=utf-8');

// JSON_UNESCAPED_UNICODE preserva acentos e o 'ç' nas chaves e valores
echo json_encode($response, JSON_UNESCAPED_UNICODE);

$con->close();
?>