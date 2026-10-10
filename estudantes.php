<?php
require_once 'backend/conexao.php';

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
    LEFT JOIN cursos c
        ON e.curso_id = c.id
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$estudantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sqlCursos = "
    SELECT id, nome
    FROM cursos
    WHERE estado = 'ativo'
    ORDER BY nome
";

$stmtCursos = $pdo->prepare($sqlCursos);
$stmtCursos->execute();
$cursos = $stmtCursos->fetchAll(PDO::FETCH_ASSOC);

include 'componentes/header.php';
?>

<div class="layout">

    <?php include 'componentes/menu.php'; ?>

    <main class="conteudo">

        <link rel="stylesheet" href="css/estudantes.css">

        <div class="page-header">
            <div>
                <h1>Estudantes</h1>
                <p>Gerencie os estudantes cadastrados no Instituto FOCO.</p>
            </div>

            <button class="btn-primary" id="btnNovoEstudante">
                + Novo estudante
            </button>
        </div>

        <div class="content-card">

            <div class="search-container">
                <input
                    type="text"
                    id="pesquisaEstudante"
                    placeholder="Pesquisar por nome ou código..."
                >
            </div>

            <div class="table-container">
                <table id="table-container">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nome completo</th>
                            <th>Curso</th>
                            <th>Nível</th>
                            <th>Contacto</th>
                            <th>Estado</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (count($estudantes) > 0){
                            foreach ($estudantes as $estudante){    ?>
                                <tr>
                                    <td><?= htmlspecialchars($estudante['codigo_estudante']) ?></td>
                                    <td><?= htmlspecialchars($estudante['nome_completo']) ?></td>
                                    <td><?= htmlspecialchars($estudante['curso'] ?? 'Não definido') ?></td>
                                    <td>
                                        <?php
                                        switch ($estudante['nivel']) {
                                            case 1:
                                                echo 'CV3 - 1º Ano';
                                                break;
                                            case 2:
                                                echo 'CV4 - 2º Ano';
                                                break;
                                            case 3:
                                                echo 'CV5 - 3º Ano';
                                                break;
                                            default:
                                                echo 'Não definido';
                                        }
                                        ?>
                                    </td>
                                    <td><?= htmlspecialchars($estudante['contacto']) ?></td>
                                    <td>
                                        <?php if ($estudante['estado'] === 'ativo'): ?>
                                            <span class="status ativo">Ativo</span>
                                        <?php else: ?>
                                            <span class="status inativo">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <button class="btn-edit" data-id="<?= $estudante['id'] ?>">Actualizar</button>
                                            <button class="btn-delete" data-id="<?= $estudante['id'] ?>">Eliminar</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php }
                        }else { ?>
                            <tr>
                                <td colspan="7" class="empty-state">Nenhum estudante cadastrado.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>

    
        <div class="modal" id="modalEstudante">
            <div class="modal-content">

                <div class="modal-header">
                    <div>
                        <h2 id="tituloModal">Novo estudante</h2>
                        <p id="descricaoModal">Preencha os dados do estudante.</p>
                    </div>
                    <button class="modal-close" id="fecharModal" type="button">&times;</button>
                </div>

                <form id="formEstudante" method="POST" action="cadastrar_estudante.php">
                    <input type="hidden" id="estudanteId" name="id">

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="codigo">Código do estudante</label>
                            <input type="text" id="codigo" name="codigo_estudante" placeholder="Ex: EST2026001" required>
                        </div>

                        <div class="form-group">
                            <label for="nome">Nome completo</label>
                            <input type="text" id="nome" name="nome_completo" placeholder="Nome do estudante" required>
                        </div>

                        <div class="form-group">
                            <label for="contacto">Contacto</label>
                            <input type="text" id="contacto" name="contacto" placeholder="Ex: 84 000 0000">
                        </div>

                        <div class="form-group">
                            <label for="curso">Curso</label>
                            <select id="curso" name="curso_id" required>
                                <option value="">Selecionar curso</option>
                                <?php foreach ($cursos as $curso){ ?>
                                    <option value="<?= $curso['id'] ?>"><?= htmlspecialchars($curso['nome']) ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="nivel">Nível</label>
                            <select id="nivel" name="ano" required>
                                <option value="">Selecionar nível</option>
                                <option value="1">CV3 - 1º Ano</option>
                                <option value="2">CV4 - 2º Ano</option>
                                <option value="3">CV5 - 3º Ano</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" id="cancelarModal">Cancelar</button>
                        <button type="submit" class="btn-primary" id="btnSalvarEstudante">Cadastrar estudante</button>
                    </div>
                </form>

            </div>
        </div>

    </main>

</div>

<script src="JS/estudantes.js"></script>
<?php include 'componentes/footer.php'; ?>