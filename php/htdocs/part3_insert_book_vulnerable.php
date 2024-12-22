<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['username'])) {
    print ("Erro: Usuário não autenticado. Por favor, faça login primeiro!<br/>");
    exit();
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
    $title = $_POST("title");
    $authors = $_POST("authors");
    $category = $_POST("category");
    $price = $_POST("price");
    $book_date = $_POST("book_date");
    $description = $_POST("description");
    $keywords = $_POST("keywords");
    $notes = $_POST("notes");
    $recomendation = $_POST("recomendation");

    // Verificar se todos os campos obrigatórios estão preenchidos
    if ($title && $authors && $category && $price !== false && $book_date && $description && $recomendation !== false) {
        // Construir query para inserção
        $query = "INSERT INTO books (title, authors, category, price, book_date, description, keywords, notes, recomendation) 
                  VALUES ($title, $authors, $category, $price, $book_date, $description, $keywords, $notes, $recomendation)";
        $result = pg_query($conn, $query);

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