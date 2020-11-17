<?php
    require("../Includes/dbh.php");
    
    $pag_no = (isset($_POST["page_no"]) && !empty($_POST["page_no"])) ? $_POST["page_no"] : 1;
    

    if (isset($_POST["btnUpdate"])) {

        if (isset($_POST["word_id"]) && isset($_POST["word_name"])) {
            if (!empty($_POST["word_id"]) && !empty($_POST["word_name"])) {
                
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

                if ($allFieldsAreFilled) {
                    $word_id = intval($_POST["word_id"]);
                    if ($word_id > 0) {
                        
                        $word_name = $_POST["word_name"];
                        
                        print($word_id);
                        print($word_name);

                        $stmt = $conn->prepare('UPDATE `words` SET `word_name`= ? WHERE `word_id` = ?');
                        $stmt->bind_param('si', $word_name, $word_id);
    
                        $stmt->execute();
    
                        $stmt->close();


                        for ($i=0; $i < count($langs); $i++) { 
                            $definition = $definitions[$langs[$i]];
    
    
                            $stmt = $conn->prepare('UPDATE `definitions` SET `def`= ? WHERE `word_id` = ? and `country_code` = ?');
                            $stmt->bind_param('sis', $definition, $word_id, $langs[$i]);
        
                            $stmt->execute();
        
                            $stmt->close();
                        }
                    }
                }
            }
        }
    }

    header("Location: ../index.php?page_no=$pag_no");
    die();
