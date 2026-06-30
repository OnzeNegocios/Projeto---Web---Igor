<?php
require_once __DIR__ . '/../config/helpers.php';

$database = new Database();
$pdo = $database->getConnection();

$metodo = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

function calcularIdade(?string $dataNascimento): ?int
{
    if (!$dataNascimento) return null;
    $nascimento = new DateTime($dataNascimento);
    $hoje = new DateTime();
    return $hoje->diff($nascimento)->y;
}

switch ($metodo) {
    case 'GET':
        if ($id) {
            $stmt = $pdo->prepare('SELECT * FROM governantes WHERE id_governante = :id');
            $stmt->execute(['id' => $id]);
            $governante = $stmt->fetch();

            if (!$governante) {
                responder(false, null, 'Governante não encontrado.', 404);
            }
            responder(true, $governante);
        } else {
            $stmt = $pdo->query('SELECT * FROM governantes ORDER BY nome ASC');
            responder(true, $stmt->fetchAll());
        }
        break;

    case 'POST':
        $dados = lerCorpoJson();
        $faltando = validarCampos($dados, ['nome']);
        if ($faltando) {
            responder(false, null, 'Campos obrigatórios ausentes: ' . implode(', ', $faltando), 400);
        }

        $idade = $dados['idade'] ?? calcularIdade($dados['data_nascimento'] ?? null);

        try {
            $stmt = $pdo->prepare(
                'INSERT INTO governantes (nome, partido_politico, data_nascimento, idade, data_inicio_mandato, data_fim_mandato)
                 VALUES (:nome, :partido, :nascimento, :idade, :inicio, :fim)'
            );
            $stmt->execute([
                'nome'       => $dados['nome'],
                'partido'    => $dados['partido_politico'] ?? null,
                'nascimento' => $dados['data_nascimento'] ?? null,
                'idade'      => $idade,
                'inicio'     => $dados['data_inicio_mandato'] ?? null,
                'fim'        => $dados['data_fim_mandato'] ?? null,
            ]);
            responder(true, ['id_governante' => $pdo->lastInsertId()], 'Governante cadastrado com sucesso.', 201);
        } catch (PDOException $e) {
            responder(false, null, 'Erro ao cadastrar governante: ' . $e->getMessage(), 500);
        }
        break;

    case 'PUT':
        if (!$id) {
            responder(false, null, 'Informe o ID do governante para atualizar.', 400);
        }
        $dados = lerCorpoJson();
        $faltando = validarCampos($dados, ['nome']);
        if ($faltando) {
            responder(false, null, 'Campos obrigatórios ausentes: ' . implode(', ', $faltando), 400);
        }

        $idade = $dados['idade'] ?? calcularIdade($dados['data_nascimento'] ?? null);

        try {
            $stmt = $pdo->prepare(
                'UPDATE governantes SET nome = :nome, partido_politico = :partido, data_nascimento = :nascimento,
                 idade = :idade, data_inicio_mandato = :inicio, data_fim_mandato = :fim
                 WHERE id_governante = :id'
            );
            $stmt->execute([
                'nome'       => $dados['nome'],
                'partido'    => $dados['partido_politico'] ?? null,
                'nascimento' => $dados['data_nascimento'] ?? null,
                'idade'      => $idade,
                'inicio'     => $dados['data_inicio_mandato'] ?? null,
                'fim'        => $dados['data_fim_mandato'] ?? null,
                'id'         => $id,
            ]);

            if ($stmt->rowCount() === 0) {
                responder(false, null, 'Governante não encontrado ou nenhum dado alterado.', 404);
            }
            responder(true, null, 'Governante atualizado com sucesso.');
        } catch (PDOException $e) {
            responder(false, null, 'Erro ao atualizar governante: ' . $e->getMessage(), 500);
        }
        break;

    case 'DELETE':
        if (!$id) {
            responder(false, null, 'Informe o ID do governante para excluir.', 400);
        }

        try {
            $stmt = $pdo->prepare('DELETE FROM governantes WHERE id_governante = :id');
            $stmt->execute(['id' => $id]);

            if ($stmt->rowCount() === 0) {
                responder(false, null, 'Governante não encontrado.', 404);
            }
            responder(true, null, 'Governante excluído com sucesso.');
        } catch (PDOException $e) {
            responder(false, null, 'Erro ao excluir governante: ' . $e->getMessage(), 500);
        }
        break;

    default:
        responder(false, null, 'Método não suportado.', 405);
}
