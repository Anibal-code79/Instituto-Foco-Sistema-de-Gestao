const btnNovaMensalidade =
    document.getElementById("btnNovaMensalidade");

const modalMensalidade =
    document.getElementById("modalMensalidade");

const fecharModal =
    document.getElementById("fecharModal");

const cancelarModal =
    document.getElementById("cancelarModal");

const formMensalidade =
    document.getElementById("formMensalidade");

const mensalidadeId =
    document.getElementById("mensalidadeId");

const btnSalvarMensalidade =
    document.getElementById("btnSalvarMensalidade");

const tituloModal =
    modalMensalidade.querySelector("h2");

const estudante =
    document.getElementById("estudante");

const curso =
    document.getElementById("curso");

const pesquisaMensalidade =
    document.getElementById("pesquisaMensalidade");

const filtroEstado =
    document.getElementById("filtroEstado");

const tabelaMensalidades =
    document.getElementById("tabelaMensalidades");

btnNovaMensalidade.addEventListener("click", function () {

    formMensalidade.reset();

    mensalidadeId.value = "";

    tituloModal.textContent =
        "Nova mensalidade";

    btnSalvarMensalidade.textContent =
        "Guardar mensalidade";

    modalMensalidade.classList.add("active");

});

function fecharModalMensalidade() {

    modalMensalidade.classList.remove("active");

}


fecharModal.addEventListener("click", function () {

    fecharModalMensalidade();

});


cancelarModal.addEventListener("click", function () {

    fecharModalMensalidade();

});


modalMensalidade.addEventListener("click", function (evento) {

    if (evento.target === modalMensalidade) {

        fecharModalMensalidade();

    }

});


document.addEventListener("keydown", function (evento) {

    if (evento.key === "Escape") {

        fecharModalMensalidade();

    }

});


// =====================================================
// PREENCHER CURSO + VALOR AUTOMATICAMENTE
// =====================================================

estudante.addEventListener("change", async function () {

    const opcao = this.options[this.selectedIndex];
    const cursoId = opcao.dataset.curso;

    curso.value = cursoId || "";

    const campoValorExibicao = document.getElementById("valor");
    const campoValorReal = document.getElementById("valorReal");

    if (!cursoId) {
        campoValorExibicao.value = "";
        campoValorReal.value = "";
        return;
    }

    try {

        const resposta = await fetch(
            "backend/config%20Mensalidades/buscar_valor_curso.php?curso_id=" + cursoId
        );

        const resultado = await resposta.json();

        if (resultado.sucesso) {

            campoValorExibicao.value =
                Number(resultado.mensalidade).toLocaleString('pt-PT', {
                    minimumFractionDigits: 2
                }) + " MT";

            campoValorReal.value = resultado.mensalidade;

        } else {

            campoValorExibicao.value = "Curso sem mensalidade definida";
            campoValorReal.value = "";

        }

    } catch (erro) {

        console.error(erro);
        campoValorExibicao.value = "Erro ao buscar valor";
        campoValorReal.value = "";

    }

});


// =====================================================
// ENVIAR FORMULÁRIO
// =====================================================

formMensalidade.addEventListener("submit", async function (evento) {

    evento.preventDefault();

    const dados =
        new FormData(formMensalidade);

    let url;

    if (mensalidadeId.value) {

        url =
            "backend/config%20Mensalidades/actualizar_mensalidade.php";

    } else {

        url =
            "backend/config%20Mensalidades/cadastrar_mensalidade.php";

    }


    try {

        const resposta =
            await fetch(url, {

                method: "POST",

                body: dados

            });


        const resultado =
            await resposta.json();


        if (resultado.sucesso) {

            alert(resultado.mensagem);

            fecharModalMensalidade();

            location.reload();

        } else {

            alert(resultado.mensagem);

        }

    } catch (erro) {

        console.error(erro);

        alert(
            "Ocorreu um erro ao comunicar com o servidor."
        );

    }

});


// =====================================================
// ACTUALIZAR MENSALIDADE
// =====================================================

const botoesEditar =
    document.querySelectorAll(".btn-edit");


botoesEditar.forEach(function (botao) {

    botao.addEventListener("click", async function () {

        const id =
            this.dataset.id;


        try {

            const resposta =
                await fetch(
                    "backend/config%20Mensalidades/buscar_mensalidade.php?id=" + id
                );


            const resultado =
                await resposta.json();


            if (!resultado.sucesso) {

                alert(resultado.mensagem);

                return;

            }


            const dados =
                resultado.dados;


            mensalidadeId.value =
                dados.id;


            estudante.value =
                dados.estudante_id;


            curso.value =
                dados.curso_id;


            document.getElementById("anoLetivo").value =
                dados.ano_letivo_id;


            document.getElementById("mes").value =
                dados.mes;


            document.getElementById("valor").value =
                Number(dados.valor).toLocaleString('pt-PT', {
                    minimumFractionDigits: 2
                }) + " MT";


            document.getElementById("valorReal").value =
                dados.valor;


            document.getElementById("dataVencimento").value =
                dados.data_vencimento;


            tituloModal.textContent =
                "Actualizar mensalidade";


            btnSalvarMensalidade.textContent =
                "Actualizar mensalidade";


            modalMensalidade.classList.add("active");


        } catch (erro) {

            console.error(erro);

            alert(
                "Não foi possível carregar os dados da mensalidade."
            );

        }

    });

});


// =====================================================
// ELIMINAR MENSALIDADE
// =====================================================

const botoesEliminar =
    document.querySelectorAll(".btn-delete");


botoesEliminar.forEach(function (botao) {

    botao.addEventListener("click", async function () {

        const id =
            this.dataset.id;


        const confirmar =
            confirm(
                "Tem certeza que deseja eliminar esta mensalidade?"
            );


        if (!confirmar) {

            return;

        }


        const dados =
            new FormData();

        dados.append("id", id);


        try {

            const resposta =
                await fetch(
                    "backend/config%20Mensalidades/eliminar_mensalidade.php",
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
                "Ocorreu um erro ao eliminar a mensalidade."
            );

        }

    });

});


// =====================================================
// PESQUISA E FILTRO
// =====================================================

pesquisaMensalidade.addEventListener(
    "input",
    aplicarFiltros
);


filtroEstado.addEventListener(
    "change",
    aplicarFiltros
);


function aplicarFiltros() {

    const texto =
        pesquisaMensalidade.value
        .toLowerCase()
        .trim();


    const estado =
        filtroEstado.value
        .toLowerCase();


    const linhas =
        tabelaMensalidades.querySelectorAll(
            "tbody tr"
        );


    linhas.forEach(function (linha) {

        const textoLinha =
            linha.textContent.toLowerCase();


        const status = linha.querySelector(".status");

        let estadoLinha = "";
        if (status) {
            estadoLinha = status.textContent.toLowerCase().trim();
        }
        const correspondeTexto = textoLinha.includes(texto);
        const correspondeEstado = estado === "" || estadoLinha === estado;

        if (
            correspondeTexto &&
            correspondeEstado
        ) {
            linha.style.display = "";
        } else {
            linha.style.display = "none";
        }
    });
}