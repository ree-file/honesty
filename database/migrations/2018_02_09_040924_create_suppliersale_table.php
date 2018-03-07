<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuppliersaleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliersale', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("supplier_id")->unsigned();
            $table->integer("goods_id")->unsigned();
            $table->string("added")->comment("上架多少");
            $table->string("leave")->comment("剩余多少");
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
        Schema::dropIfExists('suppliersale');
    }
}
