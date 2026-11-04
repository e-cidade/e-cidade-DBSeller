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
use Illuminate\Support\Facades\DB;

class M28903AtualizacaoPad2024 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "
            update configuracoes.db_layoutlinha set db51_tamlinha = 190 where db51_codigo = 1015;
            update configuracoes.db_layoutlinha set db51_tamlinha = 94 where db51_codigo = 10295;
            INSERT INTO configuracoes.db_layoutcampos VALUES (nextval('db_layoutcampos_db52_codigo_seq'), 1015, 'matriculafuncionario', 'MATRÍCULA DO FUNCIONÁRIO', 2, 177, '', 14, false, true, 'e', 'Codificação própria', 0);
            INSERT INTO configuracoes.db_layoutcampos VALUES (nextval('db_layoutcampos_db52_codigo_seq'), 10295, 'matriculafuncionario', 'MATRÍCULA DO FUNCIONÁRIO', 2, 81, '', 14, false, true, 'e', 'Codificação própria', 0);

        ";
        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = "
            update configuracoes.db_layoutlinha set db51_tamlinha = 176 where db51_codigo = 1015; 
            update configuracoes.db_layoutlinha set db51_tamlinha = 80 where db51_codigo = 10295; 
            delete from configuracoes.db_layoutcampos where db52_layoutlinha in (1015, 10295) and db52_nome = 'matriculafuncionario';
        ";
        DB::connection()->getPdo()->exec($sql);
    }
}
