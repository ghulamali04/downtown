<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function get_live(Request $request)
    {
        $query = Customer::query();
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('first_name', 'like', "%{$searchTerm}%")
                ->orWhere('last_name', 'like', "%{$searchTerm}%")
                ->orWhere('phone_number', 'like', "%{$searchTerm}%");
            });
        }
        $customers = $query->paginate(20);
        return response()->json($customers);
    }
    public function get_data(DataTables $dataTables)
    {
        $customers = Customer::query();
        return $dataTables->eloquent($customers)
        ->addColumn('timestamp', function ($customer) {
            return date("d/m/Y H:iA", strtotime($customer->created_at));
        })
        ->addColumn('action', function ($customer) {
            return $customer->id;
        })
        ->toJson();
    }
    public function index()
    {
        return view("customer.index");
    }
    public function create()
    {
        return view("customer.create");
    }
    public function edit($customer)
    {
        $customer = Customer::findOrFail($customer);
        return view("customer.edit", compact('customer'));
    }
    public function store(Request $request)
    {
        Validator::make($request->except('_token'), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'string',  'max:255'],
            'phone_number' => ['required', 'string', 'max:255',  Rule::unique(Customer::class),]
        ])->validate();

        Customer::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'phone_number' => $request->input('phone_number'),
            'address' => $request->input('address')
        ]);

        return redirect()->back()->with('success', 'Customer successfully created');
    }
    public function update(Request $request, $customer)
    {
        Validator::make($request->except('_token'), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'string',  'max:255'],
            'phone_number' => ['required', 'string', 'max:255',  Rule::unique('customers')->ignore($customer->id),]
        ])->validate();

        $customer = Customer::findOrFail($customer);

        $customer->update([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'phone_number' => $request->input('phone_number'),
            'address' => $request->input('address')
        ]);

        return redirect()->back()->with('success', 'Customer successfully updated');
    }
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return new JsonResponse([
            "status" => "success",
            "message" => "Customer successfully deleted"
        ], 200);
    }
}
