<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTriggerUsersToPayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $year = date("Y");

        DB::unprepared('
        CREATE TRIGGER users_to_payment AFTER INSERT ON users FOR EACH ROW
            BEGIN
                INSERT INTO payments (uid, type, years,status,created_at, updated_at)
                VALUES (NEW.id, 1, '.$year.' ,0,now(), null);
            END
        ');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER `users_to_payment`');
    }
}
