<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index(); // For search requirement
            $table->text('description')->nullable();
            $table->enum('unit_type', ['kg', 'm', 'cm', 'units']); // Exact from PDF
            $table->decimal('current_quantity', 10, 2)->default(0.00);
            $table->decimal('minimum_quantity', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};