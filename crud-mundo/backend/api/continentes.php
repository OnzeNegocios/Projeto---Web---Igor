<?php

require_once __DIR__ . '/../config/helpers.php';

$database = new Database();
$pdo = $database->getConnection();

$metodo = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($metodo) {
    case 'GET':
        if ($id) {
            $stmt = $pdo->prepare('SELECT * FROM continentes WHERE id_continente = :id');
            $stmt->execute(['id' => $id]);
            $continente = $stmt->fetch();

            if (!$continente) {
                responder(false, null, 'Continente não encontrado.', 404);
            }
            responder(true, $continente);
        } else {
            $stmt = $pdo->query('SELECT * FROM continentes ORDER BY nome ASC');
            responder(true, $stmt->fetchAll());
        }
        break;

    case 'POST':
        $dados = lerCorpoJson();
        $faltando = validarCampos($dados, ['nome']);
        if ($faltando) {
            responder(false, null, 'Campos obrigatórios ausentes: ' . implode(', ', $faltando), 400);
        }

        try {
            $stmt = $pdo->prepare(
                'INSERT INTO continentes (nome, populacao, area) VALUES (:nome, :populacao, :area)'
            );
            $stmt->execute([
                'nome'      => $dados['nome'],
                'populacao' => $dados['populacao'] ?? null,
                'area'      => $dados['area'] ?? null,
            ]);
            responder(true, ['id_continente' => $pdo->lastInsertId()], 'Continente cadastrado com sucesso.', 201);
        } catch (PDOException $e) {
            responder(false, null, 'Erro ao cadastrar continente: ' . $e->getMessage(), 500);
        }
        break;

    case 'PUT':
        if (!$id) {
            responder(false, null, 'Informe o ID do continente para atualizar.', 400);
        }
        $dados = lerCorpoJson();
        $faltando = validarCampos($dados, ['nome']);
        if ($faltando) {
            responder(false, null, 'Campos obrigatórios ausentes: ' . implode(', ', $faltando), 400);
        }

        try {
            $stmt = $pdo->prepare(
                'UPDATE continentes SET nome = :nome, populacao = :populacao, area = :area WHERE id_continente = :id'
            );
            $stmt->execute([
                'nome'      => $dados['nome'],
                'populacao' => $dados['populacao'] ?? null,
                'area'      => $dados['area'] ?? null,
                'id'        => $id,
            ]);

            if ($stmt->rowCount() === 0) {
                responder(false, null, 'Continente não encontrado ou nenhum dado alterado.', 404);
            }
            responder(true, null, 'Continente atualizado com sucesso.');
        } catch (PDOException $e) {
            responder(false, null, 'Erro ao atualizar continente: ' . $e->getMessage(), 500);
        }
        break;

    case 'DELETE':
        if (!$id) {
            responder(false, null, 'Informe o ID do continente para excluir.', 400);
        }

        try {
            $stmt = $pdo->prepare('DELETE FROM continentes WHERE id_continente = :id');
            $stmt->execute(['id' => $id]);

            if ($stmt->rowCount() === 0) {
                responder(false, null, 'Continente não encontrado.', 404);
            }
            responder(true, null, 'Continente excluído com sucesso.');
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                responder(false, null, 'Não é possível excluir: existem países vinculados a este continente.', 409);
            }
            responder(false, null, 'Erro ao excluir continente: ' . $e->getMessage(), 500);
        }
        break;

    default:
        responder(false, null, 'Método não suportado.', 405);
}
