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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Contractor
            $table->decimal('price', 15, 2);
            $table->text('notes')->nullable();
            $table->integer('score')->nullable(); // 0-100
            $table->string('status')->default('PENDING'); // PENDING, APPROVED, REJECTED
            $table->foreignId('evaluator_id')->nullable()->constrained('users')->onDelete('set null'); // Supervisor
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
