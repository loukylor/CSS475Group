<?php

function render_rows(string $sql_query, mysqli $conn, $get_row_func, &$bound_var, &...$bound_vars): void {
    $stmt = $conn->stmt_init();

    if (!$stmt->prepare($sql_query)) {
        render_statement_fail();
    } else {
        $stmt->execute();
        $stmt->bind_result($bound_var, ...&$bound_vars);

        echo "<ul class='row-list'>";
        while ($stmt->fetch()) {
            $clean_var = htmlspecialchars($bound_var);
            $clean_vars = array();
            for($i = 0; $i < count($bound_vars); ++$i) {
                array_push($clean_vars, htmlspecialchars($bound_vars[$i]));
            }
            
            echo "<li>" . $get_row_func($bound_var, ...$bound_vars) . "</li>";
        }
        echo "</ul>";
    }
}

function get_row_title(string $column_data): string {
    return "<strong>" . $column_data . "</strong>";
}

function get_row_sub(string $column_data): string {
    return "<span class='role'>" . $column_data . "</span>";
}

function render_statement_fail(): void {
    echo "<p style='color:red;'>Failed to prepare SQL statement.</p>";
}

?>