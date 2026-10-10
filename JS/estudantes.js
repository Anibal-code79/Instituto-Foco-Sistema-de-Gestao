const btnNovoEstudante = document.getElementById("btnNovoEstudante");

const modalEstudante = document.getElementById("modalEstudante");

const fecharModal = document.getElementById("fecharModal");

const cancelarModal = document.getElementById("cancelarModal");

const pesquisaEstudante = document.getElementById("pesquisaEstudante");

const tabelaEstudantes = document.getElementById("table-container");

const formEstudante = document.getElementById("formEstudante");

const estudanteId = document.getElementById("estudanteId");

const btnSalvarEstudante = document.getElementById("btnSalvarEstudante");

const tituloModal = modalEstudante.querySelector("h2");

btnNovoEstudante.addEventListener("click", function () {

    formEstudante.reset();

    estudanteId.value = "";

    tituloModal.textContent = "Novo estudante";

    btnSalvarEstudante.textContent = "Cadastrar estudante";

    modalEstudante.classList.add("active");

});

function fecharModalEstudante() {
    modalEstudante.classList.remove("active");
}
fecharModal.addEventListener("click", function () {
    fecharModalEstudante();
});
cancelarModal.addEventListener("click", function () {
    fecharModalEstudante();
});

modalEstudante.addEventListener("click", function (evento) {
    if (evento.target === modalEstudante) {
        fecharModalEstudante();
    }
});

formEstudante.addEventListener("submit", async function (evento) {
    evento.preventDefault();
    const dados = new FormData(formEstudante);
    let url;
    if (estudanteId.value) {
        url = "backend/config Estudante/actualizar_estudante.php";
    } else {
        url = "backend/config Estudante/cadastrar_estudante.php";

    }
    try {
        const resposta = await fetch(url, {
            method: "POST",
            body: dados
        });
        const resultado = await resposta.json();
        if (resultado.sucesso) {
            if (estudanteId.value) {
                alert(resultado.mensagem);
            } else {
                alert(
                    resultado.mensagem +
                    "\n\nUsuário: " +
                    resultado.usuario +
                    "\nSenha inicial: " +
                    resultado.senha
                );

            }
            fecharModalEstudante();
            location.reload();

        } else {
            alert(resultado.mensagem);
        }
    } catch (erro) {
    console.error(erro);
    alert("ERRO REAL: " + erro.message);
}
});

const botoesEditar = document.querySelectorAll(".btn-edit");
botoesEditar.forEach(function (botao) {
    botao.addEventListener("click", async function () {
        const id = this.dataset.id;
        try {
            const resposta = await fetch(
               "backend/config%20Estudante/buscardados.php?id=" + id
            );
            const estudante = await resposta.json();

            if (!estudante.sucesso) {
                alert(estudante.mensagem);
                return;
            }

            // Preencher formulário
            estudanteId.value = estudante.dados.id;
            document.getElementById("codigo").value = estudante.dados.codigo_estudante;
            document.getElementById("nome").value =estudante.dados.nome_completo;

            document.getElementById("contacto").value =estudante.dados.contacto;
            document.getElementById("curso").value =estudante.dados.curso_id;
            document.getElementById("nivel").value =estudante.dados.ano;

            tituloModal.textContent =
                "Actualizar estudante";
            btnSalvarEstudante.textContent =
                "Actualizar estudante";
            modalEstudante.classList.add("active");
        } catch (erro) {
    console.error(erro);
    alert("ERRO REAL: " + erro.message);
}
    });
});


const botoesEliminar =document.querySelectorAll(".btn-delete");
botoesEliminar.forEach(function (botao) {
    botao.addEventListener("click", async function () {
        const id = this.dataset.id;
        const confirmar = confirm(
            "Tem certeza que deseja eliminar este estudante?"
        );
        if (!confirmar) {
            return;
        }
        const dados = new FormData();
        dados.append("id", id);
        try {
            const resposta = await fetch(
                "eliminar_estudante.php",
                {
                    method: "POST",
                    body: dados
                }
            );

            const resultado = await resposta.json();
            if (resultado.sucesso) {
                alert(resultado.mensagem);
                location.reload();
            } else {
                alert(resultado.mensagem);
            }

        } catch (erro) {
            console.error(erro);
            alert(
                "Ocorreu um erro ao eliminar o estudante."
            );
        }
    });
});

pesquisaEstudante.addEventListener("input", function () {
    const textoPesquisa = pesquisaEstudante.value
        .toLowerCase()
        .trim();
    const linhas = tabelaEstudantes.querySelectorAll("tbody tr");


    linhas.forEach(function (linha) {
        const textoLinha =linha.textContent.toLowerCase();


        if (textoLinha.includes(textoPesquisa)) {
            linha.style.display = "";
        } else {
            linha.style.display = "none";
        }
    });
});