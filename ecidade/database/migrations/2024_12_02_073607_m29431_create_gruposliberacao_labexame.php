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

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29431CreateGruposliberacaoLabexame extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laboratorio.gruposliberacao_labexame', function (Blueprint $table) {
            $table->increments('la72_sequencial');
            $table->integer('la72_grupoliberacao')->nullable();
            $table->integer('la72_labexame');

            $table->foreign('la72_grupoliberacao')->references('la71_sequencial')->on('laboratorio.gruposliberacao');
            $table->foreign('la72_labexame')->references('la08_i_codigo')->on('laboratorio.lab_exame');
        });

        DB::unprepared(<<<SQL

      ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
      ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

COMMENT ON TABLE laboratorio.gruposliberacao_labexame IS '{
  "descricao": "Vínculo entre grupos de liberacao e exames.",
  "sigla": "la72",
  "dataincl": "2024-12-02",
  "rotulo": "Vínculo entre grupos de liberação e exames.",
  "tipotabela": 0,
  "naolibclass": true,
  "naolibfunc": true,
  "naolibprog": true,
  "naolibform": true
}';

        SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'gruposliberacao_labexame');

COMMENT ON COLUMN laboratorio.gruposliberacao_labexame.la72_sequencial IS '{
  "descricao": "Código identificador.",
  "rotulo": "Vínculo entre grupos de liberacao e exames",
  "rotulorel": "Identificar grupo de liberação.",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

        SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'gruposliberacao_labexame');

COMMENT ON COLUMN laboratorio.gruposliberacao_labexame.la72_grupoliberacao IS '{
  "descricao": "Grupo de liberação utilizado no vínculo.",
  "rotulo": "Grupo de liberação.",
  "rotulorel": "Grupo de liberação.",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

      SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'gruposliberacao_labexame');

COMMENT ON COLUMN laboratorio.gruposliberacao_labexame.la72_labexame IS '{
  "descricao": "Indica o exame no vínculo.",
  "rotulo": "Exame.",
  "rotulorel": "Exame.",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

      SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'gruposliberacao_labexame');

      SELECT configuracoes.fc_auditoria_cria_funcao('laboratorio.gruposliberacao_labexame');

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
        Schema::dropIfExists('laboratorio.gruposliberacao_labexame');
    }
}
