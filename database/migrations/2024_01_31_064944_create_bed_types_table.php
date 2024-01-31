<?php

use App\Models\BedType;
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
        Schema::create('bed_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        BedType::create(['name'=> 'Single Bed']);
        BedType::create(['name'=> 'Large Double Bed']);
        BedType::create(['name'=> 'Extra Large Double Bed']);
        BedType::create(['name'=> 'Sofa Bed']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bed_types');
    }
};
