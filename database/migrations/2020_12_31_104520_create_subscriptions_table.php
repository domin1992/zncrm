<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->integer('contractor_id');
            $table->integer('subscription_type_id');
            $table->integer('subscription_package_id');
            $table->string('reference');
            $table->text('comments');
            $table->date('date_start');
            $table->decimal('custom_price_tax_excl', 20, 6)->nullable();
            $table->string('cycle');
            $table->dateTime('canceled_at')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('subscriptions');
    }
}
