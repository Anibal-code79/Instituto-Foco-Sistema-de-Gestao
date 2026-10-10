const btnNovoPagamento =
    document.getElementById("btnNovoPagamento");

const modalPagamento =
    document.getElementById("modalPagamento");

const fecharModal =
    document.getElementById("fecharModal");

const cancelarModal =
    document.getElementById("cancelarModal");

const formPagamento =
    document.getElementById("formPagamento");

const pagamentoId =
    document.getElementById("pagamentoId");

const btnSalvarPagamento =
    document.getElementById("btnSalvarPagamento");

const tituloModal =
    modalPagamento.querySelector("h2");

const mensalidade =
    document.getElementById("mensalidade");

const pesquisaPagamento =
    document.getElementById("pesquisaPagamento");

const tabelaPagamentos =
    document.getElementById("tabelaPagamentos");


// =====================================================
// NOVO PAGAMENTO
// =====================================================

btnNovoPagamento.addEventListener("click", function () {

    formPagamento.reset();

    pagamentoId.value = "";

    document.getElementById("valorPago").value = "";
    document.getElementById("valorPagoReal").value = "";

    tituloModal.textContent =
        "Novo pagamento";

    btnSalvarPagamento.textContent =
        "Registar pagamento";

    modalPagamento.classList.add("active");

});


// =====================================================
// PREENCHER VALOR AUTOMATICAMENTE
// =====================================================

mensalidade.addEventListener("change", function () {

    const opcao = this.options[this.selectedIndex];
    const valor = opcao.dataset.valor;

    const campoValorExibicao = document.getElementById("valorPago");
    const campoValorReal = document.getElementById("valorPagoReal");

    if (!valor) {
        campoValorExibicao.value = "";
        campoValorReal.value = "";
        return;
    }

    campoValorExibicao.value =
        Number(valor).toLocaleString('pt-PT', {
            minimumFractionDigits: 2
        }) + " MT";

    campoValorReal.value = valor;

});


// =====================================================
// FECHAR MODAL
// =====================================================

function fecharModalPagamento() {

    modalPagamento.classList.remove("active");

}


fecharModal.addEventListener("click", function () {

    fecharModalPagamento();

});


cancelarModal.addEventListener("click", function () {

    fecharModalPagamento();

});


modalPagamento.addEventListener("click", function (evento) {

    if (evento.target === modalPagamento) {

        fecharModalPagamento();

    }

});


document.addEventListener("keydown", function (evento) {

    if (evento.key === "Escape") {

        fecharModalPagamento();

    }

});


// =====================================================
// ENVIAR FORMULÁRIO
// =====================================================

formPagamento.addEventListener("submit", async function (evento) {

    evento.preventDefault();

    const dados =
        new FormData(formPagamento);

    let url;

    if (pagamentoId.value) {

        url = "backend/config%20pagamentos/actualizar_pagamento.php";

    } else {

        url = "backend/config%20pagamentos/cadastrar_pagamentos.php";

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

            fecharModalPagamento();

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
// ACTUALIZAR PAGAMENTO
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
                    "backend/config%20pagamentos/buscar_pagamento.php?id=" + id
                );


            const resultado =
                await resposta.json();


            if (!resultado.sucesso) {

                alert(resultado.mensagem);

                return;

            }


            const dados =
                resultado.dados;


            pagamentoId.value =
                dados.id;


            // A mensalidade original pode já não estar na lista
            // (porque a lista só mostra "pendentes"), então
            // adicionamos temporariamente essa opção ao select
            // para conseguir seleccioná-la ao editar.

            let opcaoExistente =
                mensalidade.querySelector(
                    'option[value="' + dados.mensalidade_id + '"]'
                );

            if (!opcaoExistente) {

                opcaoExistente = document.createElement("option");
                opcaoExistente.value = dados.mensalidade_id;
                opcaoExistente.textContent = "Mensalidade #" + dados.mensalidade_id;
                opcaoExistente.dataset.valor = dados.valor_pago;

                mensalidade.appendChild(opcaoExistente);

            }

            mensalidade.value = dados.mensalidade_id;


            document.getElementById("valorPago").value =
                Number(dados.valor_pago).toLocaleString('pt-PT', {
                    minimumFractionDigits: 2
                }) + " MT";

            document.getElementById("valorPagoReal").value =
                dados.valor_pago;


            document.getElementById("metodoPagamento").value =
                dados.metodo_pagamento;


            document.getElementById("referencia").value =
                dados.referencia;


            tituloModal.textContent =
                "Actualizar pagamento";


            btnSalvarPagamento.textContent =
                "Actualizar pagamento";


            modalPagamento.classList.add("active");


        } catch (erro) {

            console.error(erro);

            alert(
                "Não foi possível carregar os dados do pagamento."
            );

        }

    });

});


// =====================================================
// ELIMINAR PAGAMENTO
// =====================================================

const botoesEliminar =
    document.querySelectorAll(".btn-delete");


botoesEliminar.forEach(function (botao) {

    botao.addEventListener("click", async function () {

        const id =
            this.dataset.id;


        const confirmar =
            confirm(
                "Tem certeza que deseja eliminar este pagamento?"
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
                    "backend/config%20pagamentos/eliminar_pagamento.php",
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
                "Ocorreu um erro ao eliminar o pagamento."
            );

        }

    });

});


// =====================================================
// PESQUISA
// =====================================================

pesquisaPagamento.addEventListener(
    "input",
    aplicarPesquisa
);


function aplicarPesquisa() {

    const texto =
        pesquisaPagamento.value
        .toLowerCase()
        .trim();


    const linhas =
        tabelaPagamentos.querySelectorAll(
            "tbody tr"
        );


    linhas.forEach(function (linha) {

        const textoLinha =
            linha.textContent.toLowerCase();


        if (textoLinha.includes(texto)) {

            linha.style.display = "";

        } else {

            linha.style.display = "none";

        }

    });

}