<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions')->nullOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete();
            $table->string('gateway');                    // sslcommerz | bkash
            $table->string('reference')->unique();        // our internal order id
            $table->string('transaction_id')->nullable(); // gateway txn id
            $table->string('status')->default('pending'); // pending | success | failed | canceled
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('BDT');
            $table->json('payload')->nullable();          // raw gateway response
            $table->timestamps();

            $table->index(['gateway', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
