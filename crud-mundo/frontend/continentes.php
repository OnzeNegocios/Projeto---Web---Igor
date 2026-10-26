<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CRUD Mundo - Continentes</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
  <header class="topo">
    <h1>CRUD Mundo</h1>
    <nav class="menu">
      <a href="index.php">Início</a>
      <a href="continentes.php" class="ativo">Continentes</a>
      <a href="paises.php">Países</a>
      <a href="cidades.php">Cidades</a>
      <a href="governantes.php">Governantes</a>
    </nav>
  </header>

  <main>
    <section class="cartao">
      <h2 id="titulo-formulario">Cadastrar Continente</h2>
      <div id="alerta-formulario"></div>

      <form id="form-continente" class="formulario" novalidate>
        <input type="hidden" id="id_continente" name="id_continente" />

        <div class="campo largo">
          <label for="nome">Nome <span class="obrigatorio">*</span></label>
          <input type="text" id="nome" name="nome" required maxlength="100" />
          <span class="erro-campo" data-erro-para="nome"></span>
        </div>

        <div class="campo">
          <label for="populacao">População</label>
          <input type="number" id="populacao" name="populacao" min="0" />
          <span class="erro-campo" data-erro-para="populacao"></span>
        </div>

        <div class="campo">
          <label for="area">Área (km²)</label>
          <input type="number" id="area" name="area" min="0" step="0.01" />
          <span class="erro-campo" data-erro-para="area"></span>
        </div>

        <div class="acoes-formulario">
          <button type="submit" class="botao">Salvar</button>
          <button type="button" id="btn-cancelar" class="botao secundario" style="display:none;">Cancelar edição</button>
        </div>
      </form>
    </section>

    <section class="cartao">
      <h2>Continentes Cadastrados</h2>
      <div id="alerta-lista"></div>

      <div class="barra-acoes">
        <input type="text" id="campo-busca" class="busca" placeholder="Pesquisar continente pelo nome..." />
      </div>

      <table>
        <thead>
          <tr>
            <th>Nome</th>
            <th>População</th>
            <th>Área (km²)</th>
            <th>Total de Países</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody id="tabela-continentes">
          <tr><td colspan="5" class="vazio">Carregando...</td></tr>
        </tbody>
      </table>
    </section>
  </main>

  <footer class="rodape">
    Projeto CRUD Mundo - Programação Web | Etec / Centro Paula Souza
  </footer>

  <div class="modal-fundo" id="modal-confirmacao">
    <div class="modal">
      <h3>Confirmar exclusão</h3>
      <p id="modal-texto">Tem certeza que deseja excluir este registro?</p>
      <div class="acoes-modal">
        <button class="botao secundario" id="modal-cancelar">Cancelar</button>
        <button class="botao perigo" id="modal-confirmar">Excluir</button>
      </div>
    </div>
  </div>

  <script src="js/api.js"></script>
  <script src="js/validacao.js"></script>
  <script src="js/continentes.js"></script>
</body>
</html>
