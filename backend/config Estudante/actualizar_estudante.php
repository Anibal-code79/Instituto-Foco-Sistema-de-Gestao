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
// Receber dados
$id = $_POST['id'] ?? '';
$nome_completo = trim($_POST['nome_completo'] ?? '');
$contacto = trim($_POST['contacto'] ?? '');
$curso_id = $_POST['curso_id'] ?? '';
$ano = $_POST['ano'] ?? '';


// Validar
if ( empty($id) || empty($nome_completo) || empty($curso_id) ||empty($ano)) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Preencha todos os campos obrigatórios.'
    ]);
    exit;
}

try {
    $sql = "
        UPDATE estudantes
        SET
            nome_completo = ?,
            contacto = ?,
            curso_id = ?,
            ano = ?
        WHERE id = ?
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $nome_completo,
        $contacto,
        $curso_id,
        $ano,
        $id
    ]);
    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Estudante actualizado com sucesso.'
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Erro ao actualizar estudante.'
    ]);
}
?>