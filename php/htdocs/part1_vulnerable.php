<?php
$conn = pg_connect("host=db dbname=ddss-database-assignment-2 user=ddss-database-assignment-2 password=ddss-database-assignment-2");

if (!$conn) {
	die("Error connecting to database: " . pg_last_error());
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
	$username = $_GET['v_username'];
	$password = $_GET['v_password'];

	// Consulta SQL vulnerável a injeção
	$sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
	$result = pg_query($conn, $sql);

	if ($result && pg_num_rows($result) > 0) {
		echo "Login bem-sucedido!";
	} else {
		echo "Nome de usuário ou senha incorretos.";
	}
}

pg_close($conn);
?>