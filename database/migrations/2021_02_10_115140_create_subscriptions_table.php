<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('client_subscription_id');
            $table->uuid('plan_platform_id');
            $table->string('status');
            $table->string('platform_subscription_id')->nullable();
            $table->string('platform_session_id')->nullable();
            $table->timestamps();

            $table->foreign('plan_platform_id')
                ->references('id')
                ->on('plan_platforms');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
}
