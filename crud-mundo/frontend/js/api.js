/**
 * api.js
 * Funções genéricas para comunicação com o back-end PHP.
 */

const API_BASE = '../backend/api';

/**
 * Faz uma requisição à API e retorna o JSON já tratado.
 */
async function chamarApi(endpoint, metodo = 'GET', corpo = null) {
  const opcoes = {
    method: metodo,
    headers: { 'Content-Type': 'application/json' },
  };
  if (corpo) {
    opcoes.body = JSON.stringify(corpo);
  }

  try {
    const resposta = await fetch(`${API_BASE}/${endpoint}`, opcoes);
    const json = await resposta.json();
    return json;
  } catch (erro) {
    return { sucesso: false, mensagem: 'Falha de comunicação com o servidor: ' + erro.message };
  }
}

/**
 * Exibe uma mensagem de alerta (sucesso/erro) dentro do container informado.
 */
function exibirAlerta(containerId, mensagem, tipo = 'sucesso') {
  const container = document.getElementById(containerId);
  if (!container) return;

  container.innerHTML = `<div class="alerta ${tipo}">${mensagem}</div>`;
  if (tipo === 'sucesso') {
    setTimeout(() => { container.innerHTML = ''; }, 4000);
  }
}

/**
 * Escapa texto para evitar problemas ao inserir no innerHTML.
 */
function escaparHtml(texto) {
  if (texto === null || texto === undefined) return '';
  const div = document.createElement('div');
  div.textContent = String(texto);
  return div.innerHTML;
}

/**
 * Formata número com separador de milhar (pt-BR).
 */
function formatarNumero(valor) {
  if (valor === null || valor === undefined || valor === '') return '-';
  return Number(valor).toLocaleString('pt-BR');
}

/* ---------------------------------------------------------
   Modal de confirmação de exclusão (reutilizável)
--------------------------------------------------------- */
let _callbackConfirmacao = null;

function abrirModalConfirmacao(mensagem, callback) {
  const modal = document.getElementById('modal-confirmacao');
  const texto = document.getElementById('modal-texto');
  if (!modal || !texto) {
    // Fallback simples caso o modal não exista na página
    if (confirm(mensagem)) callback();
    return;
  }
  texto.textContent = mensagem;
  _callbackConfirmacao = callback;
  modal.classList.add('aberto');
}

function fecharModalConfirmacao() {
  const modal = document.getElementById('modal-confirmacao');
  if (modal) modal.classList.remove('aberto');
  _callbackConfirmacao = null;
}

document.addEventListener('DOMContentLoaded', () => {
  const btnConfirmar = document.getElementById('modal-confirmar');
  const btnCancelar = document.getElementById('modal-cancelar');

  if (btnConfirmar) {
    btnConfirmar.addEventListener('click', () => {
      if (typeof _callbackConfirmacao === 'function') {
        _callbackConfirmacao();
      }
      fecharModalConfirmacao();
    });
  }
  if (btnCancelar) {
    btnCancelar.addEventListener('click', fecharModalConfirmacao);
  }
});
