<?php

class GeneralFunctions{

    function returnValue($return, $status){
        if ($status==='true')
            return json_encode(array(
                'data' => json_encode($return),
                'success' => true
                )
            );
        else 
        return json_encode(array(
                'data' => json_encode($return),
                'success' => false
                )
            );
        // return json_encode(array('success' => false));
    }

}

?>    