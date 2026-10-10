<?php $paginaAtual = basename($_SERVER['PHP_SELF']); ?>

<nav class="portal-nav">
    <a href="portal_estudante.php" class="<?= $paginaAtual === 'portal_estudante.php' ? 'ativo' : '' ?>">
        Situação financeira
    </a>
    <a href="extrato_estudante.php" class="<?= $paginaAtual === 'extrato_estudante.php' ? 'ativo' : '' ?>">
        Extrato em PDF
    </a>
    <a href="alterar_senha.php" class="<?= $paginaAtual === 'alterar_senha.php' ? 'ativo' : '' ?>">
        Alterar senha
    </a>
</nav>