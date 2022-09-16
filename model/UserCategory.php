<?php 

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json; charset=utf-8');

use OpenApi\Annotations as OA;

class UserCategory {

    protected $collection;

    protected $generalFunctions; 

    public function __construct($connection) {
        try {
            $this->collection = $connection->connect_to_user_category();
            error_log("Connection to collection User_category");
            $this->generalFunctions = new GeneralFunctions();
        }
        catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
            error_log("Problem in connection with collection User_category".$e);
        }
    }
        
    /**
     * @OA\Get(
     *   path="/usercategory/list",
     *   description="List user categories",
     *   operationId="showUsercategories",
     *   tags={"UserCategory"},
     *   @OA\Response(
     *     response="200",
     *     description="A list with user categories"
     *   ),
     *   @OA\Response(
     *     response="404",
     *     description="Error"
     *   )
     * )
     */
    public function showUsercategories() {
        try {
            $result = $this->collection->find()->toArray();
            if (count($result)>0):
                return json_encode($result);
            else:
                return $this->returnValue('false');
            endif;
        }
        catch (MongoDB\Exception\UnsupportedException $e){
            error_log("Problem in find user categories \n".$e);
            return $this->returnValue('false');
        }
        catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
            error_log("Problem in find user categories \n".$e);
            return $this->returnValue('false');
        }
        catch (MongoDB\Driver\Exception\RuntimeException $e){
            error_log("Problem in find user categories \n".$e);
            return $this->returnValue('false');
        };
        
    }

    /**
     * @OA\Get(
     *   path="/usercategory/{id}/list",
     *   description="List a user category",
     *   operationId="showUsercategory",
     *   tags={"UserCategory"},
     *   @OA\Parameter(
     *      name="id",
     *      in="path",
     *      description="User Category id to show",
     *      required=true,
     *      @OA\Schema(
     *          type="string",
     *          example="6250932b62a9e94948207113"
     *       ),
     *     ),
     *   @OA\Response(
     *     response="200",
     *     description="Returns a department"
     *   ),
     *   @OA\Response(
     *     response="404",
     *     description="Error"
     *   )
     * )
     */
    public function showUsercategory($id) {
        if( isset( $id )) {
            try {
                $result = $this->collection->findOne([
                    '_id'=>new MongoDB\BSON\ObjectId($id)
                ]);
                if ($result):
                    return json_encode($result);
                else:
                    return $this->returnValue('false');
                endif;
            }
            catch (MongoDB\Exception\UnsupportedException $e){
                error_log("Problem in findOne user category \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in findOne user category \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in findOne user category \n".$e);
                return $this->returnValue('false');
            };
        } else 
            return $this->returnValue('false'); 
    }

    /**
     * @OA\Post(
     *     path="/usercategory/create",
     *     description="Create a user category",
     *     operationId="createUsercategory",
     *     tags={"UserCategory"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="identifier",type="integer"),
     *                 @OA\Property(property="name",type="string"),
     *                 example={"identifier": 1, "name": "Προπτυχιακός Φοιτητής"}
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
    public function createUsercategory($data) {
        $identifier = $data->identifier;
        $name = $data->name;

        if( isset( $identifier ) && isset($name)) {
            try {
                $result = $this->collection->insertOne( [ 
                    'identifier' => $identifier,
                    'name' => $name
                ] );
                if ($result->getInsertedCount()==1)
                    return $this->returnValue('true');
                else 
                    return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in insert user category \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in insert user category \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in insert user category \n".$e);
                return $this->returnValue('false');
            };
        } else 
            return $this->returnValue('false');
    }

    /**
     * @OA\Delete(
     *     path="/usercategory/{id}/delete",
     *     description="Delete a user category",
     *     operationId="deleteUsercategory",
     *     tags={"UserCategory"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User Category id to delete",
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
    public function deleteUsercategory($id) {
        if (isset( $id )){
            try {
                $result = $this->collection->deleteOne([
                    '_id'=>new MongoDB\BSON\ObjectId($id)
                ]);
                if ($result->getDeletedCount()==1)
                    return $this->returnValue('true');
                else 
                    return $this->returnValue('false');
            }
            catch (MongoDB\Exception\UnsupportedException $e){
                error_log("Problem in delete user category \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in delete user category \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in delete user category \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in delete user category \n".$e);
                return $this->returnValue('false');
            };
        } else 
            return $this->returnValue('false');
    }

    /**
     * @OA\Patch(
     *     path="/usercategory/update",
     *     description="Update a user category",
     *     operationId="updateUsercategory",
     *     tags={"UserCategory"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="_id",type="string"),
     *                 @OA\Property(property="identifier",type="integer"),
     *                 @OA\Property(property="name",type="string"),
     *                 example={"_id":"6244840de0c3d34f620e5dd6", "identifier": 1, "name": "Προπτυχιακός Φοιτητής"}
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
    public function updateUsercategory($data) {
        $id = $data->_id;
        $identifier = $data->identifier;
        $name = $data->name;

        if( isset( $id ) && isset( $identifier ) && isset($name)) {
            try {
                $result = $this->collection->updateOne( 
                    [ '_id' => new MongoDB\BSON\ObjectId($id) ],
                    [ '$set' => [
                            'identifier' => $identifier,
                            'name' => $name
                        ]
                    ]
                );
                if ($result->getModifiedCount()==1)
                    return $this->returnValue('true');
                else 
                    return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in update user category \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in update user category \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in update user category \n".$e);
                return $this->returnValue('false');
            };
        } else 
            return $this->returnValue('false');
    }
}
?>