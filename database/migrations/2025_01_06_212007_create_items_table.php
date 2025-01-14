<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id('item_id'); // Primary Key
            $table->string('brand_code');
            $table->string('product_code');
            $table->string('type_code');
            $table->string('sub_type_code');
            $table->string('item_code')->unique(); // Format: TTTT-SSSS-XXXXX
            $table->text('description'); // Specifications for the item
            $table->string('unit'); // E.g., UNIT, ROL, RIM
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Foreign Keys
            $table->foreign('brand_code')->references('brand_code')->on('brands');
            $table->foreign('product_code')->references('product_code')->on('products');
            $table->foreign('type_code')->references('type_code')->on('types');
            $table->foreign('sub_type_code')->references('sub_type_code')->on('sub_types');
        });
    }

    public function down()
    {
        Schema::dropIfExists('items');
    }
}

