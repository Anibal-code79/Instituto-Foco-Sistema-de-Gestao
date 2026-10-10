<?php
session_start();
require_once 'backend/conexao.php';


if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'estudante') {
    header('Location: login.php');
    exit;
}

$sql = "
    SELECT
        e.id,
        e.codigo_estudante,
        e.nome_completo,
        e.contacto,
        e.estado,
        e.ano AS nivel,
        c.nome AS curso
    FROM estudantes e
    LEFT JOIN cursos c ON e.curso_id = c.id
    WHERE e.usuario_id = :usuario_id
";
$stmt = $pdo->prepare($sql);
$stmt->execute(['usuario_id' => $_SESSION['usuario_id']]);
$estudante = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$estudante) {
    die('Não foi encontrado nenhum registo de estudante associado a esta conta. Contacte a secretaria.');
}

// Buscar mensalidades do estudante
$sql = "
    SELECT id, mes, valor, data_vencimento, estado
    FROM mensalidades
    WHERE estudante_id = :estudante_id
    ORDER BY data_vencimento DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute(['estudante_id' => $estudante['id']]);
$mensalidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Totais
$totalDevido = 0;
$totalPago = 0;
$totalPendente = 0;

foreach ($mensalidades as $m) {
    $totalDevido += $m['valor'];
    if ($m['estado'] === 'paga') {
        $totalPago += $m['valor'];
    } else {
        $totalPendente += $m['valor'];
    }
}

// Buscar histórico de pagamentos
// Buscar histórico de pagamentos
$sql = "
    SELECT p.id, p.referencia, p.valor_pago, p.data_pagamento, m.mes
    FROM pagamentos p
    INNER JOIN mensalidades m ON p.mensalidade_id = m.id
    WHERE m.estudante_id = :estudante_id
    ORDER BY p.data_pagamento DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute(['estudante_id' => $estudante['id']]);
$pagamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$meses = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
    5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
    9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];

$niveis = [1 => 'CV3 - 1º Ano', 2 => 'CV4 - 2º Ano', 3 => 'CV5 - 3º Ano'];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal do Estudante - Instituto Foco</title>
    <link rel="stylesheet" href="css/portal_estudante.css">
</head>
<body>

    <?php include 'componentes/portal_header.php'; ?>
    <?php include 'componentes/portal_nav.php'; ?>
    <main class="portal-conteudo">

        <div class="portal-boas-vindas">
            <h1>Olá, <?= htmlspecialchars(explode(' ', $estudante['nome_completo'])[0]) ?> 👋</h1>
            <p>Aqui está a tua situação académica e financeira atualizada.</p>
        </div>

        <!-- INFO ACADÉMICA -->
        <section class="portal-cards">

            <div class="portal-card">
                <span class="portal-card-label">Curso</span>
                <strong><?= htmlspecialchars($estudante['curso'] ?? 'Não definido') ?></strong>
            </div>

            <div class="portal-card">
                <span class="portal-card-label">Nível</span>
                <strong><?= $niveis[$estudante['nivel']] ?? 'Não definido' ?></strong>
            </div>

            <div class="portal-card">
                <span class="portal-card-label">Estado</span>
                <strong class="status <?= $estudante['estado'] ?>"><?= ucfirst($estudante['estado']) ?></strong>
            </div>

        </section>

        <!-- RESUMO FINANCEIRO -->
        <section class="portal-resumo">

            <div class="resumo-item">
                <span class="resumo-label">Total devido</span>
                <strong><?= number_format($totalDevido, 2, ',', '.') ?> MT</strong>
            </div>

            <div class="resumo-item resumo-verde">
                <span class="resumo-label">Total pago</span>
                <strong><?= number_format($totalPago, 2, ',', '.') ?> MT</strong>
            </div>

            <div class="resumo-item resumo-vermelho">
                <span class="resumo-label">Pendente</span>
                <strong><?= number_format($totalPendente, 2, ',', '.') ?> MT</strong>
            </div>

        </section>

        <!-- MENSALIDADES -->
        <section class="portal-tabela-area">
            <h2>As tuas mensalidades</h2>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Mês</th>
                            <th>Valor</th>
                            <th>Data_Pagamento</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($mensalidades) > 0): ?>
                            <?php foreach ($mensalidades as $m): ?>
                                <tr>
                                    <td><?= $meses[$m['mes']] ?? '-' ?></td>
                                    <td><?= number_format($m['valor'], 2, ',', '.') ?> MT</td>
                                    <td><?= date('d/m/Y', strtotime($m['data_vencimento'])) ?></td>
                                    <td>
                                        <?php if ($m['estado'] === 'paga'): ?>
                                            <span class="status paga">Paga</span>
                                        <?php elseif ($m['estado'] === 'parcial'): ?>
                                            <span class="status parcial">Parcial</span>
                                        <?php else: ?>
                                            <span class="status pendente">Pendente</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="empty-state">Ainda não há mensalidades registadas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- HISTÓRICO DE PAGAMENTOS -->
        <section class="portal-tabela-area">
            <h2>Histórico de pagamentos</h2>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Referência</th>
                            <th>Mês pago</th>
                            <th>Valor</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($pagamentos) > 0): ?>
                            <?php foreach ($pagamentos as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p['referencia']) ?></td>
                                    <td><?= $meses[$p['mes']] ?? '-' ?></td>
                                    <td><?= number_format($p['valor_pago'], 2, ',', '.') ?> MT</td>
                                    <td><?= date('d/m/Y', strtotime($p['data_pagamento'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="empty-state">Ainda não há pagamentos registados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </main>

</body>
</html>