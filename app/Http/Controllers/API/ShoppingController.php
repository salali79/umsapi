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
use App\Models\ShoppingWalletCharge;
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
        /*if($request->has('department_id'))
        {
            $products_attributes = $saller->store->product_attributes;
            $filtered_products = $products_attributes->filter(function ($product_attribute) use($request){
                return $product_attribute->department_id == $request->department_id;
            });
            $filtered_products->all();
            $products = $filtered_products->map( function($product_attribute) use($request){
                return $product_attribute->product;
            });
        }
        else
        {
            $products = $saller->store->product_attributes->map( function($product_attribute){
                return $product_attribute->product;
            });
        }*/

        $departments = $saller->store->store_type->departments;
        $departments->map( function($department){
            $department->image = public_path('images\\'.$department->image);
        });

        /*foreach($departments as $department)
        {
            $product_attributes = $department->product_attributes;
            $department_products = $product_attributes->map( function($product_attribute){
                    return $product_attribute->product;
            });
            $department_products->map( function($product){
                $product->image = public_path('images\\'.$product->image);
            });

            $department['products'] = $department_products;

            unset($department['product_attributes']);
        }*/

        return response()->json([
            'status' => 'success',
            'departments' => $departments
        ]);
    }
    public function active_orders(Request $request)
    {
        $saller = $this->current_sales_officer($request);
        $store = $saller->store;
        $orders = ShoppingOrder::where('store_id', $store->id)
                                ->where('status', 0)
                                ->with(['order_items'])
                                ->get();
        foreach($orders as $order)
        {
            $customer = $order->wallet->walletable;
            $order['customer'] = $customer;
            unset($order['wallet']);

            $items = $order->order_items->map( function($item){
                $quantity = $item->quantity;
                $price = $item->product->price;
                $product = $item->product->name;
                $product_id = $item->product->id;
                return [
                    'id' => $product_id,
                    'product' => $product,
                    'quantity' => $quantity,
                    'price' => $price,
                ];
            });
            $order['items'] = $items;
            unset($order['order_items']);
        }
        return response()->json([
            'status' => 'success',
            'orders' => $orders
        ]);
    }
    public function orders(Request $request)
    {
        $saller = $this->current_sales_officer($request);
        $store = $saller->store;
        $orders = ShoppingOrder::where('store_id', $store->id)
                                ->where('status', 1)
                                ->with(['order_items'])
                                ->get();
        foreach($orders as $order)
        {
            $customer = $order->wallet->walletable;
            $order['customer'] = $customer;
            unset($order['wallet']);

            $items = $order->order_items->map( function($item){
                $quantity = $item->quantity;
                $price = $item->product->price;
                $product = $item->product->name;
                $product_id = $item->product->id;
                return [
                    'id' => $product_id,
                    'product' => $product,
                    'quantity' => $quantity,
                    'price' => $price,
                ];
            });
            $order['items'] = $items;
            unset($order['order_items']);
        }
        return response()->json([
            'status' => 'success',
            'orders' => $orders
        ]);
    }
    public function get_std_by_card(Request $request)
    {
        try{
            $std = Student::with('walletable')
                            ->where('card_num', $request->card_num)
                            ->first();
            if(is_null($std))
            {
                return response()->json
                ([
                    'status' => 'error',
                    'message' => 'رقم البطاقة المدخلة غير صحيح'
                ]);
            }
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
                'message' => 'رقم البطاقة المدخلة غير صحيح'
            ]);
        }
    }
    public function charge_wallet(Request $request)
    {
        $res = $this->get_std_by_card($request);
        $res = json_decode($res->getContent(), true);
        if($res['status'] == 'error')
        {
            return response()->json([
                'status' => 'error',
                'message' => 'رقم البطاقة المدخلة غير صحيح'
            ]);
        }
        else             
        {
            $std = Student::where('card_num', $request->card_num)
            ->where('pincode', $request->pincode)
            ->first();
        }
        if(is_null($std))
        {
            return response()->json([
                'status' => 'error',
                'message' => 'رقم البطاقة المدخلة غير صحيح'
            ]);
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
        $charge = new ShoppingWalletCharge([
            'wallet_id' => $w->id,
            'value' => $request->total_money,
            'date' => \Carbon\Carbon::now()
        ]);
        $charge->save();
        return response()->json([
            'status' => 'success',
            'message' => 'تم شحن البطاقة بنجاح'
        ]);
    }
    public function delete_order_item(Request $request)
    {
        $quantity = $request->has('quantity') ? $request->quantity : 1;
        $std = Student::where('card_num', $request->card_num)
        ->first();
        if(is_null($std))
        {
            return response()->json([
                'status' => 'error',
                'message' => 'رقم البطاقة المدخلة غير صحيح'
            ]);
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
                    if($prev_item->quantity < $quantity)
                    {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'الطلبية لا تحوي هذه الكمية من المنتج'
                        ]);
                    }
                    $old_price = $curr_order->total_price;
                    $curr_order->update([
                        'total_price' => $old_price-$prev_item->product->price * $quantity
                    ]);
                    if($curr_order->total_price <= 0)
                    {
                        foreach($curr_order->order_items as $curr_order->order_item)
                        {
                            $curr_order->order_item->forceDelete();
                        }
                        $curr_order->forceDelete();
                    }
                    else
                    {
                        if($prev_item->quantity == 1)
                        {
                            $prev_item->forceDelete();
                        }
                        else
                        {
                            $prev_item->decrement('quantity', $quantity);
                        }
                    }
                    return response()->json([
                        'status' => 'success',
                        'message' => 'تم حذف المنتج من الطلبية'
                    ]);
                }
            }
            return response()->json([
                'status' => 'error',
                'message' => 'المنتج غير موجود ضمن الطلبية'
            ]);
        }
        else
        {
            return response()->json([
                'status' => 'error',
                'message' => 'المستخدم لا يملك اي طلبية'
            ]);
        }
    }
    public function add_order_item(Request $request)
    {
        $quantity = $request->has('quantity') ? $request->quantity : 1;
        $std = Student::where('card_num', $request->card_num)
        ->first();
        if(is_null($std))
        {
            return response()->json([
                'status' => 'error',
                'message' => 'رقم البطاقة المدخلة غير صحيح'
            ]);
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

        $saller = $this->current_sales_officer($request);
        $store = $saller->store;
        $already_exist = null;
        ///--- Check if the product already chosen ---///
        if(!is_null($curr_order))
        {
            $prev_items = $curr_order->order_items;
            foreach($prev_items as $prev_item)
            {
                if($prev_item->product_id == $request->product_id)
                {
                    $item = $prev_item;
                    $already_exist = 1;
                    break;
                }
            }

        }
        else
        {
            $curr_order = new ShoppingOrder();
            $curr_order->store_id = $store->id;
            $wallet->orders()->save($curr_order);
        }

        $product = ShoppingProduct::where('id', $request->product_id)->first();
        if(is_null($already_exist))
        {
            $item = new ShoppingOrderItem([
                'product_id' => $product->id,
                'quantity' => $quantity
            ]);
        }
        $product_price = $product->price * $quantity;
        $order_price = $curr_order->total_price;
        if(($wallet->total_money - ($order_price + $product_price)) >= 0)
        {
            if(!is_null($already_exist))
            {
                $item->increment('quantity',$quantity);
            }
            $curr_order->order_items()->save($item);
            $curr_order->update([
                'total_price' => $order_price + $product_price
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'تم اضافة المنتج للطلبية'
            ]);
        }
        else
        {
            return response()->json([
                'status' => 'error',
                'message' => 'لا يوجد مال كافي بالبطاقة'
            ]);
        }
    }
    public function delete_order(Request $request)
    {
        $std = Student::where('card_num', $request->card_num)->first();
        if(is_null($std))
        {
            return response()->json([
                'status' => 'error',
                'message' => 'رقم البطاقة المدخلة غير صحيح'
            ]);
        }

        try {
            $wallet = $std->walletable;
            $order = ShoppingOrder::findOrFail($request->order_id);
            foreach($order->order_items as $item)
            {
                $item->forceDelete();
            }
            $order->forceDelete();
        } catch (ModelNotFoundException $ex) 
        {
            return response()->json([
                'status' => 'error',
                'message' => 'الطلبية غير متوفرة'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف الطلبية'
        ]);
    }
    public function checkout(Request $request)
    {
        $res = $this->get_std_by_card($request);
        $res = json_decode($res->getContent(), true);
        if($res['status'] == 'error')
        {
            return response()->json([
                'status' => 'error',
                'message' => 'رقم البطاقة المدخلة غير صحيح'
            ]);
        }
        else             
        {
            $std = Student::where('card_num', $request->card_num)
            ->where('pincode', $request->pincode)
            ->first();
        }

        if(is_null($std))
        {
            return response()->json([
                'status' => 'error',
                'message' => 'رقم البطاقة المدخلة غير صحيح'
            ]);
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
                'message' => 'المستخدم لا يملك اي طلبية'
            ]);
        }

        $total_price = 0;
        $items = $curr_order->order_items;
        foreach($items as $item)
        {
            $total_price += 
            $item->quantity * ShoppingProduct::where('id', $item->product_id)->first()->price;
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
                'message' => 'تمت عملية الدفع بنجاح'
            ]);
        }
        else{
            $wallet->orders()->delete($curr_order);
            $curr_order->forceDelete();
            return response()->json([
                'status' => 'error',
                'message' => 'لا يوجد مال كافي وتم حذف الطلبية'
            ]);
        }
    }

}
