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


$id = $_POST['id'] ?? '';

if (empty($id)) {

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Mensalidade não informada.'
    ]);

    exit;
}


try {

    $sql = "
        DELETE FROM mensalidades
        WHERE id = ?
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([$id]);


    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Mensalidade eliminada com sucesso.'
    ]);

} catch (PDOException $e) {

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao eliminar mensalidade.'
    ]);

}