<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CustomersController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Customer::latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('email', function ($row) {
                    return $row->email;
                })
                ->addColumn('number', function ($row) {
                    return $row->number;
                })
                ->addColumn('details', function ($row) {
                    $details = json_decode($row->details);
                    $formattedDetails = '';
                    foreach ($details as $key => $value) {
                        $formattedDetails .= $key . ': ' . $value . '<br>';
                    }
                    return $formattedDetails;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editProduct"> ' . __('Edit') . '</a>';
                    $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteProduct">' . __('Delete') . '</a>';
                    return $btn;
                })
                ->rawColumns(['details', 'action'])
                ->make(true);
        }
        return view('adminHome');
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('term');
        $customers = Customer::where('name', 'like', '%' . $searchTerm . '%')->get();
        $formattedCustomers = [];
        foreach ($customers as $customer) {
            $formattedCustomers[] = [
                'id' => $customer->id,
                'value' => $customer->name,
            ];
        }

        return response()->json($formattedCustomers);
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'number' => 'unique:customers,number',
        ]);

        $json_field = $request->input('key');
        $json_secondField = $request->input('value');

        $data = array();


        foreach ($json_field as $key => $value) {
            $data[$value] = $json_secondField[$key];
        }

        Customer::updateOrCreate([
            'id' => $request->customer_id
        ],

            [
                'name' => $request->name,
                'number' => $request->number,
                'email' => $request->email,
                'details' => json_encode($data),
            ]);
        return response()->json(['success' => 'Customer saved successfully.']);
    }

    public function edit($id)
    {
        $customer = customer::find($id);
        return response()->json($customer);
    }

    public function destroy($id)
    {
        Customer::find($id)->delete();

        return response()->json(['success' => 'Customer deleted successfully.']);
    }

}
