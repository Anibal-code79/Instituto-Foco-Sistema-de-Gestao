<?php

require_once 'backend/conexao.php';

$sql = "
    SELECT
        m.id,
        m.mes,
        m.valor,
        m.data_vencimento,
        m.estado,
        e.codigo_estudante,
        e.nome_completo,
        c.nome AS curso,
        a.ano AS ano_letivo
    FROM mensalidades m

    INNER JOIN estudantes e
        ON m.estudante_id = e.id

    INNER JOIN cursos c
        ON m.curso_id = c.id

    INNER JOIN ano_letivo a
        ON m.ano_letivo_id = a.id

    WHERE m.estado = 'pendente'

    ORDER BY m.id DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$mensalidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

$meses = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
    5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
    9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];

$metodosPagamento = [
    'deposito_bancario' => 'Depósito bancário',
    'mpesa' => 'M-Pesa',
    'emola' => 'E-Mola',
    'numerario' => 'Numerário'
];

$sql = "
    SELECT
        p.id,
        p.mensalidade_id,
        p.valor_pago,
        p.metodo_pagamento,
        p.data_pagamento,
        p.referencia,

        m.mes,
        m.valor AS valor_mensalidade,

        e.codigo_estudante,
        e.nome_completo

    FROM pagamentos p

    INNER JOIN mensalidades m
        ON p.mensalidade_id = m.id

    INNER JOIN estudantes e
        ON m.estudante_id = e.id

    ORDER BY p.id DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$pagamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php require_once 'componentes/header.php'; ?>

<div class="layout">

    <?php require_once 'componentes/menu.php'; ?>

    <main class="conteudo">

        <!-- CABEÇALHO -->
        <div class="page-header">
            <div>
                <h1>Pagamentos</h1>
                <p>
                    Registe e acompanhe os pagamentos dos estudantes.
                </p>
            </div>
            <button
                class="btn-primary"
                id="btnNovoPagamento"
            >
                + Novo pagamento
            </button>

        </div>
        <div class="content-card">
            <div class="search-container">
                <input type="text" id="pesquisaPagamento" placeholder="Pesquisar por nome, código ou referência...">
            </div>
            <div class="tabelaPagamentos">
                <table id="tabelaPagamentos">
                    <thead>
                        <tr>
                            <th>Referência</th>
                            <th>Estudante</th>
                            <th>Mês</th>
                            <th>Valor pago</th>
                            <th>Método</th>
                            <th>Data do pagamento</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($pagamentos) > 0): ?>
                            <?php foreach ($pagamentos as $pagamento): ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars($pagamento['referencia']) ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($pagamento['codigo_estudante']) ?>
                                        -
                                        <?= htmlspecialchars($pagamento['nome_completo']) ?>
                                    </td>

                                    <td>
                                        <?= $meses[$pagamento['mes']] ?? '-' ?>
                                    </td>
                                    <td>
                                        <?= number_format($pagamento['valor_pago'],2,',','.') ?>
                                        MT
                                    </td>
                                    <td>
                                        <?= $metodosPagamento[$pagamento['metodo_pagamento']] ?? '-' ?>
                                    </td>
                                    <td>
                                        <?= date('d/m/Y H:i',strtotime($pagamento['data_pagamento'])) ?>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <button
                                                class="btn-edit"
                                                data-id="<?= $pagamento['id'] ?>"
                                            >
                                                Actualizar
                                            </button>
                                            <button
                                                class="btn-delete"
                                                data-id="<?= $pagamento['id'] ?>"
                                            >
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td
                                    colspan="7"
                                    class="empty-state"
                                >
                                    Nenhum pagamento registado.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>


<!-- MODAL PAGAMENTO -->
<div
    class="modal"
    id="modalPagamento"
>

    <div class="modal-content">


        <div class="modal-header">

            <div>

                <h2>Novo pagamento</h2>

                <p>
                    Registe o pagamento de uma mensalidade.
                </p>

            </div>


            <button
                class="modal-close"
                id="fecharModal"
            >
                &times;
            </button>

        </div>


        <form id="formPagamento">

            <input
                type="hidden"
                id="pagamentoId"
                name="id"
            >


            <div class="form-grid">


                <!-- MENSALIDADE -->

                <div class="form-group">

                    <label for="mensalidade">
                        Mensalidade
                    </label>

                    <select
                        id="mensalidade"
                        name="mensalidade_id"
                        required
                    >

                        <option value="">
                            Selecionar mensalidade
                        </option>


                        <?php foreach ($mensalidades as $mensalidade): ?>

                            <option
                                value="<?= $mensalidade['id'] ?>"
                                data-valor="<?= $mensalidade['valor'] ?>"
                            >

                                <?= htmlspecialchars($mensalidade['codigo_estudante']) ?>
                                -
                                <?= htmlspecialchars($mensalidade['nome_completo']) ?>
                                -
                                <?= $meses[$mensalidade['mes']] ?? '-' ?>
                                -
                                <?= number_format($mensalidade['valor'], 2, ',', '.') ?> MT

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- VALOR (só leitura, automático) -->

                <div class="form-group">

                    <label for="valorPago">
                        Valor a pagar
                    </label>

                    <input
                        type="text"
                        id="valorPago"
                        readonly
                        placeholder="Selecione a mensalidade"
                    >
                    <input type="hidden" id="valorPagoReal" name="valor_pago">

                </div>


                <!-- MÉTODO DE PAGAMENTO -->

                <div class="form-group">

                    <label for="metodoPagamento">
                        Método de pagamento
                    </label>

                    <select
                        id="metodoPagamento"
                        name="metodo_pagamento"
                        required
                    >
                        <option value="">Selecionar método</option>
                        <?php foreach ($metodosPagamento as $chave => $nome): ?>
                            <option value="<?= $chave ?>"><?= $nome ?></option>
                        <?php endforeach; ?>
                    </select>

                </div>


                <!-- REFERÊNCIA -->

                <div class="form-group">

                    <label for="referencia">
                        Referência
                    </label>

                    <input
                        type="text"
                        id="referencia"
                        name="referencia"
                        placeholder="Ex: PAG-2026-001"
                        maxlength="50"
                        required
                    >

                </div>


            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn-secondary"
                    id="cancelarModal"
                >
                    Cancelar
                </button>


                <button
                    type="submit"
                    class="btn-primary"
                    id="btnSalvarPagamento"
                >
                    Registar pagamento
                </button>

            </div>

        </form>

    </div>

</div>
<script src="JS/pagamentos.js"></script>
<?php require_once 'componentes/footer.php'; ?>