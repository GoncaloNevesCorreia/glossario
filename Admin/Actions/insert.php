<?php

    require("../Includes/dbh.php");
    require("./functions.php");
    
    
    $pag_no = (isset($_POST["page_no"]) && !empty($_POST["page_no"])) ? $_POST["page_no"] : 1;

    if (isset($_POST["btnInsert"])) { // Se acederam ao script atraves do click no botão "btnInsert"
        
        if (isset($_POST["word_name"])) {
            if (!empty($_POST["word_name"])) {

                /*  Garantir que todos os campos foram preenchidos
                        - def_pt, def_us...
                    Ir buscar todas as linguas à base de dados
                    Verificar se existe valores no POST para cada lingua

                */

                $langs = getLangs($conn);

                $AllDefinitionsReceived = true;
                foreach ($langs as $countryCode) {
                    
                
                    if (isset($_POST["def_$countryCode"])) {
                        if (!empty($_POST["def_$countryCode"])) {
                            $definitions[$countryCode] = $_POST["def_$countryCode"];
                            continue;
                        }
                    }

                    $AllDefinitionsReceived = false;
                    break;
                }



                /*

                    Umas vez garantido que todos os campos estão preenchidos,
                    será inserido o termo na tabela "words"
                    Logo de seguida será retornado na base de dados o valor word_id
                    do termo inserido nesse momento, para ser adicionado na tabela "definitions"

                */

                if ($AllDefinitionsReceived) {
                    $word_name = $_POST["word_name"];
        
                    $word_id = InsertNewWord($word_name, $conn);
    

                    //Adicionar as definições na base de dados, por cada lingua

                    InsertDefInDB($word_id, $definitions, "iss", $langs, $conn);
                }
            }
        }
    }
        
    

    header("Location: ../index.php?page_no=$pag_no");
    die();
