<?php

// Conectar à base de dados
session_start();
$conn = pg_connect("host=db dbname=ddss-database-assignment-2 user=ddss-database-assignment-2 password=ddss-database-assignment-2");
if (!$conn) {
    error_log("Error connecting to database: " . pg_last_error());
    $errorMsg="<Error connecting to database.";
    $_SESSION['error_message'] = $errorMsg;
    header("Location: part1.php");
    exit();
    
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['r_username'];
    $password = $_POST['r_password'];

    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
       
        $errorMsg="Nome de usuário inválido.";
        $_SESSION['error_message'] = $errorMsg;
        header("Location: part1.php");
        exit();
    }

    // Sanitizar entradas
    $username_sanitized = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
    $password_sanitized = htmlspecialchars($password, ENT_QUOTES, 'UTF-8');

    if (strlen($username_sanitized) > 32 || strlen($password_sanitized) > 32) {

        $errorMsg="Erro: Nome de usuário ou senha são muito longos. Limite de 32 caracteres.";
        $_SESSION['error_message'] = $errorMsg;
        header("Location: part1.php");
        exit();
    }

    // Verificar se o usuário já existe
    $result = pg_query_params($conn, "SELECT * FROM users WHERE username = $1", array($username_sanitized));
    if (pg_num_rows($result) > 0) {
        $errorMsg="Nome de usuário já existe.";
        $_SESSION['error_message'] = $errorMsg;
        pg_close($conn);
        header("Location: part1.php");
       exit();
    }

    // Gerar um salt único
    $salt = bin2hex(random_bytes(32));

    // Hashing da senha com o salt
    $hashed_password = password_hash($password_sanitized . $salt, PASSWORD_BCRYPT);

    // Inserir o novo usuário na base de dados
    $result = pg_query_params($conn, "INSERT INTO users (username, password, salt) VALUES ($1, $2, $3)", array($username_sanitized, $hashed_password, $salt));

    if (!$result) {
        error_log("Erro ao registrar o usuário: " . pg_last_error());
        $errorMsg="Nome de usuário já existe.";
       
        
    } else {
        $errorMsg="Usuário registrado com sucesso.";
    }
} else {
    $errorMsg="Usuário registrado com sucesso.";
}

// Armazena a mensagem de erro em uma sessão
  $_SESSION['error_message'] = $errorMsg;
    
  // Redireciona para a página de login ou atual
  header("Location: part1.php");
  pg_close($conn);

exit();
?>

<html>

<head>
  <title>DDSS PA2 - Part 1.2</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!--
            V3-php info:
            - corrected a few naming bugs in the inputs
            - fixed a few syntax issues in the HTML
            - added some explanations
            - corrected the form actions for php
        -->
</head>

<body>

 
</body>

</html>