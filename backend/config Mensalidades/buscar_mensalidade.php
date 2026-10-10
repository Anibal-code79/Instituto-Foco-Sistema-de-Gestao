<?php

require_once __DIR__ . '/../conexao.php';

header('Content-Type: application/json');

$id = $_GET['id'] ?? '';

if (empty($id)) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Mensalidade não informada.'
    ]);
    exit;
}

try {

    $sql = "
        SELECT
            id,
            estudante_id,
            curso_id,
            ano_letivo_id,
            mes,
            valor,
            data_vencimento,
            estado
        FROM mensalidades
        WHERE id = ?
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    $mensalidade = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$mensalidade) {
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Mensalidade não encontrada.'
        ]);
        exit;
    }

    echo json_encode([
        'sucesso' => true,
        'dados' => $mensalidade
    ]);

} catch (Exception $e) {

    echo json_encode([
        'sucesso' => false,
        'mensagem' => $e->getMessage()
    ]);

}