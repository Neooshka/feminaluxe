<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('working_hours', function (Blueprint $table) {
            $table->id();
            $table->enum('day_of_week', [
                'monday', 'tuesday', 'wednesday', 'thursday', 
                'friday', 'saturday', 'sunday'
            ]);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_working_day')->default(true);
            $table->integer('slot_duration')->default(30); // minutes
            $table->integer('max_bookings_per_slot')->default(1);
            $table->timestamps();

            $table->unique(['day_of_week']);
        });

        // Добавляем стандартные рабочие часы
        DB::table('working_hours')->insert([
            ['day_of_week' => 'monday', 'start_time' => '09:00', 'end_time' => '21:00', 'is_working_day' => true],
            ['day_of_week' => 'tuesday', 'start_time' => '09:00', 'end_time' => '21:00', 'is_working_day' => true],
            ['day_of_week' => 'wednesday', 'start_time' => '09:00', 'end_time' => '21:00', 'is_working_day' => true],
            ['day_of_week' => 'thursday', 'start_time' => '09:00', 'end_time' => '21:00', 'is_working_day' => true],
            ['day_of_week' => 'friday', 'start_time' => '09:00', 'end_time' => '21:00', 'is_working_day' => true],
            ['day_of_week' => 'saturday', 'start_time' => '10:00', 'end_time' => '18:00', 'is_working_day' => true],
            ['day_of_week' => 'sunday', 'start_time' => null, 'end_time' => null, 'is_working_day' => false],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('working_hours');
    }
};