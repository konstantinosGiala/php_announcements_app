<?php 

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json; charset=utf-8');

use OpenApi\Annotations as OA;

class Subdepartment {

    protected $collection;

    protected $generalFunctions;

    public function __construct($connection) {
        try {
            $this->collection = $connection->connect_to_department();
            error_log("Connection to collection Department");
            $this->generalFunctions = new GeneralFunctions();
        }
        catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
            error_log("Problem in connection with collection Department".$e);
        }
    }
    
    /**
     * @OA\Get(
     *   path="/subdepartment/{id}/list",
     *   description="A list with subdepartments",
     *   operationId="showSubdepartment",
     *   tags={"Subdepartment"},
     *   @OA\Parameter(
     *      name="id",
     *      in="path",
     *      description="Department id to show Subdepartments",
     *      required=true,
     *      @OA\Schema(
     *          type="string",
     *          example="6250932b62a9e94948207113"
     *       ),
     *     ),
     *   @OA\Response(
     *     response="200",
     *     description="Returns a list of subdepartments for specific department"
     *   ),
     *   @OA\Response(
     *     response="404",
     *     description="Error"
     *   )
     * )
     */
    public function showSubdepartment($id) {
        if( isset( $id )) {
            try {
                $result = $this->collection->findOne(
                    [ '_id'=>new MongoDB\BSON\ObjectId($id) ],
                    [
                        'projection' => [
                            'subdepartment' => 1
                        ],
                    ]);
                if (count($result)>0):
                    return json_encode($result);
                else:
                    return $this->returnValue('false');
                endif;
            }
            catch (MongoDB\Exception\UnsupportedException $e){
                error_log("Problem in findOne subdepartment \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in findOne subdepartment \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in findOne subdepartment \n".$e);
                return $this->returnValue('false');
            };
        } else 
            return $this->returnValue('false'); 
    }

    /**
     * @OA\Post(
     *     path="/subdepartment/create",
     *     description="Create a subdepartment",
     *     operationId="createSubdepartment",
     *     tags={"Subdepartment"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="identifier",type="integer"),
     *                 @OA\Property(property="name",type="string"),
     *                 example={"identifier": 4, "name": "Συμβάσεις"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Retuns a json object with true or false value to field success",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(type="boolean")
     *             },
     *             @OA\Examples(example="False bool", value={"success": false}, summary="A false boolean value."),
     *             @OA\Examples(example="True bool", value={"success": true}, summary="A true boolean value."),
     *         )
     *     )
     * )
     */
    public function createSubdepartment($data) {
        $identifier = $data->identifier;
        $name = $data->name;
        if( isset( $identifier ) && isset($name)) {
            try {
                $result = $this->collection->updateOne( 
                    [ 'identifier'=>$identifier ],
                    [ 
                        '$push' => [
                            'subdepartment' => [
                                '_id' => new MongoDB\BSON\ObjectId(), 
                                'name' => $name,                            
                            ]
                        ]
                    ]
                );
                if ($result->getModifiedCount()==1)
                    return $this->returnValue('true');
                else 
                    return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in insert subdepartment \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in insert subdepartment \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in insert subdepartment \n".$e);
                return $this->returnValue('false');
            };
        } else 
            return $this->returnValue('false');

    }

    /**
     * @OA\Delete(
     *     path="/subdepartment/{identifier}/{id}/delete",
     *     description="Delete a subdepartment",
     *     operationId="deleteSubdepartment",
     *     tags={"Subdepartment"},
     *     @OA\Parameter(
     *         name="identifier",
     *         in="path",
     *         description="Department identifier to delete subdepartment",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example="4"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Subdepartment id to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="6250932b62a9e94948207113"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Retuns a json object with true or false value to field success",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(type="boolean")
     *             },
     *             @OA\Examples(example="False bool", value={"success": false}, summary="A false boolean value."),
     *             @OA\Examples(example="True bool", value={"success": true}, summary="A true boolean value."),
     *         )
     *     )
     * )
     */
    public function deleteSubdepartment($identifier,$id) {
        if( isset( $identifier ) && isset($id)) {
            try {
                $result = $this->collection->updateOne( 
                    [ 'identifier'=>intval($identifier) ],
                    [ 
                        '$pull' => [
                            'subdepartment' => [
                                '_id' => new MongoDB\BSON\ObjectId($id)
                            ]
                        ]
                    ]
                );
                if ($result->getModifiedCount()==1)
                    return $this->returnValue('true');
                else 
                    return $this->returnValue('false');
            }
            catch (MongoDB\Exception\UnsupportedException $e){
                error_log("Problem in delete subdepartment \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in delete subdepartment \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in delete subdepartment \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in delete subdepartment \n".$e);
                return $this->returnValue('false');
            };
        } else 
            return "$this->returnValue('false')";
    }

    /**
     * @OA\Patch(
     *     path="/subdepartment/update",
     *     description="Update a subdepartment",
     *     operationId="updateSubdepartment",
     *     tags={"Subdepartment"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="identifier",type="integer"),
     *                 @OA\Property(property="_id",type="string"),
     *                 @OA\Property(property="name",type="string"),
     *                 example={"identifier": 1, "_id":"6244840de0c3d34f620e5dd6", "name": "Βλάβες"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Retuns a json object with true or false value to field success",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(type="boolean")
     *             },
     *             @OA\Examples(example="False bool", value={"success": false}, summary="A false boolean value."),
     *             @OA\Examples(example="True bool", value={"success": true}, summary="A true boolean value."),
     *         )
     *     )
     * )
     */
    public function updateSubdepartment($data) {
        $identifier = $data->identifier;
        $id = $data->_id;
        $name = $data->name;
        
        if( isset( $identifier ) && isset($name) && isset($id)) {
            try {
                $result = $this->collection->updateOne( 
                    [ 
                        'identifier' => intval($identifier),
                        'subdepartment._id' => new MongoDB\BSON\ObjectId($id)
                    ],
                    [ '$set' => [ 'subdepartment.$.name' => $name ]]
                );
                if ($result->getModifiedCount()==1)
                    return $this->returnValue('true');
                else 
                    return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in update subdepartment \n".$e);
                return "$this->returnValue('false')";
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in update subdepartment \n".$e);
                return "$this->returnValue('false')";
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in update subdepartment \n".$e);
                return "$this->returnValue('false')";
            };
        } else 
            return "$this->returnValue('false')";
    }
}
?>