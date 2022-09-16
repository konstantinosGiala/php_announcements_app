<?php include 'header-script.php'; ?>
<?php

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    function saveUserCategory($data){
        global $usercategory;
        
        $data = json_decode(json_encode($data));
        $result = $usercategory->createUsercategory($data);
        return $result;
    }

    // // define variables and set to empty values
    $nameErr = $identifierErr = "";
    $name = $identifier = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
         
        if (empty($_POST["name"])) {
            $nameErr = "Name is required";
        } else {
            $name = test_input($_POST["name"]);
            // check if name only contains letters and whitespace or Greek letters
            if (!preg_match("/^[a-zA-Z\p{Greek}\s]+$/u",$name)) {
            $nameErr = "Only letters and white space allowed";
            }
        }
         
        if (empty($_POST["identifier"])) {
            $identifierErr = "Identifier is required";
        } else {
            $identifier = test_input($_POST["identifier"]);
            // check if identifier is number
            if (!is_numeric($identifier)) {
            $identifierErr = "Invalid identifier format";
            }
        }

        if (empty($identifierErr) && empty($nameErr)){
            $data = array(
                'identifier' => $identifier,
                'name' => $name
            );
            $result = saveUserCategory($data);
        }
    }

    $data = json_decode($usercategory->showUsercategories(), true);
?>

<?php include 'header.php'; ?>
<div class="container mt-4">
    <div class="row">
        <div class="align-self-center">
            <div class="card card-body"> 

                <h2>Εισαγωγή νέας κατηγοριας χρηστών</h2>
                <p><span class="text-danger">* required field</span></p>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
                    Identifier: <input type="text" name="identifier" value="<?php echo $identifier;?>">
                    <span class="text-danger">* <?php echo $identifierErr;?></span>
                    <br><br>
                    Name: <input type="text" name="name" value="<?php echo $name;?>">
                    <span class="text-danger">* <?php echo $nameErr;?></span>
                    <br><br>
                    <input type="submit" name="submit" value="Submit">  
                </form>
            
                <hr>
            
                <table class="table">
                    <tr>
                        <th>Κατηγορία</th>
                        <th>Αναγνωριστικό</th>
                    </tr>
                    <?php
                        foreach($data as $value) {
                            echo '<tr>';
                                echo '<td>'.$value['name'].'</td>';
                                echo '<td>'.$value['identifier'].'</td>';
                            echo '</tr>';
                        }
                    ?>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>