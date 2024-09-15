// scripts.js

// Script para mostrar e esconder o menu
const profileImg = document.getElementById('profile-img');
const menuPerfil = document.getElementById('menu-perfil');

profileImg.addEventListener('click', () => {
    menuPerfil.classList.toggle('active');
});

// Fecha o menu se clicar fora dele
document.addEventListener('click', (event) => {
    if (!profileImg.contains(event.target) && !menuPerfil.contains(event.target)) {
        menuPerfil.classList.remove('active');
    }
});
// Abrindo o modal quando o menu de perfil é clicado
document.getElementById("menu-perfil").onclick = function() {
    document.getElementById("editModal").style.display = "block";
}

// Fechando o modal quando o usuário clica no "X"
document.getElementsByClassName("close")[0].onclick = function() {
    document.getElementById("editModal").style.display = "none";
}

// Fechando o modal se o usuário clicar fora da área do modal
window.onclick = function(event) {
    if (event.target == document.getElementById("editModal")) {
        document.getElementById("editModal").style.display = "none";
    }
}