<?php

ini_set("default_charset", 'utf-8');

//Gets pass from ignored text file
$pass = rtrim(file_get_contents("sql_pass.txt"));

//Set proper header to reduce broken characters
header('Content-Type: text/html; charset=utf8mb4_swedish_ci');

//Connect to the SQL-database
$con = mysqli_connect("mysql513.loopia.se", "98anve32@k132604", $pass, "kodlabb_se_db_6_db_7_db_2_db_13");

//Set the correct charset
mysqli_set_charset($con, "utf8mb4");

//If the server failed to connect, echo an error message
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$stmt = mysqli_prepare($con, "INSERT INTO visits (IP, UUID, Time) VALUES (?, ?, ?);");

if ($_POST) {
    $IP = $_POST['ip'];
    $UUID = $_POST['uuid'];
    $time = time();

    mysqli_stmt_bind_param($stmt, "ssi", $IP, $UUID, $time);
    mysqli_stmt_execute($stmt);

    echo mysqli_error($con);
}

?>
