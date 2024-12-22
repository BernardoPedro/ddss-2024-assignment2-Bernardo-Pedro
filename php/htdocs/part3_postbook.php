<?php
session_start();

// Verificar se o usuário está logado
$loggedIn = isset($_SESSION['username']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DDSS - Inserir Livro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1,
        h2 {
            text-align: center;
        }

        table {
            width: 60%;
            margin: 0 auto;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        textarea,
        select {
            width: 100%;
            padding: 8px;
        }

        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <h1>Design and Development of Secure Software</h1>
    <h2>Inserir Novo Livro (Correto)</h2><a href="part3_postbook_vulnerable.php"> Visitar formulário
        vulnerável</a><a href="part3.php">Voltar para a parte 3</a>
    <form action="part3_insert_book.php" method="post">
        <table border="1">
            <thead>
                <tr>
                    <th colspan="2">Inserir Livro</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Título</td>
                    <td><input type="text" name="title" required></td>
                </tr>
                <tr>
                    <td>Autores</td>
                    <td><input type="text" name="authors" required></td>
                </tr>
                <tr>
                    <td>Categoria</td>
                    <td>
                        <select name="category" required>
                            <option value="Databases">Databases</option>
                            <option value="HTML & Web Design">HTML & Web Design</option>
                            <option value="Programming">Programming</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Preço</td>
                    <td><input type="number" step="0.01" name="price" required></td>
                </tr>
                <tr>
                    <td>Data de Publicação</td>
                    <td><input type="date" name="book_date" required></td>
                </tr>
                <tr>
                    <td>Descrição</td>
                    <td><textarea name="description" required></textarea></td>
                </tr>
                <tr>
                    <td>Palavras-chave</td>
                    <td><input type="text" name="keywords"></td>
                </tr>
                <tr>
                    <td>Notas</td>
                    <td><input type="text" name="notes"></td>
                </tr>
                <tr>
                    <td>Recomendação</td>
                    <td><input type="number" name="recomendation" required></td>
                </tr>
                <tr>
                    <td colspan="2" align="right"><button type="submit">Inserir Livro</button></td>
                </tr>
                <input type="hidden" name="csrf_token"
                    value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            </tbody>
        </table>
    </form>
</body>

</html>