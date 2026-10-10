<?php
/** @var array $estudante */
// Indica que a avriavel  $estudante já existe (vindo da página que o incluir)
?>
<header class="portal-header">
    <div class="logo">
        Instituto <span>Foco</span>
    </div>

    <div class="portal-usuario">
        <div class="avatar">
            <?= strtoupper(substr($estudante['nome_completo'], 0, 1)) ?>
        </div>
        <div class="portal-usuario-info">
            <strong><?= htmlspecialchars($estudante['nome_completo']) ?></strong>
            <span><?= htmlspecialchars($estudante['codigo_estudante']) ?></span>
        </div>
        <a href="logout.php" class="btn-sair">Sair</a>
    </div>
</header>