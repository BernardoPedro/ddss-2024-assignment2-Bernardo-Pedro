<?php
$conn = pg_connect("host=db dbname=ddss-database-assignment-2 user=ddss-database-assignment-2 password=ddss-database-assignment-2");

if (!$conn) {
    die("Error connecting to database: " . pg_last_error());
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $title = $_GET['v_name'];
    $author = $_GET['v_author'];
    $category = $_GET['v_category_id'];
    $pricemin = $_GET['v_pricemin'];
    $pricemax = $_GET['v_pricemax'];
    $search_input = $_GET['v_search_input'];
    $search_field = $_GET['v_search_field'];
    $match = $_GET['v_radio_match'];
    $radiosp_d = $_GET['v_sp_d'];
    $daterange = $_GET['v_sp_date_range'];
    $start_month = $_GET['v_sp_start_month'];
    $start_day = $_GET['v_sp_start_day'];
    $start_year = $_GET['v_sp_start_year'];
    $end_month = $_GET['v_sp_end_month'];
    $end_day = $_GET['v_end_day'];
    $end_year = $_GET['v_end_year'];
    $show_results = (int) $_GET['v_sp_c'];
    $show_summaries = $_GET['v_sp_m'];
    $show_relevance = $_GET['v_sp_s'];


    echo $radiosp_d . "</br>";

    // Construção da query vulnerável
    $sql = "SELECT * FROM books WHERE 1=1";
    if (!empty($title)) {
        $sql .= " AND title ILIKE '%$title%'";
    }
    if (!empty($author)) {
        $sql .= " AND LOWER(authors) = '$author'";
    }
    if (!empty($category)) {
        $sql .= " AND category = '$category'";
    }
    if (!empty($pricemin)) {
        $sql .= " AND price >= $pricemin";
    }
    if (!empty($pricemax)) {
        $sql .= " AND price <= $pricemax";
    }
    if (!empty($search_input) && !empty($search_field)) {
        $sql .= " AND $search_field LIKE '%$search_input%'";
    }
    if ($radiosp_d == "specific" && !empty($daterange) && !empty($start_year) && !empty($start_month) && !empty($start_day) && !empty($end_year) && !empty($end_month) && !empty($end_day)) {
        $sql .= " AND book_date BETWEEN '$start_year-$start_month-$start_day' AND '$end_year-$end_month-$end_day'";
    }

    // Ordenar por relevância e data, se solicitado
    if ($show_relevance == "relevance") {
        $sql .= " ORDER BY recomendation DESC";
    } else {
        $sql .= " ORDER BY book_date DESC";
    }

    // Limitar o número de resultados, se solicitado
    if ($show_results > 0) {
        $sql .= " LIMIT $show_results";
    }

    // Adicionando uma linha de depuração para exibir a consulta SQL
    echo "Consulta SQL: " . htmlspecialchars($sql) . "<br/>";

    // Executar a query vulnerável
    $result = pg_query($conn, $sql);
    if ($result) {
        $num_rows = pg_num_rows($result);
        echo "Número de resultados: " . $num_rows . "<br/>";

        while ($row = pg_fetch_assoc($result)) {
            echo "Title: " . htmlspecialchars($row['title']) . "<br/>";
            echo "Authors: " . htmlspecialchars($row['authors']) . "<br/>";
            echo "Category: " . htmlspecialchars($row['category']) . "<br/>";
            echo "Price: " . htmlspecialchars($row['price']) . "<br/>";
            echo "Publication Date: " . htmlspecialchars($row['book_date']) . "<br/>";

            // Mostrar resumo se solicitado
            if ($show_summaries) {
                echo "Description: " . htmlspecialchars($row['description']) . "<br/>";
            }

            echo "Keywords: " . htmlspecialchars($row['keywords']) . "<br/>";
            echo "Notes: " . htmlspecialchars($row['notes']) . "<br/>";
            echo "Recomendation: " . htmlspecialchars($row['recomendation']) . "<br/>";
            echo "<hr/>";
        }
    } else {
        echo "Erro ao executar a consulta SQL.<br/>";
    }
}

pg_close($conn);
?>