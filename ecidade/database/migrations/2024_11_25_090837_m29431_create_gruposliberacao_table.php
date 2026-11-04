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

class M29431CreateGruposliberacaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laboratorio.gruposliberacao', function (Blueprint $table) {
            $table->increments('la71_sequencial');
            $table->string('la71_descricao');
            $table->integer('la71_setor')->nullable();
            $table->integer('la71_dbusuarios');

            $table->foreign('la71_setor')->references('la23_i_codigo')->on('laboratorio.lab_setor');
            $table->foreign('la71_dbusuarios')->references('id_usuario')->on('configuracoes.db_usuarios');
        });

        DB::unprepared(<<<SQL

      ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
      ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

COMMENT ON TABLE laboratorio.gruposliberacao IS '{
  "descricao": "Tabela para armazenar os grupos de exames a serem liberados.",
  "sigla": "la71",
  "dataincl": "2024-11-25",
  "rotulo": "Tabela de grupos de liberação.",
  "tipotabela": 0,
  "naolibclass": true,
  "naolibfunc": true,
  "naolibprog": true,
  "naolibform": true
}';

        SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'gruposliberacao');

COMMENT ON COLUMN laboratorio.gruposliberacao.la71_sequencial IS '{
  "descricao": "Identifica o grupo de liberação.",
  "rotulo": "Código de identificação do grupo de liberação.",
  "rotulorel": "Identificar grupo de liberação.",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

        SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'gruposliberacao');

COMMENT ON COLUMN laboratorio.gruposliberacao.la71_descricao IS '{
  "descricao": "Descreve o grupo de liberação.",
  "rotulo": "Descrição do grupo de liberação.",
  "rotulorel": "Descreve o grupo de liberação.",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 3,
  "tamanho": 10,
  "tipoobj": "text"
}';

      SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'gruposliberacao');

COMMENT ON COLUMN laboratorio.gruposliberacao.la71_setor IS '{
  "descricao": "Setor do laboratório utilizado no grupo.",
  "rotulo": "Setor do laboratório.",
  "rotulorel": "Setor do laboratório utilizado no grupo.",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

      SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'gruposliberacao');

COMMENT ON COLUMN laboratorio.gruposliberacao.la71_dbusuarios IS '{
  "descricao": "Indica o usuário dono do grupo.",
  "rotulo": "Usuário do grupo.",
  "rotulorel": "Indica o usuário dono do grupo.",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

      SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'gruposliberacao');

      SELECT configuracoes.fc_auditoria_cria_funcao('laboratorio.gruposliberacao');

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
        Schema::dropIfExists('laboratorio.gruposliberacao');
    }
}
