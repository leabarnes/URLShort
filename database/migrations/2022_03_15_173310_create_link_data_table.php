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
        Schema::create('link_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('link_code')->nulleable(true)->default('');
            $table->string('link_url');
            $table->string('title')->nulleable(true)->default('');
            $table->bigInteger('visits')->unsigned()->default(0);
            $table->timestamps();


            $table->index(['link_url']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('link_data');
    }
};
