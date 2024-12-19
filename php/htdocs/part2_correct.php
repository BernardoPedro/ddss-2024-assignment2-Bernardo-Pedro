<?php
// Conectar à base de dados
$conn = pg_connect("host=db dbname=ddss-database-assignment-2 user=ddss-database-assignment-2 password=ddss-database-assignment-2");
session_start();
if (!isset($_SESSION['username'])) {
    printf("Error: User not logged in. Please log in first!<br/>");
    exit();
}

if (!$conn) {
    error_log("Error connecting to database: " . pg_last_error());
    exit("Error connecting to database.");
}

if (isset($_SESSION['username'])) {
    print ("Sessão não iniciada, por favor dê login!<br/>");
    exit();
} else {
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $text = $_GET['c_text'];
        // Sanitizar a entrada do usuário
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        // Preparar e executar a consulta de forma segura
        $result = pg_query_params($conn, "INSERT INTO messages (author, message) VALUES ($1, $2)", array('Correct', $text));

        if (!$result) {
            error_log("Erro ao inserir a mensagem: " . pg_last_error());
            print ("Erro ao inserir a mensagem.");
        } else {
            print ("Mensagem salva com sucesso.");
        }
    } else {
        print ("Método de requisição inválido.");
    }
}
// Fechar a conexão
pg_close($conn);
?>