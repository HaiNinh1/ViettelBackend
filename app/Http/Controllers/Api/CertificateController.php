<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    // Lấy tất cả chứng chỉ với filter
    public function index(Request $request)
    {
        $query = Certificate::with(['employee.department', 'certificateType']);

        // Filter theo status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter theo employee
        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter theo certificate type
        if ($request->has('certificate_type_id')) {
            $query->where('certificate_type_id', $request->certificate_type_id);
        }

        // Search theo certificate number
        if ($request->has('search')) {
            $query->where('certificate_number', 'like', '%' . $request->search . '%');
        }

        return response()->json($query->latest()->paginate(20));
    }

    // Tạo chứng chỉ mới
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'certificate_type_id' => 'required|exists:certificate_types,id',
            'certificate_number' => 'required|unique:certificates',
            'issued_by' => 'nullable|string',
            'issued_date' => 'required|date',
            'expiry_date' => 'nullable|date',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string',
        ]);

        // Upload file nếu có
        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('certificates', 'public');
        }

        $certificate = Certificate::create($validated);
        $certificate->updateExpiryStatus();

        return response()->json($certificate->load(['employee', 'certificateType']), 201);
    }

    // Xem chi tiết chứng chỉ
    public function show($id)
    {
        $certificate = Certificate::with(['employee.department', 'certificateType'])->findOrFail($id);
        return response()->json($certificate);
    }

    // Cập nhật chứng chỉ
    public function update(Request $request, $id)
    {
        $certificate = Certificate::findOrFail($id);

        $validated = $request->validate([
            'employee_id' => 'sometimes|exists:employees,id',
            'certificate_type_id' => 'sometimes|exists:certificate_types,id',
            'certificate_number' => 'sometimes|unique:certificates,certificate_number,' . $id,
            'issued_by' => 'nullable|string',
            'issued_date' => 'sometimes|date',
            'expiry_date' => 'nullable|date',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string',
        ]);

        // Upload file mới nếu có
        if ($request->hasFile('file')) {
            // Xóa file cũ
            if ($certificate->file_path) {
                Storage::disk('public')->delete($certificate->file_path);
            }
            $validated['file_path'] = $request->file('file')->store('certificates', 'public');
        }

        $certificate->update($validated);
        $certificate->updateExpiryStatus();

        return response()->json($certificate->load(['employee', 'certificateType']));
    }

    // Xóa chứng chỉ
    public function destroy($id)
    {
        $certificate = Certificate::findOrFail($id);
        
        // Xóa file
        if ($certificate->file_path) {
            Storage::disk('public')->delete($certificate->file_path);
        }

        $certificate->delete();
        return response()->json(['message' => 'Certificate deleted successfully']);
    }

    // Tra cứu: Nhân sự nào có chứng chỉ này?
    public function byType($typeId)
    {
        $certificates = Certificate::with(['employee.department', 'certificateType'])
            ->where('certificate_type_id', $typeId)
            ->get();

        return response()->json($certificates);
    }

    // Tra cứu: Nhân sự này có chứng chỉ gì?
    public function byEmployee($employeeId)
    {
        $certificates = Certificate::with(['employee.department', 'certificateType'])
            ->where('employee_id', $employeeId)
            ->get();

        return response()->json($certificates);
    }

    // Lấy danh sách chứng chỉ sắp hết hạn
    public function expiring()
    {
        $certificates = Certificate::with(['employee.department', 'certificateType'])
            ->expiringSoon()
            ->get();

        return response()->json($certificates);
    }

    // Lấy danh sách chứng chỉ đã hết hạn
    public function expired()
    {
        $certificates = Certificate::with(['employee.department', 'certificateType'])
            ->expired()
            ->get();

        return response()->json($certificates);
    }
}
