<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27568AdequacoesParaCenso2024 extends Migration
{
    private function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
update db_syscampo set conteudo = 'char(100)', tamanho = 100 where codcam = 1008206;
SQL
        );
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
update db_syscampo set conteudo = 'char(50)', tamanho = 50 where codcam = 1008206;
SQL
        );
    }

    private function upDadosCenso()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (24521, 'FACULDADES JURIS', 1, 4, 5208707, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (23984, 'FACULDADE DO AMAZONAS', 1, 4, 1302603, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26138, 'FACULDADE ALFAUNIPAC DE TEOFILO OTONI', 1, 4, 3168606, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26201, 'FACULDADE POLITECNICA DE CORUMBA', 1, 4, 5003207, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (24301, 'FACULDADE FUNORTE DE GOVERNADOR VALADARES', 1, 4, 3127701, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26656, 'FACULDADE FASIPE BRASILIA', 1, 4, 5300108, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (25123, 'FACULDADE VALE DO IGUACU CANOINHAS', 1, 4, 4203808, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26044, 'ESCOLA DE ENSINO SUPERIOR LEC', 1, 4, 3550308, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (15055, 'CENTRO UNIVERSITARIO UNIVERSO BELO HORIZONTE', 1, 4, 3106200, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (27103, 'UNIVERSIDADE DO DISTRITO FEDERAL', 1, 2, 5300108, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26342, 'FACULDADE GPI', 1, 4, 2211001, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (15053, 'CENTRO UNIVERSITARIO UNIVERSO GOIANIA', 1, 4, 5208707, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (25205, 'FACULDADE MARECHAL RONDON DE OSASCO', 1, 4, 3534401, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26719, 'FACULDADE OPEN', 1, 4, 3550308, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (24909, 'FACULDADE PITAGORAS UNIDADE CARUARU', 1, 4, 2604106, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26288, 'FACULDADE DO CENTRO DE MEDIADORES', 1, 4, 5300108, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26118, 'FACULDADE APRENDIZ', 1, 4, 3105608, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (25510, 'FACULDADES INTEGRADAS PLATON', 1, 4, 4205407, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (23129, 'INSTITUTO DE DESENVOLVIMENTO E APRENDIZAGEM  IDEA FORTALEZA', 1, 4, 2304400, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (24675, 'FACULDADE DE ENERGIAS RENOVAVEIS E TECNOLOGIAS INDUSTRIAIS', 1, 4, 2408102, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26377, 'FACULDADE FAVENORTE DE ESPINOSA', 1, 4, 3124302, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26543, 'FACULDADE DE CIENCIAS DA SAUDE SIRIOLIBANES', 1, 4, 3550308, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26085, 'FACULDADE APM', 1, 4, 5208707, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (25187, 'FACULDADE DE CIENCIAS E EMPREENDEDORISMO DE SIMOES FILHO', 1, 4, 2930709, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (23991, 'FACULDADE THEMIS', 1, 4, 3549805, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26202, 'FACULDADE LEONARDO DA VINCI DE CHAPECO', 1, 4, 4204202, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (24920, 'FACULDADE DE DIREITO EDUCACAO E GESTAO', 1, 4, 3550308, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26610, 'FACULDADE DE QUERENCIA', 1, 4, 5107065, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (25349, 'FACULDADE INTERNACIONAL DE SAO PAULO', 1, 4, 3550308, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26411, 'INSTITUTO DE ESTUDOS ESTRATEGICOS EM CIENCIAS SOCIAIS', 1, 4, 3550308, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (25364, 'FACULDADE INTERNACIONAL DE OURINHOS', 1, 4, 3534708, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26265, 'FACULDADE ITA EDUCACIONAL', 1, 4, 3550308, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (24286, 'FACULDADE URCI', 1, 4, 4106902, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26037, 'FACULDADE UCEFF DE FREDERICO WETSPHALEN', 1, 4, 4308508, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (25719, 'FACULDADE INPOS', 1, 4, 5208707, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26383, 'FACULDADE CDPI', 1, 4, 5208707, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (25714, 'FACULDADE FIZAP', 1, 4, 5300108, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26281, 'FACULDADE DE ENSINO SETE', 1, 4, 1505502, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26328, 'FACULDADE PETRA', 1, 4, 3136702, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (24437, 'FACULDADE MERITOS', 1, 4, 5103403, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26400, 'FACULDADE BATISTA LOGOS', 1, 4, 3550308, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (23841, 'FACULDADE ESAMC BRASILIA', 1, 4, 5300108, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (25764, 'FACULDADE LEONARDO DA VINCI DE JACOBINA', 1, 4, 2917508, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26581, 'FACULDADE MCPHERSON DE ARARAS', 1, 4, 3503307, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26529, 'ESCOLA DE COMUNICACAOMIDIA E INFORMACAO DE SAO PAULO DA FGV', 1, 4, 3550308, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26282, 'FACULDADE ELEVEN', 1, 4, 4106902, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (24390, 'FACULDADE DE TECNOLOGIA E CIENCIAS  FTC ALAGOINHAS', 1, 4, 2900702, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26376, 'FACULDADE SANTA JOANA', 1, 4, 3550308, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (25807, 'INSTITUTO DE POSGRADUACAO E ESTUDOS EM TECNOLOGIA DA SAUDE', 1, 4, 3304557, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (25545, 'FACULDADE CENTROOESTE', 1, 4, 5208707, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26559, 'FACULDADE IPC', 1, 4, 5208707, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26709, 'FACULDADE CALIFORNIA BRASIL', 1, 4, 3543402, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (24382, 'FACULDADE UNINASSAU CARPINA', 1, 4, 2604007, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (25890, 'FACULDADE PEDREIRENSE DE EDUCACAO IPEDE', 1, 4, 2108207, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (27029, 'FACULDADE SAO LEOPOLDO MANDIC DE LIMEIRA', 1, 4, 3526902, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26702, 'FACULDADE CATOLICA DO AMAZONAS', 1, 4, 1302603, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26166, 'FACULDADE FASIPE DE LUCAS DO RIO VERDE', 1, 4, 5105259, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (24281, 'FACULDADE DE ENSINO E FORMACAO DA BAHIA', 1, 4, 2914000, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (25974, 'FACULDADE SALEM', 1, 4, 4127106, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26627, 'FACULDADE BAIANA DE CIENCIAS DA SAUDE DR. OSWALDO FORTINI', 1, 4, 2919553, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (25451, 'FACULDADE PREPARA', 1, 4, 3549805, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26038, 'FACULDADE UCEFF DE SAO MIGUEL DO OESTE', 1, 4, 4217204, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (24060, 'FACULDADE FASIPE NORTE', 1, 4, 5107925, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (28986, 'FACULDADE DE TECNOLOGIA DE BARRETOS', 1, 2, 3505500, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26541, 'INSTITUTO DE MATEMATICA PURA E APLICADA', 1, 4, 3304557, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (24981, 'FACULDADE DE TECNOLOGIA SENAC SAO JOSE DOS PINHAIS', 1, 4, 4125506, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (24259, 'FACULDADE UNIFICADA DE ENSINO SUPERIOR', 1, 4, 5103403, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (25439, 'FACULDADE REGIONAL DO VALLE', 1, 4, 2901007, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26583, 'FACULDADE COMUNIDADE DAS NACOES', 1, 4, 5300108, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (25525, 'FACULDADE SANTA BARBARA', 1, 4, 2700300, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (29118, 'UNIVERSIDADE FEDERAL DO NORTE DO TOCANTINS', 1, 1, 1702109, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26858, 'UNIMT FACULDADES INTEGRADAS', 1, 4, 5100201, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (23952, 'FACULDADE TEUTONIA', 1, 4, 4321451, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (26341, 'FACULDADE DA FRONTEIRA OESTE', 1, 4, 5003702, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (28985, 'FACULDADE DE TECNOLOGIA DE REGISTRO', 1, 2, 3542602, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (22260, 'FACULDADE DE ITABERAI  FACMAIS', 1, 4, 5210406, 'ATIVA');
insert into censoinstsuperior (ed257_i_codigo,ed257_c_nome,ed257_i_dependencia,ed257_i_tipo,ed257_i_censomunic,ed257_c_situacao) values (21555, 'FACULDADE SION', 1, 4, 1400100, 'ATIVA');
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO VALE DO RIO VERDE' where ed257_i_codigo = 27;
UPDATE censoinstsuperior SET ed257_c_nome = 'UNIVERSIDADE JOSE DO ROSARIO VELLANO' where ed257_i_codigo = 30;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE TECNOLOGIA DE AMERICANA' where ed257_i_codigo = 35;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE TECNOLOGIA DO BRASIL' where ed257_i_codigo = 131;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE FILOSOFIA CIENCIAS E LETRAS SOUZA MARQUES' where ed257_i_codigo = 192;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO ITALOBRASILEIRO' where ed257_i_codigo = 206;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE SAO BERNARDO DO CAMPO  FASB' where ed257_i_codigo = 231;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE JOAO PESSOA (SANTA EMILIA DO RODAT)' where ed257_i_codigo = 339;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO PAULISTANO' where ed257_i_codigo = 360;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE PAULISTA DE SERVICO SOCIAL' where ed257_i_codigo = 362;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE DIREITO MILTON CAMPOS' where ed257_i_codigo = 638;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE IBRA DOS VALES' where ed257_i_codigo = 778;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE AVEC DE VILHENA' where ed257_i_codigo = 797;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNIRIO' where ed257_i_codigo = 900;
UPDATE censoinstsuperior SET ed257_c_nome = 'INSTITUTO SUPERIOR E CENTRO EDUCACIONAL LUTERANO  BOM JESUS  IELUSC' where ed257_i_codigo = 1014;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE LOURENCO FILHO' where ed257_i_codigo = 1059;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE CIENCIAS HUMANAS E SOCIAIS DE IGARASSU' where ed257_i_codigo = 1136;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE ADMINISTRACAO CIENCIAS EDUCACAO E LETRAS' where ed257_i_codigo = 1257;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE CIENCIAS SOCIAIS APLICADAS DE MINAS GERAIS' where ed257_i_codigo = 1314;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO UNIFACOTTUR' where ed257_i_codigo = 1328;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE BATISTA DE MINAS GERAIS' where ed257_i_codigo = 1346;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO DO VALE DO JAGUARIBE' where ed257_i_codigo = 1350;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSIARIO UNIFTC' where ed257_i_codigo = 1364;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO DA INDUSTRIA' where ed257_i_codigo = 1400;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO SAO LUCAS' where ed257_i_codigo = 1414;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ORIGENES LESSA' where ed257_i_codigo = 1433;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE SALESIANA DO NORDESTE' where ed257_i_codigo = 1454;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSIARIO UNIFTC SALVADOR' where ed257_i_codigo = 1461;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE PITAGORAS INSTITUTO CAMILLO FILHO' where ed257_i_codigo = 1513;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO MAURICIO DE UNINASSAU DE PARNAIBA' where ed257_i_codigo = 1552;
UPDATE censoinstsuperior SET ed257_c_nome = 'INSTITUTO FEDERAL DE EDUCACAO CIENCIA E TECNOLOGIA SULRIOGRANDENSE' where ed257_i_codigo = 1578;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE TECNOLOGIA E CIENCIAS DE ITABUNA' where ed257_i_codigo = 1642;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE TECNOLOGIA E CIENCIAS DE JEQUIE' where ed257_i_codigo = 1645;
UPDATE censoinstsuperior SET ed257_c_nome = 'INSTITUTO METROPOLITANO DE ENSINO SUPERIOR' where ed257_i_codigo = 1669;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE CENTRAL DE CRISTALINA' where ed257_i_codigo = 1696;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO DO SUL GOIANO' where ed257_i_codigo = 1703;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE PRESIDENTE PRUDENTE' where ed257_i_codigo = 1711;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNIBRASILIA SUL' where ed257_i_codigo = 1739;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO PADRE JOAO BAGOZZI' where ed257_i_codigo = 1759;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO LEONARDO DA VINCI' where ed257_i_codigo = 1777;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO UNIFAS' where ed257_i_codigo = 1883;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE SINOP' where ed257_i_codigo = 1934;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE CUIABA' where ed257_i_codigo = 1941;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE LEGALE' where ed257_i_codigo = 1946;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE JK  UNIDADE II  GAMA' where ed257_i_codigo = 2021;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE CIENCIAS MEDICAS DA PARAIBA' where ed257_i_codigo = 2082;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE CIENCIA E TECNOLOGIA' where ed257_i_codigo = 2117;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO DANTE' where ed257_i_codigo = 2175;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE MONTEIRO LOBATO' where ed257_i_codigo = 2198;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO U:VERSE' where ed257_i_codigo = 2343;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE MERIDIONAL' where ed257_i_codigo = 2383;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE CEARENSE' where ed257_i_codigo = 2410;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DO SUDESTE GOIANO' where ed257_i_codigo = 2532;
UPDATE censoinstsuperior SET ed257_c_nome = 'FIAMFAAM  CENTRO UNIVERSITARIO' where ed257_i_codigo = 2556;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADES INTEGRADAS DESEMBARGADOR SAVIO BRANDAO' where ed257_i_codigo = 2775;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE TECNOLOGIA DO NORDESTE' where ed257_i_codigo = 2783;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UBS' where ed257_i_codigo = 2793;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE IPORA' where ed257_i_codigo = 2796;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE MERIDIONAL RS' where ed257_i_codigo = 2821;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO DE TALENTOS HUMANOS' where ed257_i_codigo = 2935;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ESTACIO DO PANTANAL' where ed257_i_codigo = 2961;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ANHANGUERA DE SALVADOR' where ed257_i_codigo = 3034;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNIBRAS DA BAHIA' where ed257_i_codigo = 3365;
UPDATE censoinstsuperior SET ed257_c_nome = 'INSTITUTO SUPERIOR DE EDUCACAO IBITURUNA' where ed257_i_codigo = 3448;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE CATOLICA SANTA TERESINHA' where ed257_i_codigo = 3644;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE MONTES CLAROS' where ed257_i_codigo = 3657;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE PADRE DOURADO FORTALEZA' where ed257_i_codigo = 3680;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADES INTEGRADAS ADVENTISTAS DE MINAS GERAIS' where ed257_i_codigo = 3754;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO SENAC' where ed257_i_codigo = 3804;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE INTEGRACAO DO SERTAO' where ed257_i_codigo = 3881;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE ROSEIRA' where ed257_i_codigo = 4090;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO JOAQUIM NABUCO DE RECIFE' where ed257_i_codigo = 4153;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNINASSAU ALIANCA  REDENCAO' where ed257_i_codigo = 4197;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADES INTEGRADAS DO BRASIL UNIFUTURO' where ed257_i_codigo = 4201;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO DE RONDONIA' where ed257_i_codigo = 4255;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE INHUMAS  FACMAIS' where ed257_i_codigo = 4259;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE JK  UNIDADE I  GAMA' where ed257_i_codigo = 4416;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO TIRADENTES' where ed257_i_codigo = 4530;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE APOGEU' where ed257_i_codigo = 4588;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE IBRA DE TAUBATE' where ed257_i_codigo = 4873;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE INTERNACIONAL DO DELTA' where ed257_i_codigo = 4945;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE MEDICINA DE GARANHUNS' where ed257_i_codigo = 5580;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE TECNOLOGIA BANDEIRANTES' where ed257_i_codigo = 10323;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE VILLALOBOS DO CONELESTE PAULISTA' where ed257_i_codigo = 11376;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE CRISTA DA AMAZONIA' where ed257_i_codigo = 11593;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE SUDOESTE PAULISTA  TATUI  FSP' where ed257_i_codigo = 11752;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNIBRASILIA' where ed257_i_codigo = 11895;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACUMINAS FACULDADE' where ed257_i_codigo = 12189;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ACESITA' where ed257_i_codigo = 12718;
UPDATE censoinstsuperior SET ed257_c_nome = 'IPOG  INSTITUTO DE POSGRADUACAO & GRADUACAO' where ed257_i_codigo = 12916;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE ENSINO SUPERIOR' where ed257_i_codigo = 13034;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE TECNOLOGIA LOURENCO FILHO' where ed257_i_codigo = 13106;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE CIENCIAS EDUCACAO SAUDE PESQUISA E GESTAO' where ed257_i_codigo = 13631;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO MAURICIO DE NASSAU DE BELEM' where ed257_i_codigo = 13982;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE TECNOLOGIA DO COOPERATIVISMO' where ed257_i_codigo = 14181;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNICA DE TIMOTEO' where ed257_i_codigo = 14242;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE ENGENHARIA E INOVACAO TECNICO PROFISSI' where ed257_i_codigo = 14951;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE GESTAO E NEGOCIOS DE SALVADOR' where ed257_i_codigo = 14996;
UPDATE censoinstsuperior SET ed257_c_nome = 'SOBRESP  FACULDADE DE CIENCIAS DA SAUDE' where ed257_i_codigo = 15894;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE PESTALOZZI DE FRANCA' where ed257_i_codigo = 16502;
UPDATE censoinstsuperior SET ed257_c_nome = 'INSTITUTO TOCANTINENSE PRESIDENTE ANTONIO CARLOS' where ed257_i_codigo = 16728;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE TECNOLOGIA E NEGOCIOS DE CATALAO' where ed257_i_codigo = 17831;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE SOLIDARIA DO OESTE PAULISTA' where ed257_i_codigo = 18372;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE ESTUDOS SUPERIORES DO MARANHAO' where ed257_i_codigo = 18623;
UPDATE censoinstsuperior SET ed257_c_nome = 'ENSINO SUPERIOR ALBERT SABIN JK' where ed257_i_codigo = 18703;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE AGES DE LAGARTO' where ed257_i_codigo = 18735;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE ADMINISTRACAO E CIENCIAS ECONOMICAS LTDA' where ed257_i_codigo = 19563;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADES IMPACTOS BRASIL' where ed257_i_codigo = 19816;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE BRAVIUM' where ed257_i_codigo = 19878;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE SAPIENS' where ed257_i_codigo = 19943;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE AUTONOMA DO BRASIL  MACEIO' where ed257_i_codigo = 20494;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE PITAGORAS DE ALAGOINHAS' where ed257_i_codigo = 21280;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE FAEL DE CURITIBA' where ed257_i_codigo = 21366;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE MAURICIO DE NASSAU DE IMPETRATRIZ' where ed257_i_codigo = 21398;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE MERIDIONAL DE PORTO ALEGRE' where ed257_i_codigo = 21519;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE PAIVA ANDRADE' where ed257_i_codigo = 21811;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE SANTO AGOSTINHO DE ITABUNA' where ed257_i_codigo = 22088;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE SANTA CASA DE BELO HORIZONTE' where ed257_i_codigo = 22103;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE SANTO ANTONIO DE ITABUNA' where ed257_i_codigo = 22139;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNOPAR DE CIENCIAS JURIDICAS DE PETROLINA' where ed257_i_codigo = 22158;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE MAURICIO DE NASSAU SERRA TALHADA' where ed257_i_codigo = 22210;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DO AMAZONAS DE ENSINO PESQUISA E INOVACAO' where ed257_i_codigo = 22246;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE DIREITO ATAME' where ed257_i_codigo = 22249;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE SAO TOMAS DE AQUINO DE NOVA LIMA' where ed257_i_codigo = 22327;
UPDATE censoinstsuperior SET ed257_c_nome = 'FGW  FACULDADE DE GESTAO WOLI' where ed257_i_codigo = 22455;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE TIRADENTES DE JABOATAO DOS GUARARAPES' where ed257_i_codigo = 22514;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ALVORADA DE SAUDE' where ed257_i_codigo = 22605;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE SEB DE RIBEIRAO PRETO' where ed257_i_codigo = 22659;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE PITAGORAS UNOPAR DE QUIXERAMOBIM' where ed257_i_codigo = 22710;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE PITAGORAS UNOPAR DE JUAZEIRO DO NORTE' where ed257_i_codigo = 23100;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE CENTRAL DO RECIFE CENTRO' where ed257_i_codigo = 23191;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE CENTRO SAO PAULO' where ed257_i_codigo = 23409;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE IMEPAC DE ITUMBIARA' where ed257_i_codigo = 23454;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DESCOMPLICA' where ed257_i_codigo = 23820;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ITPAC SANTA INES' where ed257_i_codigo = 24550;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE TECNOLOGIA DE MATAO' where ed257_i_codigo = 24672;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE RESULTADOS' where ed257_i_codigo = 23866;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE SILVA NETO' where ed257_i_codigo = 21220;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE IMPACTOS  FACI' where ed257_i_codigo = 22650;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DOCTUM DE SETE LAGOAS' where ed257_i_codigo = 24400;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE MULTIPLA' where ed257_i_codigo = 19312;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE PITAGORAS DE  CARUARU' where ed257_i_codigo = 23897;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE SIGMA' where ed257_i_codigo = 25167;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNIAVAN JOINVILLE' where ed257_i_codigo = 24996;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE BRASILEIRA DIGITAL' where ed257_i_codigo = 25791;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE APARECIDA DE GOIANIA' where ed257_i_codigo = 25575;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ALVES LIMA' where ed257_i_codigo = 25133;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE FAMEP  UNIDADE PARAMBU  CE' where ed257_i_codigo = 25365;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE PITAGORAS DE  CAMACARI' where ed257_i_codigo = 23896;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ITPAC ABAETETUBA' where ed257_i_codigo = 25307;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADES INTEGRADAS ZONA DA MATA' where ed257_i_codigo = 24215;
UPDATE censoinstsuperior SET ed257_c_nome = 'ITPAC MANACAPURU' where ed257_i_codigo = 24551;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE EDUCA BRASIL NOEL DE MELLO' where ed257_i_codigo = 24703;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE BELA VISTA' where ed257_i_codigo = 25455;
UPDATE censoinstsuperior SET ed257_c_nome = 'ESCOLA DE COMUNICACAO MIDIA E INFORMACAO' where ed257_i_codigo = 25424;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADES FAMEP   UNIDADE URUCUI  PI' where ed257_i_codigo = 23948;
UPDATE censoinstsuperior SET ed257_c_nome = 'INSTITUTO DE DIREITO DO NORDESTE  IDN SAO LUIS' where ed257_i_codigo = 24213;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE BIOPARK II' where ed257_i_codigo = 25452;
UPDATE censoinstsuperior SET ed257_c_nome = 'ITPAC ITACOATIARA' where ed257_i_codigo = 24548;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE BEM TE VI LTDA' where ed257_i_codigo = 24689;
UPDATE censoinstsuperior SET ed257_c_nome = 'IPD INSTITUTO DE PROFISSIONALIZACAO DIGITAL' where ed257_i_codigo = 25716;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE SANTA INES' where ed257_i_codigo = 25469;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE PITAGORAS DE CACHOEIRO DE ITAPEMIRIM' where ed257_i_codigo = 23895;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE SOCIESC DE EDUCACAO DE ITAJAI' where ed257_i_codigo = 23200;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE BRASIL EMPREENDER' where ed257_i_codigo = 25182;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ITPAC BRAGANCA' where ed257_i_codigo = 25261;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNIDA DE SAO PAULO  EAD' where ed257_i_codigo = 25745;
UPDATE censoinstsuperior SET ed257_c_situacao = 'EXTINTA' where ed257_i_codigo in (622,764,837,877,1204,1254,1296,1457,1605,1607,1614,1783,1806,1852,1904,1926,2282,2319,2346,2456,2514,3039,3223,3437,3690,3743,3774,4179,4538,4725,4740,4756,5013,11748,11750,13657,13811,14132,14147,14155,14738,14878,15521,15833,16781,16914,16964,17269,17395,17657,17765,17803,18067,18153,18158,18673,18675,18958,19256,19277,19321,19406,19787,19823,20096,20406,20534,20561,20663,20685,20709,21261,21310,21411,21505,21514,21674,21808,21833,21857,21882,21891,21926,21977,22097,22128,22130,22142,22181,22185,22222,22310,22433,22738,22746,23179,23203,23725,22178);
UPDATE censoinstsuperior SET ed257_c_situacao = 'ATIVA' where ed257_i_codigo = 84;
INSERT INTO avaliacaoperguntaopcao (db104_sequencial,db104_avaliacaopergunta,db104_descricao,db104_aceitatexto,db104_identificador,db104_peso,db104_valorresposta,db104_identificadorcampo) VALUES (4001512,3000014,'Carro-pipa',false,'abastecimento-de-agua-carro-pipa',NULL,NULL,'abastecimento_de_agua_carro_pipa');
update avaliacaoperguntaopcao set db104_descricao = 'Área de vegetação ou Gramado', db104_identificador = 'area-vegetacao-ou-gramado', db104_identificadorcampo = 'area_vegetacao_ou_gramado' where db104_sequencial = 3000027;
INSERT INTO avaliacaoperguntaopcao (db104_sequencial,db104_avaliacaopergunta,db104_descricao,db104_aceitatexto,db104_identificador,db104_peso,db104_valorresposta,db104_identificadorcampo) VALUES (4001513,3000000,'Área de horta, plantio e/ou produção agrícola',false,'area-horta-plantio-e-ou-producao-agricola',NULL,NULL,'area_horta_plantio_e_ou_producao_agricola');
INSERT INTO avaliacaoperguntaopcao (db104_sequencial,db104_avaliacaopergunta,db104_descricao,db104_aceitatexto,db104_identificador,db104_peso,db104_valorresposta,db104_identificadorcampo) VALUES (4001514,4000236,'Sinalização/alarme luminoso',false,'sinalizacao-alarme-luminoso',NULL,NULL,'sinalizacao_alarme_luminoso');
INSERT INTO avaliacaoperguntaopcao (db104_sequencial,db104_avaliacaopergunta,db104_descricao,db104_aceitatexto,db104_identificador,db104_peso,db104_valorresposta,db104_identificadorcampo) VALUES (4001515,4000238,'Agrônomos(as), horticultores(as), técnicos ou monitores(as) responsáveis pela gestão da área de horta, plantio e/ou produção agrícola',true,'agronomos-tecnicos-responsaveis-producao-agricola',0,NULL,'agronomos_tecnicos_responsaveis_producao_agricola');
update avaliacaoperguntaopcao set db104_descricao = 'Auxiliar de serviços gerais, porteiro(a), zelador(a), faxineiro(a), jardineiro(a)' where db104_sequencial = 4001254;
INSERT INTO avaliacaoperguntaopcao (db104_sequencial,db104_avaliacaopergunta,db104_descricao,db104_aceitatexto,db104_identificador,db104_peso,db104_valorresposta,db104_identificadorcampo) values (4001516,4000238,'Revisor de texto Braille, assistente vidente (assistente de revisão do texto em Braille)',true,'revisor-de-texto-braille-assistente-vidente',0,NULL,'revisor_de_texto_braille_assistente_vidente');
INSERT INTO avaliacaoperguntaopcao (db104_sequencial,db104_avaliacaopergunta,db104_descricao,db104_aceitatexto,db104_identificador,db104_peso,db104_valorresposta,db104_identificadorcampo) values (4001517,4000229,'Equipamentos e instrumentos para atividades em área de horta, plantio e/ou produção agrícola',false,'equipamentos-atividades-horta-producao-agricola',0,NULL,'equipamentos_atividades_horta_producao_agricola');
INSERT INTO avaliacaoperguntaopcao (db104_sequencial,db104_avaliacaopergunta,db104_descricao,db104_aceitatexto,db104_identificador,db104_peso,db104_valorresposta,db104_identificadorcampo) values (4001518,4000229,'Materiais pedagógicos para a educação escolar quilombola',false,'materiais-pedagogicos-educacao-escolar-quilombola',0,NULL,'materiais_pedagogicos_educacao_escolar_quilombola');
INSERT INTO avaliacaoperguntaopcao (db104_sequencial,db104_avaliacaopergunta,db104_descricao,db104_aceitatexto,db104_identificador,db104_peso,db104_valorresposta,db104_identificadorcampo) values (4001519,4000229,'Materiais pedagógicos para a educação especial',false,'materiais-pedagogicos-para-a-educacao-especial',0,NULL,'materiais_pedagogicos_para_a_educacao_especial');
INSERT INTO avaliacaopergunta (db103_sequencial,db103_avaliacaotiporesposta,db103_avaliacaogrupopergunta,db103_descricao,db103_obrigatoria,db103_ativo,db103_ordem,db103_identificador,db103_tipo,db103_mascara,db103_dblayoutcampo,db103_perguntaidentificadora,db103_camposql,db103_identificadorcampo,db103_somenteleitura) values (4000372,1,3000013,'A escola desenvolve ações na área de educação ambiental',false,true,17,'a-escola-desenvolve-acoes-area-educacao-ambiental',1,'',0,false,'0','a_escola_desenvolve_acoes_area_educacao_ambiental',false);
INSERT INTO avaliacaoperguntaopcao (db104_sequencial,db104_avaliacaopergunta,db104_descricao,db104_aceitatexto,db104_identificador,db104_peso,db104_valorresposta,db104_identificadorcampo) VALUES (4001520,4000372,'Não',false,'nao14e1f715a3b3d0d2bade35c28a3b3516',0,'0','a-escola-desenvolve-acoes-area-educacao-ambiental-nao');
INSERT INTO avaliacaoperguntaopcao (db104_sequencial,db104_avaliacaopergunta,db104_descricao,db104_aceitatexto,db104_identificador,db104_peso,db104_valorresposta,db104_identificadorcampo) VALUES (4001526,4000372,'Sim',false,'sim14e1f715a3b3d0d2bade35c28a3b3516',0,'1','a-escola-desenvolve-acoes-area-educacao-ambiental-sim');
INSERT INTO avaliacaopergunta (db103_sequencial,db103_avaliacaotiporesposta,db103_avaliacaogrupopergunta,db103_descricao,db103_obrigatoria,db103_ativo,db103_ordem,db103_identificador,db103_tipo,db103_mascara,db103_dblayoutcampo,db103_perguntaidentificadora,db103_camposql,db103_identificadorcampo,db103_somenteleitura) values (4000374,3,3000013,'Informe de qual(quais) forma(s) a educação ambiental é desenvolvida na escola:',false,true,18,'informe-qual-forma-educacao-ambiental-desenvolvida',1,'',0,false,'0','informe_qual_forma_educacao_ambiental_desenvolvida',false);
INSERT INTO avaliacaoperguntaopcao (db104_sequencial,db104_avaliacaopergunta,db104_descricao,db104_aceitatexto,db104_identificador,db104_peso,db104_valorresposta,db104_identificadorcampo) VALUES (4001527,4000374,'Como conteúdo dos componentes/campos de experiências presentes no currículo',false,'como-conteudo-componentes-presentes-curriculo',0,'','como_conteudo_componentes_presentes_curriculo');
INSERT INTO avaliacaoperguntaopcao (db104_sequencial,db104_avaliacaopergunta,db104_descricao,db104_aceitatexto,db104_identificador,db104_peso,db104_valorresposta,db104_identificadorcampo) VALUES (4001528,4000374,'Como um componente curricular especial, específico, flexível ou eletivo',false,'como-componente-curricular-especial-especifico',0,'','como_componente_curricular_especial_especifico');
INSERT INTO avaliacaoperguntaopcao (db104_sequencial,db104_avaliacaopergunta,db104_descricao,db104_aceitatexto,db104_identificador,db104_peso,db104_valorresposta,db104_identificadorcampo) values (4001529,4000374,'Como um eixo estruturante do currículo',false,'como-um-eixo-estruturante-do-curriculo',0,'','como_um_eixo_estruturante_do_curriculo');
INSERT INTO avaliacaoperguntaopcao (db104_sequencial,db104_avaliacaopergunta,db104_descricao,db104_aceitatexto,db104_identificador,db104_peso,db104_valorresposta,db104_identificadorcampo) values (4001530 ,4000374,'Em eventos',false,'em-eventos',0,'','em_eventos');
INSERT INTO avaliacaoperguntaopcao (db104_sequencial,db104_avaliacaopergunta,db104_descricao,db104_aceitatexto,db104_identificador,db104_peso,db104_valorresposta,db104_identificadorcampo) values (4001531  ,4000374,'Em projetos transversais ou interdisciplinares',false,'em-projetos-transversais-ou-interdisciplinares',0,'','em_projetos_transversais_ou_interdisciplinares');
INSERT INTO avaliacaoperguntaopcao (db104_sequencial,db104_avaliacaopergunta,db104_descricao,db104_aceitatexto,db104_identificador,db104_peso,db104_valorresposta,db104_identificadorcampo) values (4001532 ,4000374,'Nenhuma das opções listadas',false,'nenhuma-das-opcoes-listadas',0,'','nenhuma_das_opcoes_listadas');
insert into cursoformacao (ed94_i_codigo, ed94_c_descr, ed94_i_codclasse, ed94_c_codigocenso, ed94_c_descrclasse, ed94_i_grauacademico, ed94_ativo) values (nextval('cursoformacao_ed94_i_codigo_seq'), 'Mediação - Tecnológico', 4, '0421M013', 'Negócios, administração e direito', 1, true);
update avaliacaopergunta set db103_descricao = 'A escola compartilha espaços para atividades de integração escola-comunidade', db103_identificador = 'escola-compartilha-espacos-integracao-escola-comun', db103_identificadorcampo = 'escola_compartilha_espacos_integracao_escola_comun' where db103_sequencial = 3000026;
SQL
        );
    }

    private function downDadosCenso()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from censoinstsuperior where ed257_i_codigo in (24521,23984,26138,26201,24301,26656,25123,26044,15055,27103,26342,15053,25205,26719,24909,26288,26118,25510,23129,24675,26377,26543,26085,25187,23991,26202,24920,26610,25349,26411,25364,26265,24286,26037,25719,26383,25714,26281,26328,24437,26400,23841,25764,26581,26529,26282,24390,26376,25807,25545,26559,26709,24382,25890,27029,26702,26166,24281,25974,26627,25451,26038,24060,28986,26541,24981,24259,25439,26583,25525,29118,26858,23952,26341,28985,22260,21555);
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO UNINCOR' where ed257_i_codigo = 27;
UPDATE censoinstsuperior SET ed257_c_nome = 'UNIVERSIDADE PROFESSOR EDSON ANTONIO VELANO' where ed257_i_codigo = 30;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE TECNOLOGIA DE AMERICANA "MINISTRO RALPH BIASI"' where ed257_i_codigo = 35;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE VERITAS COLLEGE' where ed257_i_codigo = 131;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE SOUZA MARQUES' where ed257_i_codigo = 192;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO CATOLICO ITALO BRASILEIRO' where ed257_i_codigo = 206;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE SAO BERNARDO DO CAMPO' where ed257_i_codigo = 231;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACUDADE UNINEVES' where ed257_i_codigo = 339;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO PAULISTANA' where ed257_i_codigo = 360;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE PRIMUM' where ed257_i_codigo = 362;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE MILTON CAMPOS' where ed257_i_codigo = 638;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE INTEGRADA DOS VALES' where ed257_i_codigo = 778;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE FAVOO COOP' where ed257_i_codigo = 797;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNIABRANGE' where ed257_i_codigo = 900;
UPDATE censoinstsuperior SET ed257_c_nome = 'INSTITUTO SUPERIOR E CENTRO EDUCACIONAL LUTERANO  BOM JESUS - IELUSC' where ed257_i_codigo = 1014;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE MULTIVERSA DE FORTALEZA' where ed257_i_codigo = 1059;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNIFACIG' where ed257_i_codigo = 1136;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE ADMINISTRACAO CIENCIAS, EDUCACAO E LETRAS' where ed257_i_codigo = 1257;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ABRANGES' where ed257_i_codigo = 1314;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO UNINOVO' where ed257_i_codigo = 1328;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE IPEMIG' where ed257_i_codigo = 1346;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO MULTIVERSA DO JAGUARIBE UNIJAGUARIBE' where ed257_i_codigo = 1350;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO DE EXCELENCIA DE VITORIA DA CONQUISTA' where ed257_i_codigo = 1364;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO SENAI PARANA' where ed257_i_codigo = 1400;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO SAO LUCAS PORTO VELHO' where ed257_i_codigo = 1414;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE VILAS BOAS' where ed257_i_codigo = 1433;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE ADMINISTRACAO SAUDE E TECNOLOGIA' where ed257_i_codigo = 1454;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO ZARNS  SALVADOR' where ed257_i_codigo = 1461;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE PITAGORAS ANHANGUERA DE TERESINA' where ed257_i_codigo = 1513;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO MAURICIO DE NASSAU DE PARNAIBA' where ed257_i_codigo = 1552;
UPDATE censoinstsuperior SET ed257_c_nome = 'INSTITUTO FEDERAL DE EDUCACAO CIENCIA E TECNOLOGIA SULRIO-GRANDENSE' where ed257_i_codigo = 1578;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE EXCELENCIA UNEX DE ITABUNA' where ed257_i_codigo = 1642;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE EXCELENCIA UNEX DE JEQUIE' where ed257_i_codigo = 1645;
UPDATE censoinstsuperior SET ed257_c_nome = 'AFYA FACULDADE DE CIENCIAS MEDICAS DE IPATINGA' where ed257_i_codigo = 1669;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE SOBRESP DE CRISTALINA' where ed257_i_codigo = 1696;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO UNIBRAS RIO VERDE' where ed257_i_codigo = 1703;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO DE PRESIDENTE PRUDENTE' where ed257_i_codigo = 1711;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNIBRAS GAMA' where ed257_i_codigo = 1739;
UPDATE censoinstsuperior SET ed257_c_nome = 'GRAN CENTRO UNIVERSITARIO' where ed257_i_codigo = 1759;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO UNIASSELVI DE GUARAMIRIM' where ed257_i_codigo = 1777;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO UNIME' where ed257_i_codigo = 1883;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE FASIPE' where ed257_i_codigo = 1934;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE CATOLICA DE CUIABA' where ed257_i_codigo = 1941;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE LUMINA' where ed257_i_codigo = 1946;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE JK  UNIDADE II - GAMA' where ed257_i_codigo = 2021;
UPDATE censoinstsuperior SET ed257_c_nome = 'AFYA FACULDADE DE CIENCIAS MEDICAS DA PARAIBA' where ed257_i_codigo = 2082;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE MULTIVERSA DE PALOTINA' where ed257_i_codigo = 2117;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO UNIASSELVI DE BLUMENAU' where ed257_i_codigo = 2175;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE BRASILEIRA DE INOVACAO' where ed257_i_codigo = 2198;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERITARIO UNIVOLTAIRE' where ed257_i_codigo = 2343;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ATITUS EDUCACAO PASSO FUNDO' where ed257_i_codigo = 2383;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO CEARENSE' where ed257_i_codigo = 2410;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE SOBRESP DE PIRES DO RIO' where ed257_i_codigo = 2532;
UPDATE censoinstsuperior SET ed257_c_nome = 'FIAMFAAM - CENTRO UNIVERSITARIO' where ed257_i_codigo = 2556;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE CATOLICA DE VARZEA GRANDE' where ed257_i_codigo = 2775;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE EXPEDICIONARIOS DO BRASIL' where ed257_i_codigo = 2783;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE FIA DE GESTAO E TECNOLOGIA' where ed257_i_codigo = 2793;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO DE IPORA' where ed257_i_codigo = 2796;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ATITUS EDUCACAO ONLINE' where ed257_i_codigo = 2821;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO UNIFACTHUS' where ed257_i_codigo = 2935;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO ESTACIO DO PANTANAL' where ed257_i_codigo = 2961;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ANHANGUERA UNIME DE SALVADOR' where ed257_i_codigo = 3034;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNIBRAS JUAZEIRO' where ed257_i_codigo = 3365;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE IBITURUNA' where ed257_i_codigo = 3448;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE CAICOENSE SANTA TERESINHA' where ed257_i_codigo = 3644;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNICA DE MONTES CLAROS' where ed257_i_codigo = 3657;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNINTA FORTALEZA  GESTAO E NEGOCIOS' where ed257_i_codigo = 3680;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ADVENTISTA DE MINAS GERAIS' where ed257_i_codigo = 3754;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO SENAC RS' where ed257_i_codigo = 3804;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO FIS' where ed257_i_codigo = 3881;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE FLORIDA' where ed257_i_codigo = 4090;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO MAURICIO DE NASSAU DERBY' where ed257_i_codigo = 4153;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO MAURICIO DE NASSAU TERESINA SUL' where ed257_i_codigo = 4197;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADES DE EDUCACAO GESTAO E SAUDE UNIFUTURO  FACULDADE UNIFUTURO' where ed257_i_codigo = 4201;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO MAURICIO DE NASSAU DE RONDONIA' where ed257_i_codigo = 4255;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE INHUMAS  FAC-MAIS' where ed257_i_codigo = 4259;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE JK  UNIDADE I - GAMA' where ed257_i_codigo = 4416;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO DE MACEIO' where ed257_i_codigo = 4530;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNIAPOGEU' where ed257_i_codigo = 4588;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE INTEGRADA DE TAUBATE' where ed257_i_codigo = 4873;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADES IPEDE' where ed257_i_codigo = 4945;
UPDATE censoinstsuperior SET ed257_c_nome = 'AFYA FACULDADE DE CIENCIAS MEDICAS DE GARANHUNS' where ed257_i_codigo = 5580;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE SAO PAULO TECH SCHOOL' where ed257_i_codigo = 10323;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE VILLALOBOS DO CONE-LESTE PAULISTA' where ed257_i_codigo = 11376;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE CIENCIAS DA AMAZONIA' where ed257_i_codigo = 11593;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE SUDOESTE PAULISTA  TATUI - FSP' where ed257_i_codigo = 11752;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNIBRAS BRASILIA' where ed257_i_codigo = 11895;
UPDATE censoinstsuperior SET ed257_c_nome = 'INSTITUTO DE EDUCACAO DE MONTES CLAROS' where ed257_i_codigo = 12189;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNICA DE TIMOTEO' where ed257_i_codigo = 12718;
UPDATE censoinstsuperior SET ed257_c_nome = 'IPOG  INSTITUTO DE POS-GRADUACAO & GRADUACAO' where ed257_i_codigo = 12916;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE CORAM DEO' where ed257_i_codigo = 13034;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE MULTIVERSA DE TECNOLOGIA' where ed257_i_codigo = 13106;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE CIENCIAS EDUCACAO, SAUDE, PESQUISA E GESTAO' where ed257_i_codigo = 13631;
UPDATE censoinstsuperior SET ed257_c_nome = 'CENTRO UNIVERSITARIO DA AMAZONIA BELEM' where ed257_i_codigo = 13982;
UPDATE censoinstsuperior SET ed257_c_nome = 'ESCOLA SUPERIOR DO COOPERATIVISMO' where ed257_i_codigo = 14181;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE CONEXAO' where ed257_i_codigo = 14242;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE FEITEP' where ed257_i_codigo = 14951;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DIPLOMATA' where ed257_i_codigo = 14996;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE SOBRESP DE SANTA MARIA' where ed257_i_codigo = 15894;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE AETOS' where ed257_i_codigo = 16502;
UPDATE censoinstsuperior SET ed257_c_nome = 'AFYA FACULDADE DE CIENCIAS MEDICAS DE PALMAS' where ed257_i_codigo = 16728;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNIBRAS CATALAO' where ed257_i_codigo = 17831;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNIBRAS  PRUDENTE' where ed257_i_codigo = 18372;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ALIANCA DO MARANHAO' where ed257_i_codigo = 18623;
UPDATE censoinstsuperior SET ed257_c_nome = 'ENSINO SUPERIOR ALBERT SABIN' where ed257_i_codigo = 18703;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ONE LEARNING' where ed257_i_codigo = 18735;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UMFG' where ed257_i_codigo = 19563;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ALFFA DO BRASIL' where ed257_i_codigo = 19816;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE CATEDRAL' where ed257_i_codigo = 19878;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNISAPIENS' where ed257_i_codigo = 19943;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNIFAMEC DE MACEIO' where ed257_i_codigo = 20494;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ANHANGUERA DE ALAGOINHAS' where ed257_i_codigo = 21280;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNIFAEL CURITIBA' where ed257_i_codigo = 21366;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNINASSAU IMPERATRIZ' where ed257_i_codigo = 21398;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ATITUS EDUCACAO PORTO ALEGRE' where ed257_i_codigo = 21519;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE TECNOLOGICA DE CAPACITACAO' where ed257_i_codigo = 21811;
UPDATE censoinstsuperior SET ed257_c_nome = 'AFYA FACULDADE DE CIENCIAS MEDICAS DE ITABUNA' where ed257_i_codigo = 22088;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE SAUDE SANTA CASA BH' where ed257_i_codigo = 22103;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNIFAMEC DE ITABUNA' where ed257_i_codigo = 22139;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ANHANGUERA DE PETROLINA' where ed257_i_codigo = 22158;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNINASSAU SERRA TALHADA' where ed257_i_codigo = 22210;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE MATIAS MACHLINE' where ed257_i_codigo = 22246;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE KEEP EDUC' where ed257_i_codigo = 22249;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE CTA DE NOVA LIMA' where ed257_i_codigo = 22327;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE LIBANO' where ed257_i_codigo = 22455;
UPDATE censoinstsuperior SET ed257_c_nome = 'AFYA FACULDADE DE CIENCIAS MEDICAS DE JABOATAO DOS GUARARAPES' where ed257_i_codigo = 22514;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ANCLIVEPA SAO PAULO' where ed257_i_codigo = 22605;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE HARVEN AGRIBUSINESS SCHOOL' where ed257_i_codigo = 22659;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ANHANGUERA DE QUIXERAMOBIM' where ed257_i_codigo = 22710;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ANHANGUERA DE JUAZEIRO DO NORTE' where ed257_i_codigo = 23100;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE CENTRAL DO RECIFECENTRO' where ed257_i_codigo = 23191;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNICESP' where ed257_i_codigo = 23409;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ZARNS  ITUMBIARA' where ed257_i_codigo = 23454;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DUNAMIS' where ed257_i_codigo = 23820;
UPDATE censoinstsuperior SET ed257_c_nome = 'AFYA FACULDADE DE CIENCIAS MEDICAS DE SANTA INES' where ed257_i_codigo = 24550;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE TECNOLOGIA DE MATAO "LUIZ MARCHESAN"' where ed257_i_codigo = 24672;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE IBRA DO MATO GROSSO DO SUL' where ed257_i_codigo = 23866;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE METROPOLITANA DAS AMERICAS' where ed257_i_codigo = 21220;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DO CENTRO EDUCACIONAL INTERDISCIPLINAR DO BRASIL' where ed257_i_codigo = 22650;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE MULTIVERSA DE SETE LAGOAS' where ed257_i_codigo = 24400;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE MARANATHA' where ed257_i_codigo = 19312;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ANHANGUERA DE CARUARU' where ed257_i_codigo = 23897;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNITBRASIL' where ed257_i_codigo = 25167;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE LIFE UNIC EDUCATION' where ed257_i_codigo = 24996;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE LEGALE' where ed257_i_codigo = 25791;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE VP' where ed257_i_codigo = 25575;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE DE MINAS EAD' where ed257_i_codigo = 25133;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE FAMEP  UNIDADE PARAMBU - CE' where ed257_i_codigo = 25365;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ANHANGUERA DE CAMACARI' where ed257_i_codigo = 23896;
UPDATE censoinstsuperior SET ed257_c_nome = 'AFYA FACULDADE DE CIENCIAS MEDICAS DE ABAETETUBA' where ed257_i_codigo = 25307;
UPDATE censoinstsuperior SET ed257_c_nome = 'SOBERANA FACULDADE DE JUIZ DE FORA' where ed257_i_codigo = 24215;
UPDATE censoinstsuperior SET ed257_c_nome = 'AFYA FACULDADE DE CIENCIAS MEDICAS DE MANACAPURU' where ed257_i_codigo = 24551;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE EDUCABRASIL NOEL DE MELLO' where ed257_i_codigo = 24703;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE BELAVISTA' where ed257_i_codigo = 25455;
UPDATE censoinstsuperior SET ed257_c_nome = 'ESCOLA DE COMUNICACAO MIDIA E INFORMACAO DA FUNDACAO GETULIO VARGAS' where ed257_i_codigo = 25424;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADES FAMEP   UNIDADE URUCUI - PI' where ed257_i_codigo = 23948;
UPDATE censoinstsuperior SET ed257_c_nome = 'INSTITUTO DE DESENVOLVIMENTO E APRENDIZAGEM  IDEA DIREITO' where ed257_i_codigo = 24213;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE BIOPARK' where ed257_i_codigo = 25452;
UPDATE censoinstsuperior SET ed257_c_nome = 'AFYA FACULDADE DE CIENCIAS MEDICAS DE ITACOATIARA' where ed257_i_codigo = 24548;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE BRASILIANA' where ed257_i_codigo = 24689;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE EXAME' where ed257_i_codigo = 25716;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNIBRAS SANTA INES' where ed257_i_codigo = 25469;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ANHANGUERA DE CACHOEIRO DE ITAPEMIRIM' where ed257_i_codigo = 23895;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE UNISUL DE EDUCACAO DE ITAJAI' where ed257_i_codigo = 23200;
UPDATE censoinstsuperior SET ed257_c_nome = 'FACULDADE ALCANCE ENSINO SUPERIOR EAD' where ed257_i_codigo = 25182;
UPDATE censoinstsuperior SET ed257_c_nome = 'AFYA FACULDADE DE CIENCIAS MEDICAS DE BRAGANCA' where ed257_i_codigo = 25261;
UPDATE censoinstsuperior SET ed257_c_nome = 'FUNDACAO FACULDADE PARA AS ARTES EDUCACAO E COMUNICACAO' where ed257_i_codigo = 25745;
UPDATE censoinstsuperior SET ed257_c_situacao = 'ATIVA' where ed257_i_codigo in (622,764,837,877,1204,1254,1296,1457,1605,1607,1614,1783,1806,1852,1904,1926,2282,2319,2346,2456,2514,3039,3223,3437,3690,3743,3774,4179,4538,4725,4740,4756,5013,11748,11750,13657,13811,14132,14147,14155,14738,14878,15521,15833,16781,16914,16964,17269,17395,17657,17765,17803,18067,18153,18158,18673,18675,18958,19256,19277,19321,19406,19787,19823,20096,20406,20534,20561,20663,20685,20709,21261,21310,21411,21505,21514,21674,21808,21833,21857,21882,21891,21926,21977,22097,22128,22130,22142,22181,22185,22222,22310,22433,22738,22746,23179,23203,23725,22178);
UPDATE censoinstsuperior SET ed257_c_situacao = 'EXTINTA' where ed257_i_codigo = 84;
delete from avaliacaoperguntaopcao where db104_sequencial = 4001512;
update avaliacaoperguntaopcao set db104_descricao = 'Área verde', db104_identificador = 'area-verde', db104_identificadorcampo = 'area_verde' where db104_sequencial = 3000027;
delete from avaliacaoperguntaopcao where db104_sequencial in (4001513, 4001514, 4001515);
update avaliacaoperguntaopcao set db104_descricao = 'Auxiliar de serviços gerais, porteiro(a), zelador(a), faxineiro(a), horticultor(a), jardineiro(a)' where db104_sequencial = 4001254;
delete from avaliacaoperguntaopcao where db104_sequencial in (4001516, 4001517, 4001518, 4001519);
delete from avaliacaoperguntaopcao where db104_sequencial in (4001526, 4001520);
delete from avaliacaopergunta where db103_sequencial = 4000372;
delete from avaliacaoperguntaopcao where db104_sequencial in (4001532, 4001531, 4001530, 4001529, 4001528, 4001527);
delete from avaliacaopergunta where db103_sequencial = 4000374;
update avaliacaopergunta set db103_descricao = 'Escola abre aos finais de semana para a comunidade', db103_identificador = 'escola-abre-aos-finais-de-semana-para-a-comunidade', db103_identificadorcampo = 'pregunta_escola_abre_aos_finais_de_semana_para_a_comunidade' where db103_sequencial = 3000026;
SQL
        );
    }

    private function upEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
alter table escola.escola ALTER COLUMN ed18_c_email type char(100);
SQL
        );
        DB::table('escola.mediacaodidaticopedagogica')->insert([
            'ed130_codigo' => 2,
            'ed130_descricao' => 'Semipresencial'
        ]);
    }

    private function downEstrutura()
    {
        DB::connection()->getPdo()->exec(<<<SQL
alter table escola.escola ALTER COLUMN ed18_c_email type char(50);
SQL
        );
        DB::table('escola.mediacaodidaticopedagogica')
            ->where('ed130_codigo', 2)
            ->delete();
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
        $this->upEstrutura();
        $this->upDadosCenso();
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
        $this->downDadosCenso();
    }
}
