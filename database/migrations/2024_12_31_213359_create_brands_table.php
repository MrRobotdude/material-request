<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->string('brand_code')->primary(); // Kode merk
            $table->string('brand_name'); // Nama merk
            $table->string('brand_initial'); // Initial merk
            $table->boolean('is_active')->default(1); // Status aktif
            $table->timestamps(); // Created at & Updated at
        });
    }

    public function down()
    {
        Schema::dropIfExists('brands');
    }
};
