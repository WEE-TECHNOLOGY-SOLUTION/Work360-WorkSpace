<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('customers'))
        {
            Schema::table('customers', function (Blueprint $table) {
                if(!Schema::hasColumn('customers','electronic_address_scheme'))
                {
                    $table->string('electronic_address_scheme')->nullable()->after('electronic_address');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {

        });
    }
};
