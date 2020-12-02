<?php
    require("./Includes/dbh.php");
    require("./Actions/functions.php");

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

    $langs = getLangs($conn);

    $valuesForTextboxs;

    if (isset($_POST["word_id"])) {
        if (!empty($_POST["word_id"])) {
            $word_id = (int)$_POST["word_id"];
            if ($word_id > 0) {
                
                $valuesForTextboxs = getDefinitions($conn, $word_id);

                if (count($valuesForTextboxs) !== count($langs)) { // Se o registo não tiver todas as linguas geradas...
                    foreach ($langs as $language) {
                        if (!isset($valuesForTextboxs[$language])) { // Se a lingua não estiver gerada, adiciona um registo
                            addLanguageToWordDefinition($conn, $word_id, $language);
                            $valuesForTextboxs = getDefinitions($conn, $word_id);
                        }
                    }
                }
            }
        }
    }
?>



<nav>
    <a class="logout-btn" href="logout.php">Logout</a>
</nav>


<div class="content-Wrapper">

    <!-- FORMULARIO -->

    <form action="Actions/<?php echo isset($valuesForTextboxs) ? "edit" : "insert" ?>.php" method="post"
        id="form-Actions">
        <input type="hidden" name="word_id" id="wordID"
            value="<?php echo isset($valuesForTextboxs) ? $valuesForTextboxs[array_key_first($valuesForTextboxs)]["word_id"] : "" ?>">
        <input type="hidden" id="page_no" name="page_no" value="<?php echo $page_no; ?>">


        <div id="div_Word">
            <label for="word_name">Termo: </label>
            <input
                value="<?php echo isset($valuesForTextboxs) ? $valuesForTextboxs[array_key_first($valuesForTextboxs)]["word_name"] : "" ?>"
                id="word_name" name="word_name" type="text" autocomplete="off" required>
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
        <?php include('./includes/tableResults.php'); ?>
    </div>
</div>
<script>
var rows = <?php echo json_encode($rows); ?>;
</script>