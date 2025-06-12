<?php 
namespace App\Repositories\FulfillmentOrder;

use App\Http\Resources\FulfillmentOrderResource;
use App\Http\Traits\ResponseTrait;
use App\Models\Orders\FulfillmentOrder;
use Illuminate\Support\Facades\Log;



class FulfillmentOrderRepository implements FulfillmentOrderRepositoryInterface
{
    use ResponseTrait;
    protected $model;
    public function __construct(FulfillmentOrder $FulfillmentOrder)
    {
        $this->model = $FulfillmentOrder;
    }
    public function getOrderById(int $id)
    {
        $fulfillmentOrder = $this->model->where("order_id",$id)->get();
        return $fulfillmentOrder;
    }
    public function getShopifyorderById(int $id)
    {
        $fulfillmentOrder = $this->model->where('fulfillment_order_id', $id)->first();
        return $fulfillmentOrder;
        // return new FulfillmentOrderResource($fulfillmentOrder); 
    }
    public function create(array $data)
    {
        $orderCreated = $this->model->create($data);
        return new FulfillmentOrderResource($orderCreated);
        
    }
    public function update(int $id, array $data , $shopify = false)
    { 
        if($shopify){
            $fulfillment = $this->getShopifyorderById($id);
            if($fulfillment != null){
                $fulfillment->update($data);
                return new FulfillmentOrderResource($fulfillment);
            }
            else
            {
                $this->model->create($data);
                return new FulfillmentOrderResource($data);
            }
        }else{
            $fulfillment = $this->getOrderById($id);
            if($fulfillment){
                $fulfillment->update($data);
                $this->sendResponse([true , "Customer Update Successfully" , $this->logData($data)  ] , 200);
                return new FulfillmentOrderResource($fulfillment);
            }
        }
    }
    public function delete(int $id)
    {   
        $FulfillmentOrder = $this->getOrderById($id);
        if($FulfillmentOrder){
            foreach($FulfillmentOrder as $order){
                return $order->delete();
            }
        }else{
            Log::error("Fulfillment Not Found for Deletion");
        }
       
    }
}

