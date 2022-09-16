<?php 

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json; charset=utf-8');

use OpenApi\Annotations as OA;

class Roles {
    
    protected $collection;

    protected $generalFunctions; 

    public function __construct($connection) {
        try {
            $this->collection = $connection->connect_to_user();
            error_log("Connection to collection User");
            $this->generalFunctions = new GeneralFunctions();
        }
        catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
            error_log("Problem in connection with collection User".$e);
        }
    }
    
    /**
     * @OA\Get(
     *   path="/roles/{id}/list",
     *   description="A list with roles",
     *   operationId="showRoles",
     *   tags={"Roles"},
     *   @OA\Parameter(
     *      name="id",
     *      in="path",
     *      description="User id to show Roles",
     *      required=true,
     *      @OA\Schema(
     *          type="string",
     *          example="6250932b62a9e94948207113"
     *       ),
     *     ),
     *   @OA\Response(
     *     response="200",
     *     description="Returns a list of roles for specific user"
     *   ),
     *   @OA\Response(
     *     response="404",
     *     description="Error"
     *   )
     * )
     */
    public function showRoles($id) {
        if( isset( $id )) {
            try {
                $result = $this->collection->findOne(
                    [ '_id'=>new MongoDB\BSON\ObjectId($id) ],
                    [
                        'projection' => [
                            'roles' => 1
                        ],
                    ]);
                if (count($result)>0):
                    return json_encode($result);
                else:
                    return $this->returnValue('false');
                endif;
            }
            catch (MongoDB\Exception\UnsupportedException $e){
                error_log("Problem in findOne roles \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in findOne roles \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in findOne roles \n".$e);
                return $this->returnValue('false');
            };
        } else 
            return $this->returnValue('false'); 
    }

    /**
     * @OA\Post(
     *     path="/roles/create",
     *     description="Create a role",
     *     operationId="createRoles",
     *     tags={"Roles"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="id",type="integer"),
     *                 @OA\Property(property="permission",type="string"),
     *                 @OA\Property(property="authorizations",type="array",
     *                      @OA\Items(
     *                          @OA\Property(
     *                             property="department",
     *                             type="string",
     *                             example=""
     *                          ),
     *                          @OA\Property(
     *                             property="subdepartment",
     *                              type="string",
     *                              example=""
     *                          ),
     *                      ),
     *                 ),
     *                 example={"id": "6250932b62a9e94948207113", 
     *                          "permission": "editor",
     *                          "authorizations":{{"department" : "1","subdepartment": "1_1"}}
     *                          }
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
    public function createRoles($data) {
        $id = $data->_id;
        $permission = $data->permission;
        $authorizations = $data->authorizations;
        
        if( isset( $id ) && isset($permission) && isset($authorizations)) {
            try {
                $result = $this->collection->updateOne( 
                    [ '_id' => new MongoDB\BSON\ObjectId($id) ],
                    [ 
                        '$push' => [
                            'roles' => [
                                '_id' => new MongoDB\BSON\ObjectId(),
                                'app' => 'announcement',
                                'permission' => $permission,
                                'authorizations' => $authorizations
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
                error_log("Problem in insert roles \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in insert roles \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in insert roles \n".$e);
                return $this->returnValue('false');
            };
        } else 
            return $this->returnValue('false');
    }

    /**
     * @OA\Delete(
     *     path="/roles/{id}/{roleid}/delete",
     *     description="Delete a role",
     *     operationId="deleteRole",
     *     tags={"Roles"},
     *     @OA\Parameter(
     *         name="identifier",
     *         in="path",
     *         description="User identifier to delete role",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example="6250932b62a9e94948207113"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="roleid",
     *         in="path",
     *         description="Role id to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="6250932b62a9e94948207114"
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
    public function deleteRoles($id,$roleid) {
        if( isset( $id ) && isset($roleid)) {
            try {
                $result = $this->collection->updateOne( 
                    [ '_id' => new MongoDB\BSON\ObjectId($id) ],
                    [ 
                        '$pull' => [
                            'roles' => [
                                '_id' => new MongoDB\BSON\ObjectId($roleid)
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
                error_log("Problem in delete roles \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in delete roles \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in delete roles \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in delete roles \n".$e);
                return $this->returnValue('false');
            };
        } else 
            return $this->returnValue('false');
    }

    /**
     * @OA\Patch(
     *     path="/roles/update",
     *     description="Update a role",
     *     operationId="updateRoles",
     *     tags={"Roles"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="id",type="string"),
     *                 @OA\Property(property="roleid",type="string"),
     *                 @OA\Property(property="permission",type="string"),
     *                 @OA\Property(property="authorizations",type="array",
     *                       @OA\Items(
     *                          @OA\Property(
     *                             property="department",
     *                             type="string",
     *                             example=""
     *                          ),
     *                          @OA\Property(
     *                             property="subdepartment",
     *                              type="string",
     *                              example=""
     *                          ),
     *                      ),
     *                 ),   
     *                 example={
     *                          "id": "6250932b62a9e94948207113", 
     *                          "roleid": "6250932b62a9e94948207345",
     *                          "permission": "editor",
     *                          "authorizations":{{"department":"1", "subdepartment":"11"}},
     *                         }
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
    public function updateRoles($data) {
        $id = $data->_id;
        $roleid = $data->roleid;
        $permission = $data->permission;
        $authorizations = $data->authorizations;

        if( isset( $id ) && isset($roleid) && isset($permission) && isset($authorizations)) {
            try {
                $result = $this->collection->updateOne( 
                    [ 
                        '_id' => new MongoDB\BSON\ObjectId($id),
                        'roles._id' => new MongoDB\BSON\ObjectId($roleid)
                    ],
                    [ '$set' => [ 
                            'role.$.permission' => $permission,
                            'role.$.authorizations' => $authorizations
                        ]
                    ]
                );
                if ($result->getModifiedCount()==1)
                    return $this->returnValue('true');
                else 
                    return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in update roles \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in update roles \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in update roles \n".$e);
                return $this->returnValue('false');
            };
        } else 
            return $this->returnValue('false');    
    }
}
?>