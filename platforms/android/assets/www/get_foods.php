<?php

ini_set("default_charset", 'utf-8');

//Set proper header to reduce broken characters
header('Content-Type: text/plain; charset=utf-8');

//Gets pass from ignored text file
$pass = rtrim(file_get_contents("sql_pass.txt"));

//Connect to the SQL-database
$con = mysqli_connect("mysql513.loopia.se", "98anve32@k132604", $pass, "kodlabb_se_db_6_db_7_db_2_db_13");

//Set the correct charset
mysqli_set_charset($con,"utf8mb4");

//If the server failed to connect, echo an error message
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

if ($_POST) {
    $school = $_POST['school'];

    //Save the SQL-query as a string
    $query = "SELECT * FROM food WHERE School = $school ORDER BY Week ASC, FIELD(Day, 'Måndag', 'Tisdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lördag', 'Söndag');";

    //Run the query, and save the results in an object
    if ($result = mysqli_query($con, $query)) {
        while ($object = mysqli_fetch_object($result)) {
            printf("%s %s (%s)\n",$object->Week,$object->Day,$object->Mat);
        }

        //Frees the memory used for saving the result
        mysqli_free_result($result);
    }

    //Echo the object
    echo json_encode($object);
}

//Close the connection
mysqli_close($con);

?>
