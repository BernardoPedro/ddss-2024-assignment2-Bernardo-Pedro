<?php
// Conectar à base de dados
$conn = pg_connect("host=db dbname=ddss-database-assignment-2 user=ddss-database-assignment-2 password=ddss-database-assignment-2");

if (!isset($_SESSION['username'])) {
    printf("Error: User not logged in. Please log in first!<br/>");
    exit();
}

if (!$conn) {
    print ("Erro ao conectar na base de dados.\n");
    exit;
}

// Consultar a tabela de mensagens
$query = "SELECT author, message FROM messages";
$result = pg_query($conn, $query);

if (!$result) {
    print ("Erro ao executar a consulta.\n");
    exit;
}

while ($row = pg_fetch_assoc($result)) {
    print ('<tr><td>' . htmlspecialchars($row['author']) . ': ' . $row['message'] . '</td></tr>');
}

// Fechar a conexão
pg_close($conn);

?>