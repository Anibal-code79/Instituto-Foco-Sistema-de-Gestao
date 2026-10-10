<?php

$servidor = "mysql:host=localhost;dbname=foco_financeiro;charset=utf8mb4";
$user = "root";
$password = "";

try {

    $pdo = new PDO($servidor,$user,$password);

    $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

} catch (PDOException $erro) {

    echo "Erro na conexão com a base de dados: " . $erro->getMessage();

}