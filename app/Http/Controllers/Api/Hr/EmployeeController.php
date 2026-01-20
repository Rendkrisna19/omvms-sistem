<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        // Ambil data karyawan beserta nama departemennya
        $query = Employee::with('department');

        // Filter by Department
        if ($request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        // Search by Name / NIK
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('nik', 'like', '%' . $request->search . '%');
            });
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->latest()->paginate(10)
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|unique:employees,nik',
            'full_name' => 'required',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required',
            'join_date' => 'required|date',
            'photo' => 'nullable|image|max:2048' // Max 2MB
        ]);

        // Upload Foto jika ada
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('employees', 'public');
            $validated['photo'] = $path;
        }

        $employee = Employee::create($validated);

        return response()->json(['status' => 'success', 'data' => $employee], 201);
    }

    public function show($id)
    {
        $employee = Employee::with('department')->findOrFail($id);
        return response()->json(['status' => 'success', 'data' => $employee]);
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'nik' => 'required|unique:employees,nik,' . $id,
            'full_name' => 'required',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required',
            'join_date' => 'required|date',
            'photo' => 'nullable|image|max:2048'
        ]);

        // Logic ganti foto (hapus lama, upload baru)
        if ($request->hasFile('photo')) {
            if ($employee->photo && Storage::disk('public')->exists($employee->photo)) {
                Storage::disk('public')->delete($employee->photo);
            }
            $validated['photo'] = $request->file('photo')->store('employees', 'public');
        }

        $employee->update($validated);

        return response()->json(['status' => 'success', 'data' => $employee]);
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        
        // Soft delete (data tidak hilang permanen, hanya status deleted)
        $employee->delete();

        return response()->json(['status' => 'success', 'message' => 'Data karyawan dihapus']);
    }
}