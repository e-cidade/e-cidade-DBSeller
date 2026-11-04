<?php

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26248TabelaAberturaexerciciodocumento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
        $this->upEstrutura();

        DBConfig::all('codigo')->each(function (DBConfig $institi) {
            $docs = [2001, 2003, 2021, 2032, 2033];
            foreach ($docs as $doc) {
                $dados = [
                    "c149_anousu" => 2023,
                    "c149_documento" => $doc,
                    "c149_instit" => $institi->codigo,
                    "c149_usuario" => 1
                ];
                DB::table('contabilidade.aberturaexerciciodocumento')->insert($dados);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDicionario();
        $this->downEstrutura();
    }

    private function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
create table contabilidade.aberturaexerciciodocumento (
  c149_id         serial primary key not null,
  c149_anousu     integer not null,
  c149_documento  integer not null,
  c149_instit     integer not null,
  c149_usuario    integer not null,
  created_at timestamp,
  updated_at timestamp,
  foreign key (c149_instit) references configuracoes.db_config on delete cascade,
  foreign key (c149_usuario) references configuracoes.db_usuarios on delete cascade,
  foreign key (c149_documento) references contabilidade.conhistdoc on delete cascade
);

SQL
        );
    }

    private function upEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_sysarquivo values (1011161, 'aberturaexerciciodocumento', 'Abertura de Exercício por documento', 'c149', '2023-11-09', 'Abertura de Exercício por documento', 0, 'f', 'f', 'f', 'f' );
insert into db_sysarqmod values (32,1011161);
insert into db_syscampo
values (1015537,'c149_id','int4','Código','0', 'Código',10,'f','f','f',1,'text','Código'),
       (1015538,'c149_anousu','int4','Exercício','0', 'Exercício',10,'f','f','f',1,'text','Exercício'),
       (1015539,'c149_documento','int4','Documento','0', 'Documento',10,'f','f','f',1,'text','Documento'),
       (1015540,'c149_instit','int4','Instituição','0', 'Instituição',10,'f','f','f',1,'text','Instituição'),
       (1015541,'c149_usuario','int4','Usuário','0', 'Usuário',10,'f','f','f',1,'text','Usuário');

insert into db_sysarqcamp
values (1011161,1015537,1,0),
       (1011161,1015538,2,0),
       (1011161,1015539,3,0),
       (1011161,1015540,4,0),
       (1011161,1015541,5,0),
       (1011161,1012583,6,0),
       (1011161,1012584,7,0);

insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011161,1015537,1,1015537);
insert into db_sysforkey values(1011161,1015539,1,807,0);
insert into db_sysforkey values(1011161,1015540,1,83,0);
insert into db_sysforkey values(1011161,1015541,1,109,0);
insert into db_syssequencia values(1001174, 'aberturaexerciciodocumento_c149_id_seq', 1, 1, 9223372036854775807, 1, 1);
update db_sysarqcamp set codsequencia = 1001174 where codarq = 1011161 and codcam = 1015537;

SQL
        );
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_sysforkey where codarq = 1011161;
delete from db_sysprikey where codarq = 1011161;
delete from db_sysarqcamp where codarq = 1011161;
delete from db_syssequencia where codsequencia = 1001174;
delete from db_syscampo where codcam in (1015537, 1015538, 1015539, 1015540, 1015541);
delete from db_sysarqmod where codarq = 1011161;
delete from db_sysarquivo where codarq = 1011161;
SQL
        );
    }

    private function downEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop table contabilidade.aberturaexerciciodocumento;
SQL
        );
    }
}
