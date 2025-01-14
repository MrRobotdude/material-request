<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('sub_types', function (Blueprint $table) {
            $table->string('sub_type_code')->primary();
            $table->string('sub_type_name');
            $table->string('type_code');
            $table->string('initial', 4);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();

            $table->foreign('type_code')->references('type_code')->on('types')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sub_types');
    }
};
