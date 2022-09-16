<?php include 'header-script.php'; ?>
<?php include 'header.php'; ?>
        
        <div class="container mt-4">
            <div class="row">
                <div class="align-self-center">
                    <div class="card card-body">
                        <table class="table">
                            <tr>
                                <th>Διεύθυνση</th>
                                <th>Αναγνωριστικό</th>
                                <th>Τμήματα</th>
                                <th>Κατηγορίες</th>
                            </tr>
                            <?php
                                foreach($data as $value) {
                                    echo '<tr>';
                                        echo '<td>'.$value['name'].'</td>';
                                        echo '<td>'.$value['identifier'].'</td>';
                                        echo '<td>';
                                            foreach ($value['subdepartment'] as $svalue){
                                                echo $svalue['name']."<br>";
                                            }
                                        echo '</td>';
                                        echo '<td>';
                                            foreach ($value['categories'] as $cvalue){
                                                echo $cvalue['name']."<br>";
                                            }
                                        echo '</td>';
                                    echo '</tr>';
                                }
                            ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
<?php include 'footer.php'; ?>

