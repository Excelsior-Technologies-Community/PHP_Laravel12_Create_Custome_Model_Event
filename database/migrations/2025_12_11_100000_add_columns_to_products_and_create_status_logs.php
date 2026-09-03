<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add new columns to products table
        Schema::table('products', function (Blueprint $table) {
            $table->tinyInteger('status')->default(0)->change(); // 0=inactive,1=deactivated,2=active,3=archived
            $table->timestamp('deactivated_at')->nullable()->after('activated_at');
            $table->timestamp('archived_at')->nullable()->after('deactivated_at');
        });

        // Create status logs table
        Schema::create('product_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('event');           // activated, deactivated, archived, priceChanged
            $table->string('old_value')->nullable();
            $table->string('new_value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_status_logs');
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['deactivated_at', 'archived_at']);
        });
    }
};
