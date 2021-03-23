<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShoppingProduct;

class ShoppingController extends Controller
{
    public function all_products(Request $request){

        $stores = ShoppingStore::where('status','1')->orderBy('title','ASC')->paginate(10)->get();
        $departments = ShoppingDepartment::where('status','1')->orderBy('title','ASC')->paginate(10)->get();
        $products = ShoppingProduct::all();
        if($request->has('department'))
        {
            $products->whereHas('product_attribute.department', function($query){
                $query->where('department_id',$request->department);
            })->paginate(10);
        }
        if($request->has('store'))
        {
            $products->whereHas('product_attribute.department.store', function($query){
                $query->where('store_id',$request->store);
            })->paginate(10);
        }

        return response()->json([
            'status' => 'success',
            'stores' => $stores,
            'departments' => $departments,
            'products' => $products
        ]);
    }
    
    public function add_order_item(Request $request)
    {

    }
}
