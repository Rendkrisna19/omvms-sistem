<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Department;
use App\Models\User;
use App\Models\Employee; // Tambahkan ini jika ingin buat dummy employee
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Master Roles
        $roles = [
            ['role_name' => 'Super Admin', 'role_code' => 'superadmin'],
            ['role_name' => 'Admin Departemen', 'role_code' => 'admin_dept'],
            ['role_name' => 'Karyawan', 'role_code' => 'employee'],
            ['role_name' => 'Petugas Kantin', 'role_code' => 'pos'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        // 2. Buat Master Department (IT Department)
        $deptIT = Department::create([
            'dept_code' => 'IT',
            'dept_name' => 'Information Technology',
            'description' => 'Bagian IT dan Software Development',
            'is_active' => true
        ]);

        // 3. Buat User SUPER ADMIN (Wajib ada user pertama)
        User::create([
            'username' => 'superadmin',
            'email' => 'admin@omvms.com',
            'password' => Hash::make('password'), // Password default
            'role_id' => 1, // Pastikan ID 1 adalah superadmin (karena diinsert pertama)
            'employee_id' => null, // Super admin sistem tidak wajib punya data employee
            'is_active' => true,
        ]);
        
        // 4. (Opsional) Buat 1 Dummy Employee & User Karyawan untuk test
        $employee = Employee::create([
            'nik' => 'EMP001',
            'full_name' => 'Budi Developer',
            'phone' => '08123456789',
            'department_id' => $deptIT->id,
            'position' => 'Senior Developer',
            'join_date' => '2024-01-01',
            'is_active' => true
        ]);

        User::create([
            'username' => 'budi01',
            'email' => 'budi@omvms.com',
            'password' => Hash::make('password'),
            'role_id' => 3, // Role employee
            'employee_id' => $employee->id,
            'is_active' => true,
        ]);
    }
}