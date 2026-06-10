import { inicializarFormularioConsulta } from './publico/consultar.js';
import { inicializarTema } from './publico/tema.js';

document.addEventListener('DOMContentLoaded', () => {
    inicializarTema();
    inicializarFormularioConsulta();
});
