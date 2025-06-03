<?php

function render_rows(string $sql_query, mysqli $conn, $get_row_func, &$bound_var, &...$bound_vars): void {
    // Handle POST delete if requested
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
        $username_to_delete = $_POST['username'];
        delete_user_from_db($conn, $username_to_delete);
    }
    
    $stmt = $conn->stmt_init();

    if (!$stmt->prepare($sql_query)) {
        render_statement_fail();
    } else {
        $stmt->execute();
        $stmt->bind_result($bound_var, ...$bound_vars);

        echo "<ul class='row-list'>";
        while ($stmt->fetch()) {
            $clean_var = htmlspecialchars($bound_var);
            $clean_vars = array();
            for($i = 0; $i < count($bound_vars); ++$i) {
                array_push($clean_vars, htmlspecialchars($bound_vars[$i]));
            }
            
            echo "<li style='display: flex; justify-content: space-between; align-items: center; flex-direction: row;'>
            <div>" . $get_row_func($clean_var, ...$clean_vars) . "</div>
            <form method='POST' style='margin-left: 1em;'>
                <input type='hidden' name='username' value='" . $clean_var . "'>
                <button type='submit' class='delete-btn'>Delete</button>
            </form>
          </li>";
        }
        echo "</ul>";
    }   
}

function delete_user_from_db(mysqli $conn, string $FirstName): void {
    $stmt = $conn->prepare("DELETE FROM user WHERE FirstName = ?");
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->close();
    }
}

function get_row_title(string $column_data): string {
    return "<strong>" . $column_data . "</strong>";
}

function get_row_sub(string $column_data): string {
    return  $column_data;
}

function render_statement_fail(): void {
    echo "<p style='color:red;'>Failed to prepare SQL statement.</p>";
}

?>