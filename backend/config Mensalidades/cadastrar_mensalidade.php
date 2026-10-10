<?php

require_once __DIR__ . '/../conexao.php';

header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Método inválido.'
    ]);

    exit;
}


$estudante_id = $_POST['estudante_id'] ?? '';
$curso_id = $_POST['curso_id'] ?? '';
$ano_letivo_id = $_POST['ano_letivo_id'] ?? '';
$mes = $_POST['mes'] ?? '';
$data_vencimento = $_POST['data_vencimento'] ?? '';


if (empty($estudante_id) ||empty($curso_id) ||empty($ano_letivo_id) ||empty($mes) ||empty($data_vencimento)) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Preencha todos os campos obrigatórios.'
    ]);
    exit;
}

try {
    $sqlCurso = "SELECT mensalidade FROM cursos WHERE id = ?";
    $stmtCurso = $pdo->prepare($sqlCurso);
    $stmtCurso->execute([$curso_id]);
    $curso = $stmtCurso->fetch(PDO::FETCH_ASSOC);

    if (!$curso) {
        throw new Exception('Curso não encontrado.');
    }
    $valor = $curso['mensalidade'];

    // Verificar se já existe mensalidade
    $sql = "SELECT id FROM mensalidades WHERE estudante_id = ? AND ano_letivo_id = ? AND mes = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $estudante_id,
        $ano_letivo_id,
        $mes
    ]);

    if ($stmt->fetch()) {
        throw new Exception(
            'Já existe uma mensalidade para este estudante neste mês.'
        );
    }

    $sql = "INSERT INTO mensalidades(
            estudante_id,
            curso_id,
            ano_letivo_id,
            mes,
            valor,
            data_vencimento,
            estado
        )VALUES (?, ?, ?, ?, ?, ?, 'pendente')";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([$estudante_id,$curso_id,$ano_letivo_id,$mes,$valor,$data_vencimento]);
    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Mensalidade registada com sucesso.'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => $e->getMessage()
    ]);
}
?>