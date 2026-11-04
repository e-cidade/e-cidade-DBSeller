<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28384DuplicandoPermissoes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDuplicandoItemMenuRecebimentoInclusao();
        $this->upDuplicandoItemMenuArquivamentoInclusao();
        $this->upDuplicandoItemMenuDespachoInclusao();
        $this->upDuplicandoItemMenuTransferenciaInclusao();
    }

    public function upDuplicandoItemMenuRecebimentoInclusao()
    {
        $sql = "
            INSERT INTO db_permissao (id_usuario, id_item, permissaoativa, anousu, id_instit, id_modulo)
            SELECT id_usuario, 229286, permissaoativa, anousu, id_instit, 604
            FROM db_permissao
            WHERE id_item = 2182;
        ";
        DB::connection()->getPdo()->exec($sql);
    }

    public function upDuplicandoItemMenuArquivamentoInclusao()
    {
        $sql = "
            INSERT INTO db_permissao (id_usuario, id_item, permissaoativa, anousu, id_instit, id_modulo)
            SELECT id_usuario, 229287, permissaoativa, anousu, id_instit, 604
            FROM db_permissao
            WHERE id_item = 2184;
        ";
        DB::connection()->getPdo()->exec($sql);
    }

    public function upDuplicandoItemMenuDespachoInclusao()
    {
        $sql = "
            INSERT INTO db_permissao (id_usuario, id_item, permissaoativa, anousu, id_instit, id_modulo)
            SELECT id_usuario, 229288, permissaoativa, anousu, id_instit, 604
            FROM db_permissao
            WHERE id_item = 608572;
        ";
        DB::connection()->getPdo()->exec($sql);
    }

    public function upDuplicandoItemMenuTransferenciaInclusao()
    {
        $sql = "
            INSERT INTO db_permissao (id_usuario, id_item, permissaoativa, anousu, id_instit, id_modulo)
            SELECT id_usuario, 229289, permissaoativa, anousu, id_instit, 604
            FROM db_permissao
            WHERE id_item = 2175;
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
        $this->downDuplicandoItemMenuRecebimentoInclusao();
        $this->downDuplicandoItemMenuArquivamentoInclusao();
        $this->downDuplicandoItemMenuDespachoInclusao();
        $this->downDuplicandoItemMenuTransferenciaInclusao();
    }

    public function downDuplicandoItemMenuRecebimentoInclusao()
    {
        $sql = "
            DELETE FROM db_permissao
            WHERE id_item = 229286
            AND id_modulo = 604
            AND id_usuario IN (
                SELECT id_usuario
                FROM db_permissao
                WHERE id_item = 2182
            );
        ";
        DB::connection()->getPdo()->exec($sql);
    }

    public function downDuplicandoItemMenuArquivamentoInclusao()
    {
        $sql = "
            DELETE FROM db_permissao
            WHERE id_item = 229287
            AND id_modulo = 604
            AND id_usuario IN (
                SELECT id_usuario
                FROM db_permissao
                WHERE id_item = 2184
            );
        ";
        DB::connection()->getPdo()->exec($sql);
    }

    public function downDuplicandoItemMenuDespachoInclusao()
    {
        $sql = "
            DELETE FROM db_permissao
            WHERE id_item = 229288
            AND id_modulo = 604
            AND id_usuario IN (
                SELECT id_usuario
                FROM db_permissao
                WHERE id_item = 608572
            );
        ";
        DB::connection()->getPdo()->exec($sql);
    }

    public function downDuplicandoItemMenuTransferenciaInclusao()
    {
        $sql = "
            DELETE FROM db_permissao
            WHERE id_item = 229289
            AND id_modulo = 604
            AND id_usuario IN (
                SELECT id_usuario
                FROM db_permissao
                WHERE id_item = 2175
            );
        ";
        DB::connection()->getPdo()->exec($sql);
    }
}
