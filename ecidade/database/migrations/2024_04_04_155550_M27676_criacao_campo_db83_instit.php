<?php

use Illuminate\Database\Migrations\Migration;

class M27676CriacaoCampoDb83Instit extends Migration
{
        /**
         * Run the migrations.
         *
         * @return void
         */
    public function up()
    {
            $sql = <<<SQL
    insert into db_syscampo values(
            1010010,
            'db83_instit',
            'int4',
            'Código da Instituição responsável pela conciliação bancária',
            0,
            'Código da Instituição',
            2,
            't',
            'f',
            'f',
            0,
            'text',
            'Código da Instituição');

    insert into db_sysarqcamp values(2740,1010010,10,0);

    alter table configuracoes.contabancaria add column db83_instit integer default null;

    /*
      Alteramos o cadastro da conta bancaria setando a instituicao verificando os reduzidos vinculados
      Caso encontrados mais de uma instituição ligada aos reduzidos a conta não é alterada
    */
    update configuracoes.contabancaria set db83_instit = c61_instit
    from (select distinct c56_contabancaria, c61_instit
            from conplanocontabancaria
                 inner join conplanoreduz on c61_reduz = c56_reduz
                                         and c61_anousu = c56_anousu
           where c61_anousu = 2024
             and c56_contabancaria not in (select c56_contabancaria
                                             from (select c56_contabancaria, c61_instit
                                                     from conplanocontabancaria
                                                          inner join conplanoreduz on c56_reduz = c61_reduz
                                                    where c56_anousu = c61_anousu
                                                      and c61_anousu = 2024) as dados
                                            group by c56_contabancaria having count(distinct c61_instit) > 1 )
                                            order by c56_contabancaria ) as instit
    where c56_contabancaria = db83_sequencial;

SQL;
            DB::connection()->getPdo()->exec($sql);
    }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
    public function down()
    {
            $sql = <<<SQL

    delete from db_sysarqcamp where codcam = 1010010;
    delete from db_syscampo where codcam = 1010010;

    alter table configuracoes.contabancaria drop column db83_instit;

SQL;
            DB::connection()->getPdo()->exec($sql);
    }
}
