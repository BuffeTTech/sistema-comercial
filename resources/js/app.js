import PerfectScrollbar from 'perfect-scrollbar';
window.PerfectScrollbar = PerfectScrollbar;
import Alpine from 'alpinejs';
import Swal from 'sweetalert2'

import './bootstrap'
import './custom'

window.Alpine = Alpine;

Alpine.start();

window.confirm = function (message="Deseja confirmar esta ação?") {
    return new Promise((resolve) => {
        Swal.fire({
            title: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sim",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                resolve(true);
            } else {
                resolve(false);
            }
        });
    });
}

window.close_modal = function() {
    return Swal.close()
}

window.error = function (message="Ocorreu um erro ao processar esta solicitação") {
    return Swal.fire({
        icon: "error",
        title: "Oops...",
        text: message,
        confirmButtonColor: "#3085d6",
        confirmButtonText: "Tentar novamente",
      });
}
window.basic = function (message="Operação realizada com sucesso") {
    return Swal.fire(message);
}
window.html = function (data) {
    return Swal.fire({
        title: data.title,
        icon: "info",
        html: data.content,
        showCloseButton: true,
        showCancelButton: false,
        focusConfirm: false,
        confirmButtonText: `
            Fechar!
        `,
        confirmButtonAriaLabel: "Fechar!",
      });
}

window.replaceCNPJ = function(value) {
    return value
        .replace(/\D+/g, '') // não deixa ser digitado nenhuma letra
        .replace(/(\d{2})(\d)/, '$1.$2') // captura 2 grupos de número o primeiro com 2 digitos e o segundo de com 3 digitos, apos capturar o primeiro grupo ele adiciona um ponto antes do segundo grupo de número
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1/$2') // captura 2 grupos de número o primeiro e o segundo com 3 digitos, separados por /
        .replace(/(\d{4})(\d)/, '$1-$2')
        .replace(/(-\d{2})\d+?$/, '$1') // captura os dois últimos 2 números, com um - antes dos dois números
}

window.validarCNPJ = function(cnpj) {
    cnpj = cnpj.replace(/[^\d]/g, '');

    if (cnpj.length !== 14) {
        return false;
    }

    if (/^(\d)\1+$/.test(cnpj)) {
        return false;
    }

    let tamanho = cnpj.length - 2;
    let numeros = cnpj.substring(0, tamanho);
    const digitos = cnpj.substring(tamanho);
    let soma = 0;
    let pos = tamanho - 7;

    for (let i = tamanho; i >= 1; i--) {
        soma += parseInt(numeros.charAt(tamanho - i)) * pos--;
        if (pos < 2) {
            pos = 9;
        }
    }

    let resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);

    if (resultado !== parseInt(digitos.charAt(0))) {
        return false;
    }

    tamanho += 1;
    numeros = cnpj.substring(0, tamanho);
    soma = 0;
    pos = tamanho - 7;

    for (let i = tamanho; i >= 1; i--) {
        soma += parseInt(numeros.charAt(tamanho - i)) * pos--;
        if (pos < 2) {
            pos = 9;
        }
    }

    resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);

    if (resultado !== parseInt(digitos.charAt(1))) {
        return false;
    }

    return true;
}
window.replaceCPF = function(value) {
    return value
        .replace(/\D/g, '') // substitui qualquer caracter que nao seja numero por nada
        .replace(/(\d{3})(\d)/, '$1.$2') // captura 2 grupos de numero o primeiro de 3 e o segundo de 1, apos capturar o primeiro grupo ele adiciona um ponto antes do segundo grupo de numero
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d{1,2})/, '$1-$2')
        .replace(/(-\d{2})\d+?$/, '$1') // captura 2 numeros seguidos de um traço e não deixa ser digitado mais nada
}
window.validarCPF = function(cpf) {
    cpf = cpf.replace(/[^\d]/g, '');

    if (cpf.length !== 11) {
        return false;
    }

    if (/^(\d)\1+$/.test(cpf)) {
        return false;
    }

    let soma = 0;
    for (let i = 0; i < 9; i++) {
        soma += parseInt(cpf.charAt(i)) * (10 - i);
    }
    let digito1 = 11 - (soma % 11);
    digito1 = (digito1 >= 10) ? 0 : digito1;

    if (parseInt(cpf.charAt(9)) !== digito1) {
        return false;
    }

    soma = 0;
    for (let i = 0; i < 10; i++) {
        soma += parseInt(cpf.charAt(i)) * (11 - i);
    }
    let digito2 = 11 - (soma % 11);
    digito2 = (digito2 >= 10) ? 0 : digito2;

    if (parseInt(cpf.charAt(10)) !== digito2) {
        return false;
    }

    return true;
}
window.replacePhone = function(value) {
    return value
        .replace(/\D/g, '') // substitui qualquer caracter que nao seja numero por nada
        .replace(/(\d{2})(\d)/, '($1) $2') // captura 2 grupos de numero o primeiro de 2 e o segundo de 1, apos capturar o primeiro grupo ele adiciona parênteses e espaço antes do segundo grupo de numero
        .replace(/(\d{5})(\d)/, '$1-$2') // captura 2 grupos de numero o primeiro de 5 e o segundo de 1, apos capturar o primeiro grupo ele adiciona um traço antes do segundo grupo de numero
        .replace(/(-\d{4})\d+?$/, '$1'); // captura 4 numeros seguidos de um traço e não deixa ser digitado mais nada
}

window.replaceCEP = function(value) {
    return value
        .replace(/\D/g, '') // Remove todos os caracteres que não são dígitos
        .replace(/(\d{5})(\d{0,3}).*/, '$1-$2'); // Adiciona um hífen após os primeiros cinco dígitos e limita a 3 caracteres após o hífen
}