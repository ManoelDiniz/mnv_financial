<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('analysis_results', function (Blueprint $table) {
            $table->id();
            $table->json('input_data');
            $table->json('output_data');
            $table->float('confidence_score');
            $table->string('model_version');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('analysis_results');
    }
};
