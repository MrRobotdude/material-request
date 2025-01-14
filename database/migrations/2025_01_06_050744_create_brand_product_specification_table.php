<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_product_specification', function (Blueprint $table) {
            $table->id();
            $table->string('brand_code');
            $table->string('product_code');
            $table->unsignedBigInteger('specification_id');
            $table->boolean('is_active')->default(1);
            $table->timestamps();

            // Foreign keys
            $table->foreign('brand_code')->references('brand_code')->on('brands')->onDelete('cascade');
            $table->foreign('product_code')->references('product_code')->on('products')->onDelete('cascade');
            $table->foreign('specification_id')->references('specification_id')->on('specifications')->onDelete('cascade');

            // Unique constraint to prevent duplicate entries
            $table->unique(['brand_code', 'product_code', 'specification_id'], 'bps_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('brand_product_specification');
    }
};
