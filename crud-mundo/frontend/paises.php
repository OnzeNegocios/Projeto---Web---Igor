<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CRUD Mundo - Países</title>
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
  <header class="topo">
    <h1> CRUD Mundo</h1>
    <nav class="menu">
      <a href="index.php">Início</a>
      <a href="continentes.php">Continentes</a>
      <a href="paises.php" class="ativo">Países</a>
      <a href="cidades.php">Cidades</a>
      <a href="governantes.php">Governantes</a>
      <a href="mapa.php">Mapa</a>/a>
    </nav>
  </header>

  <main>
    <section class="cartao">
      <h2 id="titulo-formulario">Cadastrar País</h2>
      <div id="alerta-formulario"></div>

      <form id="form-pais" class="formulario" novalidate>
        <input type="hidden" id="id_pais" name="id_pais" />

        <div class="campo largo">
          <label for="nome">Nome <span class="obrigatorio">*</span></label>
          <input type="text" id="nome" name="nome" required maxlength="150" />
          <span class="erro-campo" data-erro-para="nome"></span>
        </div>

        <div class="campo">
          <label for="id_continente">Continente <span class="obrigatorio">*</span></label>
          <select id="id_continente" name="id_continente" required></select>
          <span class="erro-campo" data-erro-para="id_continente"></span>
        </div>

        <div class="campo">
          <label for="id_governante">Governante</label>
          <select id="id_governante" name="id_governante">
            <option value="">Nenhum</option>
          </select>
        </div>

        <div class="campo">
          <label for="populacao">População</label>
          <input type="number" id="populacao" name="populacao" min="0" />
        </div>

        <div class="campo">
          <label for="area">Área (km²)</label>
          <input type="number" id="area" name="area" min="0" step="0.01" />
        </div>

        <div class="campo">
          <label for="idioma">Idioma</label>
          <input type="text" id="idioma" name="idioma" maxlength="100" />
        </div>

        <div class="campo">
          <label for="clima">Clima</label>
          <input type="text" id="clima" name="clima" maxlength="100" />
        </div>

        <div class="campo">
          <label for="regime_politico">Regime Político</label>
          <input type="text" id="regime_politico" name="regime_politico" maxlength="100" />
        </div>

        <div class="campo">
          <label for="moeda">Moeda</label>
          <input type="text" id="moeda" name="moeda" maxlength="100" />
        </div>

        <div class="acoes-formulario">
          <button type="submit" class="botao">Salvar</button>
          <button type="button" id="btn-cancelar" class="botao secundario" style="display:none;">Cancelar edição</button>
        </div>
      </form>
    </section>

    <section class="cartao">
      <h2>Países Cadastrados</h2>
      <div id="alerta-lista"></div>

      <div class="barra-acoes">
        <input type="text" id="campo-busca" class="busca" placeholder="Pesquisar país pelo nome..." />
        <select id="filtro-continente">
          <option value="">Todos os continentes</option>
        </select>
      </div>

      <table>
        <thead>
          <tr>
            <th>Nome</th>
            <th>Continente</th>
            <th>População</th>
            <th>Governante</th>
            <th>Moeda</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody id="tabela-paises">
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
  <script src="js/paises.js"></script>
</body>
</html>
