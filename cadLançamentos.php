<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define o cabeçalho para JSON
header('Content-Type: application/json; charset=utf-8');

// Inclui a conexão (certifique-se de que o arquivo existe e define $con)
require_once 'conexao.php';
$con->set_charset("utf8");

// Obtém o input JSON
$jsonParam = json_decode(file_get_contents('php://input'), true);

if (!$jsonParam) {
    echo json_encode(['success' => false, 'message' => 'Dados JSON inválidos ou ausentes.']);
    exit;
}

// Extração e Sanitização dos dados conforme a tabela `lançamento`
$idProduto      = intval($jsonParam['idProduto'] ?? 0);
$dtEmissao      = !empty($jsonParam['dtEmissao']) ? date('Y-m-d', strtotime($jsonParam['dtEmissao'])) : null;
$vlNota         = floatval($jsonParam['vlNota'] ?? 0);
$nrAliquotaICMS = floatval($jsonParam['nrAliquotaICMS'] ?? 0);
$nrAliquotaIPI  = floatval($jsonParam['nrAliquotaIPI'] ?? 0);

// Validação básica de campos obrigatórios
if ($idProduto <= 0 || !$dtEmissao) {
    echo json_encode(['success' => false, 'message' => 'Produto e Data de Emissão são obrigatórios.']);
    exit;
}

// Preparar a SQL (Note o uso de crases em `lançamento` devido ao caractere especial)
$sql = "INSERT INTO `lançamento` (idProduto, dtEmissao, vlNota, nrAliquotaICMS, nrAliquotaIPI) 
        VALUES (?, ?, ?, ?, ?)";

$stmt = $con->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erro na preparação: ' . $con->error]);
    exit;
}

/**
 * Bind_param tipos: 
 * i = integer (idProduto)
 * s = string (dtEmissao)
 * d = double/decimal (vlNota, nrAliquotaICMS, nrAliquotaIPI)
 */
$stmt->bind_param("isddd", $idProduto, $dtEmissao, $vlNota, $nrAliquotaICMS, $nrAliquotaIPI);

// Executa e retorna o JSON
if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Lançamento realizado com sucesso!',
        'idGerado' => $stmt->insert_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar: ' . $stmt->error]);
}

$stmt->close();
$con->close();

?>