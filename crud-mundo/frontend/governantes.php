<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CRUD Mundo - Governantes</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
  <header class="topo">
    <h1> CRUD Mundo</h1>
    <nav class="menu">
      <a href="index.php">Início</a>
      <a href="continentes.php">Continentes</a>
      <a href="paises.php">Países</a>
      <a href="cidades.php">Cidades</a>
      <a href="governantes.php" class="ativo">Governantes</a>
    </nav>
  </header>

  <main>
    <section class="cartao">
      <h2 id="titulo-formulario">Cadastrar Governante</h2>
      <div id="alerta-formulario"></div>

      <form id="form-governante" class="formulario" novalidate>
        <input type="hidden" id="id_governante" name="id_governante" />

        <div class="campo largo">
          <label for="nome">Nome <span class="obrigatorio">*</span></label>
          <input type="text" id="nome" name="nome" required maxlength="150" />
          <span class="erro-campo" data-erro-para="nome"></span>
        </div>

        <div class="campo">
          <label for="partido_politico">Partido Político</label>
          <input type="text" id="partido_politico" name="partido_politico" maxlength="150" />
          <span class="erro-campo" data-erro-para="partido_politico"></span>
        </div>

        <div class="campo">
          <label for="data_nascimento">Data de Nascimento</label>
          <input type="date" id="data_nascimento" name="data_nascimento" />
          <span class="erro-campo" data-erro-para="data_nascimento"></span>
        </div>

        <div class="campo">
          <label for="data_inicio_mandato">Início do Mandato</label>
          <input type="date" id="data_inicio_mandato" name="data_inicio_mandato" />
          <span class="erro-campo" data-erro-para="data_inicio_mandato"></span>
        </div>

        <div class="campo">
          <label for="data_fim_mandato">Fim do Mandato</label>
          <input type="date" id="data_fim_mandato" name="data_fim_mandato" />
          <span class="erro-campo" data-erro-para="data_fim_mandato"></span>
        </div>

        <div class="acoes-formulario">
          <button type="submit" class="botao">Salvar</button>
          <button type="button" id="btn-cancelar" class="botao secundario" style="display:none;">Cancelar edição</button>
        </div>
      </form>
    </section>

    <section class="cartao">
      <h2>Governantes Cadastrados</h2>
      <div id="alerta-lista"></div>

      <div class="barra-acoes">
        <input type="text" id="campo-busca" class="busca" placeholder="Pesquisar governante pelo nome..." />
      </div>

      <table>
        <thead>
          <tr>
            <th>Nome</th>
            <th>Partido</th>
            <th>Idade</th>
            <th>Início do Mandato</th>
            <th>Fim do Mandato</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody id="tabela-governantes">
          <tr><td colspan="6" class="vazio">Carregando...</td></tr>
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
  <script src="js/governantes.js"></script>
</body>
</html>
