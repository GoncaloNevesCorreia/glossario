<?php

    require("./Admin/Includes/dbh.php");
    
    $language;

    if (isset($_GET["lang"])) {
        if (!empty($_GET["lang"])) {
            $language = $_GET["lang"];
        }
    }

    if (!isset($language)) {
        $language = 'pt';
    }

    $langs = [];

    $stmt = $conn->prepare("SELECT * FROM `lang` ORDER BY `default_lang` DESC");
                            
    $stmt->execute();

    $result = $stmt-> get_result();

    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $langs[$row["country_code"]] = $row;
        }
    }

    if (!isset($langs[$language])) {
        $language = "pt";
    }

?>



    <div class="div_title_languages">
    <h1>Glossário</h1>
        <div class="languages">
        <div class="active-lang">
    
            <?php

                echo "<div class='icon'>
                        <span class='flag-icon flag-icon-$language'></span> 
                    </div>

                    {$langs[$language]['country_name']}
                ";

            ?>

        </div>

            <div class="dropdown-content">
                <?php

                    foreach ($langs as $lang) {
                        if ($lang["country_code"] !== $language) {
                            $currentLanguageCode = $lang["country_code"];
                            $currentLanguageName = $lang["country_name"];
                            
                            echo "<a href='?lang=$currentLanguageCode'><span class='flag-icon flag-icon-$currentLanguageCode'></span> $currentLanguageName</a>";
                        }
                    }

                ?>

            </div>
        </div>
</div>

    <header>
        <div class="anchors">
            <ul> 
                <li><a href="#A">A</a></li>
                <li><a href="#B">B</a></li>
                <li><a href="#C">C</a></li>
                <li><a href="#D">D</a></li>
                <li><a href="#E">E</a></li>
                <li><a href="#F">F</a></li>
                <li><a href="#G">G</a></li>
                <li><a href="#H">H</a></li>
                <li><a href="#I">I</a></li>
                <li><a href="#J">J</a></li>
                <li><a href="#K">K</a></li>
                <li><a href="#L">L</a></li>
                <li><a href="#M">M</a></li>
                <li><a href="#N">N</a></li>
                <li><a href="#O">O</a></li>
                <li><a href="#P">P</a></li>
                <li><a href="#Q">Q</a></li>
                <li><a href="#R">R</a></li>
                <li><a href="#S">S</a></li>
                <li><a href="#T">T</a></li>
                <li><a href="#U">U</a></li>
                <li><a href="#V">V</a></li>
                <li><a href="#W">W</a></li>
                <li><a href="#X">X</a></li>
                <li><a href="#Y">Y</a></li>
                <li><a href="#Z">Z</a></li>
                <li><a href="#*">*</a></li>

            
            </ul>
        </div>
    </header>

    <div class="wrapper">


        <?php
        
            $arrayAlphabet = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "*"];

            $stmt = $conn->prepare("SELECT * FROM `view_definitions` where country_code = ? ORDER BY IF(word_name RLIKE '^[a-z]', 1, 2), word_name");
            
            $stmt->bind_param('s', $language);

            $stmt->execute();

            $result = $stmt-> get_result();


            $letter = -1;

            if ($result->num_rows > 0) { //
                while ($row = $result->fetch_assoc()) {
                    if ($letter != 27) {
                        $currentLetterIndex = array_search(strtoupper(substr($row['word_name'], 0, 1)), $arrayAlphabet);

                        if ($currentLetterIndex != $letter) { // Se não começar pela mesma letra que anteriormente, mudar de seção
                            while ($currentLetterIndex != $letter) { // Ciclo para adicionar as sections
                                
                                if ($letter == 26) {
                                    break;
                                }

                                if ($letter != -1) { // Fecha a section anterior
                                    echo "</section>";
                                }

                                

                                $letter++;
                                


                                if ($letter <= count($arrayAlphabet) - 1) {
                                    // Abre uma nova section
                                    echo "<section>
                                            <a class='anchor' id='{$arrayAlphabet[$letter]}'></a>
                                            <div class='separator'>{$arrayAlphabet[$letter]}</div>";
                                }
                            }
                        }
                    }
                    
                    
                    $name = htmlspecialchars($row['word_name'], ENT_QUOTES, 'UTF-8');
                    $definition = htmlspecialchars($row['def'], ENT_QUOTES, 'UTF-8');
                    
                    echo "<div class='item'>
                            <div class='title'>
                                <span>$name</span>
                            </div>
                            <div class='definition'>
                                $definition
                            </div>
                        </div>";
                }

                
                if ($letter < count($arrayAlphabet)) {
                    while ($letter < count($arrayAlphabet)) { // Ciclo para adicionar as sections


                        if ($letter != -1) { // Fecha a section anterior
                            echo "</section>";
                        }

                        $letter++;
                        
                        if ($letter < count($arrayAlphabet)) {
                            // Abre uma nova section
                            echo "<section>
                                    <a class='anchor' id='{$arrayAlphabet[$letter]}'></a>
                                     <div class='separator'>{$arrayAlphabet[$letter]}</div>";
                        }
                    }
                }
            } else {
                echo "0 results";
            }
        ?>
    </div>