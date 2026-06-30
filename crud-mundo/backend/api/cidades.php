<?php

require_once __DIR__ . '/../config/helpers.php';

$database = new Database();
$pdo = $database->getConnection();

$metodo = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

const SELECT_BASE = "
    SELECT ci.*, p.nome AS nome_pais, g.nome AS nome_governante
    FROM cidades ci
    LEFT JOIN paises p ON p.id_pais = ci.id_pais
    LEFT JOIN governantes g ON g.id_governante = ci.id_governante
";

switch ($metodo) {
    case 'GET':
        if ($id) {
            $stmt = $pdo->prepare(SELECT_BASE . ' WHERE ci.id_cidade = :id');
            $stmt->execute(['id' => $id]);
            $cidade = $stmt->fetch();

            if (!$cidade) {
                responder(false, null, 'Cidade não encontrada.', 404);
            }
            responder(true, $cidade);
        } else {
            $sql = SELECT_BASE;
            $params = [];
            $condicoes = [];

            if (!empty($_GET['pais'])) {
                $condicoes[] = 'ci.id_pais = :pais';
                $params['pais'] = (int)$_GET['pais'];
            }
            if (!empty($_GET['busca'])) {
                $condicoes[] = 'ci.nome LIKE :busca';
                $params['busca'] = '%' . $_GET['busca'] . '%';
            }
            if ($condicoes) {
                $sql .= ' WHERE ' . implode(' AND ', $condicoes);
            }
            $sql .= ' ORDER BY ci.nome ASC';

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            responder(true, $stmt->fetchAll());
        }
        break;

    case 'POST':
        $dados = lerCorpoJson();
        $faltando = validarCampos($dados, ['nome', 'id_pais']);
        if ($faltando) {
            responder(false, null, 'Campos obrigatórios ausentes: ' . implode(', ', $faltando), 400);
        }

        try {
            $stmt = $pdo->prepare(
                'INSERT INTO cidades (nome, id_pais, populacao, area, clima, id_governante, data_fundacao)
                 VALUES (:nome, :pais, :populacao, :area, :clima, :governante, :fundacao)'
            );
            $stmt->execute([
                'nome'       => $dados['nome'],
                'pais'       => $dados['id_pais'],
                'populacao'  => $dados['populacao'] ?? null,
                'area'       => $dados['area'] ?? null,
                'clima'      => $dados['clima'] ?? null,
                'governante' => $dados['id_governante'] ?? null,
                'fundacao'   => $dados['data_fundacao'] ?? null,
            ]);
            responder(true, ['id_cidade' => $pdo->lastInsertId()], 'Cidade cadastrada com sucesso.', 201);
        } catch (PDOException $e) {
            responder(false, null, 'Erro ao cadastrar cidade: ' . $e->getMessage(), 500);
        }
        break;

    case 'PUT':
        if (!$id) {
            responder(false, null, 'Informe o ID da cidade para atualizar.', 400);
        }
        $dados = lerCorpoJson();
        $faltando = validarCampos($dados, ['nome', 'id_pais']);
        if ($faltando) {
            responder(false, null, 'Campos obrigatórios ausentes: ' . implode(', ', $faltando), 400);
        }

        try {
            $stmt = $pdo->prepare(
                'UPDATE cidades SET nome = :nome, id_pais = :pais, populacao = :populacao, area = :area,
                 clima = :clima, id_governante = :governante, data_fundacao = :fundacao
                 WHERE id_cidade = :id'
            );
            $stmt->execute([
                'nome'       => $dados['nome'],
                'pais'       => $dados['id_pais'],
                'populacao'  => $dados['populacao'] ?? null,
                'area'       => $dados['area'] ?? null,
                'clima'      => $dados['clima'] ?? null,
                'governante' => $dados['id_governante'] ?? null,
                'fundacao'   => $dados['data_fundacao'] ?? null,
                'id'         => $id,
            ]);

            if ($stmt->rowCount() === 0) {
                responder(false, null, 'Cidade não encontrada ou nenhum dado alterado.', 404);
            }
            responder(true, null, 'Cidade atualizada com sucesso.');
        } catch (PDOException $e) {
            responder(false, null, 'Erro ao atualizar cidade: ' . $e->getMessage(), 500);
        }
        break;

    case 'DELETE':
        if (!$id) {
            responder(false, null, 'Informe o ID da cidade para excluir.', 400);
        }

        try {
            $stmt = $pdo->prepare('DELETE FROM cidades WHERE id_cidade = :id');
            $stmt->execute(['id' => $id]);

            if ($stmt->rowCount() === 0) {
                responder(false, null, 'Cidade não encontrada.', 404);
            }
            responder(true, null, 'Cidade excluída com sucesso.');
        } catch (PDOException $e) {
            responder(false, null, 'Erro ao excluir cidade: ' . $e->getMessage(), 500);
        }
        break;

    default:
        responder(false, null, 'Método não suportado.', 405);
}
