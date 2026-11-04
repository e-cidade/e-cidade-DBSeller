<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26917DesabilitaObrigatoriedadeAns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
            update configuracoes.db_syscampo set nomecam = 'rh221_ans', conteudo = 'int4', descricao = 'ANS', valorinicial = '0', rotulo = 'ANS', nulo = 't', tamanho = 6, maiusculo = 'f', autocompl = 'f', aceitatipo = 1, tipoobj = 'text', rotulorel = 'ANS' where codcam = 1010051;
            insert into configuracoes.db_syscampo values(1015598,'rh221_instituicao','int4','Código da Instituição','0', 'Código da Instituição',10,'f','f','f',1,'text','Código da Instituição');
            insert into configuracoes.db_sysarqcamp values(1010333,1015598,5,0);
            delete from configuracoes.db_sysindices where codind = 1008343;
            delete from configuracoes.db_syscampodep where codcam = 1010051;
            delete from configuracoes.db_syscampodef where codcam = 1010051;
            drop INDEX operadorasaude_rh221_ans_uindex;
            drop INDEX operadorasaude_cgm_in;
            alter table pessoal.operadorasaude alter column rh221_ans drop not null;
            alter table pessoal.operadorasaude add column rh221_instituicao integer;
            CREATE UNIQUE INDEX operadorasaude_cgm_instit_in ON pessoal.operadorasaude (rh221_cgm, rh221_instituicao);

SQL;
        DB::connection()->getPdo()->exec($sql);

        $this->acertoDados();
    }

    public function acertoDados($rollback = false)
    {
        $sql = <<<SQL
            -- replicamos os registros de operadora para todas instituicoes
            insert into pessoal.operadorasaude (
                select 
                    nextval('operadorasaude_rh221_sequencial_seq'),
                    rh221_cgm,
                    rh221_ans,
                    rh221_ativo,
                    rh01_instit
                from 
                    pessoal.operadorasaude left join (select distinct rh01_instit from pessoal.rhpessoal order by 1 asc) as x on 1=1
                where rh221_instituicao is null
            );

            -- atualizamos os registros configurados dos servidores, pelo registro correto da instituicao
            update 
                pessoal.servidoroperadorasaude set rh222_operadorasaude  = x.rh221_sequencial 
            from 
                pessoal.operadorasaude a 
                inner join operadorasaude x on 
                    a.rh221_cgm = x.rh221_cgm
                inner join rhpessoal on 1=1
            where 
                a.rh221_sequencial = rh222_operadorasaude
                and x.rh221_instituicao = rh01_instit
                and rh222_servidor = rh01_regist;

            -- deletamos os registros sem instituicoes
            delete from pessoal.operadorasaude where rh221_instituicao is null;

SQL;

        if ($rollback) {
            $sql = <<<SQL
            -- setamos como null o campo instituicao o menor id das operadoras diferentes
            update pessoal.operadorasaude set rh221_instituicao = null where rh221_sequencial in (
                select sequencial from (
                    select 
                        min(rh221_sequencial) as sequencial,
                        rh221_cgm,
                        rh221_ans,
                        rh221_ativo 
                    from
                        pessoal.operadorasaude
                    group by
                        rh221_cgm,
                        rh221_ans,
                        rh221_ativo
                ) as x
            );

            -- setamos a operadora com instituicao null de todos os servidores configurados previamente
            update 
                pessoal.servidoroperadorasaude set rh222_operadorasaude  = x.rh221_sequencial 
            from 
                pessoal.operadorasaude a 
                inner join pessoal.operadorasaude x on 
                    a.rh221_cgm = x.rh221_cgm
            where 
                a.rh221_sequencial = rh222_operadorasaude
                and x.rh221_instituicao is null;

            -- deletamos todas operadoras com codigo de instituicao
            delete from pessoal.operadorasaude where rh221_instituicao is not null;
SQL;
        }

        DB::connection()->getPdo()->exec($sql);

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->acertoDados(true);

        $sql = <<<SQL
            delete from  configuracoes.db_sysarqcamp where codcam = 1015598;
            delete from  configuracoes.db_syscampo where codcam = 1015598;

            CREATE UNIQUE INDEX operadorasaude_rh221_ans_uindex ON pessoal.operadorasaude (rh221_ans);
            CREATE UNIQUE INDEX operadorasaude_cgm_in ON pessoal.operadorasaude (rh221_cgm);
            alter table pessoal.operadorasaude alter column rh221_ans set not null;
            alter table pessoal.operadorasaude drop column rh221_instituicao;
            update configuracoes.db_syscampo set nomecam = 'rh221_ans', conteudo = 'int4', descricao = 'ANS', valorinicial = '0', rotulo = 'ANS', nulo = 'f', tamanho = 6, maiusculo = 'f', autocompl = 'f', aceitatipo = 1, tipoobj = 'text', rotulorel = 'ANS' where codcam = 1010051;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
