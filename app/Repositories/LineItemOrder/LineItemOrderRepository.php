<?php 
namespace App\Repositories\LineItemOrder;

use App\Http\Resources\LineitemOrderResource;
use App\Http\Traits\ResponseTrait;
use App\Models\Orders\LineItemOrder;
use App\Repositories\LineItemOrder\LineItemOrderRepositoryInterface;
use Illuminate\Support\Facades\Log;


class LineItemOrderRepository implements LineItemOrderRepositoryInterface
{
    use ResponseTrait;
    protected $model;
    public function __construct(LineItemOrder $LineItemOrder)
    {
        $this->model = $LineItemOrder;
    }
    public function getOrderById(int $id)
    {
        $getOrderId = $this->model->where("order_id",$id)->get();
        return $getOrderId;
    }
    public function getShopifyorderById(int $id)
    {
        $getOrder = $this->model->where('shopify_lineitem_id', $id)->first();
        return $getOrder;
        // return new LineitemOrderResource($getOrder);
    }
    public function create(array $data)
    {
        if(!empty($data)){
            $orderCreated =$this->model->create($data);
            return new LineitemOrderResource($orderCreated);
        }
    }
    public function update(int $id, array $data , $shopify = false)
    { 
        if($shopify){
            $lineitem = $this->getShopifyorderById($id);
            if ($lineitem) {
                $lineitem->update($data);
                $this->sendResponse([true, "LineItems Update Successfully", $this->logData($data)], 200);
                return new LineitemOrderResource($lineitem);
            } 
            else {
                $this->sendResponse([false, "Updating LineItems not found in database", $data], 404);
            }
        }
        else{
            $lineItemByDb = $this->getOrderById($id);
            if ($lineItemByDb) {
                $lineItemByDb->update($data);
                $this->sendResponse([true, "LineItems Update Successfully", $this->logData($data)], 200);
                return new LineitemOrderResource($lineItemByDb);
            }
        }
    }
    public function delete(int $id)
    {
        $LineItemOrder = $this->getOrderById($id);
        if($LineItemOrder){
            foreach($LineItemOrder as $order)
            {
                return$order->delete();
            }
        }
        else{
            Log::error("LineItem Not Found for Deletion");
        }
    }
}

