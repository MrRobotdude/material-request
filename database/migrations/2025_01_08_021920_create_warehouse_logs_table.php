<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('warehouse_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mr_item_id');
            $table->foreign('mr_item_id')->references('id')->on('material_request_items')->onDelete('cascade');
            $table->integer('fulfilled_quantity')->notNull();
            $table->integer('remaining_quantity')->notNull();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('warehouse_logs');
    }
};
