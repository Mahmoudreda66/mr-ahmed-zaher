<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->mediumInteger('money');
            $table->smallInteger('month');
            $table->softDeletes();
            $table->unique(['money', 'month', 'student_id', 'deleted_at']);
            $table->timestamps();
        });

        if(config('database.default') === 'pgsql'){
            $statement = "ALTER SEQUENCE expenses_id_seq RESTART WITH 1000;";
        }else{
            $statement = "ALTER TABLE expenses AUTO_INCREMENT = 1000;";
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
        Schema::dropIfExists('expenses');
    }
}
