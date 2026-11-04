<?php

use Illuminate\Database\Migrations\Migration;

class M25649AlterFunctionFisVistoriasAtrasada extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ($this->checkSchema()) {
            $this->upFunction();
        }
    }

    private function upFunction()
    {
        DB::unprepared(<<<SQL
CREATE OR REPLACE FUNCTION fiscalizacao.fc_fis_verifica_vistoria_atrasada(integer, integer, integer)
 RETURNS boolean
AS $$
declare

  iCodSani alias for $1;
  iCodVist alias for $2;
  iAnousu  alias for $3;

  iCodigoVistoria    integer;
  iAnoUltimaVistoria integer;
  lReturn            boolean default false;

begin

  select y74_codvist
    into iCodigoVistoria
    from fiscalizacao.fis_vistsanitario
         inner join fiscalizacao.fis_vistorias on fis_vistorias.y70_codvist = fis_vistsanitario.y74_codvist
   where y74_codsani = iCodSani
     and y74_codvist != iCodVist
   order by y70_data desc
   limit 1;

  if iCodigoVistoria is not null then

    select extract(year from y70_data)
      into iAnoUltimaVistoria
      from fis_vistorias
     where y70_codvist = iCodigoVistoria;

    if iAnoUltimaVistoria < (iAnousu - 1) then
      lReturn := true;
    end if;

  end if;

    return lReturn;

end;
$$ language 'plpgsql';
SQL
        );
    }

    private function checkSchema()
    {
        $schema = DB::select("
            SELECT schema_name FROM information_schema.schemata
            WHERE schema_name = 'fiscalizacao'
        ");

        if (count($schema) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if ($this->checkSchema()) {
            DB::statement("drop function if exists fiscalizacao.fc_fis_verifica_vistoria_atrasada(integer, integer, integer)");
        }
    }
}
