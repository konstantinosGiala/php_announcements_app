<?php include 'header-script.php'; ?>
<?php
    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    function saveRole($data){
        global $roles;
        
        $data = json_decode(json_encode($data));
        $result = $roles->createRoles($data);
        return $result;
    }
    

    // // define variables and set to empty values
    $usernameErr = $permissionErr = $authorizationErr = "";
    $username = $permission = $authorization  = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        if (empty($_POST["username"])) {
            $usernameErr = "Username is required";
        } else {
            $username = test_input($_POST["username"]);
        }

        if (empty($_POST["permission"])) {
            $permissionErr = "Permission is required";
        } else {
            $permission = test_input($_POST["permission"]);
        }

        if (empty($_POST["authorization"])) {
            $authorizationErr = "Authorization is required";
        } else {
            $authorization = test_input($_POST["authorization"]);
        }

        if (empty($usernameErr) && empty($permissionErr) && empty($authorizationErr)){
            $data = array(
                '_id' => $username,
                'permission' => $permission,
                'authorizations' => $authorization,
            );
            $result = saveRole($data);
        }

        
    }

    $data = json_decode($user->showUsers(), true);
    $allDepartments = json_decode($department->showDepartments(), true);
?>

<?php include 'header.php'; ?>
<div class="container mt-4">
    <div class="row">
        <div class="align-self-center">
            <div class="card card-body"> 

                <h2>Εισαγωγή νέου ρόλου σε χρήστη</h2>
                <!-- <?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?> -->
                <p><span class="text-danger">* required field</span></p>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
                    Username: <select name="username" id="username">
                        <option value="" default>Επιλέξτε Χρήστη</option>
                        <?php 
                            foreach($data as $value) {
                                echo '<option value="'.$value['_id']['$oid'].'">'.$value['username']."</option>";
                            } 
                        ?>
                    </select>
                    <span class="text-danger">* <?php echo $usernameErr;?></span>
                    <br><br>
                    Δικαιώματα: <select name="permission" id="permission">
                        <option value="" default>Επιλέξτε δικαιώματα πρόσβασης</option>
                        <option value="administrator">Administrator</option>
                        <option value="editor">Editor</option>
                        <option value="reader">Reader</option>
                    </select>
                    <span class="text-danger">* <?php echo $permissionErr;?></span>
                    <br><br>
                    Πρόσβαση: <select name="authorization" id="authorization">
                        <option value="" default>Επιλέξτε τμήμα</option>
                        <?php
                            foreach($data as $value){ 
                                foreach($value['subdepartment'] as $svalue) {
                                    echo '<option value="'.$svalue['name'].'">'.$svalue['name']."</option>";
                                }
                            } 
                        ?>
                    </select>
                    <span class="text-danger">* <?php echo $authorizationErr;?></span>
                    <br><br>
                    <input type="submit" name="submit" value="Submit">  
                </form>
            
                <hr>
            
                <table class="table">
                    <tr>
                        <th>Username</th>
                        <th>Όνομα</th>
                        <th>Επίθετο</th>
                        <th>Κατηγορία</th>
                        <th>Email</th>
                        <th>Ρόλοι</th>
                    </tr>
                    <?php
                        foreach($data as $value) {
                            echo '<tr>';
                                echo '<td>'.$value['username'].'</td>';
                                echo '<td>'.$value['name'].'</td>';
                                echo '<td>'.$value['surname'].'</td>';
                                echo '<td>'.$value['user_category']['name'].'</td>';
                                echo '<td>'.$value['email'].'</td>';
                                echo '<td>';
                                foreach ($value['roles'] as $rvalue){
                                    echo $rvalue['app'].",".$rvalue['permission']."<br>";
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