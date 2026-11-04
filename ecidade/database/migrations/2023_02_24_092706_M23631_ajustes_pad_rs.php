<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23631AjustesPadRs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $prefeitura = \App\Domain\Configuracao\Instituicao\Model\DBConfig::whereRaw('prefeitura is true')->first();
        if ($prefeitura->uf === 'RS') {
            $this->ajustesBrubAnt();
            $this->ajustesCP501();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

    private function ajustesCP501()
    {
        DB::connection()->getPdo()->exec(<<<SQL
update empempenho set e60_concarpeculiar = '000' WHERE e60_concarpeculiar = '501' AND e60_anousu = 2023;
SQL
        );
    }

    private function ajustesBrubAnt()
    {
        // busca do primeiro lançamento de cada empenho o complemento e recurso que foi feito o empenho e depois
        // altera os lançamentos e ajusta a origem do empenho.
        DB::connection()->getPdo()->exec(<<<SQL
drop table if exists w_correto;
create temp table w_correto as
   select *
     from conlancamemp
   inner join conlancamdoc on c71_codlan = c75_codlan
   inner join conlancamcomplementorecurso on c75_codlan = o201_codlan
   where c71_coddoc in (1, 410)
   and extract(year from c71_data) :: int in (2021, 2022);

update conlancamcomplementorecurso
  set o201_complemento = w_correto.o201_complemento,
      o201_orctiporec = w_correto.o201_orctiporec
  from w_correto
  join conlancamemp on conlancamemp.c75_numemp = w_correto.c75_numemp
 where conlancamcomplementorecurso.o201_codlan = conlancamemp.c75_codlan
   and conlancamcomplementorecurso.o201_complemento != w_correto.o201_complemento;

update orcamento.origemcomplementorecurso
 set o206_recurso = w_correto.o201_orctiporec,
     o206_complementorecurso = w_correto.o201_complemento
  from w_correto
 where o206_origem = 1
   and o206_numero = w_correto.c75_numemp
   and o206_complementorecurso != w_correto.o201_complemento;

update orcamento.origemcomplementorecurso
 set o206_recurso = w_correto.o201_orctiporec,
     o206_complementorecurso = w_correto.o201_complemento
  from w_correto
  join empresto on e91_numemp = c75_numemp and e91_anousu = 2023
 where o206_origem = 10
   and o206_numero = w_correto.c75_numemp
   and o206_complementorecurso != w_correto.o201_complemento;
SQL
        );
    }
}
