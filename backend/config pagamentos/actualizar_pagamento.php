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
$mensalidade_id = $_POST['mensalidade_id'] ?? '';
$metodo_pagamento = $_POST['metodo_pagamento'] ?? '';
$referencia = $_POST['referencia'] ?? '';

$metodosValidos = ['deposito_bancario', 'mpesa', 'emola', 'numerario'];

if (
    empty($id) ||
    empty($mensalidade_id) ||
    empty($metodo_pagamento) ||
    empty($referencia)
) {

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Preencha todos os campos obrigatórios.'
    ]);

    exit;
}

if (!in_array($metodo_pagamento, $metodosValidos)) {

    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Método de pagamento inválido.'
    ]);

    exit;
}


try {

    $pdo->beginTransaction();


    // VERIFICAR REFERÊNCIA (excepto o próprio registo)

    $sql = "
        SELECT id
        FROM pagamentos
        WHERE referencia = ?
        AND id != ?
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $referencia,
        $id
    ]);


    if ($stmt->fetch()) {

        throw new Exception(
            'Esta referência de pagamento já existe.'
        );

    }


    // BUSCAR MENSALIDADE

    $sql = "
        SELECT id, valor
        FROM mensalidades
        WHERE id = ?
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$mensalidade_id]);

    $mensalidade = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$mensalidade) {

        throw new Exception(
            'Mensalidade não encontrada.'
        );

    }

    $valor_pago = $mensalidade['valor'];


    // ACTUALIZAR PAGAMENTO

    $sql = "
        UPDATE pagamentos
        SET
            mensalidade_id = ?,
            valor_pago = ?,
            metodo_pagamento = ?,
            referencia = ?
        WHERE id = ?
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $mensalidade_id,
        $valor_pago,
        $metodo_pagamento,
        $referencia,
        $id
    ]);


    // Garante que a mensalidade associada fica marcada como paga
    $sql = "
        UPDATE mensalidades
        SET estado = 'paga'
        WHERE id = ?
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$mensalidade_id]);


    $pdo->commit();


    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Pagamento actualizado com sucesso.'
    ]);


} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        'sucesso' => false,
        'mensagem' => $e->getMessage()
    ]);

}