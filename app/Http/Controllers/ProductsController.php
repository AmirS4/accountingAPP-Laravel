<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $date = Product::latest()->get();

            return DataTables::of($date)->addIndexColumn()->addColumn('action', function ($row) {

                $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editProduct">' . __('Edit') . '</a>';

                $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteProduct">' . __('Delete') . '</a>';

                return $btn;
            })
                ->rawColumns(['action'])->make(true);
        }
        return view('products');
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('term');
        $products = Product::where('name', 'like', '%' . $searchTerm . '%')->get();
        $formattedProducts = [];
        foreach ($products as $product) {
            $formattedProducts[] = [
                'id' => $product->id,
                'value' => $product->name,
            ];
        }

        return response()->json($formattedProducts);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
        ]);

        Product::updateOrCreate([
            'id' => $request->product_id
        ],

            [
                'title' => $request->title,
                'description' => $request->description,
                'details' => $request->details,
            ]
        );
        return response()->json(['success' => 'Product saved successfully.']);
    }


    public function edit($id)
    {
        $product = Product::find($id);
        return response()->json($product);
    }

    public function destroy($id)
    {
        Product::find($id)->delete();

        return response()->json(['success' => 'Product deleted successfully.']);
    }
}
