/**
 * paises.js - CRUD completo de países
 */

const form = document.getElementById('form-pais');
const tabela = document.getElementById('tabela-paises');
const campoBusca = document.getElementById('campo-busca');
const filtroContinente = document.getElementById('filtro-continente');
const btnCancelar = document.getElementById('btn-cancelar');
const tituloFormulario = document.getElementById('titulo-formulario');

document.addEventListener('DOMContentLoaded', async () => {
  await carregarSelectsContinentes();
  await carregarSelectGovernantes();
  listarPaises();
});

form.addEventListener('submit', async (evento) => {
  evento.preventDefault();
  if (!validarFormulario(form)) return;

  const id = document.getElementById('id_pais').value;
  const corpo = {
    nome: document.getElementById('nome').value.trim(),
    id_continente: document.getElementById('id_continente').value,
    id_governante: document.getElementById('id_governante').value || null,
    populacao: document.getElementById('populacao').value || null,
    area: document.getElementById('area').value || null,
    idioma: document.getElementById('idioma').value.trim() || null,
    clima: document.getElementById('clima').value.trim() || null,
    regime_politico: document.getElementById('regime_politico').value.trim() || null,
    moeda: document.getElementById('moeda').value.trim() || null,
  };

  const resposta = id
    ? await chamarApi(`paises.php?id=${id}`, 'PUT', corpo)
    : await chamarApi('paises.php', 'POST', corpo);

  exibirAlerta('alerta-formulario', resposta.mensagem, resposta.sucesso ? 'sucesso' : 'erro');

  if (resposta.sucesso) {
    resetarFormulario();
    listarPaises();
  }
});

btnCancelar.addEventListener('click', resetarFormulario);
campoBusca.addEventListener('input', aplicarFiltros);
filtroContinente.addEventListener('change', aplicarFiltros);

function aplicarFiltros() {
  listarPaises(campoBusca.value.trim(), filtroContinente.value);
}

function resetarFormulario() {
  form.reset();
  document.getElementById('id_pais').value = '';
  tituloFormulario.textContent = 'Cadastrar País';
  btnCancelar.style.display = 'none';
}

async function carregarSelectsContinentes() {
  const resposta = await chamarApi('continentes.php');
  if (!resposta.sucesso) return;

  const opcoes = resposta.dados
    .map((c) => `<option value="${c.id_continente}">${escaparHtml(c.nome)}</option>`)
    .join('');

  document.getElementById('id_continente').innerHTML = '<option value="">Selecione...</option>' + opcoes;
  filtroContinente.innerHTML = '<option value="">Todos os continentes</option>' + opcoes;
}

async function carregarSelectGovernantes() {
  const resposta = await chamarApi('governantes.php');
  if (!resposta.sucesso) return;

  const opcoes = resposta.dados
    .map((g) => `<option value="${g.id_governante}">${escaparHtml(g.nome)}</option>`)
    .join('');

  document.getElementById('id_governante').innerHTML = '<option value="">Nenhum</option>' + opcoes;
}

async function listarPaises(busca = '', continente = '') {
  const parametros = new URLSearchParams();
  if (busca) parametros.set('busca', busca);
  if (continente) parametros.set('continente', continente);

  const resposta = await chamarApi(`paises.php?${parametros.toString()}`);

  if (!resposta.sucesso) {
    exibirAlerta('alerta-lista', resposta.mensagem, 'erro');
    tabela.innerHTML = '<tr><td colspan="6" class="vazio">Erro ao carregar dados.</td></tr>';
    return;
  }

  if (!resposta.dados.length) {
    tabela.innerHTML = '<tr><td colspan="6" class="vazio">Nenhum país encontrado.</td></tr>';
    return;
  }

  tabela.innerHTML = resposta.dados
    .map(
      (p) => `
      <tr>
        <td>${escaparHtml(p.nome)}</td>
        <td>${escaparHtml(p.nome_continente) || '-'}</td>
        <td>${formatarNumero(p.populacao)}</td>
        <td>${escaparHtml(p.nome_governante) || '-'}</td>
        <td>${escaparHtml(p.moeda) || '-'}</td>
        <td class="acoes">
          <button class="botao pequeno" onclick="editarPais(${p.id_pais})">Editar</button>
          <button class="botao pequeno perigo" onclick="excluirPais(${p.id_pais}, '${escaparHtml(p.nome).replace(/'/g, "\\'")}')">Excluir</button>
        </td>
      </tr>`
    )
    .join('');
}

async function editarPais(id) {
  const resposta = await chamarApi(`paises.php?id=${id}`);
  if (!resposta.sucesso) {
    exibirAlerta('alerta-lista', resposta.mensagem, 'erro');
    return;
  }

  const p = resposta.dados;
  document.getElementById('id_pais').value = p.id_pais;
  document.getElementById('nome').value = p.nome;
  document.getElementById('id_continente').value = p.id_continente;
  document.getElementById('id_governante').value = p.id_governante ?? '';
  document.getElementById('populacao').value = p.populacao ?? '';
  document.getElementById('area').value = p.area ?? '';
  document.getElementById('idioma').value = p.idioma ?? '';
  document.getElementById('clima').value = p.clima ?? '';
  document.getElementById('regime_politico').value = p.regime_politico ?? '';
  document.getElementById('moeda').value = p.moeda ?? '';

  tituloFormulario.textContent = `Editar País: ${p.nome}`;
  btnCancelar.style.display = 'inline-block';
  form.scrollIntoView({ behavior: 'smooth' });
}

function excluirPais(id, nome) {
  abrirModalConfirmacao(`Tem certeza que deseja excluir o país "${nome}"? Países com cidades vinculadas não podem ser excluídos.`, async () => {
    const resposta = await chamarApi(`paises.php?id=${id}`, 'DELETE');
    exibirAlerta('alerta-lista', resposta.mensagem, resposta.sucesso ? 'sucesso' : 'erro');
    if (resposta.sucesso) listarPaises();
  });
}
