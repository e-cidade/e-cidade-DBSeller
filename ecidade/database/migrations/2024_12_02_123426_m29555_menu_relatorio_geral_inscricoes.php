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

class M29555MenuRelatorioGeralInscricoes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->criaItemMenu();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropaItemMenu();
    }

    private function criaItemMenu()
    {

        DB::table('db_itensmenu')->insert([
            'id_item' => 229361,
            'descricao' => 'Geral Inscrições c/ Situação Alocados',
            'help' => 'Geral Inscrições c/ Situação Alocados',
            'funcao' => 'web/educacao/matricula-online/relatorios/geral-inscricoes',
            'itemativo' => '1',
            'manutencao' => '1',
            'desctec' => 'Rotina utilizada para emissão do relatório geral de inscrições da lista de espera com o a situação do candidato na lista.',
            'libcliente' => true,
            'api' => false
        ]);

        DB::table('db_menu')->insert([
            'id_item' => 228933,
            'id_item_filho' => 229361,
            'menusequencia' => 3,
            'modulo' => 228923
        ]);
    }

    private function dropaItemMenu()
    {
        DB::table('db_menu')
            ->where('id_item_filho', 229361)
            ->where('modulo', 228923)
            ->delete();

        DB::table('db_itensmenu')
            ->where('id_item', 229361)
            ->delete();
    }
}
