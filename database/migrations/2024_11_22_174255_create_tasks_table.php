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
            $table->text('description')->nullable(); // incase it need to have more than 255 characters, let's make it nullable in future if restiction will be removed
            $table->date('due_date')->nullable();
            $table->tinyInteger('task_priority')->default(0)->nullable(); // 0 = User didn't choose any
            $table->integer('task_order')->default(0)->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('task_status')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('archived_at')->nullable();
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
