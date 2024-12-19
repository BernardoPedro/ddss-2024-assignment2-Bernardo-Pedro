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

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Sanitizar e validar entradas
    $title = filter_input(INPUT_GET, 'c_name', FILTER_SANITIZE_STRING);
    $author = filter_input(INPUT_GET, 'c_author', FILTER_SANITIZE_STRING);
    $category = filter_input(INPUT_GET, 'c_category_id', FILTER_SANITIZE_STRING);
    $pricemin = filter_input(INPUT_GET, 'c_pricemin', FILTER_VALIDATE_FLOAT);
    $pricemax = filter_input(INPUT_GET, 'c_pricemax', FILTER_VALIDATE_FLOAT);
    $search_input = strtolower(filter_input(INPUT_GET, 'c_search_input', FILTER_SANITIZE_STRING));
    $search_field = filter_input(INPUT_GET, 'c_search_field', FILTER_SANITIZE_STRING);
    $match = filter_input(INPUT_GET, 'c_radio_match', FILTER_SANITIZE_STRING);
    $radiosp_d = filter_input(INPUT_GET, 'c_sp_d', FILTER_SANITIZE_STRING);
    $daterange = filter_input(INPUT_GET, 'c_sp_date_range', FILTER_SANITIZE_STRING);
    $start_year = filter_input(INPUT_GET, 'c_sp_start_year', FILTER_VALIDATE_INT);
    $start_month = filter_input(INPUT_GET, 'c_sp_start_month', FILTER_VALIDATE_INT);
    $start_day = filter_input(INPUT_GET, 'c_sp_start_day', FILTER_VALIDATE_INT);
    $end_year = filter_input(INPUT_GET, 'c_end_year', FILTER_VALIDATE_INT);
    $end_month = filter_input(INPUT_GET, 'c_end_month', FILTER_VALIDATE_INT);
    $end_day = filter_input(INPUT_GET, 'c_end_day', FILTER_VALIDATE_INT);
    $show_results = filter_input(INPUT_GET, 'c_sp_c', FILTER_VALIDATE_INT);
    $show_summaries = filter_input(INPUT_GET, 'c_sp_m', FILTER_VALIDATE_BOOLEAN);
    $show_relevance = filter_input(INPUT_GET, 'c_sp_s', FILTER_SANITIZE_STRING);

    // Construir query com consultas parametrizadas
    $query = "SELECT * FROM books WHERE 1=1";
    $params = [];
    $paramIndex = 1;

    if (!empty($title)) {
        $query .= " AND title ILIKE $" . $paramIndex;
        $params[] = '%' . $title . '%';
        $paramIndex++;
    }

    if (!empty($author)) {
        $query .= " AND authors ILIKE $" . $paramIndex;
        $params[] = '%' . $author . '%';
        $paramIndex++;
    }

    if (!empty($category)) {
        if ($category == '') {
            $query .= " AND category = 'Databases' OR category = 'HTML & Web design' OR category ='Programming'";
        } elseif ($category == "2") {
            $query .= " AND category = 'Databases'";
        } elseif ($category == "3") {
            $query .= " AND category = 'HTML & Web design'";
        } elseif ($category == "1") {
            $query .= " AND category = 'Programming'";
        }

    }

    if (!empty($pricemin)) {
        $query .= " AND price >= $" . $paramIndex;
        $params[] = $pricemin;
        $paramIndex++;
    }

    if (!empty($pricemax)) {
        $query .= " AND price <= $" . $paramIndex;
        $params[] = $pricemax;
        $paramIndex++;
    }

    if (!empty($search_input)) {
        $words = explode(' ', $search_input);
        $conditions = [];

        foreach ($words as $word) {
            $currentIndex = $paramIndex++;
            if ($search_field === 'any') {
                $conditions[] = "(title ILIKE $" . $currentIndex . " OR authors ILIKE $" . $currentIndex . " OR description ILIKE $" . $currentIndex . " OR keywords ILIKE $" . $currentIndex . " OR notes ILIKE $" . $currentIndex . ")";
                $params[] = '%' . $word . '%';
            } else {
                $conditions[] = "$search_field ILIKE $" . $currentIndex;
                $params[] = '%' . $word . '%';
            }
        }

        if ($match === 'any') {
            $query .= " AND (" . implode(' OR ', $conditions) . ")";
        } elseif ($match === 'all') {
            $query .= " AND (" . implode(' AND ', $conditions) . ")";
        } elseif ($match === 'phrase') {
            if ($search_field === 'any') {
                $query .= " AND (title ILIKE $" . $paramIndex . " OR authors ILIKE $" . $paramIndex . " OR description ILIKE $" . $paramIndex . " OR keywords ILIKE $" . $paramIndex . " OR notes ILIKE $" . $paramIndex . ")";
            } else {
                $query .= " AND $search_field ILIKE $" . $paramIndex;
            }
            $params[] = '%' . $search_input . '%';
            $paramIndex++;
        }
    }

    if ($radiosp_d === "specific" && !empty($start_year) && !empty($start_month) && !empty($start_day) && !empty($end_year) && !empty($end_month) && !empty($end_day)) {
        $query .= " AND book_date BETWEEN $" . $paramIndex . " AND $" . ($paramIndex + 1);
        $params[] = "$start_year-$start_month-$start_day";
        $params[] = "$end_year-$end_month-$end_day";
        $paramIndex += 2;
    }

    if ($show_relevance === "relevance") {
        $query .= " ORDER BY recomendation DESC";
    } else {
        $query .= " ORDER BY book_date DESC";
    }

    if ($show_results > 0) {
        $query .= " LIMIT $" . $paramIndex++;
        $params[] = $show_results;
    }

    print ("Query gerada: " . htmlspecialchars($query) . "<br/>");

    // Executar query com parâmetros
    $result = pg_query_params($conn, $query, $params);

    if ($result) {
        $num_rows = pg_num_rows($result);
        print ("Número de resultados: " . htmlspecialchars($num_rows) . "<br/>");

        while ($row = pg_fetch_assoc($result)) {
            print ("Título: " . htmlspecialchars($row['title']) . "<br/>");
            print ("Autores: " . htmlspecialchars($row['authors']) . "<br/>");
            print ("Categoria: " . htmlspecialchars($row['category']) . "<br/>");
            print ("Preço: " . htmlspecialchars($row['price']) . "<br/>");
            print ("Data de Publicação: " . htmlspecialchars($row['book_date']) . "<br/>");

            if ($show_summaries) {
                print ("Descrição: " . htmlspecialchars($row['description']) . "<br/>");
            }

            print ("Palavras-chave: " . htmlspecialchars($row['keywords']) . "<br/>");
            print ("Notas: " . htmlspecialchars($row['notes']) . "<br/>");
            print ("Recomendação: " . htmlspecialchars($row['recomendation']) . "<br/>");
            print ("<hr/>");
        }
    } else {
        error_log("Erro ao executar consulta: " . pg_last_error($conn));
        print ("Erro ao buscar resultados. Tente novamente mais tarde.<br/>");
    }
}

pg_close($conn);
?>