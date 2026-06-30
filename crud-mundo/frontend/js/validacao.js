/**
 * validacao.js
 * Validação simples de formulários no lado do cliente (JavaScript).
 */

/**
 * Valida um formulário com base em regras declaradas via atributos
 * data-* nos próprios campos (required, data-min, data-max, type=number, etc.)
 * Retorna true se válido; caso contrário, exibe mensagens de erro inline.
 */
function validarFormulario(form) {
  let valido = true;

  form.querySelectorAll('[data-erro-para]').forEach((el) => (el.textContent = ''));

  form.querySelectorAll('input, select').forEach((campo) => {
    const erroEl = form.querySelector(`[data-erro-para="${campo.name}"]`);
    let mensagemErro = '';

    if (campo.hasAttribute('required') && campo.value.trim() === '') {
      mensagemErro = 'Este campo é obrigatório.';
    } else if (campo.type === 'number' && campo.value !== '') {
      const numero = Number(campo.value);
      if (Number.isNaN(numero)) {
        mensagemErro = 'Informe um número válido.';
      } else if (numero < 0) {
        mensagemErro = 'O valor não pode ser negativo.';
      }
    } else if (campo.type === 'date' && campo.value !== '') {
      const data = new Date(campo.value);
      if (Number.isNaN(data.getTime())) {
        mensagemErro = 'Informe uma data válida.';
      }
    }

    if (mensagemErro) {
      valido = false;
      if (erroEl) erroEl.textContent = mensagemErro;
      campo.setAttribute('aria-invalid', 'true');
    } else {
      campo.removeAttribute('aria-invalid');
    }
  });

  return valido;
}

/**
 * Exibe uma confirmação simples antes de excluir um registro.
 * (usada como fallback quando não há modal customizado disponível)
 */
function confirmarExclusao(mensagem = 'Tem certeza que deseja excluir este registro?') {
  return window.confirm(mensagem);
}
