<?php 

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json; charset=utf-8');

use OpenApi\Annotations as OA;

class Subscription {

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
     *   path="/subscription/{id}/list",
     *   description="A list with subscriptions",
     *   operationId="showSubscription",
     *   tags={"Subscription"},
     *   @OA\Parameter(
     *      name="id",
     *      in="path",
     *      description="User id to show Subscriptions",
     *      required=true,
     *      @OA\Schema(
     *          type="string",
     *          example="6250932b62a9e94948207113"
     *       ),
     *     ),
     *   @OA\Response(
     *     response="200",
     *     description="Returns a list of subscriptions for specific user"
     *   ),
     *   @OA\Response(
     *     response="404",
     *     description="Error"
     *   )
     * )
     */
    public function showSubscription($id) {
        if( isset( $id )) {
            try {
                $result = $this->collection->findOne(
                    [ '_id'=>new MongoDB\BSON\ObjectId($id) ],
                    [
                        'projection' => [
                            'subscription' => 1
                        ],
                    ]);
                return $result;
            }
            catch (MongoDB\Exception\UnsupportedException $e){
                error_log("Problem in findOne subscription \n".$e);
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in findOne subscription \n".$e);
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in findOne subscription \n".$e);
            };
        } else 
            return $this->returnValue('false'); 
    }

    /**
     * @OA\Post(
     *     path="/subscription/create",
     *     description="Create a subscription",
     *     operationId="createSubscription",
     *     tags={"Subscription"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="id",type="integer"),
     *                 @OA\Property(property="subscription",type="array",
     *                      @OA\Items(
     *                          @OA\Property(
     *                             property="category",
     *                             type="string",
     *                             example=""
     *                          )
     *                      ),
     *                  ),
     *                 example={"id": "6250932b62a9e94948207113", "permission": "editor", "subscription":{"1","2"}}
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
    public function createSubscription($data) {
        $id = $data->_id;
        $subscription = $data->subscription;
        
        if( isset( $id ) && isset($subscription) ) {
            try {
                $result = $this->collection->updateOne( 
                    [ '_id' => new MongoDB\BSON\ObjectId($id) ],
                    [ '$set' => [   
                        'subscription' => $subscription 
                        ]
                    ]
                );
                if ($result->getModifiedCount()==1)
                    return $this->returnValue('true');
                else 
                    return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
                error_log("Problem in insert subscription \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\BulkWriteException $e){
                error_log("Problem in insert subscription \n".$e);
                return $this->returnValue('false');
            }
            catch (MongoDB\Driver\Exception\RuntimeException $e){
                error_log("Problem in insert subscription \n".$e);
                return $this->returnValue('false');
            };
        } else 
            return $this->returnValue('false');
    }

    public function deleteSubscription($id,$subscription) {
        try {
            $result = $this->collection->updateOne( 
                [ '_id' => new MongoDB\BSON\ObjectId($id) ],
                [ '$pull' => [ 'subscription' => $subscription ]]
            );
            return $result->getModifiedCount();
        }
        catch (MongoDB\Exception\UnsupportedException $e){
            error_log("Problem in delete subscription \n".$e);
        }
        catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
            error_log("Problem in delete subscription \n".$e);
        }
        catch (MongoDB\Driver\Exception\BulkWriteException $e){
            error_log("Problem in delete subscription \n".$e);
        }
        catch (MongoDB\Driver\Exception\RuntimeException $e){
            error_log("Problem in delete subscription \n".$e);
        };
    }

    public function update_subscription($data) {
        $id = $data->_id;
        $subscription = $data->subscription;
        
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
            return $result->getModifiedCount();
        }
        catch (MongoDB\Driver\Exception\InvalidArgumentException $e){
            error_log("Problem in update roles \n".$e);
        }
        catch (MongoDB\Driver\Exception\BulkWriteException $e){
            error_log("Problem in update roles \n".$e);
        }
        catch (MongoDB\Driver\Exception\RuntimeException $e){
            error_log("Problem in update roles \n".$e);
        };
    }
}
?>