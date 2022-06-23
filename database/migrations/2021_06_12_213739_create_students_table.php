<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('level_id')->constrained()->onDelete('cascade');
            $table->string('mobile', 11)->nullable();
            $table->string('mobile2', 11)->nullable();
            $table->string('student_mobile', 11)->nullable();
            $table->boolean('gender')->comment('0 is male, 1 is female');
            $table->boolean('division')->comment('0 is scince, 1 is literary, null is global')->nullable();
            $table->boolean('sub_language')->comment('0 is french, 1 is germany, null is global')->nullable();
            $table->boolean('edu_type')->comment('0 is arabic, 1 is multilanguage');
            $table->mediumInteger('given_money')->nullable();
            $table->string('code', 8)->unique();
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });

        if(config('database.default') === 'pgsql'){
            $statement = "ALTER SEQUENCE students_id_seq RESTART WITH 1000;";
        }else{
            $statement = "ALTER TABLE students AUTO_INCREMENT = 1000;";
        }

        DB::unprepared($statement);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
