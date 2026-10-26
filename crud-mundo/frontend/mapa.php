<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CRUD Mundo - Localização da ETEC</title>
  <link rel="stylesheet" href="css/style.css" />
  <style>
    /* Estilo para centralizar e ajustar o mapa no cartão */
    .mapa-container {
      width: 100%;
      display: flex;
      justify-content: center;
      margin-top: 1rem;
    }
    .mapa-container iframe {
      width: 100%;
      max-width: 100%;
      height: 450px;
      border-radius: var(--raio);
      border: 1px solid var(--cor-borda);
    }
    .info-etec {
      margin-bottom: 1rem;
      line-height: 1.6;
      color: var(--cor-texto);
    }
    .info-etec p {
      margin: 0.3rem 0;
    }
  </style>
</head>
<body>
  <header class="topo">
    <h1>CRUD Mundo</h1>
    <nav class="menu">
      <a href="index.php">Início</a>
      <a href="continentes.php">Continentes</a>
      <a href="paises.php">Países</a>
      <a href="cidades.php">Cidades</a>
      <a href="governantes.php">Governantes</a>
      <a href="mapa.php" class="ativo">Mapa</a>
    </nav>
  </header>

  <main>
    <section class="cartao">
      <h2>Localização da Sede</h2>
      
      <div class="info-etec">
        <p><strong>Instituição:</strong> Etec Profª Ilza Nascimento Pintus (Etec de São José dos Campos)</p>
        <p><strong>Endereço:</strong> Av. Salmão, 570 - Parque Residencial Aquarius, São José dos Campos - SP</p>
      </div>

      <!-- Mapa incorporado e centralizado -->
      <div class="mapa-container">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3666.6365614644765!2d-45.90677050000001!3d-23.219911999999997!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94cc357f12807135%3A0xa7e8aa77e5f28aac!2sEscola%20T%C3%A9cnica%20Estadual%20Professora%20Ilza%20Nascimento%20Pintus%20-%20S%C3%A3o%20Jos%C3%A9%20dos%20Campos!5e0!3m2!1spt-BR!2sbr!4v1793035928942!5m2!1spt-BR!2sbr" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
      </div>
    </section>
  </main>

  <footer class="rodape">
    Projeto CRUD Mundo - Programação Web | Etec / Centro Paula Souza
  </footer>
</body>
</html>