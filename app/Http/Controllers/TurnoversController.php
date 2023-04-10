<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Turnover;
use App\Models\Customer;
use App\Models\Order;

class TurnoversController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::with(['orders' => function ($query) {
            $query->select('customer_id', \DB::raw('SUM(price) as total_price'))
                ->groupBy('customer_id');
        }])->get();

        $data = [];
        foreach ($customers as $customer) {
            $turnover = Turnover::firstOrNew(['customer_id' => $customer->id]);

            $data[] = [
                'customer_id' => $customer->id,
                'income' => $customer->orders->where('total_price', '>', 0)->sum('total_price'),
                'outcome' => abs($customer->orders->where('total_price', '<', 0)->sum('total_price')),
                'description' => $turnover->description,
                'details' => $turnover->details,
            ];
        }
        
        return DataTables::of($data)
            ->make(true);
    }


    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'income' => 'required',
            'outcome' => 'required',
            'description' => 'required',
            'details' => 'required',
        ]);

        $turnover = new Turnover();
        $turnover->customer_id = $request->customer_id;
        $turnover->income = $request->income;
        $turnover->outcome = $request->outcome;
        $turnover->description = $request->description;
        $turnover->details = $request->details;
        $turnover->save();

        return response()->json(['success' => 'Turnover created successfully.']);
    }

    public function edit($id)
    {
        $product = Turnover::find($id);
        return response()->json($product);
    }

    public function destroy($id)
    {
        Turnover::find($id)->delete();
        return response()->json(['success' => 'Product deleted successfully.']);
    }
}
