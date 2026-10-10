<?php
require_once __DIR__ . '/../conexao.php';
header('Content-Type: application/json');
$curso_id = $_GET['curso_id'] ?? '';
if (empty($curso_id)) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Curso não informado.'
    ]);
    exit;
}
try {
    $sql = "SELECT mensalidade FROM cursos WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$curso_id]);
    $curso = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$curso) {
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Curso não encontrado.'
        ]);
        exit;
    }
    echo json_encode([
        'sucesso' => true,
        'mensalidade' => $curso['mensalidade']
    ]);
} catch (Exception $e) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => $e->getMessage()
    ]);
}