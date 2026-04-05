<?php
require_once 'db.php';
if (isset($pdo)) {
    echo "PDO is defined and connection scope is active.";
} else {
    echo "PDO is NOT defined.";
}
?>
