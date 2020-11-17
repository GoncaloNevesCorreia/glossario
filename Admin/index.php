<?php
    require("./Includes/dbh.php");

    session_start();

    if (!isset($_SESSION["username"])) {
        header("Location: login.php");
        die();
    }
    
    if (isset($_GET['page_no']) && $_GET['page_no']!="") {
        $page_no = $_GET['page_no'];
    } else {
        $page_no = 1;
    }

    $langs = [];

    $stmt = $conn->prepare("SELECT * FROM `lang`");
                            
    $stmt->execute();

    $result = $stmt-> get_result();

    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($langs, $row["country_code"]);
        }
    }

    $valuesForTextboxs;

    if (isset($_POST["word_id"])) {
        if (!empty($_POST["word_id"])) {
            $word_id = (int)$_POST["word_id"];
            if ($word_id > 0) {
                $stmt = $conn->prepare("SELECT * FROM `view_definitions` WHERE `word_id` = ?");
        
                $stmt->bind_param("s", $word_id);
        
                $stmt->execute();

                $result = $stmt-> get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $valuesForTextboxs[$row["country_code"]] = $row;
                    }
                }

                if (count($valuesForTextboxs) !== count($langs)) { // Se o registo não tiver todas as linguas geradas...
                    foreach ($langs as $language) {
                        if (!isset($valuesForTextboxs[$language])) { // Se a lingua não estiver gerada, adiciona um registo
                            $stmt = $conn->prepare("INSERT INTO `definitions`(`word_id`, `country_code`) VALUES (?, ?)");
                            
                            $stmt->bind_param('is', $word_id, $language);
                    
                            $stmt->execute();

                            $stmt->close();

                            $stmt = $conn->prepare("SELECT * FROM `view_definitions` WHERE `word_id` = ?");
        
                            $stmt->bind_param("s", $word_id);
                    
                            $stmt->execute();

                            $result = $stmt-> get_result();

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $valuesForTextboxs[$row["country_code"]] = $row;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glossário - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="Assets/css/flag-icon.min.css">
    <link rel="stylesheet" href="Assets/css/style.css">
    <script defer src="Assets/js/main.js"></script>
</head>

<body>

    <nav>
        <a class="logout-btn" href="logout.php">Logout</a>
    </nav>


    <div class="content-Wrapper">

        <!-- FORMULARIO -->

        <form action="Actions/<?php echo isset($valuesForTextboxs) ? "edit" : "insert" ?>.php" method="post" id="form-Actions">  
            <input type="hidden" name="word_id" id="wordID" value="<?php echo isset($valuesForTextboxs) ? $valuesForTextboxs[array_key_first($valuesForTextboxs)]["word_id"] : "" ?>">
            <input type="hidden" id="page_no" name="page_no" value="<?php echo $page_no; ?>">

            <div id="div_Word">
                <label for="word_name">Termo: </label>
                <input value="<?php echo isset($valuesForTextboxs) ? $valuesForTextboxs[array_key_first($valuesForTextboxs)]["word_name"] : "" ?>" id="word_name" name="word_name" type="text" autocomplete="off"  required>
            </div>
            <ul class='nav nav-tabs' id='myTab' role='tablist'>
            <?php
                
                $first = true;
                foreach ($langs as $language) {
                    echo "
                    <li class='nav-item'>
                        <a class='nav-link " . ($first ? "active" : "") ."' id='$language-tab' data-toggle='tab' href='#$language' role='tab' aria-controls='$language'
                            aria-selected='true'><span class='flag-icon flag-icon-$language'></span></a>
                    </li>";
                    $first = false;
                }

            ?>
            </ul>
            <div class="tab-content" id="myTabContent">
            <?php
                $first = true;
                foreach ($langs as $language) {
                    $def = (isset($valuesForTextboxs)) ? $valuesForTextboxs[$language]["def"] : "";

                    echo "<div class='tab-pane fade show " . ($first ? "active" : "") ."' id='$language' role='tabpanel' aria-labelledby='$language-tab'>
                    <label>Definição: </label>
                    <textarea name='def_$language' class='word_definition' cols='30' rows='10' required>$def</textarea>
                </div>";
                    $first = false;
                }
            ?>
            </div>
            
            <?php
                if (isset($valuesForTextboxs)) {
                    echo "<button id='btnSubmit' type='submit' name='btnUpdate'>Editar</button>
                          <button id='btnClear' >Cancelar</button>";
                } else {
                    echo "<button id='btnSubmit' type='submit' name='btnInsert'>Inserir</button>";
                }
            ?>
            
            
        </form>

        <!-- FORMULARIO -->

        <div class="definitions">
            <form method="POST" >
                <input type="hidden" id="word_id" name="word_id">
                <button id="submitEdit" type="submit" style="display:none;"></button>
                <table id="defTable">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Definição</th>
                        </tr>
                    </thead>
                    <?php

                        $total_records_per_page = 10;

                        


                        $result_count = mysqli_query($conn, "SELECT COUNT(*) As total_records FROM `view_definitions`");
                        $total_records = mysqli_fetch_array($result_count);
                        $total_records = $total_records['total_records'];
                        $total_no_of_pages = ceil($total_records / $total_records_per_page);
                        $second_last = $total_no_of_pages - 1; // total pages minus 1
                        
                        if ($page_no <= 0) {
                            $page_no = 1;
                        }
                        if ($page_no > $total_no_of_pages) {
                            $page_no = $total_no_of_pages;
                        }

                        $offset = ($page_no-1) * $total_records_per_page;
                        $previous_page = $page_no - 1;
                        $next_page = $page_no + 1;
                        $adjacents = "2";


                        $stmt = $conn->prepare("SELECT * FROM `view_definitions` where `country_code` = 'pt' ORDER BY `word_name` ASC LIMIT $offset, $total_records_per_page");
                        
                        $stmt->execute();

                        $rows = [];
                        $result = $stmt-> get_result();

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $rows[$row['def_id']] = $row;

                                $word_id = $row['word_id'];
                                $word_name = htmlspecialchars($row['word_name'], ENT_QUOTES, 'UTF-8');
                                $definition = htmlspecialchars($row['def'], ENT_QUOTES, 'UTF-8');
                                
                                echo "<tr data-id='$word_id'>
                                        <td>$word_name</td>
                                        <td>$definition</td>
                                    </tr>";
                            }
                        } else {
                            echo "0 results";
                        }

                    ?>
                </table>

                <div style='padding: 10px 20px 0px; border-top: dotted 1px #CCC;'>
                    <strong>Page <?php echo $page_no." of ".$total_no_of_pages; ?></strong>
                </div>
                <ul class="pagination">
                    <?php if ($page_no > 1) {
                        echo "<li><a href='?page_no=1'>Primeira Página</a></li>";
                    } ?>

                    <li <?php if ($page_no <= 1) {
                        echo "class='disabled'";
                    } ?>>
                        <a <?php if ($page_no > 1) {
                        echo "href='?page_no=$previous_page'";
                    } ?>>Anterior</a>
                    </li>

                    <?php
                        if ($total_no_of_pages <= 10) {
                            for ($counter = 1; $counter <= $total_no_of_pages; $counter++) {
                                if ($counter == $page_no) {
                                    echo "<li class='active'><a>$counter</a></li>";
                                } else {
                                    echo "<li><a href='?page_no=$counter'>$counter</a></li>";
                                }
                            }
                        } elseif ($total_no_of_pages > 10) {
                            if ($page_no <= 4) {
                                for ($counter = 1; $counter < 8; $counter++) {
                                    if ($counter == $page_no) {
                                        echo "<li class='active'><a>$counter</a></li>";
                                    } else {
                                        echo "<li><a href='?page_no=$counter'>$counter</a></li>";
                                    }
                                }
                                echo "<li><a>...</a></li>";
                                echo "<li><a href='?page_no=$second_last'>$second_last</a></li>";
                                echo "<li><a href='?page_no=$total_no_of_pages'>$total_no_of_pages</a></li>";
                            } elseif ($page_no > 4 && $page_no < $total_no_of_pages - 4) {
                                echo "<li><a href='?page_no=1'>1</a></li>";
                                echo "<li><a href='?page_no=2'>2</a></li>";
                                echo "<li><a>...</a></li>";
                                for (
                                        $counter = $page_no - $adjacents;
                                        $counter <= $page_no + $adjacents;
                                        $counter++
                                        ) {
                                    if ($counter == $page_no) {
                                        echo "<li class='active'><a>$counter</a></li>";
                                    } else {
                                        echo "<li><a href='?page_no=$counter'>$counter</a></li>";
                                    }
                                }
                                echo "<li><a>...</a></li>";
                                echo "<li><a href='?page_no=$second_last'>$second_last</a></li>";
                                echo "<li><a href='?page_no=$total_no_of_pages'>$total_no_of_pages</a></li>";
                            } else {
                                echo "<li><a href='?page_no=1'>1</a></li>";
                                echo "<li><a href='?page_no=2'>2</a></li>";
                                echo "<li><a>...</a></li>";
                                for (
                                        $counter = $total_no_of_pages - 6;
                                        $counter <= $total_no_of_pages;
                                        $counter++
                                        ) {
                                    if ($counter == $page_no) {
                                        echo "<li class='active'><a>$counter</a></li>";
                                    } else {
                                        echo "<li><a href='?page_no=$counter'>$counter</a></li>";
                                    }
                                }
                            }
                        }
                    ?>

                    <li <?php if ($page_no >= $total_no_of_pages) {
                        echo "class='disabled'";
                    } ?>>
                        <a <?php if ($page_no < $total_no_of_pages) {
                        echo "href='?page_no=$next_page'";
                    } ?>>Próxima</a>
                    </li>

                    <?php if ($page_no < $total_no_of_pages) {
                        echo "<li><a href='?page_no=$total_no_of_pages'>Última &rsaquo;&rsaquo;</a></li>";
                    } ?>
                </ul>



            </form>
        </div>
    </div>
    <script>
    var rows = <?php echo json_encode($rows); ?>;
    </script>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
</body>
</html>