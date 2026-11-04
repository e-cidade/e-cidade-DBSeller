<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28901MotivosAfastamentos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

     protected $nomesView = [
        'view_dados_clonado_cessao',
        'view_dados_originais_cessao',
        'view_dados_clonado_licenca',
        'view_dados_originais_licenca',
        'view_dados_clonado_eletivo',
        'view_dados_originais_eletivo',
     ];
     public function up()
     {
         $this->UpCriandoView();
         $this->upCampoValidacao();
         $this->upAdicionaCamposDinamicos();
         $this->upAdicionaNovoCampoDinamico();
         $this->upObrigatoriedadeCessao();

         $nomesView = array_unique($this->nomesView);
         $this->downDeletandoView($nomesView);
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {
         $this->UpCriandoView();

         $this->downObrigatoriedadeCessao();
         $this->downAdicionaNovoCampoDinamico();
         $this->downAdicionaCamposDinamicos();
         $nomesView = array_unique($this->nomesView);
         $this->downDeletandoView($nomesView);
     }

     private function upObrigatoriedadeCessao()
     {
         $sql = <<<SQL
            UPDATE configuracoes.db_cadattdinamicoatributos SET db109_obrigatorio=FALSE WHERE trim(db109_descricao) = 'CNPJ órgão/entidade';
SQL;
         DB::connection()->getPdo()->exec($sql);
     }

     private function UpCriandoView()
     {
         $sql = <<<SQL
                --Cessão/Requisição
                CREATE MATERIALIZED VIEW IF NOT EXISTS view_dados_originais_cessao
                AS (SELECT
                        db_cadattdinamico.db118_sequencial
                        ,db_cadattdinamicoatributos.db109_sequencial
	        FROM
                        esocial.grupomotivoafastamentoesocial
                        INNER JOIN esocial.motivoafastamentoesocial ON eso09_grupomotivoafastamentoesocial = eso10_sequencial
                        INNER JOIN configuracoes.db_cadattdinamico ON eso10_db_cadattdinamico = db118_sequencial
                        INNER JOIN configuracoes.db_cadattdinamicoatributos on db118_sequencial = db109_db_cadattdinamico
	        WHERE eso09_sequencial IN (14)AND db109_nome = 'motivo_esocial');

                CREATE MATERIALIZED VIEW IF NOT EXISTS view_dados_clonado_cessao
                        AS (SELECT db39_camponovo, db39_campoorigem FROM configuracoes.mapeamentoatributosesocial
                        WHERE db39_campoorigem IN (
	        SELECT db109_sequencial FROM view_dados_originais_cessao));

                --Licença Maternidade
                CREATE MATERIALIZED VIEW IF NOT EXISTS view_dados_originais_licenca
                AS (SELECT
                        db_cadattdinamico.db118_sequencial
                        ,db_cadattdinamicoatributos.db109_sequencial
	        FROM
                        esocial.grupomotivoafastamentoesocial
                        INNER JOIN esocial.motivoafastamentoesocial ON eso09_grupomotivoafastamentoesocial = eso10_sequencial
                        INNER JOIN configuracoes.db_cadattdinamico ON eso10_db_cadattdinamico = db118_sequencial
                        INNER JOIN configuracoes.db_cadattdinamicoatributos on db118_sequencial = db109_db_cadattdinamico
	        WHERE eso09_sequencial IN (17,18,19,20,33)AND db109_nome = 'motivo_esocial');

                CREATE MATERIALIZED VIEW IF NOT EXISTS view_dados_clonado_licenca
                        AS (SELECT db39_camponovo, db39_campoorigem FROM configuracoes.mapeamentoatributosesocial
                        WHERE db39_campoorigem IN (
                SELECT db109_sequencial FROM view_dados_originais_licenca));

                --Cargo Eletivo
                CREATE MATERIALIZED VIEW IF NOT EXISTS view_dados_originais_eletivo
                AS (SELECT
                        db_cadattdinamico.db118_sequencial
                        ,db_cadattdinamicoatributos.db109_sequencial
                FROM
                        esocial.grupomotivoafastamentoesocial
                        INNER JOIN esocial.motivoafastamentoesocial ON eso09_grupomotivoafastamentoesocial = eso10_sequencial
                        INNER JOIN configuracoes.db_cadattdinamico ON eso10_db_cadattdinamico = db118_sequencial
                        INNER JOIN configuracoes.db_cadattdinamicoatributos on db118_sequencial = db109_db_cadattdinamico
                WHERE eso09_sequencial IN (12,13)AND db109_nome = 'motivo_esocial');

                CREATE MATERIALIZED VIEW IF NOT EXISTS view_dados_clonado_eletivo
                        AS (SELECT db39_camponovo, db39_campoorigem FROM configuracoes.mapeamentoatributosesocial
                        WHERE db39_campoorigem IN (
                SELECT db109_sequencial FROM view_dados_originais_eletivo));
SQL;
            DB::connection()->getPdo()->exec($sql);
     }

     private function upCampoValidacao()
     {
         $sql = <<<SQL
                INSERT INTO configuracoes.db_cadattdinamicoatributosopcoes
                        SELECT nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'),db109_sequencial,'14','Cód. 14: Cessão/Requisição' FROM configuracoes.db_cadattdinamicoatributos WHERE NOT EXISTS (
                        SELECT 1 FROM configuracoes.db_cadattdinamicoatributosopcoes WHERE db18_opcao = '14') AND db109_sequencial IN (
                        SELECT db109_sequencial FROM configuracoes.db_cadattdinamicoatributos WHERE db109_db_cadattdinamico IN (
                        SELECT db118_sequencial FROM configuracoes.db_cadattdinamico WHERE TRIM(db118_descricao) = 'Cessão/Requisição') AND TRIM(db109_nome) = 'motivo_esocial');

                INSERT INTO esocial.grupomotivoafastamentoesocial
                        SELECT nextval('grupomotivoafastamentoesocial_eso10_sequencial_seq'),'Cessão/Requisição',db118_sequencial FROM configuracoes.db_cadattdinamico WHERE NOT EXISTS (
                        SELECT eso10_sequencial FROM esocial.grupomotivoafastamentoesocial WHERE TRIM(eso10_descricao) = 'Cessão/Requisição' ) AND TRIM(db118_descricao) = 'Cessão/Requisição';

                INSERT INTO esocial.motivoafastamentoesocial
                        SELECT '14','Cessão / Requisição ', eso10_sequencial FROM esocial.grupomotivoafastamentoesocial WHERE NOT EXISTS (
                        SELECT 1 FROM esocial.motivoafastamentoesocial WHERE eso09_sequencial = 14 ) AND eso10_db_cadattdinamico IN (
                        SELECT db118_sequencial FROM configuracoes.db_cadattdinamico WHERE TRIM(db118_descricao) = 'Cessão/Requisição' );

                INSERT INTO configuracoes.db_cadattdinamicoatributosopcoes (db18_sequencial, db18_cadattdinamicoatributos, db18_opcao, db18_valor)
                        VALUES(nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'),(select db109_sequencial from configuracoes.db_cadattdinamicoatributos where db109_db_cadattdinamico IN
                         (select db118_sequencial from configuracoes.db_cadattdinamico where trim(db118_descricao) = 'Cessão/Requisição') and db109_nome = 'motivo_esocial' limit 1)
                        , '40', 'Cód. 40: Exercício em outro órgão de servidor ou empregado público cedido');

                INSERT INTO esocial.motivoafastamentoesocial (eso09_sequencial,eso09_descricao,eso09_grupomotivoafastamentoesocial)
	                VALUES(40,'Cessão / Requisição - Exercício em outro órgão de servidor ou empregado público cedido',(SELECT eso10_sequencial FROM esocial.grupomotivoafastamentoesocial WHERE eso10_db_cadattdinamico IN
                        (SELECT db118_sequencial FROM configuracoes.db_cadattdinamico WHERE trim(db118_descricao)='Cessão/Requisição')));

                INSERT INTO configuracoes.db_cadattdinamicoatributosopcoes (db18_sequencial, db18_cadattdinamicoatributos, db18_opcao, db18_valor)
                SELECT
                nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'),
                db39_camponovo,
                '14',
                'Cód. 14: Cessão/Requisição'
                FROM view_dados_clonado_cessao;

                INSERT INTO configuracoes.db_cadattdinamicoatributosopcoes (db18_sequencial, db18_cadattdinamicoatributos, db18_opcao, db18_valor)
                SELECT
                nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'),
                db39_camponovo,
                '40',
                'Cessão / Requisição - Exercício em outro órgão de servidor ou empregado público cedido'
                FROM
                view_dados_clonado_cessao;
SQL;
         DB::connection()->getPdo()->exec($sql);
     }

     private function upAdicionaCamposDinamicos()
     {
         $sql = <<<SQL
                INSERT INTO configuracoes.db_cadattdinamicoatributosopcoes (db18_sequencial, db18_cadattdinamicoatributos, db18_opcao, db18_valor)
                SELECT
                nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'),
                db39_camponovo,
                '35',
                'Cód. 35: Licença maternidade: Antecipação e/ou prorrogação mediante atestado médico'
                FROM view_dados_clonado_licenca;

                INSERT INTO configuracoes.db_cadattdinamicoatributosopcoes (db18_sequencial, db18_cadattdinamicoatributos, db18_opcao, db18_valor)
                        VALUES(nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'),(select db109_sequencial from configuracoes.db_cadattdinamicoatributos where db109_db_cadattdinamico IN
                        (select db118_sequencial from configuracoes.db_cadattdinamico where trim(db118_descricao) = 'Licença Maternidade') and db109_nome = 'motivo_esocial' limit 1)
                        , '35', 'Cód. 35: Licença maternidade: Antecipação e/ou prorrogação mediante atestado médico');

                INSERT INTO esocial.motivoafastamentoesocial (eso09_sequencial,eso09_descricao,eso09_grupomotivoafastamentoesocial)
                        VALUES(35,'Licença maternidade - Antecipação e/ou prorrogação mediante atestado médico',(SELECT eso10_sequencial FROM esocial.grupomotivoafastamentoesocial WHERE eso10_db_cadattdinamico IN
                        (SELECT db118_sequencial FROM configuracoes.db_cadattdinamico WHERE trim(db118_descricao)='Licença Maternidade')));



                INSERT INTO configuracoes.db_cadattdinamicoatributosopcoes (db18_sequencial, db18_cadattdinamicoatributos, db18_opcao, db18_valor)
                SELECT
                nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'),
                db39_camponovo,
                '36',
                'Cód. 36: Afastamento temporário de exercente de mandato eletivo para cargo em comissão'
                FROM view_dados_clonado_eletivo;

                INSERT INTO configuracoes.db_cadattdinamicoatributosopcoes (db18_sequencial, db18_cadattdinamicoatributos, db18_opcao, db18_valor)
                        VALUES(nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'),(select db109_sequencial from configuracoes.db_cadattdinamicoatributos where db109_db_cadattdinamico IN
                        (select db118_sequencial from configuracoes.db_cadattdinamico where trim(db118_descricao) = 'Cargo Eletivo') and db109_nome = 'motivo_esocial' limit 1)
                        , '36', 'Cód. 36: Afastamento temporário de exercente de mandato eletivo para cargo em comissão');

                INSERT INTO esocial.motivoafastamentoesocial (eso09_sequencial,eso09_descricao,eso09_grupomotivoafastamentoesocial)
                        VALUES(36,'Cargo Eletivo - Afastamento temporário de exercente de mandato eletivo para cargo em comissão',(SELECT eso10_sequencial FROM esocial.grupomotivoafastamentoesocial WHERE eso10_db_cadattdinamico IN
                        (SELECT db118_sequencial FROM configuracoes.db_cadattdinamico WHERE trim(db118_descricao)='Cargo Eletivo')));

SQL;
            DB::connection()->getPdo()->exec($sql);
     }


     private function upAdicionaNovoCampoDinamico()
     {
         $sql = <<<SQL
                -- Grupo - Escala Avulso : 38
                INSERT INTO configuracoes.db_cadattdinamico (db118_sequencial,db118_descricao)
                        VALUES (nextval('db_cadattdinamico_db118_sequencial_seq'),  'Escala Avulso');

                INSERT INTO configuracoes.db_cadattdinamicoatributos (db109_sequencial,db109_db_cadattdinamico,db109_codcam,db109_descricao,db109_valordefault,db109_tipo,db109_nome,db109_obrigatorio,db109_ativo,db109_fixo,db109_db_formulas)
                        VALUES (nextval('db_cadattdinamicoatributos_db109_sequencial_seq'), currval('db_cadattdinamico_db118_sequencial_seq'), null, 'Motivo', '', 6, 'motivo_esocial', 't','t','t',null);

                INSERT INTO configuracoes.db_cadattdinamicoatributosopcoes (db18_sequencial,db18_cadattdinamicoatributos,db18_opcao,db18_valor)
                        VALUES (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), currval('db_cadattdinamicoatributos_db109_sequencial_seq'), 38, 'Cód. 38: Escala Avulso: Impedimento de concorrência à escala para trabalho avulso');

                INSERT INTO esocial.grupomotivoafastamentoesocial (eso10_sequencial,eso10_descricao,eso10_db_cadattdinamico)
                        VALUES (nextval('grupomotivoafastamentoesocial_eso10_sequencial_seq'), 'Escala Avulso', currval('db_cadattdinamico_db118_sequencial_seq'));

                INSERT INTO esocial.motivoafastamentoesocial (eso09_sequencial,eso09_descricao,eso09_grupomotivoafastamentoesocial)
                        VALUES (38, 'Cód. 38: Escala Avulso - Impedimento de concorrência à escala para trabalho avulso', currval('grupomotivoafastamentoesocial_eso10_sequencial_seq'));

                --Grupo - Recadastramento : 39
                INSERT INTO configuracoes.db_cadattdinamico (db118_sequencial,db118_descricao)
                        VALUES (nextval('db_cadattdinamico_db118_sequencial_seq'),  'Recadastramento');

                INSERT INTO configuracoes.db_cadattdinamicoatributos (db109_sequencial,db109_db_cadattdinamico,db109_codcam,db109_descricao,db109_valordefault,db109_tipo,db109_nome,db109_obrigatorio,db109_ativo,db109_fixo,db109_db_formulas)
                        VALUES (nextval('db_cadattdinamicoatributos_db109_sequencial_seq'), currval('db_cadattdinamico_db118_sequencial_seq'), null, 'Motivo', '', 6, 'motivo_esocial', 't','t','t',null);

                INSERT INTO configuracoes.db_cadattdinamicoatributosopcoes (db18_sequencial,db18_cadattdinamicoatributos,db18_opcao,db18_valor)
                        VALUES (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), currval('db_cadattdinamicoatributos_db109_sequencial_seq'), 39, 'Cód. 39: Recadastramento: Suspensão de pagamento de servidor público por não recadastramento');

                INSERT INTO esocial.grupomotivoafastamentoesocial (eso10_sequencial,eso10_descricao,eso10_db_cadattdinamico)
                        VALUES (nextval('grupomotivoafastamentoesocial_eso10_sequencial_seq'), 'Recadastramento', currval('db_cadattdinamico_db118_sequencial_seq'));

                INSERT INTO esocial.motivoafastamentoesocial (eso09_sequencial,eso09_descricao,eso09_grupomotivoafastamentoesocial)
                        VALUES (39, 'Cód. 39: Recadastramento - Suspensão de pagamento de servidor público por não recadastramento', currval('grupomotivoafastamentoesocial_eso10_sequencial_seq'));
SQL;
            DB::connection()->getPdo()->exec($sql);
     }


    private function downObrigatoriedadeCessao()
    {
        $sql = <<<SQL
            UPDATE configuracoes.db_cadattdinamicoatributos SET db109_obrigatorio=TRUE WHERE trim(db109_descricao) = 'CNPJ órgão/entidade';
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

     private function downAdicionaNovoCampoDinamico()
     {
         $sql = <<<SQL
                --Grupo - Escala Avulso : 38
                DELETE FROM configuracoes.db_cadattdinamicoatributosopcoes WHERE db18_sequencial IN
                        (select db18_sequencial from configuracoes.db_cadattdinamicoatributosopcoes WHERE db18_cadattdinamicoatributos IN
                        (SELECT db109_sequencial FROM configuracoes.db_cadattdinamicoatributos WHERE db109_db_cadattdinamico in
                        (select db118_sequencial from configuracoes.db_cadattdinamico where trim(db118_descricao) = 'Escala Avulso')));

                DELETE FROM configuracoes.db_cadattdinamicoatributos WHERE db109_sequencial IN
                        (select db109_sequencial from configuracoes.db_cadattdinamicoatributos where db109_db_cadattdinamico in
                        (select db118_sequencial from configuracoes.db_cadattdinamico where trim(db118_descricao) = 'Escala Avulso'));

                DELETE FROM esocial.motivoafastamentoesocial WHERE eso09_sequencial IN
                        (select eso09_sequencial from esocial.motivoafastamentoesocial where eso09_grupomotivoafastamentoesocial IN
                        (select eso10_sequencial from esocial.grupomotivoafastamentoesocial where trim(eso10_descricao) = 'Escala Avulso'));

                DELETE FROM esocial.grupomotivoafastamentoesocial WHERE eso10_db_cadattdinamico in
                        (select db118_sequencial from configuracoes.db_cadattdinamico where trim(db118_descricao) = 'Escala Avulso');

                DELETE FROM configuracoes.db_cadattdinamico WHERE trim(db118_descricao) = 'Escala Avulso';

                --Grupo - Recadastramento : 39
                DELETE FROM configuracoes.db_cadattdinamicoatributosopcoes WHERE db18_sequencial IN
                        (select db18_sequencial from configuracoes.db_cadattdinamicoatributosopcoes where db18_cadattdinamicoatributos IN
                        (select db109_sequencial from configuracoes.db_cadattdinamicoatributos where db109_db_cadattdinamico IN
                        (select db118_sequencial from configuracoes.db_cadattdinamico where trim(db118_descricao) = 'Recadastramento')));

                DELETE FROM configuracoes.db_cadattdinamicoatributos WHERE db109_sequencial IN
                        (select db109_sequencial from configuracoes.db_cadattdinamicoatributos where db109_db_cadattdinamico IN
                        (select db118_sequencial from configuracoes.db_cadattdinamico where trim(db118_descricao) = 'Recadastramento'));

                DELETE FROM esocial.motivoafastamentoesocial WHERE eso09_sequencial IN
                        (select eso09_sequencial from esocial.motivoafastamentoesocial where eso09_grupomotivoafastamentoesocial IN
                        (select eso10_sequencial from esocial.grupomotivoafastamentoesocial where trim(eso10_descricao) = 'Recadastramento'));

                DELETE FROM esocial.grupomotivoafastamentoesocial WHERE eso10_db_cadattdinamico IN
                        (select db118_sequencial from configuracoes.db_cadattdinamico where trim(db118_descricao) = 'Recadastramento');

                DELETE FROM configuracoes.db_cadattdinamico WHERE trim(db118_descricao) = 'Recadastramento';
SQL;
            DB::connection()->getPdo()->exec($sql);
     }

     private function downAdicionaCamposDinamicos()
     {
         $sql = <<<SQL
                DELETE FROM configuracoes.db_cadattdinamicoatributosopcoes
                        WHERE db18_cadattdinamicoatributos in (select db109_sequencial from configuracoes.db_cadattdinamicoatributos where db109_db_cadattdinamico IN
                        (select db118_sequencial from configuracoes.db_cadattdinamico where trim(db118_descricao) = 'Licença Maternidade') and db109_nome = 'motivo_esocial'
                                ) and trim(db18_opcao) = '35';

                DELETE FROM esocial.motivoafastamentoesocial WHERE eso09_grupomotivoafastamentoesocial IN (
                        SELECT eso10_sequencial FROM esocial.grupomotivoafastamentoesocial WHERE eso10_db_cadattdinamico IN (
                                SELECT db118_sequencial FROM configuracoes.db_cadattdinamico WHERE db118_descricao = 'Licença Maternidade')) AND eso09_sequencial = 35;

                DELETE FROM configuracoes.db_cadattdinamicoatributosopcoes
                        WHERE db18_cadattdinamicoatributos in (select db109_sequencial from configuracoes.db_cadattdinamicoatributos where db109_db_cadattdinamico IN
                        (select db118_sequencial from configuracoes.db_cadattdinamico where trim(db118_descricao) = 'Cargo Eletivo') and db109_nome = 'motivo_esocial'
                                ) and trim(db18_opcao) = '36';

                DELETE FROM esocial.motivoafastamentoesocial WHERE eso09_grupomotivoafastamentoesocial IN (
                        SELECT eso10_sequencial FROM esocial.grupomotivoafastamentoesocial WHERE eso10_db_cadattdinamico IN (
                                SELECT db118_sequencial FROM configuracoes.db_cadattdinamico WHERE db118_descricao = 'Cargo Eletivo')) AND eso09_sequencial = 36;

                DELETE FROM configuracoes.db_cadattdinamicoatributosopcoes
                        WHERE db18_cadattdinamicoatributos in (select db109_sequencial from configuracoes.db_cadattdinamicoatributos where db109_db_cadattdinamico IN
                        (select db118_sequencial from configuracoes.db_cadattdinamico where trim(db118_descricao) = 'Cessão/Requisição') and db109_nome = 'motivo_esocial')
                                and trim(db18_opcao) = '40';

                DELETE FROM esocial.motivoafastamentoesocial WHERE eso09_grupomotivoafastamentoesocial IN (
                        SELECT eso10_sequencial FROM esocial.grupomotivoafastamentoesocial WHERE eso10_db_cadattdinamico IN (
                                SELECT db118_sequencial FROM configuracoes.db_cadattdinamico WHERE db118_descricao = 'Cessão/Requisição')) AND eso09_sequencial = 40;

                DELETE FROM configuracoes.db_cadattdinamicoatributosopcoes
                WHERE db18_opcao = '35'
                AND db18_cadattdinamicoatributos IN (SELECT db39_camponovo FROM view_dados_clonado_licenca);

                DELETE FROM configuracoes.db_cadattdinamicoatributosopcoes
                WHERE db18_opcao = '36'
                AND db18_cadattdinamicoatributos IN (SELECT db39_camponovo FROM view_dados_clonado_eletivo);

                DELETE FROM configuracoes.db_cadattdinamicoatributosopcoes
                WHERE db18_opcao IN ('40')
                AND db18_cadattdinamicoatributos IN (SELECT db39_camponovo FROM view_dados_clonado_cessao);


SQL;
            DB::connection()->getPdo()->exec($sql);
     }



     private function downDeletandoView(array $nomesViews)
     {
         foreach ($nomesViews as $descricao) {
             $sql = "DROP MATERIALIZED VIEW {$descricao}";
             DB::connection()->getPdo()->exec($sql);
         }
     }
}
