<?php

include_once 'env.php';

try {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
} catch (mysqli_sql_exception) {
    echo "[NurCli]: Failed while establishing database connection\n";
    exit;
}
