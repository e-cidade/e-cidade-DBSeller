<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26913RelacionamentoFailedJob extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("queued_jobs", function (Blueprint $table) {
            $table->timestamp("failed_at")->nullable();
            $table->text("payload")->nullable();
            $table->text("exception")->nullable();
            $table->text("queue")->nullable();
            $table->text("job_connection")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("queued_jobs", function (Blueprint $table) {
            $table->dropColumn("failed_job_id");
            $table->dropColumn("payload");
            $table->dropColumn("exception");
            $table->dropColumn("queue");
            $table->dropColumn("job_connection");
        });
    }
}
