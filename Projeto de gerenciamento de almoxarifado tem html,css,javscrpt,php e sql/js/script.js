let body = document.querySelector("body")
let tab = document.querySelector(".tab-container")
let btn = document.querySelector(".btn")
let closeBtn = document.querySelector(".close")
let saida = document.querySelector(".saida")
let entrada = document.querySelector(".entrada")

btn.addEventListener('click', () => {
  body.classList.toggle('show')
})

closeBtn.addEventListener('click', () => {
  body.classList.toggle('show')
})

if (document.querySelector('#meupal').style.display = 'none') {
  saida.addEventListener('click', () => {
    document.querySelector('#meupal').style.display = 'block'
  })
}

entrada.addEventListener('click', () => {
  document.querySelector('#meupal').style.display = 'none'
})

saida.addEventListener('click', () => {
  document.querySelector('#tirarNome').style.display = 'none'
})

if (document.querySelector('#tirarNome').style.display = 'block') {
  entrada.addEventListener('click', () => {
    document.querySelector('#tirarNome').style.display = 'block'
  })
}