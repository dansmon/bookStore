<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentbookTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rentbook', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('id_ident');
            $table->string('id_zap');
            $table->integer('id_member');
            $table->date('datum_od');
            $table->date('datum_do');
            $table->integer('status'); // 0- rezervacija oz izposoja končana; 1-rezervirana; 2-rezervacija in izposoja, 3-izposoja      
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
        Schema::dropIfExists('rentbook');
    }
}
