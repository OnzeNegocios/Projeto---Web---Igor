<?php
require_once __DIR__ . '/../config/helpers.php';

$database = new Database();
$pdo = $database->getConnection();

$metodo = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

const SELECT_BASE = "
    SELECT p.*, c.nome AS nome_continente, g.nome AS nome_governante
    FROM paises p
    LEFT JOIN continentes c ON c.id_continente = p.id_continente
    LEFT JOIN governantes g ON g.id_governante = p.id_governante
";

switch ($metodo) {
    case 'GET':
        if ($id) {
            $stmt = $pdo->prepare(SELECT_BASE . ' WHERE p.id_pais = :id');
            $stmt->execute(['id' => $id]);
            $pais = $stmt->fetch();

            if (!$pais) {
                responder(false, null, 'País não encontrado.', 404);
            }
            responder(true, $pais);
        } else {
            $sql = SELECT_BASE;
            $params = [];
            $condicoes = [];

            if (!empty($_GET['continente'])) {
                $condicoes[] = 'p.id_continente = :continente';
                $params['continente'] = (int)$_GET['continente'];
            }
            if (!empty($_GET['busca'])) {
                $condicoes[] = 'p.nome LIKE :busca';
                $params['busca'] = '%' . $_GET['busca'] . '%';
            }
            if ($condicoes) {
                $sql .= ' WHERE ' . implode(' AND ', $condicoes);
            }
            $sql .= ' ORDER BY p.nome ASC';

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            responder(true, $stmt->fetchAll());
        }
        break;

    case 'POST':
        $dados = lerCorpoJson();
        $faltando = validarCampos($dados, ['nome', 'id_continente']);
        if ($faltando) {
            responder(false, null, 'Campos obrigatórios ausentes: ' . implode(', ', $faltando), 400);
        }

        try {
            $stmt = $pdo->prepare(
                'INSERT INTO paises (nome, id_continente, populacao, area, idioma, id_governante, clima, regime_politico, moeda)
                 VALUES (:nome, :continente, :populacao, :area, :idioma, :governante, :clima, :regime, :moeda)'
            );
            $stmt->execute([
                'nome'       => $dados['nome'],
                'continente' => $dados['id_continente'],
                'populacao'  => $dados['populacao'] ?? null,
                'area'       => $dados['area'] ?? null,
                'idioma'     => $dados['idioma'] ?? null,
                'governante' => $dados['id_governante'] ?? null,
                'clima'      => $dados['clima'] ?? null,
                'regime'     => $dados['regime_politico'] ?? null,
                'moeda'      => $dados['moeda'] ?? null,
            ]);
            responder(true, ['id_pais' => $pdo->lastInsertId()], 'País cadastrado com sucesso.', 201);
        } catch (PDOException $e) {
            responder(false, null, 'Erro ao cadastrar país: ' . $e->getMessage(), 500);
        }
        break;

    case 'PUT':
        if (!$id) {
            responder(false, null, 'Informe o ID do país para atualizar.', 400);
        }
        $dados = lerCorpoJson();
        $faltando = validarCampos($dados, ['nome', 'id_continente']);
        if ($faltando) {
            responder(false, null, 'Campos obrigatórios ausentes: ' . implode(', ', $faltando), 400);
        }

        try {
            $stmt = $pdo->prepare(
                'UPDATE paises SET nome = :nome, id_continente = :continente, populacao = :populacao, area = :area,
                 idioma = :idioma, id_governante = :governante, clima = :clima, regime_politico = :regime, moeda = :moeda
                 WHERE id_pais = :id'
            );
            $stmt->execute([
                'nome'       => $dados['nome'],
                'continente' => $dados['id_continente'],
                'populacao'  => $dados['populacao'] ?? null,
                'area'       => $dados['area'] ?? null,
                'idioma'     => $dados['idioma'] ?? null,
                'governante' => $dados['id_governante'] ?? null,
                'clima'      => $dados['clima'] ?? null,
                'regime'     => $dados['regime_politico'] ?? null,
                'moeda'      => $dados['moeda'] ?? null,
                'id'         => $id,
            ]);

            if ($stmt->rowCount() === 0) {
                responder(false, null, 'País não encontrado ou nenhum dado alterado.', 404);
            }
            responder(true, null, 'País atualizado com sucesso.');
        } catch (PDOException $e) {
            responder(false, null, 'Erro ao atualizar país: ' . $e->getMessage(), 500);
        }
        break;

    case 'DELETE':
        if (!$id) {
            responder(false, null, 'Informe o ID do país para excluir.', 400);
        }

        try {
            $stmt = $pdo->prepare('DELETE FROM paises WHERE id_pais = :id');
            $stmt->execute(['id' => $id]);

            if ($stmt->rowCount() === 0) {
                responder(false, null, 'País não encontrado.', 404);
            }
            responder(true, null, 'País excluído com sucesso.');
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                responder(false, null, 'Não é possível excluir: existem cidades vinculadas a este país.', 409);
            }
            responder(false, null, 'Erro ao excluir país: ' . $e->getMessage(), 500);
        }
        break;

    default:
        responder(false, null, 'Método não suportado.', 405);
}
