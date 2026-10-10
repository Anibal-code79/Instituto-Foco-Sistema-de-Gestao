<?php

require_once __DIR__ . '/../conexao.php';

header('Content-Type: application/json');

$id = $_GET['id'] ?? '';

if (empty($id)) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Estudante não informado.'
    ]);
    exit;
}

try {

    $sql = "
        SELECT
            id,
            codigo_estudante,
            nome_completo,
            contacto,
            curso_id,
            ano
        FROM estudantes
        WHERE id = ?
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    $estudante = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$estudante) {
        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Estudante não encontrado.'
        ]);
        exit;
    }

    echo json_encode([
        'sucesso' => true,
        'dados' => $estudante
    ]);

} catch (PDOException $e) {

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao buscar estudante.'
    ]);

}
?>