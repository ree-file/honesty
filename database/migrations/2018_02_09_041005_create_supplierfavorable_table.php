<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupplierfavorableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplierfavorable', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("supplier_id")->unsigned();
            $table->decimal("limit")->comment("优惠下限");
            $table->decimal("discountmoney")->comment("优惠金额");
            $table->boolean("is_active");
            $table->date("starttime")->comment("开始日期")->nullable();
            $table->date("deadline")->comment("截止日期")->nullable();
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
        Schema::dropIfExists('supplierfavorable');
    }
}
