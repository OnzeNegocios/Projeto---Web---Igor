/**
 * cidades.js - CRUD completo de cidades
 */

const form = document.getElementById('form-cidade');
const tabela = document.getElementById('tabela-cidades');
const campoBusca = document.getElementById('campo-busca');
const filtroPais = document.getElementById('filtro-pais');
const btnCancelar = document.getElementById('btn-cancelar');
const tituloFormulario = document.getElementById('titulo-formulario');

document.addEventListener('DOMContentLoaded', async () => {
  await carregarSelectsPaises();
  await carregarSelectGovernantes();
  listarCidades();
});

form.addEventListener('submit', async (evento) => {
  evento.preventDefault();
  if (!validarFormulario(form)) return;

  const id = document.getElementById('id_cidade').value;
  const corpo = {
    nome: document.getElementById('nome').value.trim(),
    id_pais: document.getElementById('id_pais').value,
    id_governante: document.getElementById('id_governante').value || null,
    populacao: document.getElementById('populacao').value || null,
    area: document.getElementById('area').value || null,
    clima: document.getElementById('clima').value.trim() || null,
    data_fundacao: document.getElementById('data_fundacao').value || null,
  };

  const resposta = id
    ? await chamarApi(`cidades.php?id=${id}`, 'PUT', corpo)
    : await chamarApi('cidades.php', 'POST', corpo);

  exibirAlerta('alerta-formulario', resposta.mensagem, resposta.sucesso ? 'sucesso' : 'erro');

  if (resposta.sucesso) {
    resetarFormulario();
    listarCidades();
  }
});

btnCancelar.addEventListener('click', resetarFormulario);
campoBusca.addEventListener('input', aplicarFiltros);
filtroPais.addEventListener('change', aplicarFiltros);

function aplicarFiltros() {
  listarCidades(campoBusca.value.trim(), filtroPais.value);
}

function resetarFormulario() {
  form.reset();
  document.getElementById('id_cidade').value = '';
  tituloFormulario.textContent = 'Cadastrar Cidade';
  btnCancelar.style.display = 'none';
}

async function carregarSelectsPaises() {
  const resposta = await chamarApi('paises.php');
  if (!resposta.sucesso) return;

  const opcoes = resposta.dados
    .map((p) => `<option value="${p.id_pais}">${escaparHtml(p.nome)}</option>`)
    .join('');

  document.getElementById('id_pais').innerHTML = '<option value="">Selecione...</option>' + opcoes;
  filtroPais.innerHTML = '<option value="">Todos os países</option>' + opcoes;
}

async function carregarSelectGovernantes() {
  const resposta = await chamarApi('governantes.php');
  if (!resposta.sucesso) return;

  const opcoes = resposta.dados
    .map((g) => `<option value="${g.id_governante}">${escaparHtml(g.nome)}</option>`)
    .join('');

  document.getElementById('id_governante').innerHTML = '<option value="">Nenhum</option>' + opcoes;
}

async function listarCidades(busca = '', pais = '') {
  const parametros = new URLSearchParams();
  if (busca) parametros.set('busca', busca);
  if (pais) parametros.set('pais', pais);

  const resposta = await chamarApi(`cidades.php?${parametros.toString()}`);

  if (!resposta.sucesso) {
    exibirAlerta('alerta-lista', resposta.mensagem, 'erro');
    tabela.innerHTML = '<tr><td colspan="6" class="vazio">Erro ao carregar dados.</td></tr>';
    return;
  }

  if (!resposta.dados.length) {
    tabela.innerHTML = '<tr><td colspan="6" class="vazio">Nenhuma cidade encontrada.</td></tr>';
    return;
  }

  tabela.innerHTML = resposta.dados
    .map(
      (c) => `
      <tr>
        <td>${escaparHtml(c.nome)}</td>
        <td>${escaparHtml(c.nome_pais) || '-'}</td>
        <td>${formatarNumero(c.populacao)}</td>
        <td>${escaparHtml(c.nome_governante) || '-'}</td>
        <td>${c.data_fundacao ?? '-'}</td>
        <td class="acoes">
          <button class="botao pequeno" onclick="editarCidade(${c.id_cidade})">Editar</button>
          <button class="botao pequeno perigo" onclick="excluirCidade(${c.id_cidade}, '${escaparHtml(c.nome).replace(/'/g, "\\'")}')">Excluir</button>
        </td>
      </tr>`
    )
    .join('');
}

async function editarCidade(id) {
  const resposta = await chamarApi(`cidades.php?id=${id}`);
  if (!resposta.sucesso) {
    exibirAlerta('alerta-lista', resposta.mensagem, 'erro');
    return;
  }

  const c = resposta.dados;
  document.getElementById('id_cidade').value = c.id_cidade;
  document.getElementById('nome').value = c.nome;
  document.getElementById('id_pais').value = c.id_pais;
  document.getElementById('id_governante').value = c.id_governante ?? '';
  document.getElementById('populacao').value = c.populacao ?? '';
  document.getElementById('area').value = c.area ?? '';
  document.getElementById('clima').value = c.clima ?? '';
  document.getElementById('data_fundacao').value = c.data_fundacao ?? '';

  tituloFormulario.textContent = `Editar Cidade: ${c.nome}`;
  btnCancelar.style.display = 'inline-block';
  form.scrollIntoView({ behavior: 'smooth' });
}

function excluirCidade(id, nome) {
  abrirModalConfirmacao(`Tem certeza que deseja excluir a cidade "${nome}"?`, async () => {
    const resposta = await chamarApi(`cidades.php?id=${id}`, 'DELETE');
    exibirAlerta('alerta-lista', resposta.mensagem, resposta.sucesso ? 'sucesso' : 'erro');
    if (resposta.sucesso) listarCidades();
  });
}
