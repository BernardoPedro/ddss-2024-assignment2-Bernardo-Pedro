<?php

$conn = pg_connect("host=db dbname=ddss-database-assignment-2 user=ddss-database-assignment-2 password=ddss-database-assignment-2");

if (!$conn) {
    die("Error connecting to database: " . pg_last_error());
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $text = $_GET['v_text'];
}

$sql = "INSERT INTO messages (author, message) VALUES ('Vulnerable', '$text')";
$result = pg_query($conn, $sql);
if (!$result) {
    echo "Erro ao inserir a mensagem: " . pg_last_error();
} else {
    echo "Mensagem salva com sucesso";
}

pg_close($conn);
?>