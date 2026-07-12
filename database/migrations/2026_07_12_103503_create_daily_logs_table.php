<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('log_date');
            $table->enum('status', ['Done', 'Skipped', 'Pending'])->default('Pending');
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->enum('skip_reason', ['sick', 'holiday', 'overtime', 'other'])->nullable();
            $table->integer('actual_duration')->nullable();
            $table->timestamps();

            $table->unique(['task_id', 'log_date']);
            $table->index(['user_id', 'log_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_logs');
    }
};