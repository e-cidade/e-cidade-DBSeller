<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24018AdicionaGrauInstrucao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!DB::table('pessoal.rhinstrucao')->where('rh21_instru', 0)->exists()) {
            $sql = "insert into pessoal.rhinstrucao (rh21_instru, rh21_descr) values (0, 'NAO INFORMADO');";
            DB::connection()->getPdo()->exec($sql);
        }

        if (!DB::table('pessoal.rhinstrucao')->where('rh21_instru', 1)->exists()) {
            $sql = "insert into pessoal.rhinstrucao (rh21_instru, rh21_descr) values (1, 'ANALFABETO, INCLUSIVE, EMBORA INSTRUIDO');";
            DB::connection()->getPdo()->exec($sql);
        }

        if (!DB::table('pessoal.rhinstrucao')->where('rh21_instru', 2)->exists()) {
            $sql = "insert into pessoal.rhinstrucao (rh21_instru, rh21_descr) values (2, 'ATÉ O 5º ANO INCOMPLETO/ANTIGA 4ª SÉRIE');";
            DB::connection()->getPdo()->exec($sql);
        }

        if (!DB::table('pessoal.rhinstrucao')->where('rh21_instru', 3)->exists()) {
            $sql = "insert into pessoal.rhinstrucao (rh21_instru, rh21_descr) values (3, '5º ANO COMPLETO DO ENSINO FUNDAMENTAL');";
            DB::connection()->getPdo()->exec($sql);
        }

        if (!DB::table('pessoal.rhinstrucao')->where('rh21_instru', 4)->exists()) {
            $sql = "insert into pessoal.rhinstrucao (rh21_instru, rh21_descr) values (4, 'DO 6º AO 9º ANO DO ENSINO FUNDAMENTAL');";
            DB::connection()->getPdo()->exec($sql);
        }

        if (!DB::table('pessoal.rhinstrucao')->where('rh21_instru', 5)->exists()) {
            $sql = "insert into pessoal.rhinstrucao (rh21_instru, rh21_descr) values (5, 'ENSINO FUNDAMENTAL COMPLETO');";
            DB::connection()->getPdo()->exec($sql);
        }

        if (!DB::table('pessoal.rhinstrucao')->where('rh21_instru', 6)->exists()) {
            $sql = "insert into pessoal.rhinstrucao (rh21_instru, rh21_descr) values (6, 'ENSINO MÉDIO INCOMPLETO');";
            DB::connection()->getPdo()->exec($sql);
        }

        if (!DB::table('pessoal.rhinstrucao')->where('rh21_instru', 7)->exists()) {
            $sql = "insert into pessoal.rhinstrucao (rh21_instru, rh21_descr) values (7, 'ENSINO MÉDIO COMPLETO');";
            DB::connection()->getPdo()->exec($sql);
        }

        if (!DB::table('pessoal.rhinstrucao')->where('rh21_instru', 8)->exists()) {
            $sql = "insert into pessoal.rhinstrucao (rh21_instru, rh21_descr) values (8, 'EDUCAÇÃO SUPERIOR INCOMPLETA');";
            DB::connection()->getPdo()->exec($sql);
        }

        if (!DB::table('pessoal.rhinstrucao')->where('rh21_instru', 9)->exists()) {
            $sql = "insert into pessoal.rhinstrucao (rh21_instru, rh21_descr) values (9, 'EDUCAÇÃO SUPERIOR COMPLETA');";
            DB::connection()->getPdo()->exec($sql);
        }

        if (!DB::table('pessoal.rhinstrucao')->where('rh21_instru', 10)->exists()) {
            $sql = "insert into pessoal.rhinstrucao (rh21_instru, rh21_descr) values (10, 'MESTRADO COMPLETO');";
            DB::connection()->getPdo()->exec($sql);
        }

        if (!DB::table('pessoal.rhinstrucao')->where('rh21_instru', 11)->exists()) {
            $sql = "insert into pessoal.rhinstrucao (rh21_instru, rh21_descr) values (11, 'DOUTORADO COMPLETO');";
            DB::connection()->getPdo()->exec($sql);
        }

        if (!DB::table('pessoal.rhinstrucao')->where('rh21_instru', 12)->exists()) {
            $sql = "insert into pessoal.rhinstrucao (rh21_instru, rh21_descr) values (12, 'PÓS-GRADUAÇÃO COMPLETA');";
            DB::connection()->getPdo()->exec($sql);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        return true;
    }
}