<?php
session_start();

$conn = pg_connect("host=db dbname=ddss-database-assignment-2 user=ddss-database-assignment-2 password=ddss-database-assignment-2");

if (!$conn) {
	die("Error connecting to database: " . htmlspecialchars(pg_last_error(), ENT_QUOTES, 'UTF-8'));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	// Capturar e sanitizar entradas do usuário
	$username = filter_input(INPUT_POST, 'c_username', FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Filtra todos os caracteres especiais
	$password = filter_input(INPUT_POST, 'c_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	if (!$username || !$password) {
		die("Dados inválidos.");
	}

	// Validação: nome de usuário deve ser alfanumérico
	if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
		die("Nome de usuário inválido.");
	}

	// Consulta segura usando pg_query_params
	$sql = "SELECT username, password, salt FROM users WHERE username = $1";
	$result = pg_query_params($conn, $sql, array($username));

	if ($result && pg_num_rows($result) > 0) {
		$user_data = pg_fetch_assoc($result);
		$hashed_password = $user_data['password'];
		$salt = $user_data['salt'];

		// Verificação da senha
		if (password_verify($password . $salt, $hashed_password)) {
			// Regenerar a ID de sessão para evitar sequestro
			session_regenerate_id(true);
			$_SESSION['username'] = $username; // Armazena o valor original, mas só será sanitizado ao ser exibido
			$_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Gerar token CSRF após login bem-sucedido

			// Mensagem de login bem-sucedido com sanitização rigorosa na saída
			echo "Login bem-sucedido! Bem-vindo, " . htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') . ".<br/>";
		} else {
			// Mensagem genérica para credenciais inválidas
			echo "Credenciais inválidas!<br/>";
		}
	} else {
		// Mensagem genérica para credenciais inválidas
		echo "Credenciais inválidas.<br/>";
	}
} else {
	die("Método inválido. Use o método POST.");
}

// Fechar a conexão com o banco de dados
pg_close($conn);
?>