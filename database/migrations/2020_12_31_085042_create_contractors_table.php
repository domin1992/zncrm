<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contractors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 512);
            $table->string('name2', 512)->nullable();
            $table->string('identity');
            $table->string('eu_prefix')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('phone_number2')->nullable();
            $table->boolean('phisical_person')->default(false);
            $table->dateTime('agreed_for_electronic_invoice')->nullable();
            $table->string('invoice_email')->nullable();
            $table->boolean('supplier')->default(false);
            $table->boolean('receiver')->default(false);
            $table->boolean('abroad_address')->default(false);
            $table->string('skype')->nullable();
            $table->string('fax')->nullable();
            $table->text('comments')->nullable();
            $table->string('website')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_number')->nullable();
            $table->boolean('selected')->default(false);
            $table->boolean('send_invoice_on_both_emails')->default(false);
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
        Schema::dropIfExists('contractors');
    }
}
