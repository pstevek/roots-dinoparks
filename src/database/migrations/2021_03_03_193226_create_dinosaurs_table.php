<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDinosaursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dinosaurs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('dinosaur_id')->unique();
            $table->string('kind');
            $table->string('location')->nullable();
            $table->string('name');
            $table->string('species');
            $table->string('gender');
            $table->unsignedInteger('park_id')->default(1);
            $table->unsignedInteger('digestion_period_in_hours');
            $table->boolean('herbivore')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dinosaurs');
    }
}
