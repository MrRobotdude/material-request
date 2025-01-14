<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('material_requests', function (Blueprint $table) {
            $table->string('mr_code')->primary();
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('project_id')->on('projects')->onDelete('cascade');
            $table->text('note')->nullable();
            $table->string('created_by');
            $table->enum('status', ['created', 'approved', 'partial', 'completed', 'cancelled'])->default('created');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('material_requests');
    }
};
