<?php

require_once 'backend/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $acao = $_POST['acao'] ?? '';
    if ($acao === 'cadastrar') {

        $nome = trim($_POST['nome'] ?? '');
        $mensalidade = $_POST['mensalidade'] ?? '';
        $estado = $_POST['estado'] ?? 'ativo';

        if ($nome === '') {

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Informe o nome do curso.'
            ]);

            exit;
        }

        if ($mensalidade === '' || !is_numeric($mensalidade) || $mensalidade < 0) {

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Indique um valor de mensalidade válido.'
            ]);

            exit;
        }

        try {

            $sql = "
                INSERT INTO cursos (nome, mensalidade, estado)
                VALUES (?, ?, ?)
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                $nome,
                $mensalidade,
                $estado
            ]);

            echo json_encode([
                'sucesso' => true,
                'mensagem' => 'Curso cadastrado com sucesso.'
            ]);

        } catch (PDOException $e) {

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Não foi possível cadastrar o curso.'
            ]);
        }

        exit;
    }


    // =================================================
    // ACTUALIZAR
    // =================================================

    if ($acao === 'actualizar') {

        $id = $_POST['id'] ?? '';
        $nome = trim($_POST['nome'] ?? '');
        $mensalidade = $_POST['mensalidade'] ?? '';
        $estado = $_POST['estado'] ?? 'ativo';

        if ($id === '' || $nome === '') {

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Dados inválidos.'
            ]);

            exit;
        }

        if ($mensalidade === '' || !is_numeric($mensalidade) || $mensalidade < 0) {

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Indique um valor de mensalidade válido.'
            ]);

            exit;
        }

        try {

            $sql = "
                UPDATE cursos
                SET nome = ?, mensalidade = ?, estado = ?
                WHERE id = ?
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                $nome,
                $mensalidade,
                $estado,
                $id
            ]);

            echo json_encode([
                'sucesso' => true,
                'mensagem' => 'Curso actualizado com sucesso.'
            ]);

        } catch (PDOException $e) {

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Não foi possível actualizar o curso.'
            ]);
        }

        exit;
    }


    // =================================================
    // ELIMINAR
    // =================================================

    if ($acao === 'eliminar') {

        $id = $_POST['id'] ?? '';

        if ($id === '') {

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'ID do curso inválido.'
            ]);

            exit;
        }

        try {

            $sql = "
                DELETE FROM cursos
                WHERE id = ?
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([$id]);

            echo json_encode([
                'sucesso' => true,
                'mensagem' => 'Curso eliminado com sucesso.'
            ]);

        } catch (PDOException $e) {

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Não foi possível eliminar o curso.'
            ]);
        }

        exit;
    }
}

if (
    $_SERVER['REQUEST_METHOD'] === 'GET'
    && isset($_GET['buscar'])
) {

    $id = $_GET['buscar'];

    try {

        $sql = "
            SELECT id, nome, mensalidade, estado
            FROM cursos
            WHERE id = ?
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([$id]);

        $curso = $stmt->fetch(PDO::FETCH_ASSOC);


        if ($curso) {

            echo json_encode([
                'sucesso' => true,
                'dados' => $curso
            ]);

        } else {

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Curso não encontrado.'
            ]);
        }

    } catch (PDOException $e) {

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Erro ao buscar curso.'
        ]);
    }

    exit;
}


// =====================================================
// LISTAR CURSOS
// =====================================================

$sql = "
    SELECT
        id,
        nome,
        mensalidade,
        estado
    FROM cursos
    ORDER BY id DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php require_once 'componentes/header.php'; ?>

<div class="layout">

    <?php require_once 'componentes/menu.php'; ?>

    <main class="conteudo">

        <div class="page-header">

            <div>

                <h1>Cursos</h1>

                <p>
                    Gerencie os cursos disponíveis no Instituto FOCO.
                </p>

            </div>

            <button
                class="btn-primary"
                id="btnNovoCurso"
            >
                + Novo curso
            </button>

        </div>


        <div class="content-card">

            <div class="search-container">

                <input
                    type="text"
                    id="pesquisaCurso"
                    placeholder="Pesquisar curso..."
                >

            </div>


            <div class="table-container">

                <table id="table-container">

                    <thead>

                        <tr>

                            <th>ID</th>
                            <th>Nome do curso</th>
                            <th>Mensalidade</th>
                            <th>Estado</th>
                            <th>Ações</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if (count($cursos) > 0): ?>

                            <?php foreach ($cursos as $curso): ?>

                                <tr>

                                    <td>
                                        <?= $curso['id'] ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($curso['nome']) ?>
                                    </td>

                                    <td>
                                        <?= number_format($curso['mensalidade'], 2, ',', '.') ?> MT
                                    </td>

                                    <td>

                                        <?php if ($curso['estado'] === 'ativo'): ?>

                                            <span class="status ativo">
                                                Ativo
                                            </span>

                                        <?php else: ?>

                                            <span class="status inativo">
                                                Inativo
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td>

                                        <div class="actions">

                                            <button
                                                class="btn-edit"
                                                data-id="<?= $curso['id'] ?>"
                                            >
                                                Actualizar
                                            </button>

                                            <button
                                                class="btn-delete"
                                                data-id="<?= $curso['id'] ?>"
                                            >
                                                Eliminar
                                            </button>

                                        </div>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="5"
                                    class="empty-state"
                                >
                                    Nenhum curso cadastrado.
                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </main>

</div>


<!-- =====================================================
     MODAL
===================================================== -->

<div
    class="modal"
    id="modalCurso"
>

    <div class="modal-content">

        <div class="modal-header">

            <div>

                <h2 id="tituloModal">
                    Novo curso
                </h2>

                <p>
                    Preencha os dados do curso.
                </p>

            </div>

            <button
                class="modal-close"
                id="fecharModal"
            >
                &times;
            </button>

        </div>


        <form id="formCurso">

            <input
                type="hidden"
                id="cursoId"
                name="id"
            >

            <div class="form-grid">

                <div class="form-group">

                    <label for="nomeCurso">
                        Nome do curso
                    </label>

                    <input type="text" id="nomeCurso" name="nome" placeholder="Ex: Informática" maxlength="100"required>
                </div>
                <div class="form-group">
                    <label for="mensalidadeCurso">Mensalidade (MT) </label>
                    <input type="number" id="mensalidadeCurso" name="mensalidade" placeholder="Ex: 2500.00" min="0" step="0.01"required>
                </div>
                <div class="form-group">
                    <label for="estadoCurso"> Estado</label>
                    <select id="estadoCurso" name="estado" required>
                        <option value="ativo">
                            Ativo
                        </option>
                        <option value="inativo">
                            Inativo
                        </option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="cancelarModal">
                    Cancelar
                </button>
                <button type="submit" class="btn-primary" id="btnSalvarCurso"
                >
                    Cadastrar curso
                </button>
            </div>
        </form>
    </div>
</div>
<script src="JS/cursos.js"></script>
<?php require_once 'componentes/footer.php'; ?>