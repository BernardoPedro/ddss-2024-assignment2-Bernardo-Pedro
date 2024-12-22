<?php
session_start();

if (!isset($_SESSION['username'])) {
    printf("Error: User not logged in. Please log in first!<br/>");
    exit();
}

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

    // Construção da query vulnerável
    $sql = "SELECT * FROM books WHERE 1=1";
    if (!empty($title)) {
        $sql .= " AND title ILIKE '%$title%'";
    }
    if (!empty($author)) {
        $sql .= " AND authors ILIKE '%$author%'";
    }
    if (!empty($category)) {
        if ($category == "") {
            $sql .= " AND category = 'Databases' OR category = 'HTML & Web design' OR category ='Programming'";
        } elseif ($category == "2") {
            $sql .= "AND category = 'Databases'";
        } elseif ($category == "3") {
            $sql .= "AND category = 'HTML & Web design'";
        } elseif ($category == "1") {
            $sql .= "AND category = 'Programming'";
        }
    }
    if (!empty($pricemin)) {
        $sql .= " AND price >= $pricemin";
    }
    if (!empty($pricemax)) {
        $sql .= " AND price <= $pricemax";
    }

    // Lógica para os diferentes tipos de match
    if (!empty($search_input)) {
        $words = explode(' ', $search_input);
        if ($search_field == 'any') {
            if ($match == 'any') {
                $conditions = [];
                foreach ($words as $word) {
                    $conditions[] = " title ILIKE '%$word%' OR authors ILIKE '%$word%' OR description ILIKE '%$word%' OR keywords ILIKE '%$word%' OR notes ILIKE '%$word%'";
                }
                $sql .= " AND (" . implode(' OR ', $conditions) . ")";
            } elseif ($match == 'all') {
                $conditions = [];
                foreach ($words as $word) {
                    $conditions[] = " title ILIKE '%$word%' OR authors ILIKE '%$word%' OR description ILIKE '%$word%' OR keywords ILIKE '%$word%' OR notes ILIKE '%$word%'";
                }
                $sql .= " AND (" . implode(' AND ', $conditions) . ")";
            } elseif ($match == 'phrase') {
                $sql .= " AND title = '$search_input' OR authors = '$search_input' OR description = '$search_input' OR keywords = '$search_input' OR notes = '$search_input'";
            }
        } else {
            if ($match == 'any') {
                $conditions = [];
                foreach ($words as $word) {
                    $conditions[] = "$search_field ILIKE '%$word%'";
                }
                $sql .= " AND (" . implode(' OR ', $conditions) . ")";
            } elseif ($match == 'all') {
                $conditions = [];
                foreach ($words as $word) {
                    $conditions[] = "$search_field ILIKE '%$word%'";
                }
                $sql .= " AND (" . implode(' AND ', $conditions) . ")";
            } elseif ($match == 'phrase') {
                $sql .= " AND $search_field = '$search_input'";
            }
        }
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
    print ("Consulta SQL: " . htmlspecialchars($sql) . "<br/>");

    // Executar a query vulnerável
    $result = pg_query($conn, $sql);
    if ($result) {
        $num_rows = pg_num_rows($result);
        print ("Número de resultados: " . $num_rows . "<br/>");

        while ($row = pg_fetch_assoc($result)) {
            print ("Title: " . htmlspecialchars($row['title']) . "<br/>");
            print ("Authors: " . htmlspecialchars($row['authors']) . "<br/>");
            print ("Category: " . htmlspecialchars($row['category']) . "<br/>");
            print ("Price: " . htmlspecialchars($row['price']) . "<br/>");
            print ("Publication Date: " . htmlspecialchars($row['book_date']) . "<br/>");

            // Mostrar resumo se solicitado
            if ($show_summaries) {
                print ("Description: " . htmlspecialchars($row['description']) . "<br/>");
            }

            print ("Keywords: " . htmlspecialchars($row['keywords']) . "<br/>");
            print ("Notes: " . htmlspecialchars($row['notes']) . "<br/>");
            print ("Recomendation: " . htmlspecialchars($row['recomendation']) . "<br/>");
            print ("<hr/>");
        }
    } else {
        print ("Erro ao executar a consulta SQL.<br/>");
    }
}

pg_close($conn);
?>