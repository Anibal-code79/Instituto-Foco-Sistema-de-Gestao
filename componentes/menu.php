<?php $paginaAtual = basename($_SERVER['PHP_SELF']); ?>

<aside class="sidebar">

    <div class="menu-titulo">
        MENU
    </div>

    <nav class="menu">
        <a href="index.php" class="menu-link <?= $paginaAtual === 'index.php' ? 'ativo' : '' ?>">
            Dashboard
        </a>

        <a href="estudantes.php" class="menu-link <?= $paginaAtual === 'estudantes.php' ? 'ativo' : '' ?>">
            Estudantes
        </a>

        <a href="mensalidades.php" class="menu-link <?= $paginaAtual === 'mensalidades.php' ? 'ativo' : '' ?>">
            Mensalidades
        </a>

        <a href="pagamentos.php" class="menu-link <?= $paginaAtual === 'pagamentos.php' ? 'ativo' : '' ?>">
            Pagamentos
        </a>

        <a href="cursos.php" class="menu-link <?= $paginaAtual === 'cursos.php' ? 'ativo' : '' ?>">
            Cursos
        </a>

        <a href="relatorio.php" class="menu-link <?= $paginaAtual === 'relatorio.php' ? 'ativo' : '' ?>">
            Relatório
        </a>
    </nav>

</aside>