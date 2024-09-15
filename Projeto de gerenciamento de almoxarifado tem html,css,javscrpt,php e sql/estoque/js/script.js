document.addEventListener('DOMContentLoaded', function () {
  const body = document.querySelector("body");
  const tab = document.querySelector(".tab-container");
  const btn = document.querySelector(".btng");
  const closeBtn = document.querySelector(".close");
  const saida = document.querySelector(".saida");
  const entrada = document.querySelector(".entrada");

  // Função para alternar a visibilidade da aba "Gerenciar"
  function toggleTab() {
    if (tab.style.display === 'block') {
      tab.style.display = 'none'; // Esconde a aba se estiver visível
    } else {
      tab.style.display = 'block'; // Mostra a aba se estiver escondida
    }
  }

  // Função para fechar a aba
  function closeTab() {
    tab.style.display = 'none'; // Sempre esconde a aba
  }

  // Verifica se o botão de gerenciar existe e adiciona o evento de clique
  if (btn && tab) {
    btn.addEventListener('click', toggleTab); // Alterna a aba ao clicar no botão "Gerenciar"
  }

  // Verifica se o botão de fechar existe e adiciona o evento de clique
  if (closeBtn) {
    closeBtn.addEventListener('click', closeTab); // Fecha a aba ao clicar no botão "Fechar"
  }

  // Verifica se os elementos 'saida' e 'entrada' existem antes de adicionar os eventos
  const meuPal = document.querySelector('#meupal');
  const tirarNome = document.querySelector('#tirarNome');

  if (saida && meuPal) {
    saida.addEventListener('click', () => {
      meuPal.style.display = 'block'; // Mostra o elemento meuPal ao clicar em 'saida'
    });
  }

  if (entrada) {
    entrada.addEventListener('click', () => {
      if (meuPal) {
        meuPal.style.display = 'none'; // Esconde o elemento meuPal ao clicar em 'entrada'
      }
      if (tirarNome) {
        tirarNome.style.display = 'block'; // Mostra o elemento tirarNome
      }
    });
  }

  if (saida) {
    saida.addEventListener('click', () => {
      if (tirarNome) {
        tirarNome.style.display = 'none'; // Esconde o elemento tirarNome ao clicar em 'saida'
      }
    });
  }

  // Fecha a aba ao clicar fora dela
  window.addEventListener('click', (event) => {
    if (tab.style.display === 'block') {
      if (!tab.contains(event.target) && !btn.contains(event.target)) {
        closeTab(); // Fecha a aba se clicar fora dela
      }
    }
  });

  // Ajusta a visibilidade da aba ao redimensionar a janela
  window.addEventListener('resize', function () {
    if (window.innerWidth > 768) {
      tab.style.display = 'none'; // Garante que a aba esteja oculta no modo desktop ao redimensionar
    }
  });
});
