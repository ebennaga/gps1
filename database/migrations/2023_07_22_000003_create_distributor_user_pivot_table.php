<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistributorUserPivotTable extends Migration
{
    public function up ()
    {
        Schema::create('distributor_user', function (Blueprint $table) {
            $table->unsignedInteger('distributor_id');
            $table->foreign('distributor_id', 'distributor_id_fk_686844')->references('id')->on('distributors')->onDelete('cascade');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id', 'user_id_fk_686844')->references('id')->on('users')->onDelete('cascade');
        }
        );
    }

}
