<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Jobs\OrdersSyncJob;
use App\Repositories\Order\OrderRepositoryInterface;
use Illuminate\Http\Request;



class DashboardController extends Controller
{
    protected $OrderRepository;

    public function __construct(OrderRepositoryInterface $OrderRepository)
    {
        $this->OrderRepository = $OrderRepository;
    }
    public function home()
    {
        return $this->render('Dashboard');
    }
    public function orderSeacrhfilter(Request $request)
    {
        $filters = $request->all();
        $filters['relation'] = [
            'CustomerOrder',
            'LineItemOrder',
            'FulfillmentOrder',
            'ShippingAddressOrder'
        ];

        $filters['financial_status'] = $request->financial_status;
        $filters['fulfillment_status'] = $request->fulfillment_status;

        return $this->OrderRepository->SearchFilter( $filters);
    }
    public function SyncOrder(Request $request) {
        $data = $request->all();
        $shopDomain = $data['shop'];
        $sync_orders = $data['sync_orders'];
        if($sync_orders){
            OrdersSyncJob::dispatch($shopDomain, $data);
        }
        
        return response()->json(['status' => 'success']);
    }
}
