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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->string('tagline', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('address', 255)->nullable();
            $table->string('city', 100);
            $table->string('country', 100)->default('Indonesia');
            $table->integer('star_rating')->default(5);
            $table->decimal('review_score', 3, 2)->default(4.90);
            $table->integer('review_count')->default(85);
            $table->string('badge', 50)->nullable();
            $table->string('image_url', 500);
            $table->json('gallery')->nullable();
            $table->json('amenities')->nullable();
            $table->boolean('has_membership')->default(true);
            $table->timestamps();
        });

        Schema::create('membership_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->string('x_tenant_domain', 150);
            $table->string('client_id', 100);
            $table->text('client_secret');
            $table->string('merchant_id', 100);
            $table->string('corporate_id', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->string('code', 100);
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->integer('capacity')->default(2);
            $table->string('bed_type', 100)->default('1 King Bed');
            $table->integer('size_sqm')->default(65);
            $table->decimal('base_price', 12, 2);
            $table->string('image_url', 500);
            $table->json('gallery')->nullable();
            $table->json('features')->nullable();
            $table->boolean('is_member_rate_applicable')->default(true);
            $table->json('tier_discount_rates')->nullable();
            $table->timestamps();
        });

        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->string('code', 100);
            $table->string('name', 150);
            $table->string('category', 100);
            $table->string('duration', 50);
            $table->decimal('price', 12, 2);
            $table->string('image_url', 500);
            $table->decimal('rating', 3, 2)->default(4.95);
            $table->string('badge', 50)->nullable();
            $table->boolean('is_member_rate_applicable')->default(true);
            $table->string('member_perk', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_code', 50)->unique();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('nights')->default(1);
            $table->integer('guests')->default(2);
            $table->string('guest_name', 150);
            $table->string('guest_email', 150);
            $table->string('guest_phone', 50)->nullable();
            $table->decimal('base_total', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('service_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->boolean('is_member')->default(false);
            $table->string('member_id', 100)->nullable();
            $table->string('member_tier', 50)->nullable();
            $table->integer('points_earned')->default(0);
            $table->string('status', 50)->default('CONFIRMED');
            $table->json('transaction_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('experiences');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('membership_properties');
        Schema::dropIfExists('properties');
    }
};
