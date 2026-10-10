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
$codigo_estudante = trim($_POST['codigo_estudante'] ?? '');
$nome_completo = trim($_POST['nome_completo'] ?? '');
$contacto = trim($_POST['contacto'] ?? '');
$curso_id = $_POST['curso_id'] ?? '';
$ano = $_POST['ano'] ?? '';

if (empty($codigo_estudante) || empty($nome_completo) || empty($curso_id) ||empty($ano)) {
    echo json_encode([
        'sucesso' => false,
        'mensagem' => 'Preencha todos os campos obrigatórios.'
    ]);
    exit;
}

try {

    $pdo->beginTransaction();

    $sql = "SELECT id FROM estudantes WHERE codigo_estudante = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$codigo_estudante]);

    if ($stmt->fetch()) {
        throw new Exception('O código do estudante já existe.');
    }

    $sql = "SELECT id FROM usuarios WHERE nome_usuario = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$codigo_estudante]);

    if ($stmt->fetch()) {
        throw new Exception('Este código já está sendo usado como nome de usuário.');
    }

    $senha_inicial = '123456';
    $senha_hash = password_hash($senha_inicial, PASSWORD_DEFAULT);

    $sql = "
        INSERT INTO usuarios (nome_usuario, senha, perfil, estado)
        VALUES (?, ?, 'estudante', 'ativo')
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$codigo_estudante, $senha_hash]);

    $usuario_id = $pdo->lastInsertId();

    $sql = "
        INSERT INTO estudantes
        (codigo_estudante, nome_completo, curso_id, ano, contacto, usuario_id, estado)
        VALUES (?, ?, ?, ?, ?, ?, 'ativo')
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $codigo_estudante,
        $nome_completo,
        $curso_id,
        $ano,
        $contacto,
        $usuario_id
    ]);

    $pdo->commit();

    echo json_encode([
        'sucesso' => true,
        'mensagem' => 'Estudante cadastrado com sucesso.',
        'usuario' => $codigo_estudante,
        'senha' => $senha_inicial
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