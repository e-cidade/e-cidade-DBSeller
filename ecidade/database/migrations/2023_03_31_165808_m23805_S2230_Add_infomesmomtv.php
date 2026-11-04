<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M23805S2230AddInfomesmomtv extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
CREATE temp Table dados_originais as 
SELECT db118_sequencial, db109_sequencial
from grupomotivoafastamentoesocial
    INNER JOIN motivoafastamentoesocial ON eso09_grupomotivoafastamentoesocial = eso10_sequencial
    INNER JOIN db_cadattdinamico ON  eso10_db_cadattdinamico = db118_sequencial
    INNER JOIN db_cadattdinamicoatributos ON db109_db_cadattdinamico = db118_sequencial
    WHERE eso09_sequencial IN(1,3)
    ;
CREATE temp Table dados_copia as 
SELECT * from mapeamentoatributosesocial where db39_campoorigem IN(SELECT db109_sequencial FROM dados_originais);

CREATE temp Table dados_inserir as 
SELECT * from db_cadattdinamicoatributos where db109_sequencial in(SELECT db39_camponovo from dados_copia);


     insert into configuracoes.db_cadattdinamicoatributos (
                (SELECT
                    nextval('db_cadattdinamicoatributos_db109_sequencial_seq') AS sequencial,
                    db118_sequencial,
                    NULL,
                    'Afastamento decorrente da mesma doença informada dentro 60 dias ?',
                    NULL,
                    6,
                    'infoMesmoMtv_esocial',
                    TRUE,
                    TRUE,
                    TRUE,
                   NULL
                FROM
                    dados_originais
                group by
                    db118_sequencial)
                ); 


     insert into configuracoes.db_cadattdinamicoatributos (
                (SELECT
                    nextval('db_cadattdinamicoatributos_db109_sequencial_seq') AS sequencial,
                    db109_db_cadattdinamico,
                    NULL,
                    'Afastamento decorrente da mesma doença informada dentro 60 dias ?',
                    NULL,
                    6,
                    'infoMesmoMtv_esocial',
                    TRUE,
                    TRUE,
                    TRUE,
                   NULL
                FROM
                    dados_inserir
                group by
                    db109_db_cadattdinamico)
                ); 


                insert into configuracoes.db_cadattdinamicoatributosopcoes (
                (SELECT
                    nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'),
                    db109_sequencial,
                    'N',
                    'NAO'
                from
                    db_cadattdinamicoatributos
                where
                    db109_nome = 'infoMesmoMtv_esocial'
                )
            );
            insert into configuracoes.db_cadattdinamicoatributosopcoes (
                (SELECT
                    nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'),
                    db109_sequencial,
                    'S',
                    'SIM'
                    from
                    db_cadattdinamicoatributos
                where
                    db109_nome = 'infoMesmoMtv_esocial'
                )
            );
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
        $sql=<<<SQL
        DELETE FROM configuracoes.db_cadattdinamicoatributosopcoes WHERE db18_cadattdinamicoatributos 
        in (select db109_sequencial from configuracoes.db_cadattdinamicoatributos where db109_nome = 'infoMesmoMtv_esocial');
        delete from configuracoes.db_cadattdinamicoatributosvalor where db110_db_cadattdinamicoatributos 
        in(select db109_sequencial from configuracoes.db_cadattdinamicoatributos where db109_nome = 'infoMesmoMtv_esocial');
        delete from configuracoes.db_cadattdinamicoatributos where db109_nome = 'infoMesmoMtv_esocial';
SQL;
        DB::connection()->getPdo()->exec($sql);     
    }
}
