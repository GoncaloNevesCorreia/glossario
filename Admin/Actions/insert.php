<?php

    require("../Includes/dbh.php");
    
    $pag_no = (isset($_POST["page_no"]) && !empty($_POST["page_no"])) ? $_POST["page_no"] : 1;

    if (isset($_POST["btnInsert"])) { // Se acederam ao script atraves do click no botão "btnInsert"
        
        if (isset($_POST["word_name"])) {
            if (!empty($_POST["word_name"])) {

                /*  Garantir que todos os campos foram preenchidos
                        - def_pt, def_us... 
                    Ir buscar todas as linguas à base de dados
                    Verificar se existe valores no POST para cada lingua

                */
                $stmt = $conn->prepare("SELECT * FROM lang");
                        
                $stmt->execute();

                $result = $stmt-> get_result();

                $langs = [];
                $definitions = []; 

                $allFieldsAreFilled = true;

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $langs[] = $row["country_code"];
                        $language = $row["country_code"];
                        if(isset($_POST["def_$language"])) {
                            if(!empty($_POST["def_$language"])) {
                                $definitions[$language] = $_POST["def_$language"];
                                continue;
                            }
                        }

                        $allFieldsAreFilled = false;
                        break;
                    }
                }


                /*

                    Umas vez garantido que todos os campos estão preenchidos,
                    será inserido o termo na tabela "words"
                    Logo de seguida será retornado na base de dados o valor word_id
                    do termo inserido nesse momento, para ser adicionado na tabela "definitions"
                
                */

                if ($allFieldsAreFilled) {
                    $word_name = $_POST["word_name"];
        
                    $stmt = $conn->prepare("INSERT INTO words (word_name) VALUES (?)");
            
                    $stmt->bind_param("s", $word_name);
            
                    $stmt->execute();
    
                    $query = mysqli_query($conn, "SELECT LAST_INSERT_ID()");
                    $row = mysqli_fetch_array($query);
                    $word_id = $row[0];
    
                    /*
    
                        Adicionar as definições na base de dados, por cada lingua
                    
                    
                    */
    
                    $strFormat = "iss";
                    $strFormatSum = "";
                    $bindArray;
                    $arrayValuesSum = [];
    
                    for ($i=0; $i < count($langs); $i++) { 
                        $bindVals = "(?, ?, ?)";
                        $bindArray[] = $bindVals;
                        $strFormatSum .= $strFormat;
                        $arrayValues = [$word_id, $definitions[$langs[$i]], $langs[$i]];
                        $arrayValuesSum = array_merge($arrayValuesSum, $arrayValues);
                    }
    
                    $values = implode(', ', $bindArray);
                    
                    $Sqlquery = "INSERT INTO `definitions`(`word_id`, `def`, `country_code`) VALUES $values";
                    $stmt = $conn->prepare($Sqlquery);
                    $stmt->bind_param($strFormatSum, ...$arrayValuesSum);
                    $stmt->execute();
    
                    $stmt->close();
            
                    $conn->close();
                }
            }
        }
    }

    header("Location: ../index.php?page_no=$pag_no");
    die();
