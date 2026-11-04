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

//MODULO: PESSOAL
class LayoutBBBSFolha
{

    /***************************************************************************************************/
    /***************      TXT - publiciável que retorna texto a ser impresso no arquivo     ***************/
    /***************************************************************************************************/

    public $TEXTO = null;

    /***************************************************************************************************/
    /***************************************************************************************************/
    /*********************  publicIÁVEIS USADAS PARA GERAR ARQUIVO DO BANCO BANRISUL **********************/
    /***************************************************************************************************/
    /*
    CABEÇALHO ARQUIVO
*/
    public $BSheaderA_001_003 = null;
    public $BSheaderA_004_007 = null;
    public $BSheaderA_008_008 = null;
    public $BSheaderA_009_017 = null;
    public $BSheaderA_018_018 = null;
    public $BSheaderA_019_032 = null;
    public $BSheaderA_033_037 = null;
    public $BSheaderA_038_052 = null;
    public $BSheaderA_053_057 = null;
    public $BSheaderA_058_058 = null;
    public $BSheaderA_059_061 = null;
    public $BSheaderA_062_071 = null;
    public $BSheaderA_072_072 = null;
    public $BSheaderA_073_102 = null;
    public $BSheaderA_103_132 = null;
    public $BSheaderA_133_142 = null;
    public $BSheaderA_143_143 = null;
    public $BSheaderA_144_151 = null;
    public $BSheaderA_152_157 = null;
    public $BSheaderA_158_163 = null;
    public $BSheaderA_164_166 = null;
    public $BSheaderA_167_171 = null;
    public $BSheaderA_172_191 = null;
    public $BSheaderA_192_211 = null;
    public $BSheaderA_212_240 = null;
    /*
    CABEÇALHO LOTE
*/
    public $BSheaderL_001_003 = null;
    public $BSheaderL_004_007 = null;
    public $BSheaderL_008_008 = null;
    public $BSheaderL_009_009 = null;
    public $BSheaderL_010_011 = null;
    public $BSheaderL_012_013 = null;
    public $BSheaderL_014_016 = null;
    public $BSheaderL_017_017 = null;
    public $BSheaderL_018_018 = null;
    public $BSheaderL_019_032 = null;
    public $BSheaderL_033_037 = null;
    public $BSheaderL_038_052 = null;
    public $BSheaderL_053_057 = null;
    public $BSheaderL_058_061 = null;
    public $BSheaderL_062_071 = null;
    public $BSheaderL_072_072 = null;
    public $BSheaderL_073_102 = null;
    public $BSheaderL_103_142 = null;
    public $BSheaderL_143_172 = null;
    public $BSheaderL_173_177 = null;
    public $BSheaderL_178_192 = null;
    public $BSheaderL_193_212 = null;
    public $BSheaderL_213_220 = null;
    public $BSheaderL_221_222 = null;
    public $BSheaderL_223_224 = null;
    public $BSheaderL_225_240 = null;
    /*
    FINAL CABEÇALHOS
*/
    /*
    CORPO
*/
    public $BSregist_001_003 = null;
    public $BSregist_004_007 = null;
    public $BSregist_008_008 = null;
    public $BSregist_009_013 = null;
    public $BSregist_014_014 = null;
    public $BSregist_015_015 = null;
    public $BSregist_016_017 = null;
    public $BSregist_018_020 = null;
    public $BSregist_021_023 = null;
    public $BSregist_024_028 = null;
    public $BSregist_029_029 = null;
    public $BSregist_030_042 = null;
    public $BSregist_043_043 = null;
    public $BSregist_044_073 = null;
    public $BSregist_074_088 = null;
    public $BSregist_089_093 = null;
    public $BSregist_094_101 = null;
    public $BSregist_102_104 = null;
    public $BSregist_105_119 = null;
    public $BSregist_120_134 = null;
    public $BSregist_135_154 = null;
    public $BSregist_155_162 = null;
    public $BSregist_163_177 = null;
    public $BSregist_178_182 = null;
    public $BSregist_183_202 = null;
    public $BSregist_203_203 = null;
    public $BSregist_204_217 = null;
    public $BSregist_218_229 = null;
    public $BSregist_230_230 = null;
    public $BSregist_231_240 = null;
    /*
      FINAL CORPO
  */
    /***************************************************************************************************/

    /***************************************************************************************************/
    /***************************************************************************************************/
    /*********************  publicIÁVEIS USADAS PARA GERAR ARQUIVO DO BANCO DO BRASIL *********************/
    /***************************************************************************************************/
    /***************************************************************************************************/
    /*
      CABEÇALHO ARQUIVO
  */
    public $BBheaderA_001_003 = null;
    public $BBheaderA_004_007 = null;
    public $BBheaderA_008_008 = null;
    public $BBheaderA_009_017 = null;
    public $BBheaderA_018_018 = null;
    public $BBheaderA_019_032 = null;
    public $BBheaderA_033_052 = null;
    public $BBheaderA_053_057 = null;
    public $BBheaderA_058_058 = null;
    public $BBheaderA_059_070 = null;
    public $BBheaderA_071_071 = null;
    public $BBheaderA_072_072 = null;
    public $BBheaderA_073_102 = null;
    public $BBheaderA_103_132 = null;
    public $BBheaderA_133_142 = null;
    public $BBheaderA_143_143 = null;
    public $BBheaderA_144_151 = null;
    public $BBheaderA_152_157 = null;
    public $BBheaderA_158_163 = null;
    public $BBheaderA_164_166 = null;
    public $BBheaderA_167_171 = null;
    public $BBheaderA_172_191 = null;
    public $BBheaderA_192_211 = null;
    public $BBheaderA_212_222 = null;
    public $BBheaderA_223_225 = null;
    public $BBheaderA_226_228 = null;
    public $BBheaderA_229_230 = null;
    public $BBheaderA_231_240 = null;
    /*
      CABEÇALHO LOTE
  */
    public $BBheaderL_001_003 = null;
    public $BBheaderL_004_007 = null;
    public $BBheaderL_008_008 = null;
    public $BBheaderL_009_009 = null;
    public $BBheaderL_010_011 = null;
    public $BBheaderL_012_013 = null;
    public $BBheaderL_014_016 = null;
    public $BBheaderL_017_017 = null;
    public $BBheaderL_018_018 = null;
    public $BBheaderL_019_032 = null;
    public $BBheaderL_033_052 = null;
    public $BBheaderL_053_057 = null;
    public $BBheaderL_058_058 = null;
    public $BBheaderL_059_070 = null;
    public $BBheaderL_071_071 = null;
    public $BBheaderL_072_072 = null;
    public $BBheaderL_073_102 = null;
    public $BBheaderL_103_142 = null;
    public $BBheaderL_143_172 = null;
    public $BBheaderL_173_177 = null;
    public $BBheaderL_178_192 = null;
    public $BBheaderL_193_212 = null;
    public $BBheaderL_213_217 = null;
    public $BBheaderL_218_220 = null;
    public $BBheaderL_221_222 = null;
    public $BBheaderL_223_230 = null;
    public $BBheaderL_231_240 = null;
    /*
      FINAL CBABEÇALHOS
  */
    /*
      CORPO SEGMENTO A
  */
    public $BBregistA_001_003 = null;
    public $BBregistA_004_007 = null;
    public $BBregistA_008_008 = null;
    public $BBregistA_009_013 = null;
    public $BBregistA_014_014 = null;
    public $BBregistA_015_015 = null;
    public $BBregistA_016_017 = null;
    public $BBregistA_018_020 = null;
    public $BBregistA_021_023 = null;
    public $BBregistA_024_028 = null;
    public $BBregistA_029_029 = null;
    public $BBregistA_030_041 = null;
    public $BBregistA_042_042 = null;
    public $BBregistA_043_043 = null;
    public $BBregistA_044_073 = null;
    public $BBregistA_074_093 = null;
    public $BBregistA_094_101 = null;
    public $BBregistA_102_104 = null;
    public $BBregistA_105_119 = null;
    public $BBregistA_120_134 = null;
    public $BBregistA_135_154 = null;
    public $BBregistA_155_162 = null;
    public $BBregistA_163_177 = null;
    public $BBregistA_178_217 = null;
    public $BBregistA_218_229 = null;
    public $BBregistA_230_230 = null;
    public $BBregistA_231_240 = null;
    /*
      CORPO SEGMENTO B
  */
    public $BBregistB_001_003 = null;
    public $BBregistB_004_007 = null;
    public $BBregistB_008_008 = null;
    public $BBregistB_009_013 = null;
    public $BBregistB_014_014 = null;
    public $BBregistB_015_017 = null;
    public $BBregistB_018_018 = null;
    public $BBregistB_019_032 = null;
    public $BBregistB_033_062 = null;
    public $BBregistB_063_067 = null;
    public $BBregistB_068_082 = null;
    public $BBregistB_083_097 = null;
    public $BBregistB_098_117 = null;
    public $BBregistB_118_122 = null;
    public $BBregistB_123_125 = null;
    public $BBregistB_126_127 = null;
    public $BBregistB_128_135 = null;
    public $BBregistB_136_150 = null;
    public $BBregistB_151_165 = null;
    public $BBregistB_166_180 = null;
    public $BBregistB_181_195 = null;
    public $BBregistB_196_210 = null;
    public $BBregistB_211_225 = null;
    public $BBregistB_226_240 = null;
    /*
      FINAL CORPO
  */
    /***************************************************************************************************/

    /***************************************************************************************************/
    /***************************************************************************************************/
    /*****************  publicIÁVEIS USADAS PARA GERAR TRAILLER DO ARQUIVO DOS DOIS BANCOS ****************/
    /***************************************************************************************************/
    /***************************************************************************************************/
    /*
      TRAILLER LOTE
  */
    public $BBBStraillerL_001_003 = null;
    public $BBBStraillerL_004_007 = null;
    public $BBBStraillerL_008_008 = null;
    public $BBBStraillerL_009_017 = null;
    public $BBBStraillerL_018_023 = null;
    public $BBBStraillerL_024_041 = null;
    public $BBBStraillerL_042_059 = null;
    public $BBBStraillerL_060_230 = null;
    public $BBBStraillerL_231_240 = null;
    /*
      TRAILLER ARQUIVO
  */
    public $BBBStraillerA_001_003 = null;
    public $BBBStraillerA_004_007 = null;
    public $BBBStraillerA_008_008 = null;
    public $BBBStraillerA_009_017 = null;
    public $BBBStraillerA_018_023 = null;
    public $BBBStraillerA_024_029 = null;
    public $BBBStraillerA_230_035 = null;
    public $BBBStraillerA_236_240 = null;
    /*
	  FINAL TRAILLERS
  */
    /***************************************************************************************************/

    public $arquivo = null;
    public $nomearq = '/tmp/modelo.txt';

    //////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////// MÉTODOS LAYOUT DO BANCO BANRISUL //////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////// Início --- OBS.: Somente HEADER do arquivo, HEADER do lote e REGISTROS ///////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////
    public function geraHEADERArqBS()
    {
        $this->arquivo = fopen($this->nomearq, "w");
        fputs($this->arquivo,
            db_formatar(substr(trim($this->BSheaderA_001_003), 0, 3), "s", "0", 3, "e", 0)
            . "0000"
            . "0"
            . str_repeat(" ", 9)
            . "2"
            . db_formatar(substr(trim($this->BSheaderA_019_032), 0, 14), "s", "0", 14, "e", 0)
            . db_formatar(substr(trim($this->BSheaderA_033_037), 0, 5), "s", "0", 5, "e", 0)
            . str_repeat(" ", 15)
            . db_formatar(substr(trim(str_replace('.', '', str_replace('-', '', $this->BSheaderA_053_057))), 0, 5), "s", "0", 5, "e", 0)
            . "0"
            . "000"
            . db_formatar(substr(trim(str_replace('.', '', str_replace('-', '', $this->BSheaderA_062_071))), 0, 10), "s", "0", 10, "e", 0)
            . "0"
            . db_translate(db_formatar(substr(strtoupper($this->BSheaderA_073_102), 0, 30), 's', ' ', 30, 'd', 0))
            . db_translate(db_formatar(substr(strtoupper($this->BSheaderA_103_132), 0, 30), 's', ' ', 30, 'd', 0))
            . str_repeat(" ", 10)
            . "1"
            . str_replace('/', '', db_formatar($this->BSheaderA_144_151, "d"))
            . date("H") . date("i") . date("s")
            . db_formatar(substr($this->BSheaderA_158_163, 0, 6), "s", "0", 6, "e", 0)
            . "030"
            . str_repeat("0", 5)
            . str_repeat(" ", 20)
            . db_formatar($this->BSheaderA_192_211, "s", "0", 20, "e", 0)
            . str_repeat(" ", 29)
            . "\r\n"
        );
    }

    public function geraHEADERLoteBS()
    {
        //segundo cabeçalho
        fputs($this->arquivo,
            $this->BSheaderL_001_003
            . db_formatar($this->BSheaderL_004_007, 's', '0', 4, 'e', 0)
            . "1"
            . "C"
            . $this->BSheaderL_010_011
            . $this->BSheaderL_012_013
            . "020"
            . " "
            . "2"
            . db_formatar($this->BSheaderL_019_032, "s", "0", 14, "e", 0)
            . db_formatar($this->BSheaderL_033_037, "s", "0", 5, "e", 0)
            . str_repeat(" ", 15)
            . db_formatar($this->BSheaderL_053_057, "s", "0", 5, "e", 0)
            . str_repeat('0', 4)
            . db_formatar($this->BSheaderL_062_071, "s", "0", 10, "e", 0)
            . " "
            . db_translate(db_formatar(substr(strtoupper($this->BSheaderL_073_102), 0, 30), 's', ' ', 30, 'd', 0))
            . str_repeat(' ', 40)
            . db_translate(db_formatar(substr(strtoupper($this->BSheaderL_143_172), 0, 30), 's', ' ', 30, 'd', 0))
            . db_formatar($this->BSheaderL_173_177, "s", "0", 5, "e", 0)
            . db_translate(db_formatar(substr(strtoupper($this->BSheaderL_178_192), 0, 15), "s", " ", 15, "d", 0))
            . db_translate(db_formatar(substr(strtoupper($this->BSheaderL_193_212), 0, 20), 's', ' ', 20, 'd', 0))
            . db_formatar(substr(str_replace('.', '', str_replace('-', '', $this->BSheaderL_213_220)), 0, 8), 's', '0', 8, 'e', 0)
            . db_translate(db_formatar(substr(strtoupper($this->BSheaderL_221_222), 0, 2), 's', ' ', 2, 'd', 0))
            . str_repeat(' ', 2)
            . str_repeat(' ', 16)
            . "\r\n"
        );
    }

    public function geraREGISTROSBS()
    {
        fputs($this->arquivo,
            $this->BSregist_001_003
            . db_formatar($this->BSregist_004_007, 's', '0', 4, 'e', 0)
            . "3"
            . db_formatar($this->BSregist_009_013, 's', '0', 5, 'e', 0)
            . "A"
            . "0"
            . "00"
            . $this->BSregist_018_020
            . $this->BSregist_021_023
            . db_formatar($this->BSregist_024_028, 's', '0', 5, 'e', 0)
            . "0"
            . db_formatar($this->BSregist_030_042, 's', '0', 13, 'e', 0)
            . " "
            . db_translate(db_formatar(substr(strtoupper($this->BSregist_044_073), 0, 30), 's', ' ', 30, 'd', 0))
            . db_formatar($this->BSregist_074_088, 's', '0', 15, 'd', 0)
            . "00005"
            . str_replace('/', '', db_formatar($this->BSregist_094_101, "d"))
            . "BRL"
            . str_repeat('0', 15)
            . db_formatar(str_replace(',', '', str_replace('.', '', trim(db_formatar($this->BSregist_120_134, "f")))), 's', '0', 15, 'e', 0)
            . str_repeat(' ', 20)
            . str_repeat(' ', 8)
            . str_repeat(' ', 15)
            . str_repeat(' ', 5)
            . str_repeat(' ', 20)
            . $this->BSregist_203_203
            . db_formatar(substr(str_replace('.', '', str_replace('-', '', $this->BSregist_204_217)), 0, 14), 's', '0', 14, 'e', 0)
            . str_repeat(' ', 12)
            . "0"
            . str_repeat(' ', 10)
            . "\r\n"
        );
    }
    //////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////// FINAL MÉTODOS ARQUIVO DO BANCO DO BANRISUL ////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////

    //////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////// MÉTODOS LAYOUT DO BANCO DO BRASIL /////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////// Início --- OBS.: Somente HEADER do arquivo, HEADER do lote e REGISTROS ///////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////

    public function geraHEADERArqBB()
    {
        $this->arquivo = fopen($this->nomearq, "w");
        fputs($this->arquivo,
            $this->BBheaderA_001_003
            . str_repeat('0', 4)
            . "0"
            . str_repeat(' ', 9)
            . "2"
            . db_formatar($this->BBheaderA_019_032, 's', '0', 14, 'e', 0)
            . db_formatar($this->BBheaderA_033_052, 's', ' ', 20, 'd', 0)
            . db_formatar(str_replace('.', '', str_replace('-', '', $this->BBheaderA_053_057)), 's', '0', 5, 'e', 0)
            . $this->BBheaderA_058_058
            . db_formatar(str_replace('.', '', str_replace('-', '', $this->BBheaderA_059_070)), 's', '0', 12, 'e', 0)
            . $this->BBheaderA_071_071
            . ' '
            . db_translate(db_formatar(substr(strtoupper($this->BBheaderA_073_102), 0, 30), 's', ' ', 30, 'd', 0))
            . db_translate(db_formatar(substr(strtoupper($this->BBheaderA_103_132), 0, 30), 's', ' ', 30, 'd', 0))
            . str_repeat(' ', 10)
            . "1"
            . str_replace('/', '', db_formatar($this->BBheaderA_144_151, "d"))
            . date("H") . date("i") . date("s")
            . db_formatar($this->BBheaderA_158_163, 's', '0', 6, 'e', 0)
            . "030"
            . str_repeat('0', 5)
            . str_repeat(' ', 20)
            . db_formatar($this->BBheaderA_192_211, 's', ' ', 20, 'e', 0)
            . str_repeat(' ', 11)
            . str_repeat(' ', 3)
            . str_repeat('0', 3)
            . str_repeat(' ', 2)
            . str_repeat(' ', 10)
            . "\r\n"
        );
    }

    public function geraHEADERLoteBB()
    {
        //segundo cabeçalho
        fputs($this->arquivo,
            $this->BBheaderL_001_003
            . db_formatar($this->BBheaderL_004_007, 's', '0', 4, 'e', 0)
            . "1"
            . "C"
            . $this->BBheaderL_010_011
            . $this->BBheaderL_012_013
            . "020"
            . " "
            . "2"
            . db_formatar($this->BBheaderL_019_032, 's', '0', 14, 'e', 0)
            . db_formatar($this->BBheaderL_033_052, 's', ' ', 20, 'd', 0)
            . db_formatar(str_replace('.', '', str_replace('-', '', $this->BBheaderL_053_057)), 's', '0', 5, 'e', 0)
            . $this->BBheaderL_058_058
            . db_formatar(str_replace('.', '', str_replace('-', '', $this->BBheaderL_059_070)), 's', '0', 12, 'e', 0)
            . $this->BBheaderL_071_071
            . " "
            . substr(strtoupper($this->BBheaderL_073_102), 0, 30)
            . str_repeat(' ', 40)
            . db_translate(db_formatar(strtoupper($this->BBheaderL_143_172), 's', ' ', 30, 'd', 0))
            . db_formatar($this->BBheaderL_173_177, 's', ' ', 5, 'e', 0)
            . str_repeat(' ', 15)
            . db_translate(db_formatar(strtoupper(trim($this->BBheaderL_193_212)), 's', ' ', 20, 'd', 0))
            . db_formatar($this->BBheaderL_213_217, 's', '0', 5, 'e', 0)
            . db_translate(db_formatar($this->BBheaderL_218_220, 's', ' ', 3, 'd', 0))
            . db_translate(db_formatar($this->BBheaderL_221_222, 's', ' ', 2, 'd', 0))
            . str_repeat(' ', 8)
            . str_repeat(' ', 10)
            . "\r\n"
        );
    }

    public function geraREGISTROSBB()
    {
        fputs($this->arquivo,
            $this->BBregistA_001_003
            . db_formatar($this->BBregistA_004_007, 's', '0', 4, 'e', 0)
            . "3"
            . db_formatar($this->BBregistA_009_013, 's', '0', 5, 'e', 0)
            . "A"
            . "0"
            . "00"
            . $this->BBregistA_018_020
            . $this->BBregistA_021_023
            . db_formatar(str_replace('.', '', str_replace('-', '', $this->BBregistA_024_028)), 's', '0', 5, 'e', 0)
            . $this->BBregistA_029_029
            . db_formatar(str_replace('.', '', str_replace('-', '', $this->BBregistA_030_041)), 's', '0', 12, 'e', 0)
            . $this->BBregistA_042_042
            . $this->BBregistA_043_043
            . db_formatar(str_replace('-', '', substr($this->BBregistA_044_073, 0, 30)), 's', ' ', 30, 'd', 0)
            . db_formatar($this->BBregistA_074_093, 's', '0', 20, 'd', 0)
            . str_replace("/", '', db_formatar($this->BBregistA_094_101, "d"))
            . "BRL"
            . str_repeat('0', 15)
            . db_formatar(str_replace(',', '', str_replace('.', '', trim(db_formatar($this->BBregistA_120_134, "f")))), 's', '0', 15, 'e', 0)
            . str_repeat(' ', 20)
            . str_repeat(' ', 8)
            . str_repeat(' ', 15)
            . str_repeat(' ', 40)
            . str_repeat(' ', 12)
            . "0"
            . str_repeat(' ', 10)
            . "\r\n"
        );
        fputs($this->arquivo,
            $this->BBregistB_001_003
            . db_formatar($this->BBregistB_004_007, 's', '0', 4, 'e', 0)
            . "3"
            . db_formatar($this->BBregistB_009_013, 's', '0', 5, 'e', 0)
            . "B"
            . str_repeat(' ', 3)
            . $this->BBregistB_018_018
            . db_translate(db_formatar($this->BBregistB_019_032, 's', '0', 14, 'e', 0))
            . db_formatar(substr($this->BBregistB_033_062, 0, 30), 's', ' ', 30, 'd', 0)
            . db_translate(db_formatar(substr($this->BBregistB_063_067, 0, 5), 's', '0', 5, 'e', 0))
            . db_translate(db_formatar(substr($this->BBregistB_068_082, 0, 15), 's', ' ', 15, 'd', 0))
            . db_translate(db_formatar(substr($this->BBregistB_083_097, 0, 15), 's', ' ', 15, 'd', 0))
            . db_translate(db_formatar(substr($this->BBregistB_098_117, 0, 20), 's', ' ', 20, 'd', 0))
            . db_translate(db_formatar(substr($this->BBregistB_118_122, 0, 5), 's', ' ', 5, 'd', 0))
            . db_translate(db_formatar(substr($this->BBregistB_123_125, 5, 3), 's', ' ', 3, 'd', 0))
            . db_translate(db_formatar($this->BBregistB_126_127, 's', ' ', 2, 'd', 0))
            . str_replace("/", '', db_formatar($this->BBregistB_128_135, "d"))
            . db_formatar(str_replace(',', '', str_replace('.', '', $this->BBregistB_136_150)), 's', '0', 15, 'e', 0)
            . str_repeat('0', 15)
            . str_repeat('0', 15)
            . str_repeat('0', 15)
            . str_repeat('0', 15)
            . str_repeat('0', 15)
            . str_repeat('0', 15)
            . "\r\n"
        );
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////// FINAL MÉTODOS ARQUIVO DO BANCO DO BRASIL /////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////

    //////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////// INÍCIO MÉTODOS QUE GERAM TRAILLERS ////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////
    // OBS.: Arquivos do banco do brasil e do banrisul não mudam trailler de arquivo e trailler de lote //
    public function geraTRAILLERLote()
    {
        fputs($this->arquivo,
            $this->BBBStraillerL_001_003
            . db_formatar($this->BBBStraillerL_004_007, 's', '0', 4, 'e', 0)
            . "5"
            . str_repeat(' ', 9)
            . db_formatar(($this->BBBStraillerL_018_023 + 2), 's', '0', 6, 'e', 0)
            . db_formatar(str_replace(',', '', str_replace('.', '', trim(db_formatar($this->BBBStraillerL_024_041, "f")))), 's', '0', 18, 'e', 0)
            . str_repeat('0', 18)
            . str_repeat(' ', 171)
            . str_repeat(' ', 10)
            . "\r\n"
        );
    }

    public function geraTRAILLERArquivo()
    {
        fputs($this->arquivo,
            $this->BBBStraillerA_001_003
            . '9999'
            . '9'
            . str_repeat(' ', 9)
            . db_formatar($this->BBBStraillerA_018_023, 's', '0', 6, 'e', 0)
            . db_formatar($this->BBBStraillerA_024_029, 's', '0', 6, 'e', 0)
            . str_repeat('0', 6)
            . str_repeat(' ', 205)
            . "\r\n"
        );
    }
    //////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////// FINAL MÉTODOS QUE GERAM TRAILLERS ////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////
    //          FECHA O ARQUIVO           //
    ////////////////////////////////////////
    public function gera()
    {
        fclose($this->arquivo);
    }
    ////////////////////////////////////////
    ////////////////////////////////////////
    //          ABRE O ARQUIVO            //
    ////////////////////////////////////////
    public function abre()
    {
        $this->arquivo = fopen($this->nomearq, "w");
    }
    ////////////////////////////////////////
}


//MODULO: PESSOAL
class cl_layout_VISA
{

    /***************************************************************************************************/
    /***************      TXT - publiciável que retorna texto a ser impresso no arquivo     ***************/
    /***************************************************************************************************/

    public $TEXTO = null;

    /***************************************************************************************************/

    /***************************************************************************************************/
    /***************************************************************************************************/
    /*********************     publicIÁVEIS USADAS PARA GERAR ARQUIVO DO VISA VALE    *********************/
    /***************************************************************************************************/
    /***************************************************************************************************/
    /*
      CABEÇALHO ARQUIVO - HEADER ARQUIVO
  */
    public $VVheaderA_001_001 = null;
    public $VVheaderA_002_009 = null;
    public $VVheaderA_010_013 = null;
    public $VVheaderA_014_048 = null;
    public $VVheaderA_049_062 = null;
    public $VVheaderA_063_073 = null;
    public $VVheaderA_074_084 = null;
    public $VVheaderA_085_090 = null;
    public $VVheaderA_091_098 = null;
    public $VVheaderA_099_099 = null;
    public $VVheaderA_100_100 = null;
    public $VVheaderA_101_106 = null;
    public $VVheaderA_107_124 = null;
    public $VVheaderA_125_127 = null;
    public $VVheaderA_128_394 = null;
    public $VVheaderA_395_400 = null;
    public $VVheaderA_401_450 = null;
    /*
      REGISTRO FILIAL OU POSTO DE PESSOA JURÍDICA
  */
    public $VVregistFL_001_001 = null;
    public $VVregistFL_002_009 = null;
    public $VVregistFL_010_013 = null;
    public $VVregistFL_014_015 = null;
    public $VVregistFL_016_025 = null;
    public $VVregistFL_026_060 = null;
    public $VVregistFL_061_064 = null;
    public $VVregistFL_065_099 = null;
    public $VVregistFL_100_139 = null;
    public $VVregistFL_140_151 = null;
    public $VVregistFL_152_157 = null;
    public $VVregistFL_158_192 = null;
    public $VVregistFL_193_232 = null;
    public $VVregistFL_233_244 = null;
    public $VVregistFL_245_250 = null;
    public $VVregistFL_251_285 = null;
    public $VVregistFL_286_325 = null;
    public $VVregistFL_326_337 = null;
    public $VVregistFL_338_343 = null;
    public $VVregistFL_344_363 = null;
    public $VVregistFL_364_394 = null;
    public $VVregistFL_395_400 = null;
    public $VVregistFL_401_450 = null;
    /*
      FINAL REGISTROS
  */
    /*
      REGISTRO USUÁRIOS (FUNCIONÁRIOS)
  */
    public $VVregistFC_001_001 = null;
    public $VVregistFC_002_012 = null;
    public $VVregistFC_013_013 = null;
    public $VVregistFC_014_026 = null;
    public $VVregistFC_027_080 = null;
    public $VVregistFC_081_088 = null;
    public $VVregistFC_089_099 = null;
    public $VVregistFC_100_100 = null;
    public $VVregistFC_101_113 = null;
    public $VVregistFC_114_133 = null;
    public $VVregistFC_134_139 = null;
    public $VVregistFC_140_154 = null;
    public $VVregistFC_155_155 = null;
    public $VVregistFC_156_156 = null;
    public $VVregistFC_157_191 = null;
    public $VVregistFC_192_201 = null;
    public $VVregistFC_202_206 = null;
    public $VVregistFC_207_214 = null;
    public $VVregistFC_215_242 = null;
    public $VVregistFC_243_272 = null;
    public $VVregistFC_273_274 = null;
    public $VVregistFC_275_309 = null;
    public $VVregistFC_310_310 = null;
    public $VVregistFC_311_314 = null;
    public $VVregistFC_315_322 = null;
    public $VVregistFC_323_326 = null;
    public $VVregistFC_327_330 = null;
    public $VVregistFC_331_338 = null;
    public $VVregistFC_339_339 = null;
    public $VVregistFC_340_347 = null;
    public $VVregistFC_348_348 = null;
    public $VVregistFC_349_388 = null;
    public $VVregistFC_389_394 = null;
    public $VVregistFC_395_400 = null;
    public $VVregistFC_401_450 = null;
    /*
      REGISTRO DE USUÁRIOS (FUNCIONÁRIOS)
  */
    /*
      TRAILLER ARQUIVO VISA VALE
  */
    public $VVtraillerArq_001_001 = null;
    public $VVtraillerArq_002_007 = null;
    public $VVtraillerArq_008_022 = null;
    public $VVtraillerArq_023_394 = null;
    public $VVtraillerArq_395_400 = null;
    /*
   *  FINAL DO TRAILLER DE ARQUIVO VISA VALE
  */
    /***************************************************************************************************/

    public $arquivo = null;
    public $nomearq = '/tmp/modelo.txt';

    //////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////    MÉTODOS LAYOUT DO VISA VALE    /////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////

    public function geraHEADERArqVV()
    {
        $this->arquivo = fopen($this->nomearq, "w");
        fputs($this->arquivo,
            "0"
            . str_replace("/", '', db_formatar($this->VVheaderA_002_009, "d"))
            . "A001"
            . db_formatar(strtoupper(substr(db_translate($this->VVheaderA_014_048), 0, 35)), "s", " ", 35, "d", 0)
            . db_formatar($this->VVheaderA_049_062, "s", "0", 14, "e", 0)
            . str_repeat("0", 11)
            . db_formatar($this->VVheaderA_074_084, "s", "0", 11, "e", 0)
            . str_repeat("0", 6)
            . str_replace("/", '', db_formatar($this->VVheaderA_091_098, "d"))
            . $this->VVheaderA_099_099
            . $this->VVheaderA_100_100
            . str_replace("/", '', $this->VVheaderA_101_106)
            . str_repeat(" ", 18)
            . "007"
            . str_repeat(" ", 267)
            . db_formatar("1", "s", "0", 6, "e", 0)
            . str_repeat(" ", 50)
            . "\r\n"
        );
    }

    public function geraRegistVV()
    {
        //segundo cabeçalho
        fputs($this->arquivo,
            "1"
            . db_formatar($this->VVregistFL_002_009, "s", "0", 14, "e", 0)  // CNPJ É QUEBRADO NO ARQUIVO
            . str_repeat("0", 10)                                       // PASSEI SOMENTE UMA publicIÁVEL COM CNPJ DA PREFEITURA
            . db_formatar(strtoupper(substr(db_translate($this->VVregistFL_026_060), 0, 35)), "s", " ", 35, "d", 0)
            . db_formatar($this->VVregistFL_061_064, "s", "0", 4, "e", 0)
            . db_formatar(strtoupper(substr(db_translate($this->VVregistFL_065_099), 0, 35)), "s", " ", 35, "d", 0)
            . db_formatar(strtoupper(substr(db_translate($this->VVregistFL_100_139), 0, 40)), "s", " ", 40, "d", 0)
            . db_formatar($this->VVregistFL_140_151, "s", "0", 12, "e", 0)
            . db_formatar($this->VVregistFL_152_157, "s", "0", 6, "e", 0)
            . db_formatar(strtoupper(substr(db_translate($this->VVregistFL_158_192), 0, 35)), "s", " ", 35, "d", 0)
            . db_formatar(strtoupper(substr(db_translate($this->VVregistFL_193_232), 0, 40)), "s", " ", 40, "d", 0)
            . db_formatar($this->VVregistFL_233_244, "s", "0", 12, "e", 0)
            . db_formatar($this->VVregistFL_245_250, "s", "0", 6, "e", 0)
            . db_formatar(strtoupper(substr(db_translate($this->VVregistFL_251_285), 0, 35)), "s", " ", 35, "d", 0)
            . db_formatar(strtoupper(substr(db_translate($this->VVregistFL_286_325), 0, 40)), "s", " ", 40, "d", 0)
            . db_formatar($this->VVregistFL_326_337, "s", "0", 12, "e", 0)
            . db_formatar($this->VVregistFL_338_343, "s", "0", 6, "e", 0)
            . db_formatar($this->VVregistFL_344_363, "s", " ", 20, "d", 0)
            . str_repeat(" ", 31)
            . db_formatar("2", "s", "0", 6, "e", 0)
            . str_repeat(" ", 50)
            . "\r\n"
        );
    }

    public function geraREGISTROSVV()
    {
        fputs($this->arquivo,
            "5"
            . db_formatar((str_replace('.', '', str_replace(',', '', trim(db_formatar($this->VVregistFC_002_012, "f"))))), "s", "0", 11, "e", 0)
            . " "
            . db_formatar($this->VVregistFC_014_026, "s", " ", 13, "d", 0)
            . str_repeat(" ", 54)
            . str_replace("/", '', db_formatar($this->VVregistFC_081_088, "d"))
            . db_formatar(str_replace('-', '', str_replace('.', '', $this->VVregistFC_089_099)), "s", "0", 11, "e", 0)
            . "1"
            . db_formatar($this->VVregistFC_101_113, "s", "0", 13, "e", 0)
            . db_formatar(strtoupper(db_translate($this->VVregistFC_114_133)), "s", " ", 20, "d", 0)
            . db_formatar(strtoupper(db_translate($this->VVregistFC_134_139)), "s", " ", 6, "d", 0)
            . db_formatar($this->VVregistFC_140_154, "s", "0", 15, "e", 0)
            . strtoupper($this->VVregistFC_155_155)
            . $this->VVregistFC_156_156
            . db_formatar(strtoupper(substr(db_translate($this->VVregistFC_157_191), 0, 35)), "s", " ", 35, "d", 0)
            . db_formatar(strtoupper(substr(db_translate($this->VVregistFC_192_201), 0, 10)), "s", " ", 10, "d", 0)
            . db_formatar($this->VVregistFC_202_206, "s", "0", 5, "e", 0)
            . db_formatar(str_replace('-', '', str_replace('.', '', $this->VVregistFC_207_214)), "s", "0", 8, "e", 0)
            . db_formatar(strtoupper(substr(db_translate($this->VVregistFC_215_242), 0, 28)), "s", " ", 28, "d", 0)
            . db_formatar(strtoupper(substr(db_translate($this->VVregistFC_243_272), 0, 30)), "s", " ", 30, "d", 0)
            . db_formatar(strtoupper(substr(db_translate($this->VVregistFC_273_274), 0, 02)), "s", " ", 2, "d", 0)
            . db_formatar(strtoupper(substr(db_translate($this->VVregistFC_275_309), 0, 35)), "s", " ", 35, "d", 0)
            . "R"
            . db_formatar($this->VVregistFC_311_314, "s", "0", 4, "e", 0)
            . db_formatar(str_replace('-', '', str_replace('.', '', $this->VVregistFC_315_322)), "s", "0", 8, "e", 0)
            . db_formatar($this->VVregistFC_323_326, "s", "0", 4, "e", 0)
            . db_formatar($this->VVregistFC_327_330, "s", "0", 4, "e", 0)
            . db_formatar(str_replace('-', '', str_replace('.', '', $this->VVregistFC_331_338)), "s", "0", 8, "e", 0)
            . $this->VVregistFC_339_339
            . str_replace("/", '', db_formatar($this->VVregistFC_340_347, "d"))
            . " "
            . db_formatar(strtoupper(substr(db_translate($this->VVregistFC_349_388), 0, 40)), "s", " ", 40, "d", 0)
            . str_repeat(" ", 6)
            . db_formatar($this->VVregistFC_395_400, "s", "0", 6, "e", 0)
            . str_repeat(" ", 50)
            . "\r\n"
        );
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////    FINAL MÉTODOS ARQUIVO DO VISA VALE    /////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////   INÍCIO MÉTODO QUE GERA TRAILLER  ////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////
// OBS.: Arquivos do banco do brasil e do banrisul não mudam trailler de arquivo e trailler de lote //
    public function geraTRAILLERArq()
    {
        fputs($this->arquivo,
            "9"
            . db_formatar($this->VVtraillerArq_002_007, "s", "0", 6, "e", 0)
            . db_formatar(str_replace(',', '', str_replace('.', '', trim(db_formatar($this->VVtraillerArq_008_022, "f")))), "s", "0", 15, "e", 0)
            . str_repeat(" ", 372)
            . db_formatar($this->VVtraillerArq_395_400, "s", "0", 6, "e", 0)
            . "\r\n"
        );
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////   FINAL MÉTODO QUE GERA TRAILLER  ////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////
//          FECHA O ARQUIVO           //
////////////////////////////////////////
    public function gera()
    {
        fclose($this->arquivo);
    }
////////////////////////////////////////
////////////////////////////////////////
//          ABRE O ARQUIVO            //
////////////////////////////////////////
    public function abre()
    {
        $this->arquivo = fopen($this->nomearq, "w");
    }
////////////////////////////////////////
}


//MODULO: PESSOAL
class cl_layout_SEFIP
{

    /***************************************************************************************************/
    /***************      TXT - publiciável que retorna texto a ser impresso no arquivo     ***************/
    /***************************************************************************************************/

    public $TEXTO = null;

    /***************************************************************************************************/

    /***************************************************************************************************/
    /***************************************************************************************************/
    /*********************       publicIÁVEIS USADAS PARA GERAR ARQUIVO DA SEFIP      *********************/
    /***************************************************************************************************/
    /***************************************************************************************************/
    /*
    INFORMAÇÕES DO RESPONSÁVEL - REGISTRO '00'
*/
    public $SFPRegistro00_001_002 = null;
    public $SFPRegistro00_003_053 = null;
    public $SFPRegistro00_054_054 = null;
    public $SFPRegistro00_055_055 = null;
    public $SFPRegistro00_056_069 = null;
    public $SFPRegistro00_070_099 = null;
    public $SFPRegistro00_100_119 = null;
    public $SFPRegistro00_120_169 = null;
    public $SFPRegistro00_170_189 = null;
    public $SFPRegistro00_190_197 = null;
    public $SFPRegistro00_198_217 = null;
    public $SFPRegistro00_218_219 = null;
    public $SFPRegistro00_220_231 = null;
    public $SFPRegistro00_232_291 = null;
    public $SFPRegistro00_292_297 = null;
    public $SFPRegistro00_298_300 = null;
    public $SFPRegistro00_301_301 = null;
    public $SFPRegistro00_302_302 = null;
    public $SFPRegistro00_303_310 = null;
    public $SFPRegistro00_311_311 = null;
    public $SFPRegistro00_312_319 = null;
    public $SFPRegistro00_320_326 = null;
    public $SFPRegistro00_327_327 = null;
    public $SFPRegistro00_328_341 = null;
    public $SFPRegistro00_342_359 = null;
    public $SFPRegistro00_360_360 = null;
    /*
    INFORMAÇÕES DA EMPRESA - REGISTRO '10'
*/
    public $SFPRegistro10_001_002 = null;
    public $SFPRegistro10_003_003 = null;
    public $SFPRegistro10_004_017 = null;
    public $SFPRegistro10_018_053 = null;
    public $SFPRegistro10_054_093 = null;
    public $SFPRegistro10_094_143 = null;
    public $SFPRegistro10_144_163 = null;
    public $SFPRegistro10_164_171 = null;
    public $SFPRegistro10_172_191 = null;
    public $SFPRegistro10_192_193 = null;
    public $SFPRegistro10_194_205 = null;
    public $SFPRegistro10_206_206 = null;
    public $SFPRegistro10_207_213 = null;
    public $SFPRegistro10_214_214 = null;
    public $SFPRegistro10_215_216 = null;
    public $SFPRegistro10_217_217 = null;
    public $SFPRegistro10_218_218 = null;
    public $SFPRegistro10_219_221 = null;
    public $SFPRegistro10_222_225 = null;
    public $SFPRegistro10_226_229 = null;
    public $SFPRegistro10_230_234 = null;
    public $SFPRegistro10_235_249 = null;
    public $SFPRegistro10_250_264 = null;
    public $SFPRegistro10_265_279 = null;
    public $SFPRegistro10_280_280 = null;
    public $SFPRegistro10_281_294 = null;
    public $SFPRegistro10_295_297 = null;
    public $SFPRegistro10_298_301 = null;
    public $SFPRegistro10_302_310 = null;
    public $SFPRegistro10_311_355 = null;
    public $SFPRegistro10_356_359 = null;
    public $SFPRegistro10_360_360 = null;

    /*
     * dados do registro 12
     */
    public $SFPRegistro12_001_002 = 12;
    public $SFPRegistro12_003_003 = 1;
    public $SFPRegistro12_004_017 = null;
    public $SFPRegistro12_018_053 = null;
    public $SFPRegistro12_054_068 = null;
    public $SFPRegistro12_069_083 = " ";
    public $SFPRegistro12_084_084 = null;
    public $SFPRegistro12_085_099 = null;
    public $SFPRegistro12_100_114 = null;
    public $SFPRegistro12_115_125 = null;
    public $SFPRegistro12_126_129 = null;
    public $SFPRegistro12_130_134 = null;
    public $SFPRegistro12_135_140 = null;
    public $SFPRegistro12_141_146 = null;
    public $SFPRegistro12_147_161 = null;
    public $SFPRegistro12_162_167 = null;
    public $SFPRegistro12_168_173 = null;
    public $SFPRegistro12_174_188 = null;
    public $SFPRegistro12_189_203 = null;
    public $SFPRegistro12_204_218 = null;
    public $SFPRegistro12_219_233 = null;
    public $SFPRegistro12_234_248 = null;
    public $SFPRegistro12_249_263 = null;
    public $SFPRegistro12_264_278 = null;
    public $SFPRegistro12_279_293 = null;
    public $SFPRegistro12_294_308 = null;
    public $SFPRegistro12_309_353 = null;
    public $SFPRegistro12_354_359 = "000000";
    public $SFPRegistro12_360_360 = null;

    /*
    INCLUSÃO / ALTERAÇÃO ENDEREÇO DO TRABALHADOR - REGISTRO '14'
*/
    public $SFPRegistro14_001_002 = null;
    public $SFPRegistro14_003_003 = null;
    public $SFPRegistro14_004_017 = null;
    public $SFPRegistro14_018_053 = null;
    public $SFPRegistro14_054_064 = null;
    public $SFPRegistro14_065_072 = null;
    public $SFPRegistro14_073_074 = null;
    public $SFPRegistro14_075_144 = null;
    public $SFPRegistro14_145_151 = null;
    public $SFPRegistro14_152_156 = null;
    public $SFPRegistro14_157_206 = null;
    public $SFPRegistro14_207_226 = null;
    public $SFPRegistro14_227_234 = null;
    public $SFPRegistro14_235_254 = null;
    public $SFPRegistro14_255_256 = null;
    public $SFPRegistro14_257_359 = null;
    public $SFPRegistro14_360_360 = null;
    /*
    REGISTRO DO TRABALHADOR - REGISTRO '30'
*/
    public $SFPRegistro30_001_002 = null;
    public $SFPRegistro30_003_003 = null;
    public $SFPRegistro30_004_017 = null;
    public $SFPRegistro30_018_018 = null;
    public $SFPRegistro30_019_032 = null;
    public $SFPRegistro30_033_043 = null;
    public $SFPRegistro30_044_051 = null;
    public $SFPRegistro30_052_053 = null;
    public $SFPRegistro30_054_123 = null;
    public $SFPRegistro30_124_134 = null;
    public $SFPRegistro30_135_141 = null;
    public $SFPRegistro30_142_146 = null;
    public $SFPRegistro30_147_154 = null;
    public $SFPRegistro30_155_162 = null;
    public $SFPRegistro30_163_167 = null;
    public $SFPRegistro30_168_182 = null;
    public $SFPRegistro30_183_197 = null;
    public $SFPRegistro30_198_199 = null;
    public $SFPRegistro30_200_201 = null;
    public $SFPRegistro30_202_216 = null;
    public $SFPRegistro30_217_231 = null;
    public $SFPRegistro30_232_246 = null;
    public $SFPRegistro30_247_261 = null;
    public $SFPRegistro30_262_359 = null;
    public $SFPRegistro30_360_360 = null;
    /*
    MOVIMENTAÇÃO DO TRABALHADOR - REGISTRO '32'
*/
    public $SFPRegistro32_001_002 = null;
    public $SFPRegistro32_003_003 = null;
    public $SFPRegistro32_004_017 = null;
    public $SFPRegistro32_018_018 = null;
    public $SFPRegistro32_019_032 = null;
    public $SFPRegistro32_033_043 = null;
    public $SFPRegistro32_044_051 = null;
    public $SFPRegistro32_052_053 = null;
    public $SFPRegistro32_054_123 = null;
    public $SFPRegistro32_124_125 = null;
    public $SFPRegistro32_126_133 = null;
    public $SFPRegistro32_134_134 = null;
    public $SFPRegistro32_135_359 = null;
    public $SFPRegistro32_360_360 = null;
    /*
    REGISTRO TOTALIZADOR DO ARQUIVO - REGISTRO '90'
*/
    public $SFPRegistro90_001_002 = null;
    public $SFPRegistro90_003_053 = null;
    public $SFPRegistro90_054_359 = null;
    public $SFPRegistro90_360_360 = null;
    /***************************************************************************************************/

    public $SFPRegistro20_001_002 = null;
    public $SFPRegistro20_003_003 = null;
    public $SFPRegistro20_004_017 = null;
    public $SFPRegistro20_018_018 = null;
    public $SFPRegistro20_019_032 = null;
    public $SFPRegistro20_033_053 = null;
    public $SFPRegistro20_054_093 = null;
    public $SFPRegistro20_094_143 = null;
    public $SFPRegistro20_144_163 = null;
    public $SFPRegistro20_164_171 = null;
    public $SFPRegistro20_172_191 = null;
    public $SFPRegistro20_192_193 = null;
    public $SFPRegistro20_194_197 = null;
    public $SFPRegistro20_198_212 = null;
    public $SFPRegistro20_213_227 = null;
    public $SFPRegistro20_228_228 = null;
    public $SFPRegistro20_229_242 = null;
    public $SFPRegistro20_243_257 = null;
    public $SFPRegistro20_258_272 = null;
    public $SFPRegistro20_273_317 = null;
    public $SFPRegistro20_318_359 = null;
    public $SFPRegistro20_360_360 = null;

    public $arquivo = null;
    public $nomearq = '/tmp/SEFIP.RE';

//////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////      MÉTODOS LAYOUT DA SEFIP      /////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////

    public function geraRegist00SFP()
    {
        $this->arquivo = fopen($this->nomearq, "w");
        fputs($this->arquivo,
            "00"
            . str_repeat(" ", 51)
            . "1"
            . "1"
            . db_formatar(str_replace('.', '', str_replace('-', '', str_replace("/", '', $this->SFPRegistro00_056_069))), "s", "0", 14, "e", 0)
            . db_formatar(strtoupper(substr(db_translate($this->SFPRegistro00_070_099), 0, 30)), "s", " ", 30, "d", 0)
            . db_formatar(strtoupper(substr(db_translate($this->SFPRegistro00_100_119), 0, 20)), "s", " ", 20, "d", 0)
            . db_formatar(strtoupper(substr(db_translate($this->SFPRegistro00_120_169), 0, 50)), "s", " ", 50, "d", 0)
            . db_formatar(strtoupper(substr(db_translate($this->SFPRegistro00_170_189), 0, 20)), "s", " ", 20, "d", 0)
            . db_formatar(str_replace('-', '', str_replace('.', '', substr($this->SFPRegistro00_190_197, 0, 8))), "s", "0", 8, "e", 0)
            . db_formatar(strtoupper(substr(db_translate($this->SFPRegistro00_198_217), 0, 20)), "s", " ", 20, "d", 0)
            . db_formatar(strtoupper(substr(db_translate($this->SFPRegistro00_218_219), 0, 2)), "s", " ", 2, "d", 0)
            . db_formatar($this->SFPRegistro00_220_231, "s", "0", 12, "e", 0)
            . db_formatar(strtolower(substr(db_translate($this->SFPRegistro00_232_291), 0, 60)), "s", " ", 60, "d", 0)
            . db_formatar(strtolower(substr(db_translate(str_replace("/", '', $this->SFPRegistro00_292_297)), 0, 6)), "s", "0", 6, "e", 0)
            . db_formatar($this->SFPRegistro00_298_300, "s", "0", 3, "e", 0)
            . db_formatar($this->SFPRegistro00_301_301, "s", " ", 1, "d", 0)
            . db_formatar($this->SFPRegistro00_302_302, "s", " ", 1, "d", 0)
            . db_formatar(str_replace('-', '', str_replace("/", '', $this->SFPRegistro00_303_310)), "s", "0", 8, "e", 0)
            . $this->SFPRegistro00_311_311
            . db_formatar(str_replace('-', '', str_replace("/", '', $this->SFPRegistro00_312_319)), "s", "0", 8, "e", 0)
            . db_formatar($this->SFPRegistro00_320_326, "s", " ", 7, "e", 0)
            . "1"
            . db_formatar(str_replace('.', '', str_replace('-', '', str_replace("/", '', $this->SFPRegistro00_328_341))), "s", "0", 14, "e", 0)
            . str_repeat(" ", 18)
            . "*"
            . "\r\n"
        );
    }

    public function geraRegist10SFP()
    {
        fputs($this->arquivo,
            "10"
            . "1"
            . db_formatar($this->SFPRegistro10_004_017, "s", "0", 14, "e", 0)
            . str_repeat("0", 36)
            . db_formatar(strtoupper(substr(db_translate($this->SFPRegistro10_054_093), 0, 40)), "s", " ", 40, "d", 0)
            . db_formatar(strtoupper(substr(db_translate($this->SFPRegistro10_094_143), 0, 50)), "s", " ", 50, "d", 0)
            . db_formatar(strtoupper(substr(db_translate($this->SFPRegistro10_144_163), 0, 20)), "s", " ", 20, "d", 0)
            . db_formatar(str_replace('-', '', str_replace('.', '', substr($this->SFPRegistro10_164_171, 0, 8))), "s", "0", 8, "e", 0)
            . db_formatar(strtoupper(substr(db_translate($this->SFPRegistro10_172_191), 0, 20)), "s", " ", 20, "d", 0)
            . db_formatar(strtoupper(substr(db_translate($this->SFPRegistro10_192_193), 0, 2)), "s", " ", 2, "d", 0)
            . db_formatar($this->SFPRegistro10_194_205, "s", "0", 12, "e", 0)
            . strtoupper($this->SFPRegistro10_206_206)
            . db_formatar($this->SFPRegistro10_207_213, "s", "0", 7, "e", 0)
            . strtoupper($this->SFPRegistro10_214_214)
            . db_formatar($this->SFPRegistro10_215_216, "s", "0", 2, "d", 0)
            . "0"
            . "1"
            . db_formatar($this->SFPRegistro10_219_221, "s", "0", 3, "e", 0)
            . db_formatar($this->SFPRegistro10_222_225, "s", "0", 4, "e", 0)
            . db_formatar($this->SFPRegistro10_226_229, "s", "0", 4, "e", 0)
            . str_repeat(" ", 5)
            . db_formatar(str_replace(',', '', str_replace('.', '', trim(db_formatar($this->SFPRegistro10_235_249, "f")))), "s", "0", 15, "e", 0)
            . db_formatar(str_replace(',', '', str_replace('.', '', trim(db_formatar($this->SFPRegistro10_250_264, "f")))), "s", "0", 15, "e", 0)
            . str_repeat("0", 30)
            . str_repeat(" ", 16)
            . str_repeat("0", 45)
            . str_repeat(" ", 4)
            . "*"
            . "\r\n"
        );
    }

    public function geraRegist12SFP()
    {
        fputs($this->arquivo,
            "12"
            . "1"
            . db_formatar($this->SFPRegistro12_004_017, "s", "0", 14, "e", 0)
            . str_repeat("0", 36)
            . trim(str_pad($this->SFPRegistro12_054_068, 15, "0", STR_PAD_LEFT))
            . str_repeat("0", 15)
            . " "
            . trim(str_pad($this->SFPRegistro12_085_099, 15, "0", STR_PAD_LEFT))
            . str_pad($this->SFPRegistro12_100_114, 15, "0", STR_PAD_LEFT)
            . str_pad($this->SFPRegistro12_115_125, 11, " ", STR_PAD_LEFT)
            . str_pad($this->SFPRegistro12_126_129, 4, " ", STR_PAD_LEFT)
            . str_pad($this->SFPRegistro12_130_134, 5, " ", STR_PAD_LEFT)
            . str_pad($this->SFPRegistro12_135_140, 6, " ", STR_PAD_LEFT)
            . str_pad($this->SFPRegistro12_141_146, 6, " ", STR_PAD_LEFT)
            . db_formatar(str_replace('-', '', str_replace('.', '', substr($this->SFPRegistro12_147_161, 0, 15))), "s", "0", 15, "e", 0)
            . str_pad($this->SFPRegistro12_162_167, 6, " ", STR_PAD_LEFT)
            . str_pad($this->SFPRegistro12_168_173, 6, " ", STR_PAD_LEFT)
            . db_formatar($this->SFPRegistro12_174_188, "s", "0", 15, "e", 0)
            . db_formatar($this->SFPRegistro12_189_203, "s", "0", 15, "e", 0)
            . db_formatar($this->SFPRegistro12_204_218, "s", "0", 15, "e", 0)
            . db_formatar($this->SFPRegistro12_219_233, "s", "0", 15, "e", 0)
            . db_formatar($this->SFPRegistro12_234_248, "s", "0", 15, "e", 0)
            . db_formatar($this->SFPRegistro12_249_263, "s", "0", 15, "e", 0)
            . db_formatar($this->SFPRegistro12_264_278, "s", "0", 15, "e", 0)
            . db_formatar($this->SFPRegistro12_279_293, "s", "0", 15, "e", 0)
            . db_formatar($this->SFPRegistro12_294_308, "s", "0", 15, "e", 0)
            . str_repeat("0", 45)
            . str_repeat(chr(32), 6)
            . "*"
            . "\r\n"
        );
    }

    public function geraRegist14SFP()
    {
        fputs($this->arquivo,
            "14"
            . "1"
            . db_formatar($this->SFPRegistro14_004_017, "s", "0", 14, "e", 0)
            . str_repeat("0", 36)
            . db_formatar(str_replace('-', '', str_replace("/", '', str_replace('.', '', substr($this->SFPRegistro14_054_064, 0, 11)))), "s", "0", 11, "e", 0)
            . db_formatar(str_replace('-', '', str_replace("/", '', $this->SFPRegistro14_065_072)), "s", "0", 8, "e", 0)
            . db_formatar($this->SFPRegistro14_073_074, "s", "0", 2, "e", 0)
            . db_formatar(strtoupper(substr(db_translate($this->SFPRegistro14_075_144), 0, 70)), "s", " ", 70, "d", 0)
            . db_formatar((((int)$this->SFPRegistro14_073_074 >= 12) ? str_replace('-', '', str_replace("/", '', str_replace('.', '', substr($this->SFPRegistro14_145_151, 0, 7)))) : str_repeat(" ", 7)), "s", "0", 7, "e", 0)
            . db_formatar((((int)$this->SFPRegistro14_073_074 >= 12) ? str_replace('-', '', str_replace("/", '', str_replace('.', '', substr($this->SFPRegistro14_152_156, 0, 5)))) : str_repeat(" ", 5)), "s", "0", 5, "e", 0)
            . db_formatar(strtoupper(substr(db_translate($this->SFPRegistro14_157_206), 0, 50)), "s", " ", 50, "d", 0)
            . db_formatar(strtoupper(substr(db_translate($this->SFPRegistro14_207_226), 0, 20)), "s", " ", 20, "d", 0)
            . db_formatar(str_replace('-', '', str_replace('.', '', substr($this->SFPRegistro14_227_234, 0, 8))), "s", "0", 8, "e", 0)
            . db_formatar(strtoupper(substr(db_translate($this->SFPRegistro14_235_254), 0, 20)), "s", " ", 20, "d", 0)
            . db_formatar(strtoupper(substr(db_translate($this->SFPRegistro14_255_256), 0, 2)), "s", " ", 2, "d", 0)
            . str_repeat(" ", 103)
            . "*"
            . "\r\n"
        );
    }

    /**
     * Gera registros tipo 20
     */
    public function geraRegist20SFP()
    {
        fputs($this->arquivo,
            str_pad($this->SFPRegistro20_001_002, 2, "0")
            . str_pad($this->SFPRegistro20_003_003, 1, "0")
            . str_pad($this->SFPRegistro20_004_017, 14, "0")
            . str_pad($this->SFPRegistro20_018_018, 1, "0")
            . str_pad($this->SFPRegistro20_019_032, 14, "0")
            . str_pad($this->SFPRegistro20_033_053, 21, "0")
            . str_pad($this->SFPRegistro20_054_093, 40)
            . str_pad($this->SFPRegistro20_094_143, 50)
            . str_pad($this->SFPRegistro20_144_163, 20)
            . str_pad($this->SFPRegistro20_164_171, 8, "0")
            . str_pad($this->SFPRegistro20_172_191, 20)
            . str_pad($this->SFPRegistro20_192_193, 2)
            . str_pad($this->SFPRegistro20_194_197, 4)
            . str_pad($this->SFPRegistro20_198_212, 15, "0")
            . str_pad($this->SFPRegistro20_213_227, 15, "0")
            . str_pad($this->SFPRegistro20_228_228, 1, "0")
            . str_pad($this->SFPRegistro20_229_242, 14, "0")
            . str_pad($this->SFPRegistro20_243_257, 15, "0")
            . str_pad($this->SFPRegistro20_258_272, 15, "0")
            . str_pad($this->SFPRegistro20_273_317, 45, "0")
            . str_pad($this->SFPRegistro20_318_359, 42)
            . "*"
            . "\r\n"
        );
    }

    public function geraRegist30SFP()
    {
        fputs($this->arquivo,
            "30"
            . "1"
            . db_formatar($this->SFPRegistro30_004_017, "s", "0", 14, "e", 0)
            . str_pad($this->SFPRegistro30_018_018, 1)
            . str_pad($this->SFPRegistro30_019_032, 14)
            . db_formatar(str_replace('-', '', str_replace("/", '', str_replace('.', '', substr($this->SFPRegistro30_033_043, 0, 11)))), "s", "0", 11, "e", 0)
            . db_formatar(str_replace('-', '', str_replace("/", '', $this->SFPRegistro30_044_051)), "s", "0", 8, "e", 0)
            . db_formatar($this->SFPRegistro30_052_053, "s", "0", 2, "e", 0)
            . db_formatar(strtoupper(substr(db_translate($this->SFPRegistro30_054_123), 0, 70)), "s", " ", 70, "d", 0)
            . db_formatar($this->SFPRegistro30_124_134, "s", "0", 11, "e", 0)
            . $this->SFPRegistro30_135_141
            . $this->SFPRegistro30_142_146
            . db_formatar(str_replace('-', '', str_replace("/", '', $this->SFPRegistro30_147_154)), "s", "0", 8, "e", 0)
            . db_formatar(str_replace('-', '', str_replace("/", '', $this->SFPRegistro30_155_162)), "s", "0", 8, "e", 0)
            . db_formatar(str_replace('-', '', str_replace("/", '', str_replace('.', '', substr($this->SFPRegistro30_163_167, 0, 4)))), "s", "0", 5, "e", 0)
            . db_formatar(str_replace(',', '', str_replace('.', '', trim(db_formatar($this->SFPRegistro30_168_182, "f")))), "s", "0", 15, "e", 0)
            . db_formatar(str_replace(',', '', str_replace('.', '', trim(db_formatar($this->SFPRegistro30_183_197, "f")))), "s", "0", 15, "e", 0)
            . str_repeat(" ", 2)
            . db_formatar($this->SFPRegistro30_200_201, "s", "0", 2, "e", 0)
            . db_formatar(str_replace(',', '', str_replace('.', '', trim(db_formatar($this->SFPRegistro30_202_216, "f")))), "s", "0", 15, "e", 0)
            . db_formatar(str_replace(',', '', str_replace('.', '', trim(db_formatar($this->SFPRegistro30_217_231, "f")))), "s", "0", 15, "e", 0)
            . db_formatar(str_replace(',', '', str_replace('.', '', trim(db_formatar($this->SFPRegistro30_232_246, "f")))), "s", "0", 15, "e", 0)
            . db_formatar(str_replace(',', '', str_replace('.', '', trim(db_formatar($this->SFPRegistro30_247_261, "f")))), "s", "0", 15, "e", 0)
            . str_repeat(" ", 98)
            . "*"
            . "\r\n"
        );
    }

    public function geraRegist32SFP()
    {
        fputs($this->arquivo,
            "32"
            . "1"
            . db_formatar($this->SFPRegistro32_004_017, "s", "0", 14, "e", 0)
            . str_pad($this->SFPRegistro32_018_018, 1)
            . str_pad($this->SFPRegistro32_019_032, 14)
            . db_formatar(str_replace('-', '', str_replace("/", '', str_replace('.', '', substr($this->SFPRegistro32_033_043, 0, 11)))), "s", "0", 11, "e", 0)
            . db_formatar(str_replace('-', '', str_replace("/", '', $this->SFPRegistro32_044_051)), "s", "0", 8, "e", 0)
            . db_formatar($this->SFPRegistro32_052_053, "s", "0", 2, "e", 0)
            . db_formatar(strtoupper(substr(db_translate($this->SFPRegistro32_054_123), 0, 70)), "s", " ", 70, "d", 0)
            . db_formatar($this->SFPRegistro32_124_125, "s", " ", 2, "d", 0)
            . db_formatar(str_replace('-', '', str_replace("/", '', $this->SFPRegistro32_126_133)), "s", "0", 8, "e", 0)
            . db_formatar($this->SFPRegistro32_134_134, "s", " ", 1, "e", 0)
            . str_repeat(" ", 225)
            . "*"
            . "\r\n"
        );
    }

    public function geraRegist90SFP()
    {
        fputs($this->arquivo,
            "90"
            . str_repeat("9", 51)
            . str_repeat(" ", 306)
            . "*"
            . "\r\n"
        );
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////      FINAL MÉTODOS ARQUIVO DA SEFIP      /////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////
//          FECHA O ARQUIVO           //
////////////////////////////////////////
    public function gera()
    {
        fclose($this->arquivo);
    }
////////////////////////////////////////
}


//MODULO: PESSOAL
class cl_layout_CAGED
{

    /***************************************************************************************************/
    /***************      TXT - publiciável que retorna texto a ser impresso no arquivo     ***************/
    /***************************************************************************************************/

    public $TEXTO = null;

    /***************************************************************************************************/

    /***************************************************************************************************/
    /***************************************************************************************************/
    /*********************       publicIÁVEIS USADAS PARA GERAR ARQUIVO DA CAGED      *********************/
    /***************************************************************************************************/
    /***************************************************************************************************/
    /*
    AUTORIZADO - REGISTRO 'A'
*/
    public $KGDRegistroA_001_001 = null;
    public $KGDRegistroA_002_002 = null;
    public $KGDRegistroA_003_008 = null;
    public $KGDRegistroA_009_009 = null;
    public $KGDRegistroA_010_011 = null;
    public $KGDRegistroA_012_015 = null;
    public $KGDRegistroA_016_016 = null;
    public $KGDRegistroA_017_021 = null;
    public $KGDRegistroA_022_022 = null;
    public $KGDRegistroA_023_036 = null;
    public $KGDRegistroA_037_071 = null;
    public $KGDRegistroA_072_111 = null;
    public $KGDRegistroA_112_119 = null;
    public $KGDRegistroA_120_121 = null;
    public $KGDRegistroA_122_125 = null;
    public $KGDRegistroA_126_133 = null;
    public $KGDRegistroA_134_138 = null;
    public $KGDRegistroA_139_143 = null;
    public $KGDRegistroA_144_148 = null;
    public $KGDRegistroA_149_150 = null;
    /*
    ESTABELECIMENTO - REGISTRO 'B'
*/
    public $KGDRegistroB_001_001 = null;
    public $KGDRegistroB_002_002 = null;
    public $KGDRegistroB_003_016 = null;
    public $KGDRegistroB_017_021 = null;
    public $KGDRegistroB_022_022 = null;
    public $KGDRegistroB_023_023 = null;
    public $KGDRegistroB_024_031 = null;
    public $KGDRegistroB_032_036 = null;
    public $KGDRegistroB_037_076 = null;
    public $KGDRegistroB_077_116 = null;
    public $KGDRegistroB_117_136 = null;
    public $KGDRegistroB_137_138 = null;
    public $KGDRegistroB_139_143 = null;
    public $KGDRegistroB_144_144 = null;
    public $KGDRegistroB_145_150 = null;
    /*
    EMPREGADO - REGISTRO 'C'
*/
    public $KGDRegistroC_001_001 = null;
    public $KGDRegistroC_002_002 = null;
    public $KGDRegistroC_003_016 = null;
    public $KGDRegistroC_017_021 = null;
    public $KGDRegistroC_022_032 = null;
    public $KGDRegistroC_033_033 = null;
    public $KGDRegistroC_034_041 = null;
    public $KGDRegistroC_042_042 = null;
    public $KGDRegistroC_043_047 = null;
    public $KGDRegistroC_048_055 = null;
    public $KGDRegistroC_056_057 = null;
    public $KGDRegistroC_058_065 = null;
    public $KGDRegistroC_066_067 = null;
    public $KGDRegistroC_068_069 = null;
    public $KGDRegistroC_070_109 = null;
    public $KGDRegistroC_110_117 = null;
    public $KGDRegistroC_118_121 = null;
    public $KGDRegistroC_122_128 = null;
    public $KGDRegistroC_129_129 = null;
    public $KGDRegistroC_130_130 = null;
    public $KGDRegistroC_131_136 = null;
    public $KGDRegistroC_137_137 = null;
    public $KGDRegistroC_138_139 = null;
    public $KGDRegistroC_140_150 = null;
    /*
    ACERTO - REGISTRO 'X'
*/
    public $KGDRegistroX_001_001 = null;
    public $KGDRegistroX_002_002 = null;
    public $KGDRegistroX_003_016 = null;
    public $KGDRegistroX_017_021 = null;
    public $KGDRegistroX_022_032 = null;
    public $KGDRegistroX_033_033 = null;
    public $KGDRegistroX_034_041 = null;
    public $KGDRegistroX_042_042 = null;
    public $KGDRegistroX_043_047 = null;
    public $KGDRegistroX_048_055 = null;
    public $KGDRegistroX_056_057 = null;
    public $KGDRegistroX_058_065 = null;
    public $KGDRegistroX_066_067 = null;
    public $KGDRegistroX_068_069 = null;
    public $KGDRegistroX_070_109 = null;
    public $KGDRegistroX_110_117 = null;
    public $KGDRegistroX_118_121 = null;
    public $KGDRegistroX_122_122 = null;
    public $KGDRegistroX_123_124 = null;
    public $KGDRegistroX_125_128 = null;
    public $KGDRegistroX_129_129 = null;
    public $KGDRegistroX_130_130 = null;
    public $KGDRegistroX_131_136 = null;
    public $KGDRegistroX_137_137 = null;
    public $KGDRegistroX_138_139 = null;
    public $KGDRegistroX_140_150 = null;
    /***************************************************************************************************/

    public $arquivo = null;
    public $nomearq = '/tmp/CAGED.TXT';

//////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////      MÉTODOS LAYOUT DA SEFIP      /////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////

    public function geraRegistAKGD()
    {
        $this->arquivo = fopen($this->nomearq, "w");
        fputs($this->arquivo,
            "A"
            . "2"
            . db_formatar(substr($this->KGDRegistroA_003_008, 0, 6), "s", "0", 6, "e", 0)
            . db_formatar(substr($this->KGDRegistroA_009_009, 0, 1), "s", "0", 1, "e", 0)
            . db_formatar(substr($this->KGDRegistroA_010_011, 0, 2), "s", "0", 2, "e", 0)
            . db_formatar(substr($this->KGDRegistroA_012_015, 0, 4), "s", "0", 4, "e", 0)
            . db_formatar(substr($this->KGDRegistroA_016_016, 0, 1), "s", "0", 1, "e", 0)
            . db_formatar(substr($this->KGDRegistroA_017_021, 0, 5), "s", "0", 5, "e", 0)
            . db_formatar(substr($this->KGDRegistroA_022_022, 0, 1), "s", "0", 1, "e", 0)
            . db_formatar(substr($this->KGDRegistroA_023_036, 0, 14), "s", "0", 14, "e", 0)
            . db_formatar(substr(strtoupper(db_translate($this->KGDRegistroA_037_071)), 0, 35), "s", " ", 35, "d", 0)
            . db_formatar(substr(strtoupper(db_translate($this->KGDRegistroA_072_111)), 0, 40), "s", " ", 40, "d", 0)
            . db_formatar(substr(str_replace('-', '', str_replace('.', '', $this->KGDRegistroA_112_119)), 0, 8), "s", "0", 8, "e", 0)
            . db_formatar(substr(strtoupper(db_translate($this->KGDRegistroA_120_121)), 0, 2), "s", " ", 2, "d", 0)
            . db_formatar(substr($this->KGDRegistroA_122_125, 0, 4), "s", "0", 4, "e", 0)
            . db_formatar(substr(str_replace('.', '', str_replace('-', '', str_replace("/", '', $this->KGDRegistroA_126_133))), 0, 8), "s", "0", 8, "e", 0)
            . db_formatar(substr(str_replace('.', '', str_replace('-', '', str_replace("/", '', $this->KGDRegistroA_134_138))), 0, 5), "s", "0", 5, "e", 0)
            . db_formatar(substr($this->KGDRegistroA_139_143, 0, 5), "s", "0", 5, "e", 0)
            . db_formatar(substr($this->KGDRegistroA_144_148, 0, 5), "s", "0", 5, "e", 0)
            . "  "
            . "\r\n"
        );
    }

    public function geraRegistBKGD()
    {
        fputs($this->arquivo,
            "B"
            . "1"
            . db_formatar(substr($this->KGDRegistroB_003_016, 0, 14), "s", "0", 14, "e", 0)
            . db_formatar(substr($this->KGDRegistroB_017_021, 0, 5), "s", "0", 5, "e", 0)
            . db_formatar(substr($this->KGDRegistroB_022_022, 0, 1), "s", "0", 1, "e", 0)
            . db_formatar(substr($this->KGDRegistroB_023_023, 0, 1), "s", "0", 1, "e", 0)
            . db_formatar(substr(str_replace('-', '', str_replace('.', '', $this->KGDRegistroB_024_031)), 0, 8), "s", "0", 8, "e", 0)
            . db_formatar(substr($this->KGDRegistroB_032_036, 0, 5), "s", "0", 5, "e", 0)
            . db_formatar(substr(strtoupper(db_translate($this->KGDRegistroB_037_076)), 0, 40), "s", " ", 40, "d", 0)
            . db_formatar(substr(strtoupper(db_translate($this->KGDRegistroB_077_116)), 0, 40), "s", " ", 40, "d", 0)
            . db_formatar(substr(strtoupper(db_translate($this->KGDRegistroB_117_136)), 0, 20), "s", " ", 20, "d", 0)
            . db_formatar(substr(strtoupper(db_translate($this->KGDRegistroB_137_138)), 0, 2), "s", " ", 2, "d", 0)
            . db_formatar(substr($this->KGDRegistroB_139_143, 0, 5), "s", "0", 5, "e", 0)
            . "2"
            . db_formatar(substr($this->KGDRegistroB_145_150, 0, 6), "s", " ", 6, "d", 0)
            . "\r\n"
        );
    }

    public function geraRegistCKGD()
    {
        fputs($this->arquivo,
            "C"
            . "1"
            . db_formatar(substr($this->KGDRegistroC_003_016, 0, 14), "s", "0", 14, "e", 0)
            . db_formatar(substr($this->KGDRegistroC_017_021, 0, 5), "s", "0", 5, "e", 0)
            . db_formatar(substr(str_replace('.', '', str_replace('-', '', str_replace("/", '', $this->KGDRegistroC_022_032))), 0, 11), "s", "0", 11, "e", 0)
            . db_formatar(substr($this->KGDRegistroC_033_033, 0, 1), "s", "0", 1, "e", 0)
            . str_replace("/", '', db_formatar($this->KGDRegistroC_034_041, "d"))
            . db_formatar(substr($this->KGDRegistroC_042_042, 0, 1), "s", "0", 1, "e", 0)
            . str_repeat(" ", 5)
            . db_formatar(substr(str_replace(',', '', str_replace('.', '', trim(db_formatar($this->KGDRegistroC_048_055, "f")))), 0, 8), 's', '0', 8, 'e', 0)
            . db_formatar(substr($this->KGDRegistroC_056_057, 0, 2), "s", "0", 2, "e", 0)
            . str_replace("/", '', db_formatar($this->KGDRegistroC_058_065, "d"))
            . db_formatar(substr($this->KGDRegistroC_066_067, 0, 2), "s", "0", 2, "e", 0)
            . db_formatar(substr($this->KGDRegistroC_068_069, 0, 2), "s", "0", 2, "e", 0)
            . db_formatar(substr(strtoupper(db_translate($this->KGDRegistroC_070_109)), 0, 40), "s", " ", 40, "d", 0)
            . db_formatar(substr($this->KGDRegistroC_110_117, 0, 8), "s", "0", 8, "e", 0)
            . db_formatar(substr($this->KGDRegistroC_118_121, 0, 4), "s", "0", 4, "e", 0)
            . db_formatar(substr($this->KGDRegistroC_122_128, 0, 7), "s", " ", 7, "d", 0)
            . db_formatar(substr($this->KGDRegistroC_129_129, 0, 1), "s", "0", 1, "e", 0)
            . "2"
            . db_formatar(substr($this->KGDRegistroC_131_136, 0, 6), "s", "0", 6, "e", 0)
            . "2"
            . db_formatar(substr($this->KGDRegistroC_138_139, 0, 2), "s", " ", 2, "d", 0)
            . db_formatar(substr($this->KGDRegistroC_140_150, 0, 11), "s", " ", 11, "d", 0)
            . "\r\n"
        );
    }

    public function geraRegistXKGD()
    {
        fputs($this->arquivo,
            "X"
            . "1"
            . db_formatar(substr($this->KGDRegistroX_003_016, 0, 14), "s", "0", 14, "e", 0)
            . db_formatar(substr($this->KGDRegistroX_017_021, 0, 6), "s", "0", 5, "e", 0)
            . db_formatar(substr(str_replace('.', '', str_replace('-', '', str_replace("/", '', $this->KGDRegistroX_022_032))), 0, 11), "s", "0", 11, "e", 0)
            . db_formatar(substr($this->KGDRegistroX_033_033, 0, 1), "s", "0", 1, "e", 0)
            . str_replace("/", '', db_formatar($this->KGDRegistroX_034_041, "d"))
            . db_formatar(substr($this->KGDRegistroX_042_042, 0, 1), "s", "0", 1, "e", 0)
            . str_repeat(" ", 5)
            . db_formatar(substr(str_replace(',', '', str_replace('.', '', trim(db_formatar($this->KGDRegistroX_048_055, "f")))), 0, 8), 's', '0', 8, 'e', 0)
            . db_formatar(substr($this->KGDRegistroX_056_057, 0, 2), "s", "0", 2, "e", 0)
            . str_replace("/", '', db_formatar($this->KGDRegistroX_058_065, "d"))
            . db_formatar(substr($this->KGDRegistroX_066_067, 0, 2), "s", "0", 2, "e", 0)
            . db_formatar(substr($this->KGDRegistroX_068_069, 0, 2), "s", "0", 2, "e", 0)
            . db_formatar(substr(strtoupper(db_translate($this->KGDRegistroX_070_109)), 0, 40), "s", " ", 40, "d", 0)
            . db_formatar(substr($this->KGDRegistroX_110_117, 0, 8), "s", "0", 8, "e", 0)
            . db_formatar(substr($this->KGDRegistroX_118_121, 0, 4), "s", "0", 4, "e", 0)
            . "2"
            . db_formatar(substr($this->KGDRegistroX_123_124, 0, 2), "s", "0", 2, "e", 0)
            . db_formatar(substr($this->KGDRegistroX_125_128, 0, 4), "s", "0", 4, "e", 0)
            . db_formatar(substr($this->KGDRegistroX_129_129, 0, 1), "s", "0", 1, "e", 0)
            . "2"
            . db_formatar(substr($this->KGDRegistroX_131_136, 0, 6), "s", "0", 6, "e", 0)
            . "2"
            . db_formatar(substr($this->KGDRegistroX_138_139, 0, 2), "s", " ", 2, "d", 0)
            . db_formatar(substr($this->KGDRegistroX_140_150, 0, 11), "s", " ", 11, "d", 0)
            . "\r\n"
        );
    }

//////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////      FINAL MÉTODOS ARQUIVO DA SEFIP      /////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////
//          FECHA O ARQUIVO           //
////////////////////////////////////////
    public function gera()
    {
        fclose($this->arquivo);
    }
////////////////////////////////////////
}


//MODULO: PESSOAL

class cl_layout_IPE
{

    /***************************************************************************************************/
    /***************      TXT - publiciável que retorna texto a ser impresso no arquivo     ***************/
    /***************************************************************************************************/

    public $TEXTO = null;

    /***************************************************************************************************/

    /***************************************************************************************************/
    /***************************************************************************************************/
    /**********************       publicIÁVEIS USADAS PARA GERAR ARQUIVO DO IPE      **********************/
    /***************************************************************************************************/
    /***************************************************************************************************/
    /*
    HEADER DE ARQUIVO
*/
    public $IPEHeader_001_003 = null;
    public $IPEHeader_004_011 = null;
    public $IPEHeader_012_017 = null;
    public $IPEHeader_018_018 = null;
    public $IPEHeader_019_250 = null;
    /*
    REGISTRO
*/
    public $IPERegistro_001_003 = null;
    public $IPERegistro_004_011 = null;
    public $IPERegistro_012_024 = null;
    public $IPERegistro_025_026 = null;
    public $IPERegistro_027_058 = null;
    public $IPERegistro_059_098 = null;
    public $IPERegistro_099_106 = null;
    public $IPERegistro_107_114 = null;
    public $IPERegistro_115_122 = null;
    public $IPERegistro_123_130 = null;
    public $IPERegistro_131_131 = null;
    public $IPERegistro_132_132 = null;
    public $IPERegistro_133_142 = null;
    public $IPERegistro_143_153 = null;
    public $IPERegistro_154_164 = null;
    public $IPERegistro_165_250 = null;
    /*
    TRAILLER
*/
    public $IPETrailler_001_003 = null;
    public $IPETrailler_004_011 = null;
    public $IPETrailler_012_016 = null;
    public $IPETrailler_017_033 = null;
    public $IPETrailler_034_250 = null;
    /***************************************************************************************************/

    public $arquivo = null;
    public $nomearq = null;

    // Construtor
    public function __construct()
    {
        $this->nomearq = '/tmp/IPE' . date("mY") . '.TXT';
    }


//////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////      MÉTODOS LAYOUT DA SEFIP      /////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////

    public function geraHeaderIPE()
    {
        $this->arquivo = fopen($this->nomearq, "w");
        fputs($this->arquivo,
            db_formatar(substr($this->IPEHeader_001_003, 0, 3), "s", "0", 3, "e", 0)
            . str_repeat("0", 8)
            . db_formatar(substr($this->IPEHeader_012_017, 0, 6), "s", "0", 6, "e", 0)
            . db_formatar(substr($this->IPEHeader_018_018, 0, 1), "s", "0", 1, "e", 0)
            . str_repeat(" ", 232)
            . "\r\n"
        );
    }

    public function geraRegistIPE()
    {
        fputs($this->arquivo,
            db_formatar(substr($this->IPERegistro_001_003, 0, 3), "s", "0", 3, "e", 0)
            . db_formatar(substr($this->IPERegistro_004_011, 0, 8), "s", "0", 8, "e", 0)
            . db_formatar(substr($this->IPERegistro_012_024, 0, 13), "s", "0", 13, "e", 0)
            . db_formatar(substr($this->IPERegistro_025_026, 0, 2), "s", "0", 2, "e", 0)
            . db_formatar(substr(strtoupper(db_translate($this->IPERegistro_027_058)), 0, 32), "s", " ", 32, "d", 0)
            . db_formatar(substr(strtoupper(db_translate($this->IPERegistro_059_098)), 0, 40), "s", " ", 40, "d", 0)
            . db_formatar(substr(str_replace("/", '', str_replace('-', '', str_replace('.', '', $this->IPERegistro_099_106))), 0, 8), "s", "0", 8, "e", 0)
            . db_formatar(substr(str_replace("/", '', str_replace('-', '', str_replace('.', '', $this->IPERegistro_107_114))), 0, 8), "s", "0", 8, "e", 0)
            . db_formatar(substr(str_replace("/", '', str_replace('-', '', str_replace('.', '', $this->IPERegistro_115_122))), 0, 8), "s", "0", 8, "e", 0)
            . db_formatar(substr(str_replace("/", '', str_replace('-', '', str_replace('.', '', $this->IPERegistro_123_130))), 0, 8), "s", "0", 8, "e", 0)
            . $this->IPERegistro_131_131
            . $this->IPERegistro_132_132
            . db_formatar(substr(str_replace("/", '', str_replace('-', '', str_replace('.', '', $this->IPERegistro_133_142))), 0, 10), "s", "0", 10, "e", 0)
            . db_formatar(substr(str_replace("/", '', str_replace('-', '', str_replace('.', '', $this->IPERegistro_143_153))), 0, 11), "s", "0", 11, "e", 0)
            . db_formatar(substr(str_replace(',', '', str_replace('.', '', trim(db_formatar($this->IPERegistro_154_164, "f")))), 0, 11), "s", "0", 11, "e", 0)
            . str_repeat(" ", 86)
            . "\r\n"
        );
    }

    public function geraTraillerIPE()
    {
        fputs($this->arquivo,
            db_formatar(substr($this->IPETrailler_001_003, 0, 3), "s", "0", 3, "e", 0)
            . str_repeat("9", 8)
            . db_formatar(substr($this->IPETrailler_012_016, 0, 5), "s", "0", 5, "e", 0)
            . db_formatar(substr(str_replace(',', '', str_replace('.', '', trim(db_formatar($this->IPETrailler_017_033, "f")))), 0, 17), "s", "0", 17, "e", 0)
            . str_repeat(" ", 217)
            . "\r\n"
        );
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////       FINAL MÉTODOS ARQUIVO DO IPE       /////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////
//          FECHA O ARQUIVO           //
////////////////////////////////////////
    public function gera()
    {
        fclose($this->arquivo);
    }
////////////////////////////////////////
}


//MODULO: PESSOAL

class cl_layout_BLV
{

    /***************************************************************************************************/
    /***************      TXT - publiciável que retorna texto a ser impresso no arquivo     ***************/
    /***************************************************************************************************/

    public $TEXTO = null;

    /***************************************************************************************************/

    /***************************************************************************************************/
    /***************************************************************************************************/
    /*****************       publicIÁVEIS USADAS PARA GERAR ARQUIVO DO BLV BANRISUL       *****************/
    /***************************************************************************************************/
    /***************************************************************************************************/
    /*
    HEADER DE ARQUIVO
*/
    public $BLVHeader_001_006 = null;
    public $BLVHeader_007_127 = null;
    public $BLVHeader_128_128 = null;
    /*
    REGISTRO
*/
    public $BLVRegistro_001_005 = null;
    public $BLVRegistro_006_015 = null;
    public $BLVRegistro_016_050 = null;
    public $BLVRegistro_051_075 = null;
    public $BLVRegistro_076_090 = null;
    public $BLVRegistro_091_096 = null;
    public $BLVRegistro_097_127 = null;
    public $BLVRegistro_128_128 = null;
    /*
    TRAILLER
*/
    public $BLVTrailler_001_006 = null;
    public $BLVTrailler_007_021 = null;
    public $BLVTrailler_022_127 = null;
    public $BLVTrailler_128_128 = null;
    /***************************************************************************************************/

    public $arquivo = null;
    public $nomearq = null;

    // Construtor
    public function __construct()
    {
        $this->nomearq = '/tmp/BLV' . date("mY") . '.TXT';
    }


//////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////      MÉTODOS LAYOUT DA SEFIP      /////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////

    public function geraHeaderBLV()
    {
        $this->arquivo = fopen($this->nomearq, "w");
        fputs($this->arquivo,
            "BLV000"
            . str_repeat(" ", 121)
            . "*"
            . "\r\n"
        );
    }

    public function geraRegistBLV()
    {
        fputs($this->arquivo,
            db_formatar(substr($this->BLVRegistro_001_005, 0, 5), "s", "0", 5, "e", 0)
            . db_formatar(substr($this->BLVRegistro_006_015, 0, 10), "s", "0", 10, "e", 0)
            . db_formatar(substr(strtoupper(db_translate($this->BLVRegistro_016_050)), 0, 35), "s", " ", 35, "d", 0)
            . str_repeat(" ", 25)
            . db_formatar(substr(trim(str_replace('.', '', str_replace(',', '', db_formatar($this->BLVRegistro_076_090, "f")))), 0, 15), "s", "0", 15, "e", 0)
            . db_formatar(substr($this->BLVRegistro_091_096, 0, 6), "s", "0", 6, "e", 0)
            . str_repeat(" ", 31)
            . "*"
            . "\r\n"
        );
    }

    public function geraTraillerBLV()
    {
        fputs($this->arquivo,
            "BLV999"
            . db_formatar(substr(trim(str_replace('.', '', str_replace(',', '', db_formatar($this->BLVTrailler_007_021, "f")))), 0, 15), "s", "0", 15, "e", 0)
            . str_repeat(" ", 106)
            . "*"
            . "\r\n"
        );
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////       FINAL MÉTODOS ARQUIVO DO BLV       /////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////
//          FECHA O ARQUIVO           //
////////////////////////////////////////
    public function gera()
    {
        fclose($this->arquivo);
    }
////////////////////////////////////////
}


class cl_layout_PREVID
{

    /***************************************************************************************************/
    /***************      TXT - publiciável que retorna texto a ser impresso no arquivo     ***************/
    /***************************************************************************************************/

    public $TEXTO = null;

    /***************************************************************************************************/

    /***************************************************************************************************/
    /***************************************************************************************************/
    /*****************     publicIÁVEIS USADAS PARA GERAR ARQUIVO DO PREVID - CAPSEM      *****************/
    /***************************************************************************************************/
    /***************************************************************************************************/
    /*
    HEADER DE ARQUIVO
*/
    public $PVDHeader_001_002 = null;
    public $PVDHeader_003_009 = null;
    public $PVDHeader_010_027 = null;
    /*
    REGISTRO DETALHE
*/
    public $PVDRegistro_001_002 = null;
    public $PVDRegistro_006_015 = null;
    public $PVDRegistro_016_050 = null;
    public $PVDRegistro_051_075 = null;
    public $PVDRegistro_076_090 = null;
    public $PVDRegistro_091_096 = null;
    public $PVDRegistro_097_127 = null;
    public $PVDRegistro_128_128 = null;
    /*
    TRAILLER
*/
    public $PVDTrailler_001_006 = null;
    public $PVDTrailler_007_021 = null;
    public $PVDTrailler_022_127 = null;
    public $PVDTrailler_128_128 = null;
    /***************************************************************************************************/

    public $arquivo = null;
    public $nomearq = null;

    // Construtor
    public function cl_layout_PVD()
    {
        $this->nomearq = '/tmp/PVD' . date("mY") . '.TXT';
    }


//////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////      MÉTODOS LAYOUT DA SEFIP      /////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////

    public function geraHeaderPVD()
    {
        $this->arquivo = fopen($this->nomearq, "w");
        fputs($this->arquivo,
            "PVD000"
            . str_repeat(" ", 121)
            . "*"
            . "\r\n"
        );
    }

    public function geraRegistPVD()
    {
        fputs($this->arquivo,
            db_formatar(substr($this->PVDRegistro_001_005, 0, 5), "s", "0", 5, "e", 0)
            . db_formatar(substr($this->PVDRegistro_006_015, 0, 10), "s", "0", 10, "e", 0)
            . db_formatar(substr(strtoupper(db_translate($this->PVDRegistro_016_050)), 0, 35), "s", " ", 35, "d", 0)
            . str_repeat(" ", 25)
            . db_formatar(substr(trim(str_replace('.', '', str_replace(',', '', db_formatar($this->PVDRegistro_076_090, "f")))), 0, 15), "s", "0", 15, "e", 0)
            . db_formatar(substr($this->PVDRegistro_091_096, 0, 6), "s", "0", 6, "e", 0)
            . str_repeat(" ", 31)
            . "*"
            . "\r\n"
        );
    }

    public function geraTraillerPVD()
    {
        fputs($this->arquivo,
            "PVD999"
            . db_formatar(substr(trim(str_replace('.', '', str_replace(',', '', db_formatar($this->PVDTrailler_007_021, "f")))), 0, 15), "s", "0", 15, "e", 0)
            . str_repeat(" ", 106)
            . "*"
            . "\r\n"
        );
    }
//////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////       FINAL MÉTODOS ARQUIVO DO PVD       /////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////
//          FECHA O ARQUIVO           //
////////////////////////////////////////
    public function gera()
    {
        fclose($this->arquivo);
    }
////////////////////////////////////////
}
