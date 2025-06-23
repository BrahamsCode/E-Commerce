<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->enum('document_type', ['dni', 'ruc', 'ce', 'passport'])->nullable();
            $table->string('document_number')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_ruc')->nullable();
            $table->text('billing_address')->nullable();
            $table->text('shipping_address')->nullable();
            $table->timestamps();

            $table->index('phone');
            $table->index('email');
            $table->index('document_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
