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


$mensalidade_id = $_POST['mensalidade_id'] ?? '';
$metodo_pagamento = $_POST['metodo_pagamento'] ?? '';
$referencia = $_POST['referencia'] ?? '';

// O valor NUNCA vem do formulário — é sempre o valor total da mensalidade

$metodosValidos = ['deposito_bancario', 'mpesa', 'emola', 'numerario'];

if (
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

    // VERIFICAR REFERÊNCIA

    $sql = "
        SELECT id
        FROM pagamentos
        WHERE referencia = ?
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$referencia]);

    if ($stmt->fetch()) {

        throw new Exception(
            'Esta referência de pagamento já existe.'
        );

    }

    // BUSCAR MENSALIDADE

    $sql = "
        SELECT id, valor, estado
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

    if ($mensalidade['estado'] === 'paga') {

        throw new Exception(
            'Esta mensalidade já foi paga.'
        );

    }

    // O pagamento é sempre pelo valor total da mensalidade
    $valor_pago = $mensalidade['valor'];


    // USUÁRIO LOGADO

    session_start();

    $usuario_id = $_SESSION['usuario_id'] ?? null;


    if (!$usuario_id) {

        throw new Exception(
            'Usuário não autenticado.'
        );

    }


    $sql = "
        INSERT INTO pagamentos
        (
            mensalidade_id,
            valor_pago,
            metodo_pagamento,
            referencia,
            usuario_id
        )
        VALUES (?, ?, ?, ?, ?)
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $mensalidade_id,
        $valor_pago,
        $metodo_pagamento,
        $referencia,
        $usuario_id
    ]);


    // A mensalidade passa sempre a "paga" — não há meio-termo
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
        'mensagem' => 'Pagamento registado com sucesso.'
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