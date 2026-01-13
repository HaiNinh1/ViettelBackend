<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        return response()->json(Project::with('employees')->latest()->paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|unique:projects',
            'description' => 'nullable|string',
            'client' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'status' => 'sometimes|in:Đang thực hiện,Hoàn thành,Tạm dừng',
        ]);

        $project = Project::create($validated);
        return response()->json($project, 201);
    }

    public function show($id)
    {
        $project = Project::with('employees.department')->findOrFail($id);
        return response()->json($project);
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|unique:projects,code,' . $id,
            'description' => 'nullable|string',
            'client' => 'nullable|string',
            'start_date' => 'sometimes|date',
            'end_date' => 'nullable|date',
            'status' => 'sometimes|in:Đang thực hiện,Hoàn thành,Tạm dừng',
        ]);

        $project->update($validated);
        return response()->json($project);
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();
        return response()->json(['message' => 'Project deleted successfully']);
    }

    // Phân công nhân sự vào dự án
    public function assignEmployee(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'role' => 'nullable|string',
            'joined_date' => 'required|date',
        ]);

        $project->employees()->attach($validated['employee_id'], [
            'role' => $validated['role'] ?? null,
            'joined_date' => $validated['joined_date'],
        ]);

        return response()->json(['message' => 'Employee assigned successfully']);
    }

    // Xóa nhân sự khỏi dự án
    public function removeEmployee($projectId, $employeeId)
    {
        $project = Project::findOrFail($projectId);
        $project->employees()->detach($employeeId);

        return response()->json(['message' => 'Employee removed successfully']);
    }
}
