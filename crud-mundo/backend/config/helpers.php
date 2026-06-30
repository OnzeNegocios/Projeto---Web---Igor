<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/database.php';

function responder(bool $sucesso, $dados = null, string $mensagem = '', int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode([
        'sucesso'  => $sucesso,
        'mensagem' => $mensagem,
        'dados'    => $dados,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}


function lerCorpoJson(): array
{
    $input = file_get_contents('php://input');
    $dados = json_decode($input, true);
    return is_array($dados) ? $dados : [];
}


function validarCampos(array $dados, array $obrigatorios): array
{
    $faltando = [];
    foreach ($obrigatorios as $campo) {
        if (!isset($dados[$campo]) || trim((string)$dados[$campo]) === '') {
            $faltando[] = $campo;
        }
    }
    return $faltando;
}
