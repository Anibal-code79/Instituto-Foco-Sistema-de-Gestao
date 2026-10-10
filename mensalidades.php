<?php

require_once 'backend/conexao.php';

$sql = "
    SELECT
        e.id,
        e.codigo_estudante,
        e.nome_completo,
        e.curso_id,
        c.nome AS curso
    FROM estudantes e
    LEFT JOIN cursos c
        ON e.curso_id = c.id
    WHERE e.estado = 'ativo'
    ORDER BY e.nome_completo
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$estudantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "
    SELECT id, nome
    FROM cursos
    WHERE estado = 'ativo'
    ORDER BY nome
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "
    SELECT id, ano
    FROM ano_letivo
    WHERE estado = 'ativo'
    ORDER BY ano DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$anosLetivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "
    SELECT
        m.id,
        m.estudante_id,
        m.curso_id,
        m.ano_letivo_id,
        m.mes,
        m.valor,
        m.data_vencimento,
        m.estado,
        e.codigo_estudante,
        e.nome_completo,
        c.nome AS curso,
        a.ano AS ano_letivo
    FROM mensalidades m
    INNER JOIN estudantes e ON m.estudante_id = e.id
    INNER JOIN cursos c ON m.curso_id = c.id
    INNER JOIN ano_letivo a ON m.ano_letivo_id = a.id
    ORDER BY m.id DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$mensalidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php require_once 'componentes/header.php'; ?>

<div class="layout">

    <?php require_once 'componentes/menu.php'; ?>

    <main class="conteudo">

        <div class="page-header">
            <div>
                <h1>Mensalidades</h1>
                <p>Gerencie as mensalidades dos estudantes.</p>
            </div>
            <button class="btn-primary" id="btnNovaMensalidade">
                + Nova mensalidade
            </button>
        </div>

        <div class="content-card">

            <div class="search-container">
                <input
                    type="text"
                    id="pesquisaMensalidade"
                    placeholder="Pesquisar por estudante ou código..."
                >

                <select id="filtroEstado">
                    <option value="">Todos os estados</option>
                    <option value="pendente">Pendente</option>
                    <option value="paga">Paga</option>
                </select>
            </div>

            <div class="tabelaMensalidades">
                <table id="tabelaMensalidades">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Estudante</th>
                            <th>Curso</th>
                            <th>Mês</th>
                            <th>Valor</th>
                            <th>Data_Pagamento</th>
                            <th>Estado</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php if (count($mensalidades) > 0): ?>

                            <?php foreach ($mensalidades as $mensalidade): ?>

                                <tr>
                                    <td><?= htmlspecialchars($mensalidade['codigo_estudante']) ?></td>
                                    <td><?= htmlspecialchars($mensalidade['nome_completo']) ?></td>
                                    <td><?= htmlspecialchars($mensalidade['curso']) ?></td>
                                    <td>
                                        <?php
                                        $meses = [
                                            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março',
                                            4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
                                            7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro',
                                            10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
                                        ];
                                        echo $meses[$mensalidade['mes']] ?? 'Desconhecido';
                                        ?>
                                    </td>
                                    <td><?= number_format($mensalidade['valor'], 2, ',', '.') ?> MT</td>
                                    <td><?= date('d/m/Y', strtotime($mensalidade['data_vencimento'])) ?></td>
                                    <td>
                                        <?php if ($mensalidade['estado'] === 'paga'): ?>
                                            <span class="status paga">Paga</span>
                                        <?php else: ?>
                                            <span class="status pendente">Pendente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <button class="btn-edit" data-id="<?= $mensalidade['id'] ?>">Actualizar</button>
                                            <button class="btn-delete" data-id="<?= $mensalidade['id'] ?>">Eliminar</button>
                                        </div>
                                    </td>
                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="8" class="empty-state">Nenhuma mensalidade registada.</td>
                            </tr>

                        <?php endif; ?>

                    </tbody>
                </table>
            </div>

        </div>

        <!-- MODAL -->
        <div class="modal" id="modalMensalidade">
            <div class="modal-content">

                <div class="modal-header">
                    <div>
                        <h2>Nova mensalidade</h2>
                        <p>Registe uma nova mensalidade.</p>
                    </div>
                    <button class="modal-close" id="fecharModal">&times;</button>
                </div>

                <form id="formMensalidade">
                    <input type="hidden" id="mensalidadeId" name="id">

                    <div class="form-grid">

                        <div class="form-group">
                            <label for="estudante">Estudante</label>
                            <select id="estudante" name="estudante_id" required>
                                <option value="">Selecionar estudante</option>
                                <?php foreach ($estudantes as $estudante): ?>
                                    <option value="<?= $estudante['id'] ?>" data-curso="<?= $estudante['curso_id'] ?>">
                                        <?= htmlspecialchars($estudante['codigo_estudante']) ?> - <?= htmlspecialchars($estudante['nome_completo']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="curso">Curso</label>
                            <select id="curso" name="curso_id" required>
                                <option value="">Selecionar curso</option>
                                <?php foreach ($cursos as $curso): ?>
                                    <option value="<?= $curso['id'] ?>"><?= htmlspecialchars($curso['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="anoLetivo">Ano letivo</label>
                            <select id="anoLetivo" name="ano_letivo_id" required>
                                <option value="">Selecionar ano letivo</option>
                                <?php foreach ($anosLetivos as $anoLetivo): ?>
                                    <option value="<?= $anoLetivo['id'] ?>"><?= htmlspecialchars($anoLetivo['ano']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="mes">Mês</label>
                            <select id="mes" name="mes" required>
                                <option value="">Selecionar mês</option>
                                <option value="1">Janeiro</option>
                                <option value="2">Fevereiro</option>
                                <option value="3">Março</option>
                                <option value="4">Abril</option>
                                <option value="5">Maio</option>
                                <option value="6">Junho</option>
                                <option value="7">Julho</option>
                                <option value="8">Agosto</option>
                                <option value="9">Setembro</option>
                                <option value="10">Outubro</option>
                                <option value="11">Novembro</option>
                                <option value="12">Dezembro</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="valor">Valor</label>
                            <input
                                type="text"
                                id="valor"
                                readonly
                                placeholder="Selecione o estudante e o curso"
                            >
                            <!-- campo oculto que realmente vai no POST -->
                            <input type="hidden" id="valorReal" name="valor">
                        </div>

                        <div class="form-group">
                            <label for="dataVencimento">Data de vencimento</label>
                            <input type="date" id="dataVencimento" name="data_vencimento" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" id="cancelarModal">Cancelar</button>
                        <button type="submit" class="btn-primary" id="btnSalvarMensalidade">Guardar mensalidade</button>
                    </div>
                </form>

            </div>
        </div>

    </main>

</div>

<script src="JS/mensalidades.js"></script>
<?php require_once 'componentes/footer.php'; ?>