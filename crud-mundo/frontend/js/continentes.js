/**
 * continentes.js - CRUD completo de continentes (listar, criar, editar, excluir)
 */

const form = document.getElementById('form-continente');
const tabela = document.getElementById('tabela-continentes');
const campoBusca = document.getElementById('campo-busca');
const btnCancelar = document.getElementById('btn-cancelar');
const tituloFormulario = document.getElementById('titulo-formulario');

document.addEventListener('DOMContentLoaded', listarContinentes);

form.addEventListener('submit', async (evento) => {
  evento.preventDefault();
  if (!validarFormulario(form)) return;

  const id = document.getElementById('id_continente').value;
  const corpo = {
    nome: document.getElementById('nome').value.trim(),
    populacao: document.getElementById('populacao').value || null,
    area: document.getElementById('area').value || null,
  };

  const resposta = id
    ? await chamarApi(`continentes.php?id=${id}`, 'PUT', corpo)
    : await chamarApi('continentes.php', 'POST', corpo);

  exibirAlerta('alerta-formulario', resposta.mensagem, resposta.sucesso ? 'sucesso' : 'erro');

  if (resposta.sucesso) {
    resetarFormulario();
    listarContinentes();
  }
});

btnCancelar.addEventListener('click', resetarFormulario);

campoBusca.addEventListener('input', () => {
  listarContinentes(campoBusca.value.trim());
});

function resetarFormulario() {
  form.reset();
  document.getElementById('id_continente').value = '';
  tituloFormulario.textContent = 'Cadastrar Continente';
  btnCancelar.style.display = 'none';
}

async function listarContinentes(filtro = '') {
  const resposta = await chamarApi('continentes.php');

  if (!resposta.sucesso) {
    exibirAlerta('alerta-lista', resposta.mensagem, 'erro');
    tabela.innerHTML = '<tr><td colspan="5" class="vazio">Erro ao carregar dados.</td></tr>';
    return;
  }

  let dados = resposta.dados;
  if (filtro) {
    dados = dados.filter((c) => c.nome.toLowerCase().includes(filtro.toLowerCase()));
  }

  if (!dados.length) {
    tabela.innerHTML = '<tr><td colspan="5" class="vazio">Nenhum continente encontrado.</td></tr>';
    return;
  }

  tabela.innerHTML = dados
    .map(
      (c) => `
      <tr>
        <td>${escaparHtml(c.nome)}</td>
        <td>${formatarNumero(c.populacao)}</td>
        <td>${formatarNumero(c.area)}</td>
        <td>${formatarNumero(c.total_paises)}</td>
        <td class="acoes">
          <button class="botao pequeno" onclick="editarContinente(${c.id_continente})">Editar</button>
          <button class="botao pequeno perigo" onclick="excluirContinente(${c.id_continente}, '${escaparHtml(c.nome).replace(/'/g, "\\'")}')">Excluir</button>
        </td>
      </tr>`
    )
    .join('');
}

async function editarContinente(id) {
  const resposta = await chamarApi(`continentes.php?id=${id}`);
  if (!resposta.sucesso) {
    exibirAlerta('alerta-lista', resposta.mensagem, 'erro');
    return;
  }

  const c = resposta.dados;
  document.getElementById('id_continente').value = c.id_continente;
  document.getElementById('nome').value = c.nome;
  document.getElementById('populacao').value = c.populacao ?? '';
  document.getElementById('area').value = c.area ?? '';

  tituloFormulario.textContent = `Editar Continente: ${c.nome}`;
  btnCancelar.style.display = 'inline-block';
  form.scrollIntoView({ behavior: 'smooth' });
}

function excluirContinente(id, nome) {
  abrirModalConfirmacao(`Tem certeza que deseja excluir o continente "${nome}"?`, async () => {
    const resposta = await chamarApi(`continentes.php?id=${id}`, 'DELETE');
    exibirAlerta('alerta-lista', resposta.mensagem, resposta.sucesso ? 'sucesso' : 'erro');
    if (resposta.sucesso) listarContinentes();
  });
}
