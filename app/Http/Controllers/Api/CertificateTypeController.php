<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CertificateType;
use Illuminate\Http\Request;

class CertificateTypeController extends Controller
{
    public function index()
    {
        return response()->json(CertificateType::withCount('certificates')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|unique:certificate_types',
            'description' => 'nullable|string',
            'validity_period' => 'nullable|integer',
            'required_renewal' => 'sometimes|boolean',
        ]);

        $certificateType = CertificateType::create($validated);
        return response()->json($certificateType, 201);
    }

    public function show($id)
    {
        $certificateType = CertificateType::with('certificates.employee')->findOrFail($id);
        return response()->json($certificateType);
    }

    public function update(Request $request, $id)
    {
        $certificateType = CertificateType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|unique:certificate_types,code,' . $id,
            'description' => 'nullable|string',
            'validity_period' => 'nullable|integer',
            'required_renewal' => 'sometimes|boolean',
        ]);

        $certificateType->update($validated);
        return response()->json($certificateType);
    }

    public function destroy($id)
    {
        $certificateType = CertificateType::findOrFail($id);
        $certificateType->delete();
        return response()->json(['message' => 'Certificate type deleted successfully']);
    }
}
