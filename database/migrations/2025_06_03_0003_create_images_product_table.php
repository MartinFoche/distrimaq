<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesProductTable extends Migration {
    public function up() {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('url');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('product_images');
    }
}
