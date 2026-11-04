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

class M29008CreatePrioridadetipoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laboratorio.prioridadetipo', function (Blueprint $table) {
            $table->increments('la69_sequencial');
            $table->string('la69_descricao');
        });

        DB::unprepared(<<<SQL

      ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
      ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

COMMENT ON TABLE laboratorio.prioridadetipo IS '{
  "descricao": "Tabela para armazenar os diferentes tipos de prioridades de atendimento.",
  "sigla": "la69",
  "dataincl": "2024-10-18",
  "rotulo": "Tabela de prioridades.",
  "tipotabela": 0,
  "naolibclass": false,
  "naolibfunc": false,
  "naolibprog": false,
  "naolibform": false
}';

        SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'prioridadetipo');

COMMENT ON COLUMN laboratorio.prioridadetipo.la69_sequencial IS '{
  "descricao": "Identifica a prioridade.",
  "rotulo": "Código de identificação da prioridade.",
  "rotulorel": "Identificar prioridade.",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

        SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'prioridadetipo');

COMMENT ON COLUMN laboratorio.prioridadetipo.la69_descricao IS '{
  "descricao": "Descreve a prioridade.",
  "rotulo": "Descrição da prioridade.",
  "rotulorel": "Descreve a prioridade.",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 0,
  "tamanho": 10,
  "tipoobj": "text"
}';

      SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'prioridadetipo');

      SELECT configuracoes.fc_auditoria_cria_funcao('laboratorio.prioridadetipo');

ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;

insert into laboratorio.prioridadetipo (la69_descricao) values ('IDOSO'), ('GESTANTE');

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
        Schema::dropIfExists('laboratorio.prioridadetipo');
    }
}
