<?php
session_start();
$conn = pg_connect("host=db dbname=ddss-database-assignment-2 user=ddss-database-assignment-2 password=ddss-database-assignment-2");
if (!$conn) {
	die("Error connecting to database: " . pg_last_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$username = $_POST['v_username'];
	$password = $_POST['v_password'];

	print ("username: " . $username . " password: " . $password . "<br/>");
	$sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
	$result = pg_query($conn, $sql);

	if ($result && pg_num_rows($result) > 0) {
		print ("Login bem-sucedido!");
		session_regenerate_id(true);
		$_SESSION['username'] = $username;
	} else {
		print ("Nome de usuário ou senha incorretos.");
	}
}

pg_close($conn);
?>