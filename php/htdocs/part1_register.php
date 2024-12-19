<?php

// Conectar à base de dados
$conn = pg_connect("host=db dbname=ddss-database-assignment-2 user=ddss-database-assignment-2 password=ddss-database-assignment-2");

if (!$conn) {
    error_log("Error connecting to database: " . pg_last_error());
    exit("Error connecting to database.");
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $username = $_GET['r_username'];
    $password = $_GET['r_password'];

    // Verificar se o usuário já existe
    $result = pg_query_params($conn, "SELECT * FROM users WHERE username = $1", array($username));
    if (pg_num_rows($result) > 0) {
        print ("Username already exists.");
        exit;
    }

    // Gerar um salt único
    $salt = bin2hex(random_bytes(32));

    // Hashing da senha com o salt
    $hashed_password = password_hash($password . $salt, PASSWORD_BCRYPT);

    // Inserir o novo usuário na base de dados
    $result = pg_query_params($conn, "INSERT INTO users (username, password, salt) VALUES ($1, $2, $3)", array($username, $hashed_password, $salt));

    if (!$result) {
        error_log("Erro ao registrar o usuário: " . pg_last_error());
        print ("Erro ao registrar o usuário.");
    } else {
        print ("Usuário registrado com sucesso.");
    }
} else {
    print ("Método de requisição inválido.");
}

pg_close($conn);
?>