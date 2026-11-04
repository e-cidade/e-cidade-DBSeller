<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class M27378ProcessamentoRedesim extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('issqn.establishment_data_batches', function (Blueprint $table) {
            $table->boolean('q189_error')->default(false);
        });

        DB::connection()->getPdo()->exec(<<<SQL
            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.establishment_data_batches');
            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.establishment_data_batches');

            insert into db_syscampo values(1015632,'q189_error','text','Seta se deu erro no processamento','', 'Erro',1,'f','t','f',0,'text','Erro');
            insert into db_sysarqcamp values(1011117,1015632,6,0);

            create index establishment_data_batches_q189_processed_idx on issqn.establishment_data_batches (q189_processed);
            create index establishment_data_batches_q189_error_idx on issqn.establishment_data_batches (q189_error);
SQL
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('issqn.establishment_data_batches', function(Blueprint $table) {
            $table->dropColumn('q189_error');
        });

        DB::connection()->getPdo()->exec(<<<SQL
            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.establishment_data_batches');
            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.establishment_data_batches');

            delete from db_sysarqcamp where codcam in (
                /* establishment_data_batches */
                1015632
            );

            delete from db_syscampo where codcam in (
                /* establishment_data_batches */
                1015632
            );

            drop index if exists establishment_data_batches_q189_processed_idx;
            drop index if exists establishment_data_batches_q189_error_idx;
SQL
        );
    }
}
