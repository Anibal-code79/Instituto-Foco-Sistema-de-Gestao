<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Instituto Foco - Sistema Financeiro</title>

    <link rel="stylesheet" href="css/mensalidades.css">
<link rel="stylesheet" href="css/cursos.css">
<link rel="stylesheet" href="css/pagamentos.css">
<link rel="stylesheet" href="css/estudantes.css">
<link rel="stylesheet" href="css/style.css">
</head>

<body>

<header class="header">
    <div class="logo">
    <img src="img/logo.jpg" alt="Instituto Foco" class="logo-img">
    Instituto <span>Foco</span>
</div>

    <div class="usuario-logado">
        <span><?= htmlspecialchars($_SESSION['nome_usuario']) ?></span>
        <a href="logout.php" class="btn-sair">Sair</a>
    </div>
</header>