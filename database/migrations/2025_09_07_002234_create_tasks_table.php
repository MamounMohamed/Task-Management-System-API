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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'cancelled', 'completed'])->default('pending');
            $table->date('due_date');
            $table->foreignId('assignee_id')->constrained('users','id')->cascadeOnDelete()->index('tasks_assignee_id_index');
            $table->foreignId('creator_id')->constrained('users','id')->cascadeOnDelete()->index('tasks_creator_id_index');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
