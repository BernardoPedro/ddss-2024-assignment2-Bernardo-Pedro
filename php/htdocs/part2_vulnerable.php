<?php

$conn = pg_connect("host=db dbname=ddss-database-assignment-2 user=ddss-database-assignment-2 password=ddss-database-assignment-2");

session_start();
if (!isset($_SESSION['username'])) {
    printf("Error: User not logged in. Please log in first!<br/>");
    exit();
}

if (!$conn) {
    die("Error connecting to database: " . pg_last_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $text = $_POST['v_text'];
}

$sql = "INSERT INTO messages (author, message) VALUES ('Vulnerable', '$text')";
$result = pg_query($conn, $sql);
if (!$result) {
    print ("Erro ao inserir a mensagem: " . pg_last_error());
} else {
    print ("Mensagem salva com sucesso")
    ;
}

pg_close($conn);
?>