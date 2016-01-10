<?php

ini_set("default_charset", 'utf-8');

//Gets pass from ignored text file
$pass = rtrim(file_get_contents("sql_pass.txt"));

//Set proper header to reduce broken characters
header('Content-Type: text/html; charset=utf8mb4_swedish_ci');

//Connect to the SQL-database
$con = mysqli_connect("mysql513.loopia.se", "98anve32@k132604", $pass, "kodlabb_se_db_6_db_7_db_2_db_13");

//Set the correct charset
mysqli_set_charset($con,"utf8mb4");

//If the server failed to connect, echo an error message
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$query = "SELECT Name, ID FROM schools;";

$stmt = mysqli_prepare($con, "INSERT INTO food (School, Week, Day, Mat, ID) VALUES (?, ?, ?, ?, ?);");

//Run the query, and save the results in an object
if ($result = mysqli_query($con, $query)) {
    while ($object = mysqli_fetch_object($result)) {
        $school = $object->Name;
        $ID = $object->ID;

        $hasArguments = false;

        if (strpos($school, "(") !== false) {
            $hasArguments = true;
        }
        else if (strpos($school, "[") !== false) {
            $hasArguments = true;
        }

        if ($hasArguments) {
            if (strpos($school, "&") === false && strpos($school, "[") !== false) {
                $charPos = strpos($school, "[");
                $firstChar = "[";
                $lastChar = "]";
            }
            else {
                $charPos = strpos($school, "(");
                $firstChar = "(";
                $lastChar = ")";
            }

            $length = strlen($school);

            $mainPart = substr($school, 0, $length - ($length - $charPos));

            $arguments = str_replace($mainPart, "", $school);

            if (strpos($school, "&") !== false) {
                $arguments = explode("&", $arguments);

                $arguments[0] = trim($arguments[0], "\(\)");
                $arguments[1] = trim($arguments[1], "\[\]");

                $school = $mainPart . $arguments[1];
            }
            else {
                if (strpos($arguments, "(") !== false) {
                    $school = $mainPart;
                }
                else {
                    $arguments = trim($arguments, $firstChar);
                    $arguments = trim($arguments, $lastChar);

                    $school = $mainPart . $arguments;
                }
            }
        }

        $school = mb_strtolower($school, "UTF-8");

        $specialChars = ["å", "ä", "ö", "é", "è", " & ", " ", "/"];
        $replaceChars = ["a", "a", "o", "e", "", "-", "-", "-"];

        $school = str_replace($specialChars, $replaceChars, $school);

        $school = trim($school);

        if (@file_get_contents("http://meny.dinskolmat.se/$school/rss/") !== false) {
            $rss = simplexml_load_file("http://meny.dinskolmat.se/$school/rss/");

            foreach ($rss->channel->item as $item) {
                $title = $item->title;
                $titleItems = explode(" ", $title);

                $foodWeek = intval($titleItems[3]);
                $foodDay = $titleItems[0];

                $foodDesc = $item->description;

                $replaceValues = ["[CDATA[", "]]", "( ", " )", "/ "];
                $replacementValues = ["", "", "(", ")", "/"];

                $foodDesc = str_replace($replaceValues, $replacementValues, $foodDesc);

                $foodDesc = trim($foodDesc);

                if (strpos($foodDesc, "<br/>") !== false) {
                    $foods = explode("<br/>", $foodDesc, 2);
                    $foodDesc = $foods[0];
                }

                if ($foodDesc !== "Menyn saknas") {
                    $uuid = md5(serialize([$school, $foodWeek, $foodDay, foodDesc]));

                    mysqli_stmt_bind_param($stmt, "sisss", $ID, $foodWeek, $foodDay, $foodDesc, $uuid);
                    mysqli_stmt_execute($stmt);

                    echo mysqli_error($con);
                }
            }
        }
    }

    //Frees the memory used for saving the result
    mysqli_free_result($result);
}

//Close the connection
mysqli_close($con);

?>
