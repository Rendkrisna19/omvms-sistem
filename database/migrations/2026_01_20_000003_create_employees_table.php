<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('employees', function (Blueprint $table) {
        $table->id();
        $table->string('nik')->unique();
        $table->string('full_name');
        $table->string('phone')->nullable();
        
        // Relasi ke departments table
        $table->foreignId('department_id')->constrained('departments')->onDelete('restrict');
        
        $table->string('position');
        $table->date('join_date');
        $table->string('photo')->nullable();
        $table->boolean('is_active')->default(true);
        
        $table->timestamps();
        $table->softDeletes();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
