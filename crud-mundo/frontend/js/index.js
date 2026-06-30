/**
 * index.js - lógica da página inicial (estatísticas)
 */

document.addEventListener('DOMContentLoaded', async () => {
  await carregarCidadesPorContinente();
  await carregarSelectPaises();

  const selectPais = document.getElementById('select-pais-estatistica');
  selectPais.addEventListener('change', carregarCidadeMaisPopulosa);
});

async function carregarCidadesPorContinente() {
  const container = document.getElementById('estatistica-continentes');
  const resposta = await chamarApi('estatisticas.php?tipo=cidades_por_continente');

  if (!resposta.sucesso) {
    exibirAlerta('alerta-estatisticas', resposta.mensagem, 'erro');
    container.innerHTML = '<p class="vazio">Não foi possível carregar as estatísticas.</p>';
    return;
  }

  if (!resposta.dados.length) {
    container.innerHTML = '<p class="vazio">Nenhum continente cadastrado.</p>';
    return;
  }

  container.innerHTML = resposta.dados
    .map(
      (item) => `
      <div class="estatistica">
        <strong>${escaparHtml(item.continente)}</strong>
        ${formatarNumero(item.total_cidades)} cidade(s) cadastrada(s)
      </div>`
    )
    .join('');
}

async function carregarSelectPaises() {
  const select = document.getElementById('select-pais-estatistica');
  const resposta = await chamarApi('paises.php');

  if (!resposta.sucesso || !resposta.dados.length) {
    select.innerHTML = '<option value="">Nenhum país cadastrado</option>';
    return;
  }

  select.innerHTML =
    '<option value="">Selecione...</option>' +
    resposta.dados
      .map((pais) => `<option value="${pais.id_pais}">${escaparHtml(pais.nome)}</option>`)
      .join('');
}

async function carregarCidadeMaisPopulosa() {
  const idPais = this.value;
  const container = document.getElementById('estatistica-cidade-populosa');

  if (!idPais) {
    container.innerHTML = '<span class="vazio">Selecione um país para ver a cidade mais populosa.</span>';
    return;
  }

  const resposta = await chamarApi(`estatisticas.php?tipo=cidade_mais_populosa&pais=${idPais}`);

  if (!resposta.sucesso) {
    container.innerHTML = `<span class="vazio">${escaparHtml(resposta.mensagem)}</span>`;
    return;
  }

  if (!resposta.dados) {
    container.innerHTML = '<span class="vazio">Este país ainda não possui cidades cadastradas.</span>';
    return;
  }

  container.innerHTML = `
    <strong>${escaparHtml(resposta.dados.nome)}</strong>
    População: ${formatarNumero(resposta.dados.populacao)}
  `;
}
