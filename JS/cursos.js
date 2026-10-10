const btnNovoCurso = document.getElementById("btnNovoCurso");
const modalCurso = document.getElementById("modalCurso");
const fecharModal = document.getElementById("fecharModal");
const cancelarModal = document.getElementById("cancelarModal");
const formCurso = document.getElementById("formCurso");
const cursoId = document.getElementById("cursoId");
const nomeCurso = document.getElementById("nomeCurso");
const mensalidadeCurso = document.getElementById("mensalidadeCurso");
const estadoCurso = document.getElementById("estadoCurso");
const tituloModal = document.getElementById("tituloModal");
const btnSalvarCurso = document.getElementById("btnSalvarCurso");
const pesquisaCurso = document.getElementById("pesquisaCurso");
const tabelaCursos = document.getElementById("table-container");
btnNovoCurso.addEventListener("click", function () {
    formCurso.reset();
    cursoId.value = "";
    tituloModal.textContent = "Novo curso";
    btnSalvarCurso.textContent = "Cadastrar curso";
    modalCurso.classList.add("active");
    nomeCurso.focus();
});
function fecharModalCurso() {
    modalCurso.classList.remove("active");
}
fecharModal.addEventListener("click", fecharModalCurso);
cancelarModal.addEventListener("click", fecharModalCurso);
modalCurso.addEventListener("click", function (evento) {
    if (evento.target === modalCurso) {
        fecharModalCurso();
    }
});

document.addEventListener("keydown", function (evento) {
    if (evento.key === "Escape") {
        fecharModalCurso();
    }
});
formCurso.addEventListener("submit", async function (evento) {
    evento.preventDefault();
    const dados = new FormData(formCurso);
    if (cursoId.value === "") {
        dados.append("acao", "cadastrar");
    } else {
        dados.append("acao", "actualizar");
    }
    try {
        const resposta = await fetch("cursos.php", {
            method: "POST",
            body: dados
        });
        const resultado = await resposta.json();
        if (resultado.sucesso) {
            alert(resultado.mensagem);
            fecharModalCurso();
            location.reload();
        } else {
            alert(resultado.mensagem);
        }
    } catch (erro) {
        console.error(erro);
        alert(
            "Ocorreu um erro ao processar o curso."
        );
    }
});
pesquisaCurso.addEventListener("input", function () {
    const texto =
        pesquisaCurso.value
        .toLowerCase()
        .trim();
    const linhas =
        tabelaCursos.querySelectorAll("tbody tr");
    linhas.forEach(function (linha) {
        const conteudo =
            linha.textContent.toLowerCase();
        if (conteudo.includes(texto)) {
            linha.style.display = "";
        } else {
            linha.style.display = "none";
        }
    });
});
const botoesEditar =
    document.querySelectorAll(".btn-edit");
botoesEditar.forEach(function (botao) {
    botao.addEventListener("click", async function () {
        const id = this.dataset.id;

        try {
            const resposta = await fetch(
                "cursos.php?buscar=" + id
            );
            const resultado =await resposta.json();

            if (!resultado.sucesso) {
                alert(resultado.mensagem);
                return;
            }
            const curso =
                resultado.dados;
            // Preencher formulário
            cursoId.value =
                curso.id;
            nomeCurso.value =
                curso.nome;
            mensalidadeCurso.value =
                curso.mensalidade;
            estadoCurso.value =
                curso.estado;
            // Alterar título e botão
            tituloModal.textContent =
                "Actualizar curso";
            btnSalvarCurso.textContent =
                "Actualizar curso";

            // Abrir modal
            modalCurso.classList.add("active");
            nomeCurso.focus();
        } catch (erro) {
            console.error(erro);
            alert(
                "Não foi possível carregar os dados do curso."
            );
        }
    });
});

// ELIMINAR CURSO

const botoesEliminar =
    document.querySelectorAll(".btn-delete");
botoesEliminar.forEach(function (botao) {
    botao.addEventListener("click", async function () {
        const id = this.dataset.id;
        const confirmar = confirm(
            "Tem certeza que deseja eliminar este curso?"
        );
        if (!confirmar) {
            return;
        }
        const dados = new FormData();
        dados.append("acao", "eliminar");
        dados.append("id", id);


        try {
            const resposta = await fetch(
                "cursos.php",
                {
                    method: "POST",
                    body: dados
                }
            );
            const resultado =
                await resposta.json();
            if (resultado.sucesso) {
                alert(resultado.mensagem);
                location.reload();
            } else {
                alert(resultado.mensagem);
            }
        } catch (erro) {
            console.error(erro);
            alert(
                "Ocorreu um erro ao eliminar o curso."
            );
        }
    });
});