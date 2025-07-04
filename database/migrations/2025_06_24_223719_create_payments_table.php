<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
   public function up()
   {
       Schema::create('payments', function (Blueprint $table) {
           $table->id();
           $table->unsignedBigInteger('order_id');
           $table->string('payment_method');
           $table->string('payment_proof')->nullable();
           $table->enum('status', ['pending', 'confirmed', 'rejected'])->default('pending');
           $table->timestamps();

           $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
       });
   }

   public function down()
   {
       Schema::dropIfExists('payments');
   }

}
