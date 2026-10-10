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
        'mensagem' => 'Pagamento não informado.'
    ]);

    exit;
}


try {

    $pdo->beginTransaction();


    // BUSCAR PAGAMENTO

    $sql = "
        SELECT mensalidade_id
        FROM pagamentos
        WHERE id = ?
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    $pagamento = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$pagamento) {

        throw new Exception(
            'Pagamento não encontrado.'
        );

    }


    $mensalidade_id = $pagamento['mensalidade_id'];


    // ELIMINAR PAGAMENTO

    $sql = "
        DELETE FROM pagamentos
        WHERE id = ?
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);


    // Sem pagamento associado, a mensalidade volta a pendente
    // (como não há parciais, é sempre um dos dois estados)
    $sql = "
        UPDATE mensalidades
        SET estado = 'pendente'
        WHERE id = ?
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$mensalidade_id]);


    $pdo->commit();


    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Pagamento eliminado com sucesso.'
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