<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShoppingProduct;
use App\Models\ShoppingStore;
use App\Models\ShoppingDepartment;
use App\Models\ShoppingOrderItem;
use App\Models\ShoppingOrder;
use App\Models\Student;
use App\Models\ShoppingWallet;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon;
use JWTAuth;


class ShoppingController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth:sales_officer', ['except' => ['all_products']]);
      $this->guard = "sales_officer";
    }
    function current_sales_officer(Request $request)
    {
        if(!is_null($request->lang)) app()->setLocale($request->lang);
        $headers = apache_request_headers();
        $request->headers->set('Authorization', $headers['Authorization']);
        $token = $request->headers->get('Authorization');
        JWTAuth::setToken($token);
        $sales_officer = auth('sales_officer')->user();
        return $sales_officer;
    }
    public function products(Request $request)
    {
        $saller = $this->current_sales_officer($request);
        $products = $saller->store->product_attributes->map( function($product_attribute){
            return $product_attribute->product;
        });
        $products->map( function($product){
            $product->image = public_path('images\\'.$product->image);
        });

        return response()->json([
            'status' => 'success',
            'products' => $products
        ]);
    }
    public function get_std_by_cart(Request $request)
    {
        try{
            $std = Student::where('cart_num', $request->cart_num)
                            ->where('pincode', $request->pincode)
                            ->first();
            return response()->json
            ([
                'status' => 'success',
                'student' => $std
            ]);
        }
        catch (ModelNotFoundException $ex) {
            return response()->json
            ([
                'status' => 'error',
                'message' => 'student doesnot have available cart',
            ]);
        }
    }
    public function charge_wallet(Request $request)
    {
        $res = $this->get_std_by_cart($request);
        $res = json_decode($res->getContent(), true);
        if($res['status'] == 'error')
        {
            return response()->json([
                'status' => 'error',
                'message' => 'Cart number or pincode is wrong'
            ]);
        }
        else             
        {
            $std = Student::where('cart_num', $request->cart_num)
            ->where('pincode', $request->pincode)
            ->first();
        }
        $w = $std->walletable;
        if(is_null($w))
        {
            $w = new ShoppingWallet();
            $w->total->money = $request->total_money;
            $std->walletable()->save($w);
        }
        else 
        {
            $old_money = $std->walletable->total_money;
            $std->walletable()->update([
                'total_money' => $old_money + $request->total_money
            ]);
        }
    }
    public function delete_order_item(Request $request)
    {
        $res = $this->get_std_by_cart($request);
        $res = json_decode($res->getContent(), true);
        if($res['status'] == 'error')
        {
            return response()->json([
                'status' => 'error',
                'message' => 'Cart number or pincode is wrong'
            ]);
        }
        else             
        {
            $std = Student::where('cart_num', $request->cart_num)
            ->where('pincode', $request->pincode)
            ->first();
        }

        ///--- Get the old unfinish order ---///
        $wallet = $std->walletable;
        $curr_order = null;
        foreach($wallet->orders as $order)
        {
            if($order->status == 0)
            {
                $curr_order = $order;
                break;
            }
        }

        ///--- Check if the product chosen ---///
        if(!is_null($curr_order))
        {
            $prev_items = $curr_order->order_items;
            foreach($prev_items as $prev_item)
            {
                if($prev_item->product_id == $request->product_id)
                {
                    //$curr_order->order_items()->delete($prev_item);
                    $old_price = $curr_order->total_price;
                    $curr_order->update([
                        'total_price' => $old_price-$prev_item->product->price
                    ]);
                    if($curr_order->total_price <= 0)
                    {
                        $curr_order->forceDelete();
                    }
                    $prev_item->forceDelete();
                    return response()->json([
                        'status' => 'success',
                        'message' => 'item order deleted successfully'
                    ]);
                }
            }
            return response()->json([
                'status' => 'error',
                'message' => 'item order doesn\'t exist'
            ]);
        }
        else
        {
            return response()->json([
                'status' => 'error',
                'message' => 'the user deosn\'t have any active order'
            ]);
        }
    }
    public function add_order_item(Request $request)
    {
        $res = $this->get_std_by_cart($request);
        $res = json_decode($res->getContent(), true);
        if($res['status'] == 'error')
        {
            return response()->json([
                'status' => 'error',
                'message' => 'Cart number or pincode is wrong'
            ]);
        }
        else             
        {
            $std = Student::where('cart_num', $request->cart_num)
            ->where('pincode', $request->pincode)
            ->first();
        }

        ///--- Get the old unfinish order ---///
        $wallet = $std->walletable;
        $curr_order = null;
        foreach($wallet->orders as $order)
        {
            if($order->status == 0)
            {
                $curr_order = $order;
                break;
            }
        }

        ///--- Check if the product already chosen ---///
        if(!is_null($curr_order))
        {
            /*$prev_items = $curr_order->order_items;
            foreach($prev_items as $prev_item)
            {
                if($prev_item->product_id == $request->product_id)
                {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'item order added already'
                    ]);
                }
            }*/

        }
        else
        {
            $curr_order = new ShoppingOrder();
            $wallet->orders()->save($curr_order);
        }

        $product = ShoppingProduct::where('id', $request->product_id)->first();
        $item = new ShoppingOrderItem([
            'product_id' => $product->id
        ]);
        $product_price = $product->price;
        $order_price = $curr_order->total_price;
        if(($wallet->total_money - ($order_price + $product_price)) >= 0)
        {
            $curr_order->order_items()->save($item);
            $curr_order->update([
                'total_price' => $order_price + $product_price
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'add item order'
            ]);
        }
        else
        {
            return response()->json([
                'status' => 'error',
                'message' => 'the user\'s wallet don\'t have enough money'
            ]);
        }
    }
    public function checkout(Request $request)
    {
        $res = $this->get_std_by_cart($request);
        $res = json_decode($res->getContent(), true);
        if($res['status'] == 'error')
        {
            return response()->json([
                'status' => 'error',
                'message' => 'Cart number or pincode is wrong'
            ]);
        }
        else             
        {
            $std = Student::where('cart_num', $request->cart_num)
            ->where('pincode', $request->pincode)
            ->first();
        }

        $wallet = $std->walletable;
        $orders = $wallet->orders;
        $curr_order = null;
        foreach($orders as $order)
        {
            if($order->status == '0')
            {
                $curr_order = $order;
                break;
            }
        }

        if(is_null($curr_order))
        {
            return response()->json([
                'status' => 'error',
                'message' => 'the user deosn\'t have any active order'
            ]);
        }

        $total_price = 0;
        $items = $curr_order->order_items;
        foreach($items as $item)
        {
            $total_price += ShoppingProduct::where('id', $item->product_id)->first()->price;
        }

        $curr_order->update([
            'total_price' => $total_price,
            'date' => \Carbon\Carbon::now()
        ]);

        $wallet_money = $wallet->total_money;
        if($total_price <= $wallet_money)
        {
            $w = ShoppingWallet::where('id', $wallet->id);
            $w->update([
                'total_money' => ($wallet_money - $total_price)
            ]);
            $curr_order->update([
                'status' => '1'
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'checkout order'
            ]);
        }
        else{
            $wallet->orders()->delete($curr_order);
            $curr_order->forceDelete();
            return response()->json([
                'status' => 'error',
                'message' => 'the user\'s wallet doesn\'t have enough money so we delete the order'
            ]);
        }
    }

}
