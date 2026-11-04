<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23870AlteracaoEstruturaCaiparametro extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sqlDD = <<<SQL
insert into db_syscampo values(1014818,'k29_utilizarcontaextra','bool','Utiliza conta extra-orçamentária','f', 'Utiliza conta extra-orçamentária',1,'f','f','f',5,'text','Utiliza conta extra-orçamentária');
insert into db_syscampodef values(1014818,'t','SIM');
insert into db_syscampodef values(1014818,'f','NÃO');

update db_syscampo set nomecam = 'k29_gerarslipautomaticoreceitaretencao', 
                       conteudo = 'int4', 
                       valorinicial = '0', 
                       aceitatipo = 1 
 where codcam = 1013980;

insert into db_syscampodef values(1013980,'0','NÃO');
insert into db_syscampodef values(1013980,'1','SIM - No momento da Liquidação');
insert into db_syscampodef values(1013980,'2','SIM - Na configuração do movimento na agenda');

insert into db_sysarqcamp values(1503,1014818,17,0);

SQL;
        
        
        $sqlEstrutura = <<<SQL
alter table caixa.caiparametro alter column k29_gerarslipautomaticoreceitaretencao set default null;
alter table caixa.caiparametro alter column k29_gerarslipautomaticoreceitaretencao type integer using (case when k29_gerarslipautomaticoreceitaretencao is false then 0 else 1 end);
alter table caixa.caiparametro alter column k29_gerarslipautomaticoreceitaretencao set default 0;

alter table caixa.caiparametro add column k29_utilizarcontaextra bool not null default true;
SQL;
        
        DB::connection()->getPdo()->exec($sqlDD);
        DB::connection()->getPdo()->exec($sqlEstrutura);
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        $sqlDD = <<<SQL
delete from db_sysarqcamp where codcam = 1014818;
delete from db_syscampodef where codcam in (1014818, 1013980);
delete from db_syscampo where codcam in  (1014818);

update db_syscampo set conteudo = 'bool', valorinicial = 'f', aceitatipo = 5 where codcam = 1013980;

SQL;
        
        
        $sqlEstrutura = <<<SQL
alter table caixa.caiparametro alter column k29_gerarslipautomaticoreceitaretencao set default null;
alter table caixa.caiparametro alter column k29_gerarslipautomaticoreceitaretencao type bool using (case when k29_gerarslipautomaticoreceitaretencao = 0 then false else true end);
alter table caixa.caiparametro alter column k29_gerarslipautomaticoreceitaretencao set default false;

alter table caixa.caiparametro drop column k29_utilizarcontaextra;
SQL;
        
        DB::connection()->getPdo()->exec($sqlDD);
        DB::connection()->getPdo()->exec($sqlEstrutura);
    }
}
