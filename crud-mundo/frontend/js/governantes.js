/**
 * governantes.js - CRUD completo de governantes
 */

const form = document.getElementById('form-governante');
const tabela = document.getElementById('tabela-governantes');
const campoBusca = document.getElementById('campo-busca');
const btnCancelar = document.getElementById('btn-cancelar');
const tituloFormulario = document.getElementById('titulo-formulario');

document.addEventListener('DOMContentLoaded', listarGovernantes);

form.addEventListener('submit', async (evento) => {
  evento.preventDefault();
  if (!validarFormulario(form)) return;

  const id = document.getElementById('id_governante').value;
  const corpo = {
    nome: document.getElementById('nome').value.trim(),
    partido_politico: document.getElementById('partido_politico').value.trim() || null,
    data_nascimento: document.getElementById('data_nascimento').value || null,
    data_inicio_mandato: document.getElementById('data_inicio_mandato').value || null,
    data_fim_mandato: document.getElementById('data_fim_mandato').value || null,
  };

  const resposta = id
    ? await chamarApi(`governantes.php?id=${id}`, 'PUT', corpo)
    : await chamarApi('governantes.php', 'POST', corpo);

  exibirAlerta('alerta-formulario', resposta.mensagem, resposta.sucesso ? 'sucesso' : 'erro');

  if (resposta.sucesso) {
    resetarFormulario();
    listarGovernantes();
  }
});

btnCancelar.addEventListener('click', resetarFormulario);

campoBusca.addEventListener('input', () => {
  listarGovernantes(campoBusca.value.trim());
});

function resetarFormulario() {
  form.reset();
  document.getElementById('id_governante').value = '';
  tituloFormulario.textContent = 'Cadastrar Governante';
  btnCancelar.style.display = 'none';
}

async function listarGovernantes(filtro = '') {
  const resposta = await chamarApi('governantes.php');

  if (!resposta.sucesso) {
    exibirAlerta('alerta-lista', resposta.mensagem, 'erro');
    tabela.innerHTML = '<tr><td colspan="6" class="vazio">Erro ao carregar dados.</td></tr>';
    return;
  }

  let dados = resposta.dados;
  if (filtro) {
    dados = dados.filter((g) => g.nome.toLowerCase().includes(filtro.toLowerCase()));
  }

  if (!dados.length) {
    tabela.innerHTML = '<tr><td colspan="6" class="vazio">Nenhum governante encontrado.</td></tr>';
    return;
  }

  tabela.innerHTML = dados
    .map(
      (g) => `
      <tr>
        <td>${escaparHtml(g.nome)}</td>
        <td>${escaparHtml(g.partido_politico) || '-'}</td>
        <td>${g.idade ?? '-'}</td>
        <td>${formatarData(g.data_inicio_mandato)}</td>
        <td>${formatarData(g.data_fim_mandato)}</td>
        <td class="acoes">
          <button class="botao pequeno" onclick="editarGovernante(${g.id_governante})">Editar</button>
          <button class="botao pequeno perigo" onclick="excluirGovernante(${g.id_governante}, '${escaparHtml(g.nome).replace(/'/g, "\\'")}')">Excluir</button>
        </td>
      </tr>`
    )
    .join('');
}

function formatarData(data) {
  if (!data) return '-';
  const [ano, mes, dia] = data.split('-');
  return `${dia}/${mes}/${ano}`;
}

async function editarGovernante(id) {
  const resposta = await chamarApi(`governantes.php?id=${id}`);
  if (!resposta.sucesso) {
    exibirAlerta('alerta-lista', resposta.mensagem, 'erro');
    return;
  }

  const g = resposta.dados;
  document.getElementById('id_governante').value = g.id_governante;
  document.getElementById('nome').value = g.nome;
  document.getElementById('partido_politico').value = g.partido_politico ?? '';
  document.getElementById('data_nascimento').value = g.data_nascimento ?? '';
  document.getElementById('data_inicio_mandato').value = g.data_inicio_mandato ?? '';
  document.getElementById('data_fim_mandato').value = g.data_fim_mandato ?? '';

  tituloFormulario.textContent = `Editar Governante: ${g.nome}`;
  btnCancelar.style.display = 'inline-block';
  form.scrollIntoView({ behavior: 'smooth' });
}

function excluirGovernante(id, nome) {
  abrirModalConfirmacao(`Tem certeza que deseja excluir o governante "${nome}"?`, async () => {
    const resposta = await chamarApi(`governantes.php?id=${id}`, 'DELETE');
    exibirAlerta('alerta-lista', resposta.mensagem, resposta.sucesso ? 'sucesso' : 'erro');
    if (resposta.sucesso) listarGovernantes();
  });
}
