function single_click(event, boton, titulo, subtitulo) {

    let enviar = true;
    if (typeof titulo !== 'undefined') {
        enviar = confirm(titulo + '\n\n' + subtitulo);
    }

    const spinner = boton.childNodes[1];

    if (enviar) {
        spinner.style.display = "inline-block";
        boton.classList.add('disabled');
    } else {
        event.preventDefault();
        spinner.style.display = "none";
        boton.classList.remove('disabled');
    }
}
