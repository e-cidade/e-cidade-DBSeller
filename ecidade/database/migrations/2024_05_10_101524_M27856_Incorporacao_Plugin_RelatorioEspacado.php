<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27856IncorporacaoPluginRelatorioEspacado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        $this->upDicionario();
        $this->upEstrutura();
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDicionario();
        $this->downEstrutura();
    }

    public function upDicionario(){
DB::unprepared(<<<SQL
insert into db_syscampo values(1015663,'la49_espacolinhas','float4','Espaço entre linhas','0', 'Espaço entre linhas',10,'t','f','f',4,'text','Espaço entre linhas');
update db_syscampo set nomecam = 'la49_espacolinhas', conteudo = 'float4', descricao = ' Permite aumentar a altura da linha entre os atributos no laudo.', valorinicial = '1.5', rotulo = 'Espaço entre linhas', nulo = 't', tamanho = 10, maiusculo = 'f', autocompl = 'f', aceitatipo = 4, tipoobj = 'text', rotulorel = 'Espaço entre linhas' where codcam = 1015663;
insert into db_syscampo values(1015664,'la49_tamanhofonte','float4','Tamanho da Fonte','7', 'Tamanho da Fonte',10,'t','f','f',4,'text','Tamanho da Fonte');
update db_syscampo set nomecam = 'la49_tamanhofonte', conteudo = 'float4', descricao = 'Altera o tamanho da fonte dos campos material, método e observações no laudo.', valorinicial = '10', rotulo = 'Tamanho da Fonte', nulo = 't', tamanho = 10, maiusculo = 'f', autocompl = 'f', aceitatipo = 4, tipoobj = 'text', rotulorel = 'Tamanho da Fonte' where codcam = 1015664;
insert into db_sysarqcamp values(2909,1015663,11,0);
insert into db_sysarqcamp values(2909,1015664,12,0);
SQL
        );      
    }                 
    

    public function downDicionario(){
DB::unprepared(<<<SQL
delete from db_sysarqcamp where codcam in (1015663,1015664);
delete from db_syscampo where codcam in (1015663,1015664);
SQL
        );         
    } 
    
    public function upEstrutura(){
DB::unprepared(<<<SQL
ALTER TABLE lab_parametros ADD COLUMN la49_espacolinhas double precision default 1.5;
ALTER TABLE lab_parametros ADD COLUMN la49_tamanhofonte double precision default 10;
SQL
        );      
    }   
    
    public function downEstrutura(){
DB::unprepared(<<<SQL
ALTER TABLE lab_parametros DROP COLUMN la49_espacolinhas;
ALTER TABLE lab_parametros DROP COLUMN la49_tamanhofonte;
SQL
        );        
    }          
}
