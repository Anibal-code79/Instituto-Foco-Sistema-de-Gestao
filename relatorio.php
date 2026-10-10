<?php
require_once 'backend/conexao.php';

// Totais gerais
$totalEstudantes = $pdo->query("SELECT COUNT(*) FROM estudantes WHERE estado = 'ativo'")->fetchColumn();

$totalMensalidades = $pdo->query("SELECT COUNT(*) FROM mensalidades")->fetchColumn();

$totalPago = $pdo->query("SELECT COALESCE(SUM(valor_pago), 0) FROM pagamentos")->fetchColumn();

$totalDevido = $pdo->query("SELECT COALESCE(SUM(valor), 0) FROM mensalidades")->fetchColumn();

$totalPendente = $totalDevido - $totalPago;

// Mensalidades por estado
$sql = "
    SELECT estado, COUNT(*) AS total
    FROM mensalidades
    GROUP BY estado
";
$porEstado = $pdo->query($sql)->fetchAll(PDO::FETCH_KEY_PAIR);

// Pagamentos recentes (últimos 15)
$sql = "
    SELECT
        p.referencia,
        p.valor_pago,
        p.data_pagamento,
        e.nome_completo,
        e.codigo_estudante
    FROM pagamentos p
    INNER JOIN mensalidades m ON p.mensalidade_id = m.id
    INNER JOIN estudantes e ON m.estudante_id = e.id
    ORDER BY p.data_pagamento DESC
    LIMIT 15
";
$pagamentosRecentes = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Total recebido hoje
$sql = "
    SELECT COALESCE(SUM(valor_pago), 0)
    FROM pagamentos
    WHERE DATE(data_pagamento) = CURDATE()
";
$totalHoje = $pdo->query($sql)->fetchColumn();

// Total recebido este mês
$sql = "
    SELECT COALESCE(SUM(valor_pago), 0)
    FROM pagamentos
    WHERE MONTH(data_pagamento) = MONTH(CURDATE())
    AND YEAR(data_pagamento) = YEAR(CURDATE())
";
$totalMes = $pdo->query($sql)->fetchColumn();

// Estudantes com mensalidades pendentes
$sql = "
    SELECT
        e.nome_completo,
        e.codigo_estudante,
        COUNT(m.id) AS mensalidades_pendentes,
        SUM(m.valor) AS total_pendente
    FROM mensalidades m
    INNER JOIN estudantes e ON m.estudante_id = e.id
    WHERE m.estado != 'paga'
    GROUP BY e.id
    ORDER BY total_pendente DESC
    LIMIT 10
";
$devedores = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'componentes/header.php'; ?>

<div class="layout">

    <?php include 'componentes/menu.php'; ?>

    <main class="conteudo">

        <div class="pagina-cabecalho">
            <h1>Relatório</h1>
            <p>Resumo dos movimentos financeiros do Instituto FOCO</p>
        </div>

        <!-- CARDS DE RESUMO -->
        <section class="cards">

            <div class="card">
                <h3>Estudantes ativos</h3>
                <strong><?= $totalEstudantes ?></strong>
                <p>Registados no sistema</p>
            </div>

            <div class="card card-verde">
                <h3>Total recebido</h3>
                <strong><?= number_format($totalPago, 2, ',', '.') ?> MT</strong>
                <p>Soma de todos os pagamentos</p>
            </div>

            <div class="card">
                <h3>Total pendente</h3>
                <strong><?= number_format($totalPendente, 2, ',', '.') ?> MT</strong>
                <p>Ainda por receber</p>
            </div>

        </section>

        <!-- RECEBIDO HOJE / MÊS -->
        <section class="cards">

            <div class="card card-verde">
                <h3>Recebido hoje</h3>
                <strong><?= number_format($totalHoje, 2, ',', '.') ?> MT</strong>
                <p><?= date('d/m/Y') ?></p>
            </div>

            <div class="card card-verde">
                <h3>Recebido este mês</h3>
                <strong><?= number_format($totalMes, 2, ',', '.') ?> MT</strong>
                <p><?= date('F/Y') ?></p>
            </div>

            <div class="card">
                <h3>Mensalidades registadas</h3>
                <strong><?= $totalMensalidades ?></strong>
                <p>
                    <?= $porEstado['paga'] ?? 0 ?> pagas ·
                    <?= $porEstado['pendente'] ?? 0 ?> pendentes ·
                    <?= $porEstado['parcial'] ?? 0 ?> parciais
                </p>
            </div>

        </section>

        <!-- ÚLTIMOS PAGAMENTOS -->
        <section class="area-tabela">
            <div class="tabela-cabecalho">
                <h2>Últimos pagamentos</h2>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Estudante</th>
                        <th>Referência</th>
                        <th>Valor</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($pagamentosRecentes) > 0): ?>
                        <?php foreach ($pagamentosRecentes as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['nome_completo']) ?> (<?= htmlspecialchars($p['codigo_estudante']) ?>)</td>
                                <td><?= htmlspecialchars($p['referencia']) ?></td>
                                <td><?= number_format($p['valor_pago'], 2, ',', '.') ?> MT</td>
                                <td><?= date('d/m/Y H:i', strtotime($p['data_pagamento'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="empty-state">Ainda não há pagamentos registados.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section><br><br><br>


        <!-- ESTUDANTES COM MAIS DÍVIDA -->
        <section class="area-tabela">
            <div class="tabela-cabecalho">
                <h2>Estudantes com mensalidades pendentes</h2>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Estudante</th>
                        <th>Código</th>
                        <th>Mensalidades pendentes</th>
                        <th>Total pendente</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($devedores) > 0): ?>
                        <?php foreach ($devedores as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars($d['nome_completo']) ?></td>
                                <td><?= htmlspecialchars($d['codigo_estudante']) ?></td>
                                <td><?= $d['mensalidades_pendentes'] ?></td>
                                <td><?= number_format($d['total_pendente'], 2, ',', '.') ?> MT</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="empty-state">Nenhum estudante com pendências.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

    </main>

</div>

<?php include 'componentes/footer.php'; ?>