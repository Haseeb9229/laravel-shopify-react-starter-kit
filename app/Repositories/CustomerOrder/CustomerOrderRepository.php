<?php 
namespace App\Repositories\CustomerOrder;

use App\Http\Resources\CustomerOrderResource;
use App\Http\Traits\ResponseTrait;
use App\Models\Orders\CustomerOrder;
use App\Repositories\CustomerOrder\CustomerOrderRepositoryInterface;
use Illuminate\Support\Facades\Log;


class CustomerOrderRepository implements CustomerOrderRepositoryInterface
{
    use ResponseTrait;
    protected $model;
    public function __construct(CustomerOrder $product)
    {
        $this->model = $product;
    }
    public function getorderById(int $id)
    {
        $getCustomerId = $this->model->where("order_id",$id)->first();
        return $getCustomerId;
    }
    public function getShopifyorderById(int $id)
    {
        $getOrder = $this->model->where('shopify_customer_id', $id)->first();
        return $getOrder;
        // return new CustomerOrderResource($getOrder);
    }
    public function create(array $data)
    {
        if(!empty($data)){
            $orderCreated = $this->model->create($data); 
            return new CustomerOrderResource($orderCreated);   
        }
    }
    public function update(int $id, array $data , $shopify = false)
    { 
        if($shopify){
            $customer = $this->getShopifyorderById($id);
            if($customer){
                $customer->update($data);
                $this->sendResponse([true , "Customer Update Successfully" , $this->logData($data)  ] , 200);
                return new CustomerOrderResource($customer);
            }
            else
            {
                $this->sendResponse([false , "Updating Customer not found in Database" , $data ] , 404);
            }
        }
        else{
            $customer = $this->getorderById($id);
            if($customer){
                $customer->update($data);
                $this->sendResponse([true , "Customer Update Successfully" , $this->logData($data)  ] , 200);
                return new CustomerOrderResource($customer);
            }
        }

    }
    public function delete(int $id)
    {
        $CustomerOrder = $this->getorderById($id);
        if($CustomerOrder){
            return $CustomerOrder->delete();
        }
        else{
            Log::error("Customer Order Not Found for Deletion");
        }
    }

}

