<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistributorManagerPivotTable extends Migration
{
    public function up ()
    {
        Schema::create('distributor_manager', function (Blueprint $table) {
            $table->unsignedInteger('distributor_id');
            $table->foreign('distributor_id', 'distributor_id_fk_686822')->references('id')->on('distributors')->onDelete('cascade');
            $table->unsignedInteger('manager_id');
            $table->foreign('manager_id', 'manager_id_fk_686822')->references('id')->on('users')->onDelete('cascade');
        }
        );
    }

}
