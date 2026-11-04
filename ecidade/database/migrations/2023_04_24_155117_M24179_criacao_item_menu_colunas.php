<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M24179CriacaoItemMenuColunas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL

insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) 
values ( 228912 ,'Acompanhamento de Alunos PcD' ,'Acompanhamento de Alunos PcD' ,
            'web/educacao/escola/procedimentos/diario-classe/acompanhamento-alunos-pcd' ,'1' ,'1' ,
            'Acompanhamento de Alunos PcD' ,'true'
            );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 1100930 ,228912 ,8 ,1100747 );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) 
values ( 228919 ,'Recursos Utilizados / AEE' ,'Recursos Utilizados / AEE' ,
            'web/educacao/secretaria/cadastros/tabelas/recursos-utilizados-aee' ,'1' ,'1' ,
            'Recursos Utilizados / AEE' ,'true'
            );                
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 1100791 ,228919 ,20 ,7159 );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) 
values ( 228928 ,'Acompanhamento de Alunos PcD' ,'Acompanhamento de Alunos PcD' ,
            'web/educacao/secretaria/procedimentos/acompanhamento-alunos-pcd' ,'1' ,'1' ,
            'Acompanhamento de Alunos PcD' ,'true' 
            );                
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 3444 ,228928 ,26 ,7159 );
insert into db_sysarquivo values (1011091, 'alunoatendimentoespecial', 'aluno atendimento especial', 'ed197', 
                                    '2023-05-31', 'aluno atendimento especial', 0, 'f', 'f', 'f', 'f' );
insert into db_sysarqmod values (1008004,1011091);
insert into db_sysarquivo values (1011092, 'recursosutilizados', 'recursos utilizados', 'ed198', '2023-05-31', 
                                    'recursos utilizados', 0, 'f', 'f', 'f', 'f' );
insert into db_sysarqmod values (1008004,1011092);
insert into db_sysarquivo values (1011093, 'recursosatendimentos', 'recursos atendimentos', 'ed199', 
                                    '2023-05-31', 'recursos atendimentos', 0, 'f', 'f', 'f', 'f' );
insert into db_sysarqmod values (1008004,1011093);
insert into db_syscampo values(1015124,'ed197_codigo','int8','código','0', 'código',10,'f','f','f',1,'text','código');
insert into db_syscampo values(1015125,'ed197_aluno','int8','aluno','0', 'aluno',10,'f','f','f',1,'text','aluno');
insert into db_syscampo values(1015126,'ed197_data_emissao','date','atendimentos','null', 'data emissao',10,'f',
                                'f','f',1,'text','data emissao');
insert into db_syscampo values(1015127,'ed197_parecer','text','parecer','', 'parecer',500,'f','t','f',0,'text',
                                'parecer');
insert into db_syscampo values(1015128,'ed197_atendimentos','text','atendimentos','', 'atendimentos',40,'f','t',
                                'f',0,'text','atendimentos');   
insert into db_syscampo values(1015129,'ed198_codigo','int8','codigo','0', 'codigo',10,'f','f','f',1,'text','codigo');
insert into db_syscampo values(1015130,'ed198_descricao','text','descricao','', 'descricao',200,'f','t','f',0,
                                'text','descricao');
insert into db_syscampo values(1015131,'ed199_codigo','int8','codigo','0', 'codigo',10,'f','f','f',1,'text','codigo');
insert into db_syscampo values(1015132,'ed199_alunoatendimentos','int8','aluno atendimentos','0', 
                                'aluno atendimentos',10,'f','f','f',1,'text','aluno atendimentos');
insert into db_syscampo values(1015133,'ed199_recursosutilizados','int8','recursos utilizados','0', 
                                'recursos utilizados',10,'f','f','f',1,'text','recursos utilizados');     
insert into db_sysarqcamp values(1011091,1015124,1,0);
insert into db_sysarqcamp values(1011091,1015125,2,0);
insert into db_sysarqcamp values(1011091,1015128,3,0);
insert into db_sysarqcamp values(1011091,1015126,4,0);
insert into db_sysarqcamp values(1011091,1015127,5,0);   
insert into db_sysarqcamp values(1011092,1015129,1,0);
insert into db_sysarqcamp values(1011092,1015130,2,0);
insert into db_sysarqcamp values(1011093,1015131,1,0);
insert into db_sysarqcamp values(1011093,1015132,2,0);
insert into db_sysarqcamp values(1011093,1015133,3,0);
insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011093,1015131,1,1015131);
insert into db_sysforkey values(1011093,1015132,1,1011091,0);
insert into db_sysforkey values(1011093,1015133,1,1011092,0);
insert into db_sysindices values(1008870,'recursosatendimentos_codigo_in',1011093,'0');
insert into db_syscadind values(1008870,1015131,1);
insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011091,1015124,1,1015124);
insert into db_sysforkey values(1011091,1015125,1,1010051,0);
insert into db_sysindices values(1008868,'alunoatendimentoespecial_codigo_in',1011091,'0');
insert into db_syscadind values(1008868,1015124,1);
insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011092,1015129,1,1015129);
insert into db_sysindices values(1008869,'recursosutilizados_codigo_in',1011092,'0');
insert into db_syscadind values(1008869,1015129,1);
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
        DB::connection()->getPdo()->exec(<<<SQL

delete from db_menu where id_item_filho = 228912 AND modulo = 1100747;
delete from db_itensmenu where id_item = 228912;
delete from db_menu where id_item_filho = 228919 AND modulo = 7159;
delete from db_itensmenu where id_item = 228919;
delete from db_menu where id_item_filho = 228928 AND modulo = 7159;
delete from db_itensmenu where id_item = 228928;
delete from db_sysforkey where codarq = 1011091 and referen = 0;
delete from db_sysforkey where codarq = 1011093 and referen = 0;
delete from db_sysforkey where codarq = 1011093 and referen = 0;
delete from db_sysprikey where codarq = 1011093;
delete from db_sysprikey where codarq = 1011091;
delete from db_sysprikey where codarq = 1011092;
delete from db_sysarqcamp where codarq = 1011091;
delete from db_sysarqcamp where codarq = 1011092;
delete from db_sysarqcamp where codarq = 1011093;
delete from db_syscampo where codcam in (1015124, 1015125, 1015128, 1015126, 1015127, 1015129, 1015130, 
                                            1015131, 1015132, 1015133);
delete from db_sysarqmod where codarq = 1011093;
delete from db_sysarquivo where codarq = 1011093;
delete from db_sysarqmod where codarq = 1011092;
delete from db_sysarquivo where codarq = 1011092;
delete from db_sysarqmod where codarq = 1011091;
delete from db_sysarquivo where codarq = 1011091;

SQL
        );
    }
}
