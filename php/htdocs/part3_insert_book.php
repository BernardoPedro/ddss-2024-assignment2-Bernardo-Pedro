<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['username'])) {
    print ("Erro: Usuário não autenticado. Por favor, faça login primeiro!<br/>");
    exit();
}

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Erro: Token CSRF inválido.");
}

// Conexão segura com o banco de dados
$conn = pg_connect("host=db dbname=ddss-database-assignment-2 user=ddss-database-assignment-2 password=ddss-database-assignment-2");
if (!$conn) {
    error_log("Erro ao conectar ao banco de dados: " . pg_last_error());
    die("Erro ao conectar ao banco de dados. Tente novamente mais tarde.");
}

// Verificar se a requisição é POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitizar e validar entradas
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $authors = filter_input(INPUT_POST, 'authors', FILTER_SANITIZE_STRING);
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $book_date = filter_input(INPUT_POST, 'book_date', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $keywords = filter_input(INPUT_POST, 'keywords', FILTER_SANITIZE_STRING);
    $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING);
    $recomendation = filter_input(INPUT_POST, 'recomendation', FILTER_VALIDATE_INT);

    // Verificar se todos os campos obrigatórios estão preenchidos
    if ($title && $authors && $category && $price !== false && $book_date && $description && $recomendation !== false) {
        // Construir query para inserção
        $query = "INSERT INTO books (title, authors, category, price, book_date, description, keywords, notes, recomendation) 
                  VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)";

        $params = array($title, $authors, $category, $price, $book_date, $description, $keywords, $notes, $recomendation);

        // Executar query com parâmetros
        $result = pg_query_params($conn, $query, $params);

        if ($result) {
            print ("Livro inserido com sucesso!");
        } else {
            error_log("Erro ao inserir livro: " . pg_last_error($conn));
            print ("Erro ao inserir livro. Tente novamente mais tarde.<br/>");
        }
    } else {
        print ("Por favor, preencha todos os campos obrigatórios corretamente.<br/>");
    }
}

pg_close($conn);
?>