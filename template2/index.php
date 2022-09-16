<?php include 'header-script.php'; ?>
<?php include 'header.php'; ?>
        
        <div class="container mt-4">

            <table class="table table-striped">
                <tr>
                    <th>Διεύθυνση</th>
                    <th>Αναγνωριστικό</th>
                    <th>Τμήματα</th>
                    <th>Κατηγορίες</th>
                </tr>
                <?php
                    foreach ($data as $value){
                        echo "<tr>";
                            echo "<td>".$value['name']."</td>";
                            echo "<td>".$value['identifier']."</td>";
                            echo "<td>";
                                foreach ($value["subdepartment"] as $valueX){
                                    echo $valueX["name"]."<br>";
                                }
                            echo "</td>";
                            echo "<td>";
                                foreach ($value["categories"] as $valueX){
                                    echo $valueX["name"]."(".$valueX["subdepartment_id"].")<br>";
                                }
                            echo "</td>";
                        echo "</tr>";    
                    }
                ?>
            </table>
        </div>
<?php include 'footer.php'; ?>

