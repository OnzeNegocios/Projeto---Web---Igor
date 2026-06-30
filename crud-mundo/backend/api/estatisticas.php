<?php
require_once __DIR__ . '/../config/helpers.php';

$database = new Database();
$pdo = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder(false, null, 'Método não suportado.', 405);
}

$tipo = $_GET['tipo'] ?? '';

switch ($tipo) {
    case 'cidade_mais_populosa':
        $idPais = (int)($_GET['pais'] ?? 0);
        if (!$idPais) {
            responder(false, null, 'Informe o ID do país.', 400);
        }
        $stmt = $pdo->prepare(
            'SELECT nome, populacao FROM cidades WHERE id_pais = :pais ORDER BY populacao DESC LIMIT 1'
        );
        $stmt->execute(['pais' => $idPais]);
        $cidade = $stmt->fetch();
        responder(true, $cidade ?: null);
        break;

    case 'cidades_por_continente':
        $stmt = $pdo->query(
            'SELECT c.nome AS continente, COUNT(ci.id_cidade) AS total_cidades
             FROM continentes c
             LEFT JOIN paises p ON p.id_continente = c.id_continente
             LEFT JOIN cidades ci ON ci.id_pais = p.id_pais
             GROUP BY c.id_continente, c.nome
             ORDER BY c.nome ASC'
        );
        responder(true, $stmt->fetchAll());
        break;

    default:
        responder(false, null, 'Tipo de estatística inválido.', 400);
}
