<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

use Illuminate\Database\Migrations\Migration;

class M29389AlteraComentariosTabelas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            <<<SQL

      ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
      ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

COMMENT ON TABLE laboratorio.gruposliberacao IS '{
  "descricao": "Tabela para armazenar os grupos de exames a serem liberados.",
  "sigla": "la71",
  "dataincl": "2024-11-25",
  "rotulo": "Tabela de grupos de liberaзгo.",
  "tipotabela": 0,
  "naolibclass": false,
  "naolibfunc": false,
  "naolibprog": false,
  "naolibform": false
}';

        COMMENT ON COLUMN laboratorio.gruposliberacao.la71_sequencial IS '{
  "descricao": "Identifica o grupo de liberaзгo.",
  "rotulo": "Cуdigo",
  "rotulorel": "Identificar grupo de liberaзгo.",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

        SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'gruposliberacao');

COMMENT ON COLUMN laboratorio.gruposliberacao.la71_descricao IS '{
  "descricao": "Descreve o grupo de liberaзгo.",
  "rotulo": "Descriзгo",
  "rotulorel": "Descreve o grupo de liberaзгo.",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 3,
  "tamanho": 10,
  "tipoobj": "text"
}';

      SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'gruposliberacao');

COMMENT ON COLUMN laboratorio.gruposliberacao.la71_setor IS '{
  "descricao": "Setor do laboratуrio utilizado no grupo.",
  "rotulo": "Setor",
  "rotulorel": "Setor do laboratуrio utilizado no grupo.",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

      SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'gruposliberacao');

COMMENT ON COLUMN laboratorio.gruposliberacao.la71_dbusuarios IS '{
  "descricao": "Indica o usuбrio dono do grupo.",
  "rotulo": "Usuбrio",
  "rotulorel": "Indica o usuбrio dono do grupo.",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

      SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'gruposliberacao');
      SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'gruposliberacao');

COMMENT ON TABLE laboratorio.gruposliberacao_labexame IS '{
  "descricao": "Vнnculo entre grupos de liberacao e exames.",
  "sigla": "la72",
  "dataincl": "2024-12-02",
  "rotulo": "Vнnculo entre grupos de liberaзгo e exames.",
  "tipotabela": 0,
  "naolibclass": false,
  "naolibfunc": false,
  "naolibprog": false,
  "naolibform": false
}';

        SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'gruposliberacao_labexame');

COMMENT ON COLUMN laboratorio.gruposliberacao_labexame.la72_sequencial IS '{
  "descricao": "Cуdigo identificador.",
  "rotulo": "Cуdigo",
  "rotulorel": "Identificar grupo de liberaзгo.",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

        SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'gruposliberacao_labexame');

COMMENT ON COLUMN laboratorio.gruposliberacao_labexame.la72_grupoliberacao IS '{
  "descricao": "Grupo de liberaзгo utilizado no vнnculo.",
  "rotulo": "Grupo",
  "rotulorel": "Grupo de liberaзгo.",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

      SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'gruposliberacao_labexame');

COMMENT ON COLUMN laboratorio.gruposliberacao_labexame.la72_labexame IS '{
  "descricao": "Indica o exame no vнnculo.",
  "rotulo": "Exame",
  "rotulorel": "Exame.",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

      SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'gruposliberacao_labexame');
      SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'gruposliberacao_labexame');

      ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
      ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;

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
        DB::unprepared(
            <<<SQL

      ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
      ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

COMMENT ON TABLE laboratorio.gruposliberacao IS '{
  "descricao": "Tabela para armazenar os grupos de exames a serem liberados.",
  "sigla": "la71",
  "dataincl": "2024-11-25",
  "rotulo": "Tabela de grupos de liberaзгo.",
  "tipotabela": 0,
  "naolibclass": true,
  "naolibfunc": true,
  "naolibprog": true,
  "naolibform": true
}';

        SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'gruposliberacao');

COMMENT ON COLUMN laboratorio.gruposliberacao.la71_sequencial IS '{
  "descricao": "Identifica o grupo de liberaзгo.",
  "rotulo": "Cуdigo de identificaзгo do grupo de liberaзгo.",
  "rotulorel": "Identificar grupo de liberaзгo.",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

        SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'gruposliberacao');

COMMENT ON COLUMN laboratorio.gruposliberacao.la71_descricao IS '{
  "descricao": "Descreve o grupo de liberaзгo.",
  "rotulo": "Descriзгo do grupo de liberaзгo.",
  "rotulorel": "Descreve o grupo de liberaзгo.",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 3,
  "tamanho": 10,
  "tipoobj": "text"
}';

      SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'gruposliberacao');

COMMENT ON COLUMN laboratorio.gruposliberacao.la71_setor IS '{
  "descricao": "Setor do laboratуrio utilizado no grupo.",
  "rotulo": "Setor do laboratуrio.",
  "rotulorel": "Setor do laboratуrio utilizado no grupo.",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

      SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'gruposliberacao');

COMMENT ON COLUMN laboratorio.gruposliberacao.la71_dbusuarios IS '{
  "descricao": "Indica o usuбrio dono do grupo.",
  "rotulo": "Usuбrio do grupo.",
  "rotulorel": "Indica o usuбrio dono do grupo.",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

      SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'gruposliberacao');

COMMENT ON COLUMN laboratorio.gruposliberacao_labexame.la72_sequencial IS '{
  "descricao": "Cуdigo identificador.",
  "rotulo": "Vнnculo entre grupos de liberacao e exames",
  "rotulorel": "Identificar grupo de liberaзгo.",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

        SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'gruposliberacao_labexame');

COMMENT ON COLUMN laboratorio.gruposliberacao_labexame.la72_grupoliberacao IS '{
  "descricao": "Grupo de liberaзгo utilizado no vнnculo.",
  "rotulo": "Grupo de liberaзгo.",
  "rotulorel": "Grupo de liberaзгo.",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

      SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'gruposliberacao_labexame');

COMMENT ON COLUMN laboratorio.gruposliberacao_labexame.la72_labexame IS '{
  "descricao": "Indica o exame no vнnculo.",
  "rotulo": "Exame.",
  "rotulorel": "Exame.",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

      SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'gruposliberacao_labexame');

ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;

SQL
        );
    }
}
