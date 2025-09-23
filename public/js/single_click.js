/******/ (() => { // webpackBootstrap
/*!**************************************!*\
  !*** ./resources/js/single_click.js ***!
  \**************************************/
function single_click(event, boton, titulo, subtitulo) {
  var enviar = true;
  if (typeof titulo !== 'undefined') {
    enviar = confirm(titulo + '\n\n' + subtitulo);
  }
  var spinner = boton.childNodes[1];
  if (enviar) {
    spinner.style.display = "inline-block";
    boton.classList.add('disabled');
  } else {
    event.preventDefault();
    spinner.style.display = "none";
    boton.classList.remove('disabled');
  }
}
/******/ })()
;
//# sourceMappingURL=single_click.js.map