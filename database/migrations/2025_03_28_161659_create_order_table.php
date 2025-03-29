<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('occassion');    //Birthday, Anniversary etc.
            $table->integer('cake_type');   //Vanilla,Chocolate etc.
            $table->integer('flavor');  //Straberry,Vanilla,Rasmalai etc.
            $table->integer('weight');  //500GM,1KG etc.
            $table->timestamp('order_date');
            $table->timestamp('delivery_date_time');
            $table->integer('instruction'); //Special instruction from customer
            $table->string('design_reference'); //Uploaded reference image of cake
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
