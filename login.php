<?php
session_start();
require_once 'backend/conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nomeUsuario = trim($_POST['nome_usuario']);
    $senha = $_POST['senha'];

    if ($nomeUsuario === '' || $senha === '') {
        $erro = 'Preencha o nome de usuário e a senha.';
    } else {
        $sql = "
            SELECT id, nome_usuario, senha, perfil, estado
            FROM usuarios
            WHERE nome_usuario = :nome_usuario
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['nome_usuario' => $nomeUsuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            $erro = 'Usuário ou senha incorretos.';

        } elseif (!password_verify($senha, $usuario['senha'])) {
            $erro = 'Usuário ou senha incorretos.';
        } else {

            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nome_usuario'] = $usuario['nome_usuario'];
            $_SESSION['perfil'] = $usuario['perfil'];

            if ($usuario['perfil'] === 'estudante') {
                header('Location: portal_estudante.php');
            } else {
                header('Location: index.php');
            }
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Login - Instituto Foco</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

    <div class="login-container">

        <div class="login-box">

            <img src="img/logo.jpg" alt="Instituto Foco" class="logo-login">

            <h1>Instituto Foco</h1>
            <p class="subtitulo">Ou Foco ou Frustração, você escolhe!</p>

            <?php if ($erro): ?>
                <div class="alerta-erro">
                    <?= htmlspecialchars($erro) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">

                <div class="form-group">
                    <label for="nome_usuario">Usuário</label>
                    <input
                        type="text"
                        id="nome_usuario"
                        name="nome_usuario"
                        placeholder="Digite o seu usuário"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        placeholder="Digite a sua senha"
                        required
                    >
                </div>

                <button type="submit" class="btn-primary">
                    Entrar
                </button>

            </form>

        </div>

    </div>

</body>
</html>