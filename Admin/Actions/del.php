<?php
    require("../Includes/dbh.php");

    $pag_no = (isset($_POST["page_no"]) && !empty($_POST["page_no"])) ? $_POST["page_no"] : 1;

    if (isset($_POST["delete"])) {
        if (isset($_POST["word_id"])) {
            if (!empty($_POST["word_id"])) {
                $word_id = intval($_POST["word_id"]);
                if ($word_id > 0) {
                
                    $stmt = $conn->prepare('DELETE FROM `definitions` WHERE `word_id` = ?');
                    $stmt->bind_param('i', $word_id); // 's' specifies the variable type => 'string'

                    $stmt->execute();

                    $stmt = $conn->prepare('DELETE FROM `words` WHERE `word_id` = ?');
                    $stmt->bind_param('i', $word_id); // 's' specifies the variable type => 'string'

                    $stmt->execute();
                }
            }
        }
    }
    
    header("Location: ../index.php?page_no=$pag_no");
    exit();
