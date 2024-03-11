<?php

use App\Models\Apartment;
use App\Models\User;
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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Apartment::class)->constrained();
            $table->foreignIdFor(User::class)->constrained();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('guests_adults');
            $table->unsignedInteger('guests_children');
            $table->unsignedInteger('total_price');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
