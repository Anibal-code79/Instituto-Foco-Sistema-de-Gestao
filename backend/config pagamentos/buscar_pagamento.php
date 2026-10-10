<?php

require_once __DIR__ . '/../conexao.php';

header('Content-Type: application/json');

$id = $_GET['id'] ?? '';


if (empty($id)) {

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Pagamento não informado.'
    ]);

    exit;
}


try {

    $sql = "
        SELECT
            id,
            mensalidade_id,
            valor_pago,
            metodo_pagamento,
            data_pagamento,
            referencia
        FROM pagamentos
        WHERE id = ?
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([$id]);

    $pagamento = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$pagamento) {

        echo json_encode([
            'sucesso' => false,
            'mensagem' => 'Pagamento não encontrado.'
        ]);

        exit;
    }


    echo json_encode([
        'sucesso' => true,
        'dados' => $pagamento
    ]);


} catch (PDOException $e) {

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao buscar pagamento.'
    ]);

}