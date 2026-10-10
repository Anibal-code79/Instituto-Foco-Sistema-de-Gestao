<?php
session_start();
require_once 'backend/conexao.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'estudante') {
    header('Location: login.php');
    exit;
}

$sql = "
    SELECT e.codigo_estudante, e.nome_completo, e.ano AS nivel, c.nome AS curso
    FROM estudantes e
    LEFT JOIN cursos c ON e.curso_id = c.id
    WHERE e.usuario_id = :usuario_id
";
$stmt = $pdo->prepare($sql);
$stmt->execute(['usuario_id' => $_SESSION['usuario_id']]);
$estudante = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = "
    SELECT mes, valor, data_vencimento, estado
    FROM mensalidades
    WHERE estudante_id = (SELECT id FROM estudantes WHERE usuario_id = :usuario_id)
    ORDER BY data_vencimento
";
$stmt = $pdo->prepare($sql);
$stmt->execute(['usuario_id' => $_SESSION['usuario_id']]);
$mensalidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

$meses = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
    5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
    9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];
$niveis = [1 => 'CV3 - 1º Ano', 2 => 'CV4 - 2º Ano', 3 => 'CV5 - 3º Ano'];

$totalDevido = array_sum(array_column($mensalidades, 'valor'));
$totalPago = array_sum(array_map(fn($m) => $m['estado'] === 'paga' ? $m['valor'] : 0, $mensalidades));
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Extrato Financeiro - <?= htmlspecialchars($estudante['nome_completo']) ?></title>
    <link rel="stylesheet" href="css/portal_estudante.css">
    <link rel="stylesheet" href="css/extrato.css">
</head>
<body>

    <div class="extrato-topo no-print">
        <a href="portal_estudante.php" class="btn-secondary">← Voltar</a>
        <button onclick="window.print()" class="btn-primary">🖨 Gerar PDF</button>
    </div>

    <div class="extrato-folha">

        <div class="extrato-cabecalho">
            <h1>Instituto Foco</h1>
            <p>Extrato financeiro do estudante</p>
        </div>

        <table class="extrato-info">
            <tr><td><strong>Nome:</strong></td><td><?= htmlspecialchars($estudante['nome_completo']) ?></td></tr>
            <tr><td><strong>Código:</strong></td><td><?= htmlspecialchars($estudante['codigo_estudante']) ?></td></tr>
            <tr><td><strong>Curso:</strong></td><td><?= htmlspecialchars($estudante['curso'] ?? '-') ?></td></tr>
            <tr><td><strong>Nível:</strong></td><td><?= $niveis[$estudante['nivel']] ?? '-' ?></td></tr>
            <tr><td><strong>Data de emissão:</strong></td><td><?= date('d/m/Y H:i') ?></td></tr>
        </table>

        <table class="extrato-tabela">
            <thead>
                <tr><th>Mês</th><th>Valor</th><th>Vencimento</th><th>Estado</th></tr>
            </thead>
            <tbody>
                <?php foreach ($mensalidades as $m): ?>
                    <tr>
                        <td><?= $meses[$m['mes']] ?? '-' ?></td>
                        <td><?= number_format($m['valor'], 2, ',', '.') ?> MT</td>
                        <td><?= date('d/m/Y', strtotime($m['data_vencimento'])) ?></td>
                        <td><?= ucfirst($m['estado']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <table class="extrato-totais">
            <tr><td>Total devido:</td><td><?= number_format($totalDevido, 2, ',', '.') ?> MT</td></tr>
            <tr><td>Total pago:</td><td><?= number_format($totalPago, 2, ',', '.') ?> MT</td></tr>
            <tr><td><strong>Saldo pendente:</strong></td><td><strong><?= number_format($totalDevido - $totalPago, 2, ',', '.') ?> MT</strong></td></tr>
        </table>

        <p class="extrato-rodape">Documento gerado automaticamente pelo sistema. Instituto Foco - Educação Responsável.</p>

    </div>

</body>
</html>