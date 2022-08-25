<?php

    //require $_SERVER['DOCUMENT_ROOT'].'/php_api_jwt/vendor/autoload.php';
    require dirname(__FILE__,2).'/vendor/autoload.php';

    // include $_SERVER['DOCUMENT_ROOT'].'/php_api_jwt/connect.php';
    // include $_SERVER['DOCUMENT_ROOT'].'/php_api_jwt/model/Department.php';
    include dirname(__FILE__,2).'/connect.php';
    include dirname(__FILE__,2).'/model/Department.php';
    
    // Uncomment for localhost running
    // $dotenv = Dotenv\Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT'].'/php_api_jwt');
    $dotenv = Dotenv\Dotenv::createImmutable(dirname(__FILE__,2));
    $dotenv->load();

    $MDB_USER = $_ENV['MDB_USER'];
    $MDB_PASS = $_ENV['MDB_PASS'];
    $ATLAS_CLUSTER_SRV = $_ENV['ATLAS_CLUSTER_SRV'];

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    function saveDepartment($data){
        global $department;
        
        $data = json_decode(json_encode($data));
        $result = $department->createDepartment($data);
        return $result;
    }
    
    $connection = new Connection($MDB_USER, $MDB_PASS, $ATLAS_CLUSTER_SRV);
    $department = new Department($connection);
    header('Content-Type: text/html; charset=utf-8');

    // // define variables and set to empty values
    $nameErr = $identifierErr = "";
    $name = $identifier = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
         
        if (empty($_POST["name"])) {
            $nameErr = "Name is required";
        } else {
            $name = test_input($_POST["name"]);
            // check if name only contains letters and whitespace or Greek letters
            if (!preg_match("/^[a-zA-Z\p{Greek}]+$/u",$name)) {
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
            $result = saveDepartment($data);
        }
    }

    $allDepartments = json_decode($department->showDepartments(), true);
?>

<!DOCTYPE HTML>  
<html>
    <head>
        <title>Departments</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <style>
            .error {color: #FF0000;}
        </style>
    </head>
    <body>  

        <h2>Εισαγωγή νέου Department</h2>
        <!-- <?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?> -->
        <p><span class="error">* required field</span></p>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
            Identifier: <input type="text" name="identifier" value="<?php echo $identifier;?>">
            <span class="error">* <?php echo $identifierErr;?></span>
            <br><br>
            Name: <input type="text" name="name" value="<?php echo $name;?>">
            <span class="error">* <?php echo $nameErr;?></span>
            <br><br>
            <input type="submit" name="submit" value="Submit">  
        </form>
        
        <hr>
        
        <table border="1px">
            <tr>
                <th>Διεύθυνση</th>
                <th>Αναγνωριστικό</th>
            </tr>
            <?php
                foreach($allDepartments as $value) {
                    echo '<tr>';
                        echo '<td>'.$value['name'].'</td>';
                        echo '<td>'.$value['identifier'].'</td>';
                    echo '</tr>';
                }
            ?>
        </table>

    </body>
</html>