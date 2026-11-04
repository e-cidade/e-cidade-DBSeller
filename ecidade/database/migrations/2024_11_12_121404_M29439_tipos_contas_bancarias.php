<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29439TiposContasBancarias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

COMMENT ON TABLE compras.pcfornecon IS '{
     "sigla": "pc63 ",
     "rotulo": "Contas banco dos fornecedores",
     "dataincl": "2004-01-01",
     "descricao": "Cadastro das contas de banco dos fornecedores",
     "naolibfor": false,
     "naolibfunc": false,
     "naolibprog": false,
     "tipotabela": 0,
     "naolibclass": false
 }';

 COMMENT ON COLUMN compras.pcfornecon.pc63_tipoconta IS '{
     "rotulo": "Tipo da Conta",
     "tipoob": "select",
     "tamanho": 10,
     "autocompl": false,
     "descricao": "Tipo da Conta",
     "maiusculo": false,
     "rotulorel": "Tipo da Conta",
     "aceitatipo": 1,
     "syscampodef": [
       {
         "defcampo": "1",
         "defdescr": "Conta Corrente"
       },
       {
         "defcampo": "2",
         "defdescr": "Conta Poupança"
       },
       {
         "defcampo": "3",
         "defdescr": "Conta FGTS"
       },
       {
         "defcampo": "4",
         "defdescr": "Conta Eletrônica"
       },
       {
         "defcampo": "5",
         "defdescr": "Conta Salário"
       },
       {
         "defcampo": "6",
         "defdescr": "Débito em Conta"
       }
     ]
 }';

 COMMENT ON COLUMN compras.pcfornecon.pc63_agencia IS '{
     "rotulo": "Agência",
     "tipoob": "text",
     "tamanho": 10,
     "autocompl": false,
     "descricao": "Agência da conta do fornecedor",
     "maiusculo": false,
     "rotulorel": "Agência",
     "aceitatipo": 1
 }';

 COMMENT ON COLUMN compras.pcfornecon.pc63_chavepix IS '{
     "rotulo": "Chave PIX",
     "tipoob": "text",
     "tamanho": 1,
     "autocompl": false,
     "descricao": "Chave PIX",
     "maiusculo": false,
     "rotulorel": "Chave PIX",
     "aceitatipo": 0
 }';

 COMMENT ON COLUMN compras.pcfornecon.pc63_contabanco IS '{
     "rotulo": "Código Conta",
     "tipoob": "text",
     "tamanho": 6,
     "autocompl": false,
     "descricao": "Código Conta do banco do fornecedor",
     "maiusculo": false,
     "rotulorel": "Código Conta",
     "aceitatipo": 1
 }';

 COMMENT ON COLUMN compras.pcfornecon.pc63_banco IS '{
     "rotulo": "Banco",
     "tipoob": "text",
     "tamanho": 10,
     "autocompl": false,
     "descricao": "Código do Banco da conta do fornecedor",
     "maiusculo": true,
     "rotulorel": "Banco",
     "aceitatipo": 0
 }';

 COMMENT ON COLUMN compras.pcfornecon.pc63_dataconf IS '{
     "rotulo": "Conferido",
     "tipoob": "text",
     "tamanho": 10,
     "autocompl": false,
     "descricao": "Conferido",
     "maiusculo": false,
     "rotulorel": "Conferido",
     "aceitatipo": 1
 }';

 COMMENT ON COLUMN compras.pcfornecon.pc63_agencia_dig IS '{
     "rotulo": "Dígito verificador da agência",
     "tipoob": "text",
     "tamanho": 2,
     "autocompl": false,
     "descricao": "Dígito verificador da agência",
     "maiusculo": true,
     "rotulorel": "DV da agência",
     "aceitatipo": 3
 }';

 COMMENT ON COLUMN compras.pcfornecon.pc63_tipopix IS '{
     "rotulo": "Tipo de Chave PIX",
     "tipoob": "text",
     "tamanho": 10,
     "autocompl": false,
     "descricao": "Tipos aceitos de chaves pix",
     "maiusculo": false,
     "rotulorel": "Tipo de Chave PIX",
     "aceitatipo": 1,
     "syscampodef": [
       {
         "defcampo": "1",
         "defdescr": "Escolha um tipo de chave"
       },
       {
         "defcampo": "2",
         "defdescr": "CPF"
       },
       {
         "defcampo": "3",
         "defdescr": "CNPJ"
       },
       {
         "defcampo": "4",
         "defdescr": "Celular"
       },
       {
         "defcampo": "5",
         "defdescr": "E-mail"
       },
       {
         "defcampo": "6",
         "defdescr": "Chave Aleatória"
       }
     ]
 }';

 COMMENT ON COLUMN compras.pcfornecon.pc63_conta_dig IS '{
     "rotulo": "Dígito verificador da conta",
     "tipoob": "text",
     "tamanho": 2,
     "autocompl": false,
     "descricao": "Dígito verificador da conta",
     "maiusculo": true,
     "rotulorel": "DV da conta",
     "aceitatipo": 3
 }';

 COMMENT ON COLUMN compras.pcfornecon.pc63_identcli IS '{
     "rotulo": "Identificação do cliente",
     "tipoob": "text",
     "tamanho": 20,
     "autocompl": false,
     "descricao": "Identificação do cliente no banco",
     "maiusculo": true,
     "rotulorel": "Identificação do cliente",
     "aceitatipo": 0
 }';

 COMMENT ON COLUMN compras.pcfornecon.pc63_cnpjcpf IS '{
     "rotulo": "CNPJ/CPF",
     "tipoob": "text",
     "tamanho": 15,
     "autocompl": false,
     "descricao": "CNPJ/CPF",
     "maiusculo": true,
     "rotulorel": "CNPJ/CPF",
     "aceitatipo": 1
 }';

 COMMENT ON COLUMN compras.pcfornecon.pc63_id_usuario IS '{
     "rotulo": "Usuário",
     "tipoob": "text",
     "tamanho": 10,
     "autocompl": false,
     "descricao": "codigo do usuario",
     "maiusculo": false,
     "rotulorel": "Usuário",
     "aceitatipo": 1
 }';

 COMMENT ON COLUMN compras.pcfornecon.pc63_numcgm IS '{
     "rotulo": "Fornecedor",
     "tipoob": "text",
     "tamanho": 10,
     "autocompl": false,
     "descricao": "Código do fornecedor, sendo este, o mesmo número do cadastro geral do município (CGM)",
     "maiusculo": false,
     "rotulorel": "Forne",
     "aceitatipo": 1
 }';

 COMMENT ON COLUMN compras.pcfornecon.pc63_conta IS '{
     "rotulo": "Conta",
     "tipoob": "text",
     "tamanho": 40,
     "autocompl": false,
     "descricao": "Conta do fornecedor",
     "maiusculo": false,
     "rotulorel": "Conta",
     "aceitatipo": 1
 }';

 COMMENT ON COLUMN compras.pcfornecon.pc63_codigooperacao IS '{
     "rotulo": "Código da Operação",
     "tipoob": "text",
     "tamanho": 4,
     "autocompl": false,
     "descricao": "Código da Operação",
     "maiusculo": true,
     "rotulorel": "Código da Operação",
     "aceitatipo": 0
 }';

select fc_gera_dicionario_apartir_tabela('compras','pcfornecon');

ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL
        );
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
