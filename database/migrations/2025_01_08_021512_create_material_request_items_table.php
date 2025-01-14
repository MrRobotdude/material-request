<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('material_request_items', function (Blueprint $table) {
            $table->id();
            $table->string('mr_code');
            $table->foreign('mr_code')->references('mr_code')->on('material_requests')->onDelete('cascade');
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('item_id')->on('items')->onDelete('cascade');
            $table->integer('quantity');
            $table->integer('fulfilled_quantity')->default(0);
            $table->enum('status', ['pending', 'partial', 'fulfilled', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('material_request_items');
    }
};
