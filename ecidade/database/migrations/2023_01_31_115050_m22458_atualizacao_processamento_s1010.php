<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class M22458AtualizacaoProcessamentoS1010 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
        $this->upEstrutura();
        $this->upAcertoEmBase();
        $this->upDesativaMenu();
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDicionario();
        $this->downEstruturaComplementar();
        $this->downAcertoEmBase();
        $this->downEstrutura();
        $this->downDesativaMenu();
    }
    
    /**
     *
     */
    private function upDicionario()
    {
        $sql = <<<SQL
            insert into configuracoes.db_syscampo values(1014722,'eso26_codinccp','varchar(3)','Incidência de Contrib. Previdenciária','', 'Incidência de Contrib. Previdenciária',3,'f','t','f',0,'text','Incidência de Contrib. Previdenciária');
            insert into configuracoes.db_syscampo values(1014723,'eso26_codincirrf','varchar(5)','Incidência de IRRF','', 'Incidência de IRRF',5,'f','t','f',0,'text','Incidência de IRRF');
            insert into configuracoes.db_syscampo values(1014724,'eso26_codincfgts','varchar(3)','Incidência de FGTS','', 'Incidência de FGTS',3,'f','t','f',0,'text','Incidência de FGTS');
            insert into configuracoes.db_syscampo values(1014727,'eso26_codinccprp','varchar(3)','Código de incidência da rubrica para as contribuições do Regime Próprio de Previdência Social - RPPS/regime militar.','', 'Incidência rubrica RPPS/regime militar',3,'t','t','f',0,'text','Incidência rubrica RPPS/regime militar');
            insert into configuracoes.db_syscampo values(1014729,'eso26_tetoremun','char(1)','Informar se a rubrica compõe o teto remuneratório específico (art. 37, XI, da CF/1988).','', 'Rubrica teto remuneratório específico',1,'t','t','f',0,'text','Rubrica teto remuneratório específico');

            delete from db_sysarqcamp where codarq = 1010325;
            insert into db_sysarqcamp values(1010325,1009986,1,1000770);
            insert into db_sysarqcamp values(1010325,1009987,2,0);
            insert into db_sysarqcamp values(1010325,1009988,3,0);
            insert into db_sysarqcamp values(1010325,1009994,4,0);
            insert into db_sysarqcamp values(1010325,1010003,5,0); 
            insert into db_sysarqcamp values(1010325,1010004,6,0); 
            insert into db_sysarqcamp values(1010325,1014222,7,0); 
            insert into db_sysarqcamp values(1010325,1014722,8,0);
            insert into db_sysarqcamp values(1010325,1014723,9,0);
            insert into db_sysarqcamp values(1010325,1014724,10,0);
            insert into db_sysarqcamp values(1010325,1014727,11,0);
            insert into db_sysarqcamp values(1010325,1014729,12,0);
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
    
    /**
     *
     */
    private function upEstrutura()
    {
        $sql = <<<SQL

        alter table esocial.esocialrubricas 
            add column eso26_codinccp varchar(3),
            add column eso26_codincirrf varchar(5),
            add column eso26_codincfgts varchar(3),
            add column eso26_codinccprp varchar(3),
            add column eso26_tetoremun char(1);
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
    
    /**
     */
    private function upAcertoEmBase()
    {
        $sql = <<<SQL

            UPDATE 
                esocial.esocialrubricas
            SET 
                eso26_codinccp = 
            CASE  
                WHEN eso26_avaliacaoperguntaopcaocodinccp = '3003779' THEN '00'
                WHEN eso26_avaliacaoperguntaopcaocodinccp = '3003780' THEN '01'
                WHEN eso26_avaliacaoperguntaopcaocodinccp = '3003781' THEN '11'
                WHEN eso26_avaliacaoperguntaopcaocodinccp = '3003782' THEN '12'
                WHEN eso26_avaliacaoperguntaopcaocodinccp = '3003783' THEN '13'
                WHEN eso26_avaliacaoperguntaopcaocodinccp = '3003784' THEN '14'
                WHEN eso26_avaliacaoperguntaopcaocodinccp = '3003785' THEN '15'
                WHEN eso26_avaliacaoperguntaopcaocodinccp = '3003786' THEN '16'
                WHEN eso26_avaliacaoperguntaopcaocodinccp = '3003787' THEN '21'
                WHEN eso26_avaliacaoperguntaopcaocodinccp = '3003788' THEN '22'
                WHEN eso26_avaliacaoperguntaopcaocodinccp = '3003791' THEN '25'
                WHEN eso26_avaliacaoperguntaopcaocodinccp = '3003792' THEN '26'
                WHEN eso26_avaliacaoperguntaopcaocodinccp = '3003793' THEN '31'
                WHEN eso26_avaliacaoperguntaopcaocodinccp = '3003794' THEN '32'
                WHEN eso26_avaliacaoperguntaopcaocodinccp = '3003795' THEN '34'
                WHEN eso26_avaliacaoperguntaopcaocodinccp = '3003796' THEN '35'
                WHEN eso26_avaliacaoperguntaopcaocodinccp = '3003797' THEN '51'
                WHEN eso26_avaliacaoperguntaopcaocodinccp = '3003799' THEN '91'
                WHEN eso26_avaliacaoperguntaopcaocodinccp = '3003800' THEN '92'
                WHEN eso26_avaliacaoperguntaopcaocodinccp = '3003801' THEN '93'
                WHEN eso26_avaliacaoperguntaopcaocodinccp = '3003802' THEN '94'
                WHEN eso26_avaliacaoperguntaopcaocodinccp = '3003803' THEN '95'
                WHEN eso26_avaliacaoperguntaopcaocodinccp = '3003804' THEN '96'
                WHEN eso26_avaliacaoperguntaopcaocodinccp = '3003805' THEN '97'
                WHEN eso26_avaliacaoperguntaopcaocodinccp = '3003806' THEN '98'
                ELSE eso26_codinccp END,
                
                    eso26_codincirrf = 
                CASE  
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001359' THEN '9047'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001358' THEN '9046'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001341' THEN '48'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001373' THEN '9083'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001372' THEN '9082'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001371' THEN '9067'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001370' THEN '9066'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001369' THEN '9065'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001368' THEN '9064'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001367' THEN '9063'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001366' THEN '9062'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001365' THEN '9061'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001364' THEN '9054'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001363' THEN '9053'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001362' THEN '9052'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001361' THEN '9051'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001360' THEN '9048'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001357' THEN '9043'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001356' THEN '9042'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001355' THEN '9041'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001354' THEN '9834'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001353' THEN '9833'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001352' THEN '9832'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001351' THEN '9831'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001350' THEN '9034'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001349' THEN '9033'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001348' THEN '9032'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001347' THEN '9031'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001346' THEN '9014'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001345' THEN '9013'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001344' THEN '9012'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001343' THEN '9011'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001342' THEN '9'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001340' THEN '65'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001339' THEN '66'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001338' THEN '67'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001337' THEN '700'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '4001336' THEN '701'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003844' THEN '11'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003843' THEN '12'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003842' THEN '13'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003841' THEN '14'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003839' THEN '31'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003838' THEN '32'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003837' THEN '33'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003836' THEN '34'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003834' THEN '41'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003833' THEN '42'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003832' THEN '43'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003830' THEN '46'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003829' THEN '47'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003828' THEN '51'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003827' THEN '52'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003826' THEN '53'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003825' THEN '54'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003823' THEN '61'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003822' THEN '62'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003821' THEN '63'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003820' THEN '64'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003819' THEN '70'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003818' THEN '71'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003817' THEN '72'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003816' THEN '73'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003815' THEN '74'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003814' THEN '75'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003813' THEN '76'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003812' THEN '77'
                    WHEN eso26_avaliacaoperguntaopcaocodincirrf = '3003810' THEN '79'
                    ELSE eso26_codincirrf END,

                    eso26_codincfgts = 
                CASE
                    WHEN eso26_avaliacaoperguntaopcaocodincfgts = '3003853' THEN '93'
                    WHEN eso26_avaliacaoperguntaopcaocodincfgts = '3003852' THEN '92'
                    WHEN eso26_avaliacaoperguntaopcaocodincfgts = '3003851' THEN '00'
                    WHEN eso26_avaliacaoperguntaopcaocodincfgts = '3003850' THEN '11'
                    WHEN eso26_avaliacaoperguntaopcaocodincfgts = '3003849' THEN '12'
                    WHEN eso26_avaliacaoperguntaopcaocodincfgts = '3003848' THEN '21'
                    WHEN eso26_avaliacaoperguntaopcaocodincfgts = '3003847' THEN '91'
                    ELSE eso26_codincfgts END,
                    
                    eso26_codinccprp =
                CASE
                    WHEN eso26_avaliacaoperguntaopcaocodinccprp = '4001498' THEN '92'
                    WHEN eso26_avaliacaoperguntaopcaocodinccprp = '4001379' THEN '91'
                    WHEN eso26_avaliacaoperguntaopcaocodinccprp = '4001378' THEN '32'
                    WHEN eso26_avaliacaoperguntaopcaocodinccprp = '4001377' THEN '31'
                    WHEN eso26_avaliacaoperguntaopcaocodinccprp = '4001376' THEN '12'
                    WHEN eso26_avaliacaoperguntaopcaocodinccprp = '4001375' THEN '11'
                    WHEN eso26_avaliacaoperguntaopcaocodinccprp = '4001374' THEN '00'
                    ELSE eso26_codinccprp END,

                    eso26_tetoremun =
                CASE
                    WHEN eso26_avaliacaoperguntaopcaocodtetoremun = '4001381' THEN 'N'
                    WHEN eso26_avaliacaoperguntaopcaocodtetoremun = '4001380' THEN 'S'
                    ELSE '' END;

                alter table esocial.esocialrubricas
                    drop column eso26_avaliacaoperguntaopcaocodinccp,
                    drop column eso26_avaliacaoperguntaopcaocodincirrf,
                    drop column eso26_avaliacaoperguntaopcaocodincfgts,
                    drop column eso26_avaliacaoperguntaopcaocodinccprp,
                    drop column eso26_avaliacaoperguntaopcaocodtetoremun;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
    
    
    /**
     * Desativa o menu de carga de rúbricas e S1010-Tabela de Rubricas
     */
    private function upDesativaMenu()
    {
        $sql = <<<SQL
            update configuracoes.db_itensmenu set libcliente = 'false' where id_item in (10426, 10568);
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
    
    /**
     * Reverte as migrations anteriores
     */

    private function downEstruturaComplementar()
    {
        $sql = <<<SQL

        alter table esocial.esocialrubricas 
                        add column eso26_avaliacaoperguntaopcaocodinccp integer,
                        add column eso26_avaliacaoperguntaopcaocodincirrf integer,
                        add column eso26_avaliacaoperguntaopcaocodincfgts integer,
                        add column eso26_avaliacaoperguntaopcaocodinccprp integer,
                        add column eso26_avaliacaoperguntaopcaocodtetoremun varchar(20);
SQL;
        DB::connection()->getPdo()->exec($sql);

    }
    
    private function downDesativaMenu()
    {
        $sql = <<<SQL
            update configuracoes.db_itensmenu set libcliente = 'true' where id_item in (10426, 10568);
SQL;
    DB::connection()->getPdo()->exec($sql);
    }
    
    private function downDicionario()
    {
        $sql = <<<SQL
            delete from db_sysarqcamp where codarq = 1010325;
            insert into db_sysarqcamp values(1010325, 1009986, 1, 1000770);
            insert into db_sysarqcamp values(1010325, 1009987, 2, 0);
            insert into db_sysarqcamp values(1010325, 1009988, 3, 0);
            insert into db_sysarqcamp values(1010325, 1009989, 4, 0);
            insert into db_sysarqcamp values(1010325, 1009991, 5, 0);
            insert into db_sysarqcamp values(1010325, 1009992, 6, 0);
            insert into db_sysarqcamp values(1010325, 1009994, 7, 0);
            insert into db_sysarqcamp values(1010325, 1010003, 8, 0);
            insert into db_sysarqcamp values(1010325, 1010004, 9, 0);
            insert into db_sysarqcamp values(1010325, 1013457, 10, 0);
            insert into db_sysarqcamp values(1010325, 1013454, 11, 0);
            insert into db_sysarqcamp values(1010325, 1014222, 12, 0);

            delete from configuracoes.db_syscampo where codcam = 1014722;
            delete from configuracoes.db_syscampo where codcam = 1014723;
            delete from configuracoes.db_syscampo where codcam = 1014724;
            delete from configuracoes.db_syscampo where codcam = 1014727;
            delete from configuracoes.db_syscampo where codcam = 1014729;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    private function downEstrutura()
    {
        $sql = <<<SQL
        alter table esocial.esocialrubricas
                    drop column eso26_codinccp,
                    drop column eso26_codincirrf,
                    drop column eso26_codincfgts,
                    drop column eso26_codinccprp,
                    drop column eso26_tetoremun;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
    
    private function downAcertoEmBase()
    {

    $sql = <<<SQL
        UPDATE 
            esocial.esocialrubricas
        SET 
            eso26_avaliacaoperguntaopcaocodinccp =
        CASE  
            WHEN eso26_codinccp = '00' THEN '3003779'
            WHEN eso26_codinccp = '01' THEN '3003780'
            WHEN eso26_codinccp = '11' THEN '3003781'
            WHEN eso26_codinccp = '12' THEN '3003782'
            WHEN eso26_codinccp = '13' THEN '3003783'
            WHEN eso26_codinccp = '14' THEN '3003784'
            WHEN eso26_codinccp = '15' THEN '3003785'
            WHEN eso26_codinccp = '16' THEN '3003786'
            WHEN eso26_codinccp = '21' THEN '3003787'
            WHEN eso26_codinccp = '22' THEN '3003788'
            WHEN eso26_codinccp = '25' THEN '3003791'
            WHEN eso26_codinccp = '26' THEN '3003792'
            WHEN eso26_codinccp = '31' THEN '3003793'
            WHEN eso26_codinccp = '32' THEN '3003794'
            WHEN eso26_codinccp = '34' THEN '3003795'
            WHEN eso26_codinccp = '35' THEN '3003796'
            WHEN eso26_codinccp = '51' THEN '3003797'
            WHEN eso26_codinccp = '91' THEN '3003799'
            WHEN eso26_codinccp = '92' THEN '3003800'
            WHEN eso26_codinccp = '93' THEN '3003801'
            WHEN eso26_codinccp = '94' THEN '3003802'
            WHEN eso26_codinccp = '95' THEN '3003803'
            WHEN eso26_codinccp = '96' THEN '3003804'
            WHEN eso26_codinccp = '97' THEN '3003805'
            WHEN eso26_codinccp = '98' THEN '3003806'
            ELSE eso26_avaliacaoperguntaopcaocodinccp END,
        
            eso26_avaliacaoperguntaopcaocodincirrf = 
        CASE  
            WHEN eso26_codincirrf = '9047' THEN '4001359' 
            WHEN eso26_codincirrf = '9046' THEN '4001358' 
            WHEN eso26_codincirrf = '48'   THEN '4001341' 
            WHEN eso26_codincirrf = '9083' THEN '4001373' 
            WHEN eso26_codincirrf = '9082' THEN '4001372' 
            WHEN eso26_codincirrf = '9067' THEN '4001371' 
            WHEN eso26_codincirrf = '9066' THEN '4001370' 
            WHEN eso26_codincirrf = '9065' THEN '4001369' 
            WHEN eso26_codincirrf = '9064' THEN '4001368' 
            WHEN eso26_codincirrf = '9063' THEN '4001367' 
            WHEN eso26_codincirrf = '9062' THEN '4001366' 
            WHEN eso26_codincirrf = '9061' THEN '4001365' 
            WHEN eso26_codincirrf = '9054' THEN '4001364' 
            WHEN eso26_codincirrf = '9053' THEN '4001363' 
            WHEN eso26_codincirrf = '9052' THEN '4001362' 
            WHEN eso26_codincirrf = '9051' THEN '4001361' 
            WHEN eso26_codincirrf = '9048' THEN '4001360' 
            WHEN eso26_codincirrf = '9043' THEN '4001357' 
            WHEN eso26_codincirrf = '9042' THEN '4001356' 
            WHEN eso26_codincirrf = '9041' THEN '4001355' 
            WHEN eso26_codincirrf = '9834' THEN '4001354' 
            WHEN eso26_codincirrf = '9833' THEN '4001353' 
            WHEN eso26_codincirrf = '9832' THEN '4001352' 
            WHEN eso26_codincirrf = '9831' THEN '4001351' 
            WHEN eso26_codincirrf = '9034' THEN '4001350' 
            WHEN eso26_codincirrf = '9033' THEN '4001349' 
            WHEN eso26_codincirrf = '9032' THEN '4001348' 
            WHEN eso26_codincirrf = '9031' THEN '4001347' 
            WHEN eso26_codincirrf = '9014' THEN '4001346' 
            WHEN eso26_codincirrf = '9013' THEN '4001345' 
            WHEN eso26_codincirrf = '9012' THEN '4001344' 
            WHEN eso26_codincirrf = '9011' THEN '4001343' 
            WHEN eso26_codincirrf = '9'    THEN '4001342' 
            WHEN eso26_codincirrf = '65'   THEN '4001340' 
            WHEN eso26_codincirrf = '66'   THEN '4001339' 
            WHEN eso26_codincirrf = '67'   THEN '4001338' 
            WHEN eso26_codincirrf = '700'  THEN '4001337' 
            WHEN eso26_codincirrf = '701'  THEN '4001336' 
            WHEN eso26_codincirrf = '11'   THEN '3003844' 
            WHEN eso26_codincirrf = '12'   THEN '3003843' 
            WHEN eso26_codincirrf = '13'   THEN '3003842' 
            WHEN eso26_codincirrf = '14'   THEN '3003841' 
            WHEN eso26_codincirrf = '31'   THEN '3003839' 
            WHEN eso26_codincirrf = '32'   THEN '3003838' 
            WHEN eso26_codincirrf = '33'   THEN '3003837' 
            WHEN eso26_codincirrf = '34'   THEN '3003836' 
            WHEN eso26_codincirrf = '41'   THEN '3003834' 
            WHEN eso26_codincirrf = '42'   THEN '3003833' 
            WHEN eso26_codincirrf = '43'   THEN '3003832' 
            WHEN eso26_codincirrf = '46'   THEN '3003830' 
            WHEN eso26_codincirrf = '47'   THEN '3003829' 
            WHEN eso26_codincirrf = '51'   THEN '3003828' 
            WHEN eso26_codincirrf = '52'   THEN '3003827' 
            WHEN eso26_codincirrf = '53'   THEN '3003826' 
            WHEN eso26_codincirrf = '54'   THEN '3003825' 
            WHEN eso26_codincirrf = '61'   THEN '3003823' 
            WHEN eso26_codincirrf = '62'   THEN '3003822' 
            WHEN eso26_codincirrf = '63'   THEN '3003821' 
            WHEN eso26_codincirrf = '64'   THEN '3003820' 
            WHEN eso26_codincirrf = '70'   THEN '3003819' 
            WHEN eso26_codincirrf = '71'   THEN '3003818' 
            WHEN eso26_codincirrf = '72'   THEN '3003817' 
            WHEN eso26_codincirrf = '73'   THEN '3003816' 
            WHEN eso26_codincirrf = '74'   THEN '3003815' 
            WHEN eso26_codincirrf = '75'   THEN '3003814' 
            WHEN eso26_codincirrf = '76'   THEN '3003813' 
            WHEN eso26_codincirrf = '77'   THEN '3003812' 
            WHEN eso26_codincirrf = '79'   THEN '3003810' 
            ELSE eso26_avaliacaoperguntaopcaocodincirrf END,

            eso26_avaliacaoperguntaopcaocodincfgts = 
        CASE
            WHEN  eso26_codincfgts = '93' THEN '3003853' 
            WHEN  eso26_codincfgts = '92' THEN '3003852' 
            WHEN  eso26_codincfgts = '00' THEN '3003851' 
            WHEN  eso26_codincfgts = '11' THEN '3003850' 
            WHEN  eso26_codincfgts = '12' THEN '3003849' 
            WHEN  eso26_codincfgts = '21' THEN '3003848' 
            WHEN  eso26_codincfgts = '91' THEN '3003847' 
            ELSE eso26_avaliacaoperguntaopcaocodincfgts END,
            
            eso26_avaliacaoperguntaopcaocodinccprp =
        CASE
            WHEN eso26_codinccprp = '92' THEN '4001498'
            WHEN eso26_codinccprp = '91' THEN '4001379'
            WHEN eso26_codinccprp = '32' THEN '4001378'
            WHEN eso26_codinccprp = '31' THEN '4001377'
            WHEN eso26_codinccprp = '12' THEN '4001376'
            WHEN eso26_codinccprp = '11' THEN '4001375'
            WHEN eso26_codinccprp = '00' THEN '4001374'
            ELSE eso26_avaliacaoperguntaopcaocodinccprp END,

            eso26_avaliacaoperguntaopcaocodtetoremun =
        CASE
            WHEN eso26_tetoremun = 'N' THEN '4001381' 
            WHEN eso26_tetoremun = 'S' THEN '4001380' 
            ELSE '' END;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}