<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23495novoAtributoDdr extends Migration
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
        $this->upContaCorrente();
        $this->vinculaContaCorrente();
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
        $this->downContaCorrente();
    }

    private function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente )
values ( 228847 ,'Implantação de saldo da DDR' ,'Implantação de saldo da DDR' , 'web/financeiro/contabilidade/conta-corrente/implantacao/ddr' ,'1' ,'1' ,'Implantação de saldos da DDR' ,'true' );

insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 4197 ,228847 ,21 ,209 );

insert into db_sysarquivo
values (1011021, 'conplanoexecontacorrente', 'Saldo inicial da conta por conta corrente', 'c143', '2023-02-09', 'Saldo da conta corrente', 0, 'f', 'f', 'f', 'f' ),
       (1011022, 'conplanoexecontacorrenteatributo', 'Atributos que compõe o saldo inicial da conta corrente', 'c144', '2023-02-09', 'conplanoexecontacorrenteatributo', 0, 'f', 'f', 'f', 'f' );

insert into db_sysarqmod
values (32,1011021),
       (32,1011022);

insert into db_syscampo
values (1014739,'c143_conplanoreduz','int4','Reduzido da conta','0', 'Reduzido',10,'f','f','f',1,'text','Reduzido'),
       (1014740,'c143_exercicio','int4','Exercício','0', 'Exercício',10,'f','f','f',1,'text','Exercício'),
       (1014741,'c143_conplanosistema','int4','Vínculo com a Conta Corrente','0', 'Conta Corrente',10,'f','f','f',1,'text','Conta Corrente'),
       (1014742,'c143_saldo','float8','Saldo da conta para uma conta corrente','0', 'Saldo da conta',10,'f','f','f',4,'text','Saldo da conta'),
       (1014743,'c143_natureza','char(1)','Natureza do saldo','', 'Natureza',1,'f','t','f',0,'text','Natureza'),
       (1014744,'c144_conplanoexecontacorrente','int4','Vínculo com o saldo inicial','0', 'Saldo inicial',10,'f','f','f',1,'text','Saldo inicial'),
       (1014745,'c144_conplanoinfocomplementar','int4','Vínculo com o atributo','0', 'Atributo',10,'f','f','f',1,'text','Atributo'),
       (1014746,'c144_valor','varchar(255)','Valor do atributo','', 'Valor do atributo',255,'f','t','f',0,'text','Valor do atributo');

insert into db_sysarqcamp
values (1011021,1011345,1,0),
       (1011021,1014739,2,0),
       (1011021,1014740,3,0),
       (1011021,1014741,4,0),
       (1011021,1014742,5,0),
       (1011021,1014743,6,0),
       (1011022,1011345,1,0),
       (1011022,1014744,2,0),
       (1011022,1014745,3,0),
       (1011022,1014746,4,0);

insert into db_sysprikey (codarq,codcam,sequen,camiden)
values (1011021,1011345,1,1011345),
       (1011022,1011345,1,1011345);

insert into db_sysforkey
values (1011021,1014739,1,773,0),
       (1011021,1014740,2,773,0),
       (1011022,1014744,1,1011021,0),
       (1011022,1014745,1,1010256,0);

insert into db_sysindices
values (1008836,'conplanoexecontacorrente_conplanoreduz_exercicio_conplanosistema_in',1011021,'0'),
       (1008837,'conplanoexecontacorrenteatributo_conplanoexecontacorrente_conplanoinfocomplementar_in',1011022,'0');

insert into db_syscadind
values (1008836,1014739,1),
       (1008836,1014740,2),
       (1008836,1014741,3),
       (1008837,1014744,1),
       (1008837,1014745,2);

insert into db_syssequencia
values (1001111, 'conplanoexecontacorrenteatributo_id_seq', 1, 1, 9223372036854775807, 1, 1),
       (1001112, 'conplanoexecontacorrente_id_seq', 1, 1, 9223372036854775807, 1, 1);

update db_sysarqcamp set codsequencia = 1001111 where codarq = 1011022 and codcam = 1011345;
update db_sysarqcamp set codsequencia = 1001112 where codarq = 1011021 and codcam = 1011345;

SQL
        );
    }

    private function upEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
create table contabilidade.conplanoexecontacorrente(
  id bigserial primary key,
  c143_conplanoreduz bigint not null,
  c143_exercicio integer not null,
  c143_conplanosistema integer,
  c143_saldo numeric(15,2) not null,
  c143_natureza char(1),
  FOREIGN KEY (c143_conplanosistema) references contabilidade.conplanosistema,
  FOREIGN KEY (c143_conplanoreduz, c143_exercicio) references contabilidade.conplanoreduz
);

create table contabilidade.conplanoexecontacorrenteatributo(
    id bigserial primary key,
    c144_conplanoexecontacorrente integer not null,
    c144_conplanoinfocomplementar integer not null,
    c144_valor varchar(255),
    FOREIGN KEY (c144_conplanoinfocomplementar) references contabilidade.conplanoinfocomplementar,
    FOREIGN KEY (c144_conplanoexecontacorrente) references contabilidade.conplanoexecontacorrente on DELETE CASCADE
);

CREATE INDEX conplanoexecontacorrente_conplanoreduz_exercicio_conplanosistema_in ON contabilidade.conplanoexecontacorrente(c143_conplanoreduz,c143_exercicio,c143_conplanosistema);
CREATE INDEX conplanoexecontacorrenteatributo_conplanoexecontacorrente_conplanoinfocomplementar_in ON contabilidade.conplanoexecontacorrenteatributo(c144_conplanoexecontacorrente,c144_conplanoinfocomplementar);

select configuracoes.fc_auditoria_cria_funcao('contabilidade.conplanoexecontacorrente');
select configuracoes.fc_auditoria_cria_funcao('contabilidade.conplanoexecontacorrenteatributo');
SQL
        );
    }

    private function upContaCorrente()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into conplanoinfocomplementar (c121_sequencial,c121_sigla,c121_descricao,c121_sql,c121_ajuda,c121_nomepropriedade,c121_valorpadrao)
values (100, 'DDR', '[DDR] - Fonte Recurso', 'SELECT o201_orctiporec
  FROM conlancamcomplementorecurso
 WHERE o201_codlan = codigo_lancamento
 union
 SELECT c130_orctiporec
   FROM conlancamrecurso
  WHERE c130_conlancam = codigo_lancamento
    AND c130_conta = conta_reduzida
    AND c130_natureza = natureza
 LIMIT 1
', '[DDR] - Fonte Recurso', 'codigo_recurso', 'NI');

insert into conplanosistema (c122_sequencial, c122_descricao, c122_tipo)
values (100, 'Fonte de Recursos', 2);

select setval('conplanosistemaatributos_c129_sequencial_seq', (select max(c129_sequencial) from conplanosistemaatributos));

insert into conplanosistemaatributos (c129_sequencial, c129_conplanosistema, c129_conplanoinfocomplementar, c129_ordem)
values (nextval('conplanosistemaatributos_c129_sequencial_seq'), 100, 100, 1);
SQL
        );
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_menu where id_item_filho = 228847 AND modulo = 209;
delete from db_itensmenu where id_item = 228847;

delete from db_sysprikey where codarq in (1011021, 1011022);
delete from db_sysforkey where codarq in (1011021, 1011022);
delete from db_syssequencia where codsequencia in (1001111, 1001112);
delete from db_syscadind where codind in (1008836, 1008837);
delete from db_sysindices where codind in (1008836, 1008837);
delete from db_sysarqcamp where codarq in (1011021, 1011022);
delete from db_syscampo where codcam in (1014739, 1014740, 1014741, 1014742, 1014743, 1014744, 1014745, 1014746);
delete from db_sysarqmod where codarq in (1011021, 1011022);
delete from db_sysarquivo where codarq in (1011021, 1011022);
SQL
        );
    }

    private function downEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop table contabilidade.conplanoexecontacorrenteatributo;
drop table contabilidade.conplanoexecontacorrente;
SQL
        );
    }

    private function downContaCorrente()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from conplanosistemaatributos where c129_conplanosistema = 100;
delete from conplanoatributos where c120_infocomplementar = 100;
delete from conplanoinfocomplementar where c121_sequencial = 100;
delete from conplanosistema where c122_sequencial = 100;
SQL
        );
    }

    private function vinculaContaCorrente()
    {
        $sql = "
            select distinct c60_codcon, c60_anousu from conplano
                join conplanoreduz on (c61_codcon, c61_anousu) = (c60_codcon, c60_anousu)
             where c60_anousu = 2023 and (c60_estrut like '82%' or c60_estrut like '72%');
        ";
        $contas = DB::select($sql);

        $sqlInsert = '
        insert into contabilidade.conplanoatributos
            (c120_anousu, c120_conplano, c120_infocomplementar, c120_conplanosistema) values (?, ?, ?, ?)';
        foreach ($contas as $conta) {
                DB::insert($sqlInsert, [$conta->c60_anousu, $conta->c60_codcon, 100, 100]);
        }
    }
}
