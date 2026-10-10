<?php
session_start();
require_once 'backend/conexao.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'estudante') {
    header('Location: login.php');
    exit;
}
// Buscar dados do estudante para o header
$stmt = $pdo->prepare("SELECT nome_completo, codigo_estudante FROM estudantes WHERE usuario_id = :usuario_id");
$stmt->execute(['usuario_id' => $_SESSION['usuario_id']]);
$estudante = $stmt->fetch(PDO::FETCH_ASSOC);

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $senhaAtual = $_POST['senha_atual'] ?? '';
    $novaSenha = $_POST['nova_senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';

    // Buscar senha atual do usuário
    $stmt = $pdo->prepare("SELECT senha FROM usuarios WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['usuario_id']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!password_verify($senhaAtual, $usuario['senha'])) {
        $erro = 'A senha atual está incorreta.';

    } elseif ($novaSenha !== $confirmarSenha) {
        $erro = 'A nova senha e a confirmação não coincidem.';

    } else {

        $novoHash = password_hash($novaSenha, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE id = :id");
        $stmt->execute([
            'senha' => $novoHash,
            'id' => $_SESSION['usuario_id']
        ]);
        $sucesso = 'Senha alterada com sucesso!';
    }
}

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar senha - Instituto Foco</title>
       <link rel="stylesheet" href="css/portal_estudante.css">
    <link rel="stylesheet" href="css/alterar_senha.css">
</head>
<body>

    <?php include 'componentes/portal_header.php'; ?>
    <?php include 'componentes/portal_nav.php'; ?>

    <main class="portal-conteudo">

    <div class="senha-container">
        <div class="senha-card">

            <div class="senha-icone">🔒</div>

            <h1>Alterar senha</h1>
            <p>Por segurança, escolhe uma nova senha diferente da inicial que a instituição te forneceu.</p>

            <?php if ($erro): ?>
                <div class="alerta-erro"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <?php if ($sucesso): ?>
                <div class="alerta-sucesso"><?= htmlspecialchars($sucesso) ?></div>
            <?php endif; ?>

            <form method="POST" action="alterar_senha.php">

                <div class="form-group">
                    <label for="senha_atual">Senha atual</label>
                    <input type="password" id="senha_atual" name="senha_atual" required>
                </div>

                <div class="form-group">
                    <label for="nova_senha">Nova senha</label>
                    <input type="password" id="nova_senha" name="nova_senha" minlength="6" required>
                    <p class="senha-dica">Mínimo de 6 caracteres.</p>
                </div>

                <div class="form-group">
                    <label for="confirmar_senha">Confirmar nova senha</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" minlength="6" required>
                </div>

                <button type="submit" class="btn-primary">Guardar nova senha</button>

            </form>

        </div>
    </div>

</main>

</body>
</html>