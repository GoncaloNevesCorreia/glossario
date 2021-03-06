<?php

function InsertDefInDB($word_id, $definitions, $formatOfValuesForOneRow, $langs, $conn)
{
    $formatOfValues = "";

    $bindVals = "(";
    for ($i = 0; $i < strLen($formatOfValuesForOneRow); $i++) {
        $bindVals .= ($i + 1 ==  strLen($formatOfValuesForOneRow)) ? "?" : "?,";
    }
    $bindVals .= ")";


    $AllBindVals = [];
    $AllRows = [];

    for ($i = 0; $i < count($langs); $i++) {
        $AllBindVals[] = $bindVals;
        $formatOfValues .= $formatOfValuesForOneRow;
        $Row = [$word_id, $definitions[$langs[$i]], $langs[$i]];
        $AllRows = array_merge($AllRows, $Row);
    }

    $values = implode(', ', $AllBindVals);

    $Sqlquery = "INSERT INTO `definitions`(`word_id`, `def`, `country_code`) VALUES $values";
    $stmt = $conn->prepare($Sqlquery);
    $stmt->bind_param($formatOfValues, ...$AllRows);
    $stmt->execute();

    $stmt->close();

    $conn->close();
}

function InsertNewWord($word_name, $conn)
{
    $stmt = $conn->prepare("INSERT INTO words (word_name) VALUES (?)");
            
    $stmt->bind_param("s", $word_name);
            
    $stmt->execute();
    
    $query = mysqli_query($conn, "SELECT LAST_INSERT_ID()");
    $row = mysqli_fetch_array($query);
    return $row[0];
}


function getLangs($conn)
{
    $stmt = $conn->prepare("SELECT * FROM lang ORDER BY default_lang DESC");
                        
    $stmt->execute();

    $result = $stmt-> get_result();

    $langs = [];
    $definitions = [];


    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $langs[] = $row["country_code"];
        }
    }

    return $langs;
}

function getDefinitions($conn, $word_id, $language = "")
{   
    // . ($language !== "") ? "country_code = $language" : ""

    $query = "SELECT * FROM `view_definitions` WHERE `word_id` = ?";

    $stmt = $conn->prepare($query);
        
    $stmt->bind_param("i", $word_id);
        
    $stmt->execute();

    $result = $stmt-> get_result();

    $response = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $response[$row["country_code"]] = $row;
        }
    }

    return $response;
};

function addLanguageToWordDefinition($conn, $word_id, $language) {
    $stmt = $conn->prepare("INSERT INTO `definitions`(`word_id`, `country_code`) VALUES (?, ?)");
                            
    $stmt->bind_param('is', $word_id, $language);

    $stmt->execute();

    $stmt->close();
}