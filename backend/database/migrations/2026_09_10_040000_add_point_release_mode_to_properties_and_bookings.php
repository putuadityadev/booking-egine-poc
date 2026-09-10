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
        Schema::table('properties', function (Blueprint $table) {
            $table->string('point_release_mode')->default('automated')->after('has_membership');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('point_release_mode')->default('automated')->after('points_earned');
            $table->boolean('is_points_materialized')->default(false)->after('point_release_mode');
            $table->timestamp('materialized_at')->nullable()->after('is_points_materialized');
            $table->string('membership_transaction_id')->nullable()->after('materialized_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('point_release_mode');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'point_release_mode',
                'is_points_materialized',
                'materialized_at',
                'membership_transaction_id',
            ]);
        });
    }
};
