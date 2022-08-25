<?php include 'header-script.php'; ?>

<?php
    function saveDepartment($data){
        global $department;

        $datatosave = json_decode(json_encode($data));
        $result = $department->createDepartment($datatosave);
        return $result;
    }
