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
        'mensagem' => 'Estudante não informado.'
    ]);
    exit;
}

try {

    $pdo->beginTransaction();
    $sql = "
        SELECT usuario_id
        FROM estudantes
        WHERE id = ?
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $estudante = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$estudante) {
        throw new Exception(
            'Estudante não encontrado.'
        );
    }
    $usuario_id = $estudante['usuario_id'];


    $sql = "
        DELETE FROM estudantes
        WHERE id = ?
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    $sql = "
        DELETE FROM usuarios
        WHERE id = ?
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario_id]);

    $pdo->commit();
    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Estudante eliminado com sucesso.'
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
?>