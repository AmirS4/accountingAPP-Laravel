<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
//            $date = Order::latest()->get();
            $date = Order::with('customer', 'product')->latest()->get();

            return DataTables::of($date)->addIndexColumn()->addColumn('action', function ($row) {

                $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editProduct">' . __('Edit') . '</a>';

                $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteProduct">' . __('Delete') . '</a>';

                return $btn;
            })
                ->rawColumns(['action'])->make(true);
        }
        return view('orders');
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('term');
        $orders = Order::where('name', 'like', '%' . $searchTerm . '%')->get();
        $formattedOrders = [];
        foreach ($orders as $order) {
            $formattedOrders[] = [
                'id' => $order->id,
                'value' => $order->name,
            ];
        }

        return response()->json($formattedOrders);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'customer_name' => 'required',
            'product_name' => 'required',
            'date' => 'required',
        ]);

        $carbonDate = Jalalian::fromFormat('Y/m/d', $request->input('date'))->toCarbon();
        $persianDate = Jalalian::fromCarbon($carbonDate);

        $order = Order::updateOrCreate(
            ['id' => $request->input('order_id')],
            [
//                'date' => $request->input('date'),
                'date' => $carbonDate->toDateString(),
                'persian_date' => $persianDate->format('Y/m/d'), // Save the Persian date as a string
                'price' => $request->input('price'),
                'details' => $request->input('details'),
                'customer_id' => $this->getCustomerId($request->input('customer_name')),
                'product_id' => $this->getProductId($request->input('product_name')),
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Order created or updated successfully!',
            'data' => $order
        ]);
    }


    public function edit($id)
    {
        $order = Order::find($id);
        return response()->json($order);
    }


    public function destroy($id)
    {
        Order::find($id)->delete();

        return response()->json(['success' => 'Order deleted successfully.']);
    }

    private function getCustomerId($name)
    {
        $customer = Customer::where('name', $name)->first();
        return $customer ? $customer->id : null;
    }

    private function getProductId($title)
    {
        $product = Product::where('title', $title)->first();
        return $product ? $product->id : null;
    }


    public function autocompleteCustomers(Request $request)
    {
        $search = $request->get('term');
        $customers = Customer::where('name', 'LIKE', '%' . $search . '%')->get();

        $response = [];
        foreach ($customers as $customer) {
            $response[] = [
                'id' => $customer->id,
                'value' => $customer->name
            ];
        }

        return response()->json($response);
    }

    public function autocompleteProducts(Request $request)
    {
        $search = $request->get('term');
        $products = Product::where('title', 'LIKE', '%' . $search . '%')->get();

        $response = [];
        foreach ($products as $product) {
            $response[] = [
                'id' => $product->id,
                'value' => $product->title
            ];
        }

        return response()->json($response);
    }
}
