<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospitals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_open_sun');
            $table->boolean('is_open_mon');
            $table->boolean('is_open_tue');
            $table->boolean('is_open_wed');
            $table->boolean('is_open_thu');
            $table->boolean('is_open_fri');
            $table->boolean('is_open_sat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospitals');
    }
};
