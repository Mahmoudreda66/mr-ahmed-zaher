<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->nullable()->onDelete('cascade');
            $table->foreignId('level_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained()->nullable()->onDelete('cascade');
            $table->timestamp('date');
            $table->smallInteger('duration');
            $table->tinyInteger('type')->comment('0 is choose - t&f and autocorrect the exam, 1 is global');
            $table->boolean('exam_type')->comment('0 is electronic, 1 is paper');
            $table->time('status')->comment('null is off, time is on at this')->nullable();
            $table->text('header')->comment('text header of the exam')->nullable();
            $table->text('footer')->comment('text footer of the exam')->nullable();
            $table->unique(['subject_id', 'level_id', 'teacher_id', 'date', 'duration', 'type']);
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
        Schema::dropIfExists('exams');
    }
}
