<?php include 'componentes/header.php'; ?>
<div class="layout">

    <?php include 'componentes/menu.php'; ?>

    <main class="conteudo">

        <div class="pagina-cabecalho">
            <h1>Dashboard</h1>
            <p>Bem-vindo ao Sistema Financeiro Escolar</p>
        </div>

        <section class="cards">

    <a href="estudantes.php" class="card">
        <h3>Estudantes</h3>
        <strong>2</strong>
        <p>Estudantes registados</p>
    </a>

    <a href="mensalidades.php" class="card">
        <h3>Mensalidades</h3>
        <strong>4</strong>
        <p>Mensalidades registadas</p>
    </a>

    <a href="pagamentos.php" class="card card-verde">
        <h3>Pagamentos</h3>
        <strong>3.000 MT</strong>
        <p>Total recebido</p>
    </a>

</section>

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
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Aires Cossa</td>
                        <td>PAG20260001</td>
                        <td>2.000 MT</td>
                    </tr>
                    <tr>
                        <td>Aires Cossa</td>
                        <td>PAG20260002</td>
                        <td>1.000 MT</td>
                    </tr>
                </tbody>
            </table>
        </section>

    </main>

</div>
<?php include 'componentes/footer.php'; ?>