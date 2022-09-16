<?php 

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json; charset=utf-8');

use OpenApi\Annotations as OA;

class Categories {

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
     *   path="/categories/{id}/list",
     *   description="A list with categories",
     *   operationId="showCategories",
     *   tags={"Categories"},
     *   @OA\Parameter(
     *      name="id",
     *      in="path",
     *      description="Department id to show Categories",
     *      required=true,
     *      @OA\Schema(
     *          type="string",
     *          example="6250932b62a9e94948207113"
     *       ),
     *     ),
     *   @OA\Response(
     *     response="200",
     *     description="Returns a list of categories for specific department"
     *   ),
     *   @OA\Response(
     *     response="404",
     *     description="Error"
     *   )
     * )
     */
    public function showCategories($id) {
        if( isset( $id )) {
            try {
                $result = $this->collection->findOne(
                    [ '_id'=>new MongoDB\BSON\ObjectId($id) ],
                    [
                        'projection' => [
                            'categories' => 1
                        ],
                    ]);
                if (count($result)>0):
                    return json_encode($result);
                else:
                    return $this->returnValue('false');
                endif;
            }
            catch (MongoDB\Exception\UnsupportedException $e){
                error_log("Problem in findOne categories \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in findOne categories \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in findOne categories \n".$e);
                return $this->returnValue('false');
            }
        } else 
            return $this->returnValue('false'); 
    }

    /**
     * @OA\Post(
     *     path="/categories/create",
     *     description="Create a category",
     *     operationId="createCategories",
     *     tags={"Categories"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="subdepartment_id",type="string"),
     *                 @OA\Property(property="name",type="string"),
     *                 example={"subdepartment_id": "6250932b62a9e94948207113", "name": "Συμβάσεις"}
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
    public function createCategories($data) {
        $identifier = $data->identifier;
        $subdepartment_id = $data->subdepartment_id;
        $name = $data->name;
        try {
            $result = $this->collection->updateOne( 
                [ 'identifier'=>intval($identifier) ],
                [ 
                    '$push' => [
                        'categories' => [
                            '_id' => new MongoDB\BSON\ObjectId(),
                            'subdepartment_id' => new MongoDB\BSON\ObjectId($subdepartment_id), 
                            'name' => $name,                            
                        ]
                    ]
                ]
            );
            return $result->getModifiedCount();
        }
        catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
            error_log("Problem in insert categories \n".$e);
        }
        catch (MongoDB\Driver\Exception\BulkWriteException $e){
            error_log("Problem in insert categories \n".$e);
        }
        catch (MongoDB\Driver\Exception\RuntimeException $e){
            error_log("Problem in insert categories \n".$e);
        };
    }

    /**
     * @OA\Delete(
     *     path="/categories/{identifier}/{id}/delete",
     *     description="Delete a category",
     *     operationId="deleteCategories",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="identifier",
     *         in="path",
     *         description="Department identifier to delete category",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example="4"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Category id to delete",
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
    public function deleteCategories($identifier,$id) {
        try {
            $result = $this->collection->updateOne( 
                [ 'identifier'=>$identifier ],
                [ 
                    '$pull' => [
                        'categories' => [
                            '_id' => new MongoDB\BSON\ObjectId($id)
                        ]
                    ]
                ]
            );
            return $result->getModifiedCount();
        }
        catch (MongoDB\Exception\UnsupportedException $e){
            error_log("Problem in delete categories \n".$e);
        }
        catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
            error_log("Problem in delete categories \n".$e);
        }
        catch (MongoDB\Driver\Exception\BulkWriteException $e){
            error_log("Problem in delete categories \n".$e);
        }
        catch (MongoDB\Driver\Exception\RuntimeException $e){
            error_log("Problem in delete categories \n".$e);
        };
    }

    /**
     * @OA\Patch(
     *     path="/categories/update",
     *     description="Update a category",
     *     operationId="updateCategory",
     *     tags={"Categories"},
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
    public function updateCategories($data) {
        $identifier = $data->identifier;
        $id = $data->id;
        $name = $data->name;

        try {
            $result = $this->collection->updateOne( 
                [ 
                    'identifier' => $identifier,
                    'categories._id' => new MongoDB\BSON\ObjectId($id)
                ],
                [ '$set' => [ 'categories.$.name' => $name ]]
            );
            return $result->getModifiedCount();
        }
        catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
            error_log("Problem in update categories \n".$e);
        }
        catch (MongoDB\Driver\Exception\BulkWriteException $e){
            error_log("Problem in update categories \n".$e);
        }
        catch (MongoDB\Driver\Exception\RuntimeException $e){
            error_log("Problem in update categories \n".$e);
        };
    }
}
?>