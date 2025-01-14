<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->string('product_code')->primary(); // Kode produk sebagai primary key
            $table->string('product_name'); // Nama produk
            $table->string('product_initial'); // Initial produk
            $table->boolean('is_active')->default(1); // Status aktif
            $table->timestamps(); // Created at & Updated at
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
