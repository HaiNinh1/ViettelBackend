<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['department', 'certificates.certificateType', 'projects']);

        // Search theo tên hoặc mã nhân viên
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', '%' . $search . '%')
                  ->orWhere('employee_code', 'like', '%' . $search . '%');
            });
        }

        // Filter theo phòng ban
        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter theo status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->latest()->paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'employee_code' => 'required|unique:employees',
            'email' => 'required|email|unique:employees',
            'phone' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'position' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $employee = Employee::create($validated);
        return response()->json($employee->load('department'), 201);
    }

    public function show($id)
    {
        $employee = Employee::with(['department', 'contracts', 'certificates.certificateType', 'projects'])
            ->findOrFail($id);
        return response()->json($employee);
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'full_name' => 'sometimes|string|max:255',
            'employee_code' => 'sometimes|unique:employees,employee_code,' . $id,
            'email' => 'sometimes|email|unique:employees,email,' . $id,
            'phone' => 'nullable|string',
            'department_id' => 'sometimes|exists:departments,id',
            'position' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $employee->update($validated);
        return response()->json($employee->load('department'));
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();
        return response()->json(['message' => 'Employee deleted successfully']);
    }
}
