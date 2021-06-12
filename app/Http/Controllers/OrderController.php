<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ProductsOrders;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        
        try{
            DB::beginTransaction();
            $user = new Order();
            $user->quantity = $request->quantity;
            $user->shipping = $request->shipping;
            $user->total = $request->total;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->address = $request->address;
            $user->zip_code = $request->zip_code;
            $user->state = $request->state;
            $user->city = $request->city;
            $user->card_number = $request->card_number;
            $user->cvv = $request->cvv;
            $user->exp_month = $request->exp_month;
            $user->exp_year = $request->exp_year;
            $user->status = 1;
            $user->save();

            foreach ($request->products as $value) {
                $ordenProducto = new ProductsOrders();
                $ordenProducto->actual_price = $value['price'];
                $ordenProducto->quantity = $value['quantity'];
                $ordenProducto->product_id = $value['id'];
                $ordenProducto->order_id = $user->id;
                $ordenProducto->status = 1;
                $ordenProducto->save();
            }

            DB::commit();

        } catch (Exception $e){
            DB::rollBack();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
}
