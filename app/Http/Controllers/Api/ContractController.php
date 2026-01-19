<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;
use App\Exports\ContractExport;
use App\Imports\ContractImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

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

    /**
     * Export contracts to Excel
     */
    public function export()
    {
        try {
            return Excel::download(new ContractExport, 'contracts_' . date('Y-m-d_His') . '.xlsx');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Export failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import contracts from Excel
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            Excel::import(new ContractImport, $request->file('file'));
            
            return response()->json([
                'message' => 'Contracts imported successfully'
            ], 200);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            
            foreach ($failures as $failure) {
                $errors[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                    'values' => $failure->values(),
                ];
            }
            
            return response()->json([
                'message' => 'Import validation failed',
                'errors' => $errors
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Import failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
