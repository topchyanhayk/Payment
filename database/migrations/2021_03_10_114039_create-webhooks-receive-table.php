<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebhooksReceiveTable extends Migration
{
    public function up(): void
    {
        Schema::create('webhooks-receive', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('platform_id')->unique();
            $table->string('platform');
            $table->string('status');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhooks-receive');
    }
}
