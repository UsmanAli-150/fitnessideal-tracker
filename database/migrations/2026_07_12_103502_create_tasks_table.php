<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 255);
            $table->enum('category', ['Health', 'Work', 'Learning', 'Chores', 'Other'])->default('Other');
            $table->time('scheduled_time');
            $table->enum('priority', ['High', 'Medium', 'Low'])->default('Medium');
            $table->enum('recurrence', ['Daily', 'Weekdays', 'Weekends', 'Custom'])->default('Daily');
            $table->json('custom_days')->nullable();
            $table->integer('estimated_duration')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->index(['user_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};