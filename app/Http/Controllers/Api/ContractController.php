<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $query = Contract::with('employee.department');

        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        return response()->json($query->latest()->paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'contract_number' => 'required|unique:contracts',
            'contract_type' => 'required|in:Thử việc,Chính thức,Hợp đồng',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'salary' => 'nullable|numeric',
            'status' => 'sometimes|in:active,expired,terminated',
        ]);

        $contract = Contract::create($validated);
        return response()->json($contract->load('employee'), 201);
    }

    public function show($id)
    {
        $contract = Contract::with('employee.department')->findOrFail($id);
        return response()->json($contract);
    }

    public function update(Request $request, $id)
    {
        $contract = Contract::findOrFail($id);

        $validated = $request->validate([
            'employee_id' => 'sometimes|exists:employees,id',
            'contract_number' => 'sometimes|unique:contracts,contract_number,' . $id,
            'contract_type' => 'sometimes|in:Thử việc,Chính thức,Hợp đồng',
            'start_date' => 'sometimes|date',
            'end_date' => 'nullable|date',
            'salary' => 'nullable|numeric',
            'status' => 'sometimes|in:active,expired,terminated',
        ]);

        $contract->update($validated);
        return response()->json($contract->load('employee'));
    }

    public function destroy($id)
    {
        $contract = Contract::findOrFail($id);
        $contract->delete();
        return response()->json(['message' => 'Contract deleted successfully']);
    }
}
