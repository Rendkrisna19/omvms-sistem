<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    // GET: List Departemen (Support Search)
    public function index(Request $request)
    {
        $query = Department::query();

        if ($request->search) {
            $query->where('dept_name', 'like', '%' . $request->search . '%')
                  ->orWhere('dept_code', 'like', '%' . $request->search . '%');
        }

        // Tampilkan 10 data per halaman
        return response()->json([
            'status' => 'success',
            'data' => $query->latest()->paginate(10)
        ]);
    }

    // POST: Tambah Departemen
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dept_code' => 'required|unique:departments,dept_code|max:10',
            'dept_name' => 'required|string|max:100',
            'description' => 'nullable|string'
        ]);

        $dept = Department::create($validated);

        return response()->json(['status' => 'success', 'data' => $dept], 201);
    }

    // PUT: Update Departemen
    public function update(Request $request, $id)
    {
        $dept = Department::findOrFail($id);
        
        $validated = $request->validate([
            'dept_code' => 'required|max:10|unique:departments,dept_code,' . $id,
            'dept_name' => 'required|string|max:100',
            'description' => 'nullable|string'
        ]);

        $dept->update($validated);
        return response()->json(['status' => 'success', 'data' => $dept]);
    }

    // DELETE: Hapus Departemen
    public function destroy($id)
    {
        $dept = Department::findOrFail($id);
        
        // Cek apakah ada karyawan di departemen ini?
        if ($dept->employees()->exists()) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Gagal hapus. Masih ada karyawan di departemen ini.'
            ], 400);
        }

        $dept->delete();
        return response()->json(['status' => 'success', 'message' => 'Departemen dihapus']);
    }
}