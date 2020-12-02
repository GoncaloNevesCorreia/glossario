<form method="POST">
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