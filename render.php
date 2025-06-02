<?php

function render_rows(string $sql_query, mixed &$bound_var, mixed &...$bound_vars, $get_row_func): void {
    $stmt = $conn->stmt_init();

    if (!$stmt->prepare($sql_query)) {
        render_statement_fail();
    } else {
        $stmt->execute();
        $stmt->bind_result($bound_var, ...$bound_vars);

        echo "<ul class='row-list'>";
        while ($stmt->fetch()) {
            echo htmlspecialchars("<li>" + $get_row_func($bound_var, ...$bound_vars) + "</li>");
        }
        echo "</ul>";
    }
}

function get_row_title(string $column_data): string {
    return "<li><strong>" + $column_data + "</strong></li>";
}

function get_row_sub(string $column_data): string {
    return "<span class='role'>" . htmlspecialchars($userType ?? 'Regular User') . "</span>";
}

function render_statement_fail(): void {
    echo "<p style='color:red;'>Failed to prepare SQL statement.</p>";
}

?>