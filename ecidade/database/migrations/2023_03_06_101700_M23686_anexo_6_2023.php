<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23686Anexo62023 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from orcparamseqfiltropadrao where o132_orcparamrel = 264 and o132_anousu = 2023;
delete from orcparamseqfiltroorcamento where o133_orcparamrel = 264 and o133_anousu = 2023;
SQL
        );

        // relatorio
        DB::connection()->getPdo()->exec(<<<SQL
insert into orcparamrel (o42_codparrel, o42_descrrel, o42_orcparamrelgrupo, o42_notapadrao) values (271, 'ANEXO VI - ED.13 - DEMONS. RESULT. PRIMÁRIO E NOMINAL', 1, 'FONTE: Sistema E-Cidade, Unidade Responsável: [nome_departamento]. Emissão: [data_emissao], às [hora_emissao]. Assinado Digitalmente no dia [data_emissao], às [hora_emissao].');
insert into orcparamrelperiodos (o113_sequencial, o113_periodo, o113_orcparamrel) values (nextval('orcparamrelperiodos_o113_sequencial_seq'), 6, 271);
insert into orcparamrelperiodos (o113_sequencial, o113_periodo, o113_orcparamrel) values (nextval('orcparamrelperiodos_o113_sequencial_seq'), 7, 271);
insert into orcparamrelperiodos (o113_sequencial, o113_periodo, o113_orcparamrel) values (nextval('orcparamrelperiodos_o113_sequencial_seq'), 8, 271);
insert into orcparamrelperiodos (o113_sequencial, o113_periodo, o113_orcparamrel) values (nextval('orcparamrelperiodos_o113_sequencial_seq'), 9, 271);
insert into orcparamrelperiodos (o113_sequencial, o113_periodo, o113_orcparamrel) values (nextval('orcparamrelperiodos_o113_sequencial_seq'), 10, 271);
insert into orcparamrelperiodos (o113_sequencial, o113_periodo, o113_orcparamrel) values (nextval('orcparamrelperiodos_o113_sequencial_seq'), 11, 271);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 1, 'RECEITAS CORRENTES (EXCETO FONTES RPPS) (I)', 1, 1, 0, 'f', 'f', 'f', 'f', 'f', 'RECEITAS CORRENTES (EXCETO FONTES RPPS) (I)', 'f', 't', 1, 1, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 2, 'Impostos, Taxas e Contribuições de Melhoria', 1, 1, 1, 'f', 'f', 'f', 'f', 'f', 'Impostos, Taxas e Contribuições de Melhoria', 'f', 't', 2, 2, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 3, 'IPTU', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'IPTU', 't', 'f', 3, 3, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 4, 'ISS', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'ISS', 't', 'f', 4, 3, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 5, 'ITBI', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'ITBI', 't', 'f', 5, 3, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 6, 'IRRF', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'IRRF', 't', 'f', 6, 3, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 7, 'Outros Impostos , Taxas e Contribuições de Melhoria', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'Outros Impostos , Taxas e Contribuições de Melhoria', 't', 'f', 7, 3, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 8, 'Contribuições', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'Contribuições', 't', 'f', 8, 2, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 9, 'Receita Patrimonial', 1, 1, 1, 'f', 'f', 'f', 'f', 'f', 'Receita Patrimonial', 'f', 't', 9, 2, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 10, 'Aplicações Financeiras (II)', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'Aplicações Financeiras (II)', 't', 'f', 10, 3, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 11, 'Outras Receitas Patrimoniais', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'Outras Receitas Patrimoniais', 't', 'f', 11, 3, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 12, 'Transferências Correntes', 1, 1, 1, 'f', 'f', 'f', 'f', 'f', 'Transferências Correntes', 'f', 't', 12, 2, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 13, 'Cota-Parte do FPM', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'Cota-Parte do FPM', 't', 'f', 13, 3, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 14, 'Cota-Parte do ICMS', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'Cota-Parte do ICMS', 't', 'f', 14, 3, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 15, 'Cota-Parte do IPVA', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'Cota-Parte do IPVA', 't', 'f', 15, 3, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 16, 'Cota-Parte do ITR', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'Cota-Parte do ITR', 't', 'f', 16, 3, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 17, 'Transferências da LC 61/1989', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'Transferências da LC 61/1989', 't', 'f', 17, 3, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 18, 'Transferências do FUNDEB', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'Transferências do FUNDEB', 't', 'f', 18, 3, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 19, 'Outras Transferências Correntes', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'Outras Transferências Correntes', 't', 'f', 19, 3, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 20, 'Demais Receitas Correntes', 1, 1, 1, 'f', 'f', 'f', 'f', 'f', 'Demais Receitas Correntes', 'f', 't', 20, 2, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 21, 'Outras Receitas Financeiras (III)', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'Outras Receitas Financeiras (III)', 't', 'f', 21, 3, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 22, 'Receitas Correntes Restantes', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'Receitas Correntes Restantes', 't', 'f', 22, 3, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 23, 'RECEITAS PRIMÁRIAS CORRENTES (EXCETO FONTES RPPS) (IV) = (I ', 1, 1, 0, 'f', 'f', 'f', 'f', 'f', 'RECEITAS PRIMÁRIAS CORRENTES (EXCETO FONTES RPPS) (IV) = (I - II - III)', 'f', 't', 23, 1, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 24, 'RECEITAS PRIMÁRIAS CORRENTES (COM FONTES RPPS) (V)', 1, 0, 0, 'f', 'f', 'f', 'f', 'f', 'RECEITAS PRIMÁRIAS CORRENTES (COM FONTES RPPS) (V)', 't', 'f', 24, 1, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 25, 'RECEITAS NÃO PRIMÁRIAS CORRENTES (COM FONTES RPPS) (VI)', 1, 0, 0, 'f', 'f', 'f', 'f', 'f', 'RECEITAS NÃO PRIMÁRIAS CORRENTES (COM FONTES RPPS) (VI)', 't', 'f', 25, 0, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 26, 'RECEITAS DE CAPITAL (EXCETO FONTES RPPS) (VII)', 1, 1, 0, 'f', 'f', 'f', 'f', 'f', 'RECEITAS DE CAPITAL (EXCETO FONTES RPPS) (VII)', 'f', 't', 26, 1, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 27, 'Operações de Crédito (VIII)', 1, 0, 0, 'f', 'f', 'f', 'f', 'f', 'Operações de Crédito (VIII)', 't', 'f', 27, 2, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 28, 'Amortização de Empréstimos (IX)', 1, 0, 0, 'f', 'f', 'f', 'f', 'f', 'Amortização de Empréstimos (IX)', 't', 'f', 28, 2, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 29, 'Alienação de Bens', 1, 1, 1, 'f', 'f', 'f', 'f', 'f', 'Alienação de Bens', 'f', 't', 29, 2, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 30, 'Receitas de Alienação de Investimentos Temporários (X)', 1, 0, 0, 'f', 'f', 'f', 'f', 'f', 'Receitas de Alienação de Investimentos Temporários (X)', 't', 'f', 30, 3, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 31, 'Receitas de Alienação de Investimentos Permanentes (XI)', 1, 0, 0, 'f', 'f', 'f', 'f', 'f', 'Receitas de Alienação de Investimentos Permanentes (XI)', 't', 'f', 31, 3, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 32, 'Outras Alienações de Bens', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'Outras Alienações de Bens', 't', 'f', 32, 3, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 33, 'Transferências de Capital', 1, 1, 1, 'f', 'f', 'f', 'f', 'f', 'Transferências de Capital', 'f', 't', 33, 2, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 34, 'Convênios', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'Convênios', 't', 'f', 34, 3, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 35, 'Outras Transferências de Capital', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'Outras Transferências de Capital', 't', 'f', 35, 3, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 36, 'Outras Receitas de Capital', 1, 1, 1, 'f', 'f', 'f', 'f', 'f', 'Outras Receitas de Capital', 'f', 't', 36, 2, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 37, 'Outras Receitas de Capital Não Primárias (XII)', 1, 0, 0, 'f', 'f', 'f', 'f', 'f', 'Outras Receitas de Capital Não Primárias (XII)', 't', 'f', 37, 3, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 38, 'Outras Receitas de Capital Primárias', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'Outras Receitas de Capital Primárias', 't', 'f', 38, 3, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 39, 'RECEITAS PRIMÁRIAS DE CAPITAL (EXCETO FONTES RPPS) (XIII) = ', 1, 1, 0, 'f', 'f', 'f', 'f', 'f', 'RECEITAS PRIMÁRIAS DE CAPITAL (EXCETO FONTES RPPS) (XIII) = [VII - (VIII + IX + X + XI + XII)]', 'f', 't', 39, 1, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 40, 'RECEITAS PRIMÁRIAS DE CAPITAL (COM FONTES RPPS) (XIV)', 1, 0, 0, 'f', 'f', 'f', 'f', 'f', 'RECEITAS PRIMÁRIAS DE CAPITAL (COM FONTES RPPS) (XIV)', 't', 'f', 40, 1, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 41, 'RECEITAS NÃO PRIMÁRIAS DE CAPITAL (COM FONTES RPPS) (XV)', 1, 0, 0, 'f', 'f', 'f', 'f', 'f', 'RECEITAS NÃO PRIMÁRIAS DE CAPITAL (COM FONTES RPPS) (XV)', 't', 'f', 41, 1, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 42, 'RECEITA PRIMÁRIA TOTAL (XVI) = (IV + V + XIII + XIV)', 1, 1, 0, 'f', 'f', 'f', 'f', 'f', 'RECEITA PRIMÁRIA TOTAL (XVI) = (IV + V + XIII + XIV)', 'f', 't', 42, 1, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 43, 'RECEITA PRIMÁRIA TOTAL (EXCETO FONTES RPPS) (XVII) = (IV + X', 1, 1, 0, 'f', 'f', 'f', 'f', 'f', 'RECEITA PRIMÁRIA TOTAL (EXCETO FONTES RPPS) (XVII) = (IV + XIII)', 'f', 't', 43, 1, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 44, 'DESPESAS CORRENTES (EXCETO FONTES RPPS) (XVIII)', 1, 1, 0, 'f', 'f', 'f', 'f', 'f', 'DESPESAS CORRENTES (EXCETO FONTES RPPS) (XVIII)', 'f', 't', 44, 1, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 45, 'Pessoal e Encargos Sociais', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'Pessoal e Encargos Sociais', 't', 'f', 45, 2, '', 'f', 2);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 46, 'Juros e Encargos da Dívida (XIX)', 1, 0, 0, 'f', 'f', 'f', 'f', 'f', 'Juros e Encargos da Dívida (XIX)', 't', 'f', 46, 2, '', 'f', 2);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 47, 'Outras Despesas Correntes', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'Outras Despesas Correntes', 't', 'f', 47, 2, '', 'f', 2);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 48, 'DESPESAS PRIMÁRIAS CORRENTES (EXCETO FONTES RPPS) (XX) = (XV', 1, 1, 0, 'f', 'f', 'f', 'f', 'f', 'DESPESAS PRIMÁRIAS CORRENTES (EXCETO FONTES RPPS) (XX) = (XVIII - XIX)', 'f', 't', 48, 1, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 49, 'DESPESAS PRIMÁRIAS CORRENTES (COM FONTES RPPS) (XXI)', 1, 0, 0, 'f', 'f', 'f', 'f', 'f', 'DESPESAS PRIMÁRIAS CORRENTES (COM FONTES RPPS) (XXI)', 't', 'f', 49, 1, '', 'f', 2);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 50, 'DESPESAS NÃO PRIMÁRIAS CORRENTES (COM FONTES RPPS) (XXII)', 1, 0, 0, 'f', 'f', 'f', 'f', 'f', 'DESPESAS NÃO PRIMÁRIAS CORRENTES (COM FONTES RPPS) (XXII)', 't', 'f', 50, 1, '', 'f', 2);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 51, 'DESPESAS DE CAPITAL (EXCETO FONTES RPPS) (XXIII)', 1, 1, 0, 'f', 'f', 'f', 'f', 'f', 'DESPESAS DE CAPITAL (EXCETO FONTES RPPS) (XXIII)', 'f', 't', 51, 1, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 52, 'Investimentos', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'Investimentos', 't', 'f', 52, 2, '', 'f', 2);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 53, 'Inversões Financeiras', 1, 1, 1, 'f', 'f', 'f', 'f', 'f', 'Inversões Financeiras', 'f', 't', 53, 2, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 54, 'Concessão de Empréstimos e Financiamentos (XXIV)', 1, 0, 0, 'f', 'f', 'f', 'f', 'f', 'Concessão de Empréstimos e Financiamentos (XXIV)', 't', 'f', 54, 3, '', 'f', 2);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 55, 'Aquisição de Título de Capital já Integralizado (XXV)', 1, 0, 0, 'f', 'f', 'f', 'f', 'f', 'Aquisição de Título de Capital já Integralizado (XXV)', 't', 'f', 55, 3, '', 'f', 2);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 56, 'Aquisição de Título de Crédito (XXVI)', 1, 0, 0, 'f', 'f', 'f', 'f', 'f', 'Aquisição de Título de Crédito (XXVI)', 't', 'f', 56, 3, '', 'f', 2);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 57, 'Demais Inversões Financeiras', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'Demais Inversões Financeiras', 't', 'f', 57, 3, '', 'f', 2);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 58, 'Amortização da Dívida (XXVII)', 1, 0, 0, 'f', 'f', 'f', 'f', 'f', 'Amortização da Dívida (XXVII)', 't', 'f', 58, 2, '', 'f', 2);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 59, 'DESPESAS PRIMÁRIAS DE CAPITAL (EXCETO FONTES RPPS) (XXVIII) ', 1, 1, 0, 'f', 'f', 'f', 'f', 'f', 'DESPESAS PRIMÁRIAS DE CAPITAL (EXCETO FONTES RPPS) (XXVIII) = [XXIII - (XXIV + XXV + XXVI + XXVII)]', 'f', 't', 59, 1, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 60, 'RESERVA DE CONTINGÊNCIA (XXIX)', 1, 0, 0, 'f', 'f', 'f', 'f', 'f', 'RESERVA DE CONTINGÊNCIA (XXIX)', 't', 'f', 60, 1, '', 'f', 2);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 61, 'DESPESAS PRIMÁRIAS DE CAPITAL (COM FONTES RPPS) (XXX)', 1, 0, 0, 'f', 'f', 'f', 'f', 'f', 'DESPESAS PRIMÁRIAS DE CAPITAL (COM FONTES RPPS) (XXX)', 't', 'f', 61, 1, '', 'f', 2);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 62, 'DESPESAS NÃO PRIMÁRIAS DE CAPITAL (COM FONTES RPPS) (XXXI)', 1, 0, 0, 'f', 'f', 'f', 'f', 'f', 'DESPESAS NÃO PRIMÁRIAS DE CAPITAL (COM FONTES RPPS) (XXXI)', 't', 'f', 62, 1, '', 'f', 2);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 63, 'DESPESA PRIMÁRIA TOTAL (XXXII) = (XX + XXI + XXVIII + XXIX +', 1, 1, 0, 'f', 'f', 'f', 'f', 'f', 'DESPESA PRIMÁRIA TOTAL (XXXII) = (XX + XXI + XXVIII + XXIX + XXX)', 'f', 't', 63, 1, '', 'f', 2);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 64, 'DESPESA PRIMÁRIA TOTAL (EXCETO FONTES RPPS) (XXXIII) = (XX +', 1, 1, 0, 'f', 'f', 'f', 'f', 'f', 'DESPESA PRIMÁRIA TOTAL (EXCETO FONTES RPPS) (XXXIII) = (XX + XXVIII + XXIX)', 'f', 't', 64, 1, '', 'f', 2);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 65, 'RESULTADO PRIMÁRIO (COM RPPS) - Acima da Linha (XXXIV) = [XV', 1, 1, 0, 'f', 'f', 'f', 'f', 'f', 'RESULTADO PRIMÁRIO (COM RPPS) - Acima da Linha (XXXIV) = [XVIa - (XXXIIa +XXXIIb + XXXIIc)]', 'f', 't', 65, 1, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 66, 'RESULTADO PRIMÁRIO (SEM RPPS) - Acima da Linha (XXXV) = [XVI', 1, 1, 0, 'f', 'f', 'f', 'f', 'f', 'RESULTADO PRIMÁRIO (SEM RPPS) - Acima da Linha (XXXV) = [XVIIa - (XXXIIIa +XXXIIIb + XXXIIIc)]', 'f', 't', 66, 1, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 67, 'Meta fixada no Anexo de Metas Fiscais da LDO para o exercíci', 1, 1, 1, 'f', 'f', 'f', 'f', 'f', 'Meta fixada no Anexo de Metas Fiscais da LDO para o exercício de referência', 't', 'f', 67, 1, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 68, 'Juros, Encargos e Variações Monetárias Ativos (Exceto RPPS) ', 1, 0, 0, 'f', 'f', 'f', 'f', 'f', 'Juros, Encargos e Variações Monetárias Ativos (Exceto RPPS) (XXXVI)', 't', 'f', 68, 1, '', 'f', 3);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 69, 'Juros, Encargos e Variações Monetárias Passivos (Exceto RPPS', 1, 0, 0, 'f', 'f', 'f', 'f', 'f', 'Juros, Encargos e Variações Monetárias Passivos (Exceto RPPS) (XXXVII)', 't', 'f', 69, 1, '', 'f', 3);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 70, 'RESULTADO NOMINAL (SEM RPPS) - Acima da Linha (XXXVIII) =  X', 1, 1, 0, 'f', 'f', 'f', 'f', 'f', 'RESULTADO NOMINAL (SEM RPPS) - Acima da Linha (XXXVIII) =  XXXV + (XXXVI - XXXVII)', 'f', 't', 70, 1, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 71, 'DÍVIDA CONSOLIDADA (XXXIX)', 1, 0, 0, 'f', 'f', 'f', 'f', 'f', 'DÍVIDA CONSOLIDADA (XXXIX)', 't', 'f', 71, 1, '', 'f', 3);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 72, 'DEDUÇÕES (XL)', 1, 1, 0, 'f', 'f', 'f', 'f', 'f', 'DEDUÇÕES (XL)', 'f', 't', 72, 1, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 73, 'Disponibilidade de Caixa', 1, 1, 1, 'f', 'f', 'f', 'f', 'f', 'Disponibilidade de Caixa', 'f', 't', 73, 2, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 74, 'Disponibilidade de Caixa Bruta', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'Disponibilidade de Caixa Bruta', 't', 'f', 74, 3, '', 'f', 3);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 75, '(-) Restos a Pagar Processados (XLI)', 1, 0, 0, 'f', 'f', 'f', 'f', 'f', '(-) Restos a Pagar Processados (XLI)', 't', 'f', 75, 4, '', 'f', 3);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 76, '(-) Depósitos Restituíveis e Valores Vinculados', 1, 1, 1, 'f', 'f', 'f', 'f', 'f', '(-) Depósitos Restituíveis e Valores Vinculados', 't', 'f', 76, 4, '', 'f', 3);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 77, 'Demais Haveres Financeiros', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'Demais Haveres Financeiros', 't', 'f', 77, 2, '', 'f', 3);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 78, 'DÍVIDA CONSOLIDADA LÍQUIDA (XLII) = (XXXIX - XL)', 1, 1, 0, 'f', 'f', 'f', 'f', 'f', 'DÍVIDA CONSOLIDADA LÍQUIDA (XLII) = (XXXIX - XL)', 'f', 't', 78, 1, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 79, 'RESULTADO NOMINAL (SEM RPPS) - Abaixo da Linha (XLIII) = (XL', 1, 1, 0, 'f', 'f', 'f', 'f', 'f', 'RESULTADO NOMINAL (SEM RPPS) - Abaixo da Linha (XLIII) = (XLIIa - XLIIb)', 'f', 't', 79, 1, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 80, 'Meta fixada no Anexo de Metas Fiscais da LDO para o exercíci', 1, 1, 1, 'f', 'f', 'f', 'f', 'f', 'Meta fixada no Anexo de Metas Fiscais da LDO para o exercício de referência', 't', 'f', 80, 1, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 81, 'VARIAÇÃO DO SALDO RPP (XLIV) = (XLIa - XLIb)', 1, 1, 0, 'f', 'f', 'f', 'f', 'f', 'VARIAÇÃO DO SALDO RPP (XLIV) = (XLIa - XLIb)', 'f', 't', 81, 1, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 82, 'RECEITA DE ALIENAÇÃO DE INVESTIMENTOS PERMANENTES (XLV) = (X', 1, 1, 0, 'f', 'f', 'f', 'f', 'f', 'RECEITA DE ALIENAÇÃO DE INVESTIMENTOS PERMANENTES (XLV) = (XI)', 'f', 't', 82, 1, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 83, 'VARIAÇÃO CAMBIAL (XLVI)', 1, 0, 0, 'f', 'f', 'f', 'f', 'f', 'VARIAÇÃO CAMBIAL (XLVI)', 't', 'f', 83, 1, '', 'f', 3);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 84, 'VARIAÇÃO DO SALDO DE PRECATÓRIOS INTEGRANTES DA DC (XLVII)', 1, 0, 0, 'f', 'f', 'f', 'f', 'f', 'VARIAÇÃO DO SALDO DE PRECATÓRIOS INTEGRANTES DA DC (XLVII)', 't', 'f', 84, 1, '', 'f', 3);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 85, 'VARIAÇÃO DO SALDO DAS DEMAIS OBRIGAÇÕES INTEGRANTES DA DC (X', 1, 0, 0, 'f', 'f', 'f', 'f', 'f', 'VARIAÇÃO DO SALDO DAS DEMAIS OBRIGAÇÕES INTEGRANTES DA DC (XLVIII)', 't', 'f', 85, 1, '', 'f', 3);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 86, 'OUTROS AJUSTES (XLXIX)', 1, 1, 0, 'f', 'f', 'f', 'f', 'f', 'OUTROS AJUSTES (XLXIX)', 'f', 'f', 86, 1, '', 'f', 3);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 87, 'RESULTADO NOMINAL AJUSTADO (SEM RPPS) AJUSTADO - Abaixo da L', 1, 1, 0, 'f', 'f', 'f', 'f', 'f', 'RESULTADO NOMINAL AJUSTADO (SEM RPPS) AJUSTADO - Abaixo da Linha (L) = [XLIII + (XLIV - XLV + XLVI + XLVII + XLVIII) +/- (XLXIX)]', 'f', 't', 87, 1, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 88, 'RESULTADO PRIMÁRIO (SEM RPPS) - Abaixo da Linha (LI) =  (L) ', 1, 1, 0, 'f', 'f', 'f', 'f', 'f', 'RESULTADO PRIMÁRIO (SEM RPPS) - Abaixo da Linha (LI) =  (L) - (XXXVI - XXXVII)', 'f', 't', 88, 1, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 89, 'SALDO DE EXERCÍCIOS ANTERIORES', 1, 1, 1, 'f', 'f', 'f', 'f', 'f', 'SALDO DE EXERCÍCIOS ANTERIORES', 'f', 't', 89, 1, '', 'f', 0);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 90, 'Recursos Arrecadados em Exercícios Anteriores - RPPS', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'Recursos Arrecadados em Exercícios Anteriores - RPPS', 't', 'f', 90, 2, '', 'f', 1);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 91, 'Superávit Financeiro Utilizado para Abertura e Reabertura de', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'Superávit Financeiro Utilizado para Abertura e Reabertura de Créditos Adicionais', 't', 'f', 91, 2, '', 'f', 3);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 92, 'RESERVA ORÇAMENTÁRIA DO RPPS', 1, 0, 1, 'f', 'f', 'f', 'f', 'f', 'RESERVA ORÇAMENTÁRIA DO RPPS', 't', 'f', 92, 1, '', 'f', 2);
insert into orcparamseq (o69_codparamrel, o69_codseq, o69_descr, o69_grupo, o69_grupoexclusao, o69_nivel, o69_libnivel, o69_librec, o69_libsubfunc, o69_libfunc, o69_verificaano, o69_labelrel, o69_manual, o69_totalizador, o69_ordem, o69_nivellinha, o69_observacao, o69_desdobrarlinha, o69_origem) values (271, 93, '(-) Restos a Pagar Processados - Intra', 1, 0, 0, 'f', 'f', 'f', 'f', 'f', '(-) Restos a Pagar Processados - Intra', 't', 'f', 93, 4, '', 'f', 4);
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 3, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="411125000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="911125000000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 3, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="411180110000000" nivel="" exclusao="false" /><conta estrutural="911180110000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 3, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="411180110000000" nivel="" exclusao="false" /><conta estrutural="911180110000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 3, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="411180110000000" nivel="" exclusao="false" /><conta estrutural="911180110000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 4, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="411145110000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="911145110000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="411145120000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="911145120000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 4, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="411180230000000" nivel="" exclusao="false" /><conta estrutural="911180230000000" nivel="" exclusao="false" /><conta estrutural="411180240000000" nivel="" exclusao="false" /><conta estrutural="911180240000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 4, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="411180230000000" nivel="" exclusao="false" /><conta estrutural="911180230000000" nivel="" exclusao="false" /><conta estrutural="411180240000000" nivel="" exclusao="false" /><conta estrutural="911180240000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 4, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="411180230000000" nivel="" exclusao="false" /><conta estrutural="911180230000000" nivel="" exclusao="false" /><conta estrutural="411180240000000" nivel="" exclusao="false" /><conta estrutural="911180240000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 5, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="411180140000000" nivel="" exclusao="false" /><conta estrutural="911180140000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 5, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="411130300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="911130300000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 5, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="411180140000000" nivel="" exclusao="false" /><conta estrutural="911180140000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 5, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="411180140000000" nivel="" exclusao="false" /><conta estrutural="911180140000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 6, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="411130300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="911130300000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 6, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="411130310000000" nivel="" exclusao="false" /><conta estrutural="911130310000000" nivel="" exclusao="false" /><conta estrutural="411130340000000" nivel="" exclusao="false" /><conta estrutural="911130340000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 6, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="411130310000000" nivel="" exclusao="false" /><conta estrutural="911130310000000" nivel="" exclusao="false" /><conta estrutural="411130340000000" nivel="" exclusao="false" /><conta estrutural="911130340000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 6, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="411130310000000" nivel="" exclusao="false" /><conta estrutural="911130310000000" nivel="" exclusao="false" /><conta estrutural="411130340000000" nivel="" exclusao="false" /><conta estrutural="911130340000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 7, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="411100000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="911100000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="411125000000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="911125000000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="411451100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="911451100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="411451200000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="911451200000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="411125300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="911125300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="411130300000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="911130300000000" nivel="" exclusao="true" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 7, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="411000000000000" nivel="" exclusao="false" /><conta estrutural="411180110000000" nivel="" exclusao="true" /><conta estrutural="411180230000000" nivel="" exclusao="true" /><conta estrutural="411180240000000" nivel="" exclusao="true" /><conta estrutural="411180140000000" nivel="" exclusao="true" /><conta estrutural="411130310000000" nivel="" exclusao="true" /><conta estrutural="411130340000000" nivel="" exclusao="true" /><conta estrutural="911000000000000" nivel="" exclusao="false" /><conta estrutural="911180110000000" nivel="" exclusao="true" /><conta estrutural="911180230000000" nivel="" exclusao="true" /><conta estrutural="911180240000000" nivel="" exclusao="true" /><conta estrutural="911180140000000" nivel="" exclusao="true" /><conta estrutural="911130310000000" nivel="" exclusao="true" /><conta estrutural="911130340000000" nivel="" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 7, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="411000000000000" nivel="" exclusao="false" /><conta estrutural="411180110000000" nivel="" exclusao="true" /><conta estrutural="411180230000000" nivel="" exclusao="true" /><conta estrutural="411180240000000" nivel="" exclusao="true" /><conta estrutural="411180140000000" nivel="" exclusao="true" /><conta estrutural="411130310000000" nivel="" exclusao="true" /><conta estrutural="411130340000000" nivel="" exclusao="true" /><conta estrutural="911000000000000" nivel="" exclusao="false" /><conta estrutural="911180110000000" nivel="" exclusao="true" /><conta estrutural="911180230000000" nivel="" exclusao="true" /><conta estrutural="911180240000000" nivel="" exclusao="true" /><conta estrutural="911180140000000" nivel="" exclusao="true" /><conta estrutural="911130310000000" nivel="" exclusao="true" /><conta estrutural="911130340000000" nivel="" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 7, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="411000000000000" nivel="" exclusao="false" /><conta estrutural="411180110000000" nivel="" exclusao="true" /><conta estrutural="411180230000000" nivel="" exclusao="true" /><conta estrutural="411180240000000" nivel="" exclusao="true" /><conta estrutural="411180140000000" nivel="" exclusao="true" /><conta estrutural="411130310000000" nivel="" exclusao="true" /><conta estrutural="411130340000000" nivel="" exclusao="true" /><conta estrutural="911000000000000" nivel="" exclusao="false" /><conta estrutural="911180110000000" nivel="" exclusao="true" /><conta estrutural="911180230000000" nivel="" exclusao="true" /><conta estrutural="911180240000000" nivel="" exclusao="true" /><conta estrutural="911180140000000" nivel="" exclusao="true" /><conta estrutural="911130310000000" nivel="" exclusao="true" /><conta estrutural="911130340000000" nivel="" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 8, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="412000000000000" nivel="" exclusao="false" /><conta estrutural="912000000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 8, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="412000000000000" nivel="" exclusao="false" /><conta estrutural="912000000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 8, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="412000000000000" nivel="" exclusao="false" /><conta estrutural="912000000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 8, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="412000000000000" nivel="" exclusao="false" /><conta estrutural="912000000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 10, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="413210100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="913210100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="413210200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="913210200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="413210300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="913210300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="413210400000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="913210400000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="413210500000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="913210500000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="413299900000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="913299900000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 10, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="413210010000000" nivel="" exclusao="false" /><conta estrutural="413210020000000" nivel="" exclusao="false" /><conta estrutural="413210030000000" nivel="" exclusao="false" /><conta estrutural="413210040000000" nivel="" exclusao="false" /><conta estrutural="413210050000000" nivel="" exclusao="false" /><conta estrutural="413290010000000" nivel="" exclusao="false" /><conta estrutural="913210010000000" nivel="" exclusao="false" /><conta estrutural="913210020000000" nivel="" exclusao="false" /><conta estrutural="913210030000000" nivel="" exclusao="false" /><conta estrutural="913210040000000" nivel="" exclusao="false" /><conta estrutural="913210050000000" nivel="" exclusao="false" /><conta estrutural="913290010000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 10, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="413210010000000" nivel="" exclusao="false" /><conta estrutural="413210020000000" nivel="" exclusao="false" /><conta estrutural="413210030000000" nivel="" exclusao="false" /><conta estrutural="413210040000000" nivel="" exclusao="false" /><conta estrutural="413210050000000" nivel="" exclusao="false" /><conta estrutural="413290010000000" nivel="" exclusao="false" /><conta estrutural="913210010000000" nivel="" exclusao="false" /><conta estrutural="913210020000000" nivel="" exclusao="false" /><conta estrutural="913210030000000" nivel="" exclusao="false" /><conta estrutural="913210040000000" nivel="" exclusao="false" /><conta estrutural="913210050000000" nivel="" exclusao="false" /><conta estrutural="913290010000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 10, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="413210010000000" nivel="" exclusao="false" /><conta estrutural="413210020000000" nivel="" exclusao="false" /><conta estrutural="413210030000000" nivel="" exclusao="false" /><conta estrutural="413210040000000" nivel="" exclusao="false" /><conta estrutural="413210050000000" nivel="" exclusao="false" /><conta estrutural="413290010000000" nivel="" exclusao="false" /><conta estrutural="913210010000000" nivel="" exclusao="false" /><conta estrutural="913210020000000" nivel="" exclusao="false" /><conta estrutural="913210030000000" nivel="" exclusao="false" /><conta estrutural="913210040000000" nivel="" exclusao="false" /><conta estrutural="913210050000000" nivel="" exclusao="false" /><conta estrutural="913290010000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 11, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="413000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="413210010000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="413210020000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="413210030000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="413210040000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="413210050000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="913000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="913210010000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="913210020000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="913210030000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="913210040000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="913210050000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="413299900000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="913299900000000" nivel="" exclusao="true" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 11, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="413000000000000" nivel="" exclusao="false" /><conta estrutural="413210010000000" nivel="" exclusao="true" /><conta estrutural="413210020000000" nivel="" exclusao="true" /><conta estrutural="413210030000000" nivel="" exclusao="true" /><conta estrutural="413210040000000" nivel="" exclusao="true" /><conta estrutural="413210050000000" nivel="" exclusao="true" /><conta estrutural="413290010000000" nivel="" exclusao="true" /><conta estrutural="913000000000000" nivel="" exclusao="false" /><conta estrutural="913210010000000" nivel="" exclusao="true" /><conta estrutural="913210020000000" nivel="" exclusao="true" /><conta estrutural="913210030000000" nivel="" exclusao="true" /><conta estrutural="913210040000000" nivel="" exclusao="true" /><conta estrutural="913210050000000" nivel="" exclusao="true" /><conta estrutural="913290010000000" nivel="" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 11, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="413000000000000" nivel="" exclusao="false" /><conta estrutural="413210010000000" nivel="" exclusao="true" /><conta estrutural="413210020000000" nivel="" exclusao="true" /><conta estrutural="413210030000000" nivel="" exclusao="true" /><conta estrutural="413210040000000" nivel="" exclusao="true" /><conta estrutural="413210050000000" nivel="" exclusao="true" /><conta estrutural="413290010000000" nivel="" exclusao="true" /><conta estrutural="913000000000000" nivel="" exclusao="false" /><conta estrutural="913210010000000" nivel="" exclusao="true" /><conta estrutural="913210020000000" nivel="" exclusao="true" /><conta estrutural="913210030000000" nivel="" exclusao="true" /><conta estrutural="913210040000000" nivel="" exclusao="true" /><conta estrutural="913210050000000" nivel="" exclusao="true" /><conta estrutural="913290010000000" nivel="" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 11, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="413000000000000" nivel="" exclusao="false" /><conta estrutural="413210010000000" nivel="" exclusao="true" /><conta estrutural="413210020000000" nivel="" exclusao="true" /><conta estrutural="413210030000000" nivel="" exclusao="true" /><conta estrutural="413210040000000" nivel="" exclusao="true" /><conta estrutural="413210050000000" nivel="" exclusao="true" /><conta estrutural="413290010000000" nivel="" exclusao="true" /><conta estrutural="913000000000000" nivel="" exclusao="false" /><conta estrutural="913210010000000" nivel="" exclusao="true" /><conta estrutural="913210020000000" nivel="" exclusao="true" /><conta estrutural="913210030000000" nivel="" exclusao="true" /><conta estrutural="913210040000000" nivel="" exclusao="true" /><conta estrutural="913210050000000" nivel="" exclusao="true" /><conta estrutural="913290010000000" nivel="" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 13, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="417115110000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="917115110000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="417115120000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="917115120000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 13, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="417180120000000" nivel="" exclusao="false" /><conta estrutural="417180130000000" nivel="" exclusao="false" /><conta estrutural="417180140000000" nivel="" exclusao="false" /><conta estrutural="917180120000000" nivel="" exclusao="false" /><conta estrutural="917180130000000" nivel="" exclusao="false" /><conta estrutural="917180140000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 13, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="417180120000000" nivel="" exclusao="false" /><conta estrutural="417180130000000" nivel="" exclusao="false" /><conta estrutural="417180140000000" nivel="" exclusao="false" /><conta estrutural="917180120000000" nivel="" exclusao="false" /><conta estrutural="917180130000000" nivel="" exclusao="false" /><conta estrutural="917180140000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 13, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="417180120000000" nivel="" exclusao="false" /><conta estrutural="417180130000000" nivel="" exclusao="false" /><conta estrutural="417180140000000" nivel="" exclusao="false" /><conta estrutural="917180120000000" nivel="" exclusao="false" /><conta estrutural="917180130000000" nivel="" exclusao="false" /><conta estrutural="917180140000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 14, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="417215000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="917215000000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 14, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="417280110000000" nivel="" exclusao="false" /><conta estrutural="917280110000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 14, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="417280110000000" nivel="" exclusao="false" /><conta estrutural="917280110000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 14, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="417280110000000" nivel="" exclusao="false" /><conta estrutural="917280110000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 15, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="417215100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="917215100000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 15, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="417280120000000" nivel="" exclusao="false" /><conta estrutural="917280120000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 15, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="417280120000000" nivel="" exclusao="false" /><conta estrutural="917280120000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 15, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="417280120000000" nivel="" exclusao="false" /><conta estrutural="917280120000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 16, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="417115200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="917115200000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 16, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="417180150000000" nivel="" exclusao="false" /><conta estrutural="917180150000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 16, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="417180150000000" nivel="" exclusao="false" /><conta estrutural="917180150000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 16, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="417180150000000" nivel="" exclusao="false" /><conta estrutural="917180150000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 17, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="417215200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="917215200000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 17, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="417280130000000" nivel="" exclusao="false" /><conta estrutural="917280130000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 17, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="417280130000000" nivel="" exclusao="false" /><conta estrutural="917280130000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 17, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="417280130000000" nivel="" exclusao="false" /><conta estrutural="917280130000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 18, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="417150000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="917150000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="417510000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="917510000000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 18, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="417580100000000" nivel="" exclusao="false" /><conta estrutural="917580100000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 18, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="417580100000000" nivel="" exclusao="false" /><conta estrutural="917580100000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 18, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="417580100000000" nivel="" exclusao="false" /><conta estrutural="917580100000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 19, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="917000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="917000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="417115110000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="917115110000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="417115120000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="917115120000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="417215000000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="917215000000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="417215100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="917215100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="417115200000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="917115200000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="417150000000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="917150000000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="417510000000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="917510000000000" nivel="" exclusao="true" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 19, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="417000000000000" nivel="" exclusao="false" /><conta estrutural="417180120000000" nivel="" exclusao="true" /><conta estrutural="417180130000000" nivel="" exclusao="true" /><conta estrutural="417180140000000" nivel="" exclusao="true" /><conta estrutural="417280110000000" nivel="" exclusao="true" /><conta estrutural="417280120000000" nivel="" exclusao="true" /><conta estrutural="417180150000000" nivel="" exclusao="true" /><conta estrutural="417180610000000" nivel="" exclusao="true" /><conta estrutural="417280130000000" nivel="" exclusao="true" /><conta estrutural="417580100000000" nivel="" exclusao="true" /><conta estrutural="917000000000000" nivel="" exclusao="false" /><conta estrutural="917180120000000" nivel="" exclusao="true" /><conta estrutural="917180130000000" nivel="" exclusao="true" /><conta estrutural="917180140000000" nivel="" exclusao="true" /><conta estrutural="917280110000000" nivel="" exclusao="true" /><conta estrutural="917280120000000" nivel="" exclusao="true" /><conta estrutural="917180150000000" nivel="" exclusao="true" /><conta estrutural="917180610000000" nivel="" exclusao="true" /><conta estrutural="917280130000000" nivel="" exclusao="true" /><conta estrutural="917580100000000" nivel="" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 19, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="417000000000000" nivel="" exclusao="false" /><conta estrutural="417180120000000" nivel="" exclusao="true" /><conta estrutural="417180130000000" nivel="" exclusao="true" /><conta estrutural="417180140000000" nivel="" exclusao="true" /><conta estrutural="417280110000000" nivel="" exclusao="true" /><conta estrutural="417280120000000" nivel="" exclusao="true" /><conta estrutural="417180150000000" nivel="" exclusao="true" /><conta estrutural="417180610000000" nivel="" exclusao="true" /><conta estrutural="417280130000000" nivel="" exclusao="true" /><conta estrutural="417580100000000" nivel="" exclusao="true" /><conta estrutural="917000000000000" nivel="" exclusao="false" /><conta estrutural="917180120000000" nivel="" exclusao="true" /><conta estrutural="917180130000000" nivel="" exclusao="true" /><conta estrutural="917180140000000" nivel="" exclusao="true" /><conta estrutural="917280110000000" nivel="" exclusao="true" /><conta estrutural="917280120000000" nivel="" exclusao="true" /><conta estrutural="917180150000000" nivel="" exclusao="true" /><conta estrutural="917180610000000" nivel="" exclusao="true" /><conta estrutural="917280130000000" nivel="" exclusao="true" /><conta estrutural="917580100000000" nivel="" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 19, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="417000000000000" nivel="" exclusao="false" /><conta estrutural="417180120000000" nivel="" exclusao="true" /><conta estrutural="417180130000000" nivel="" exclusao="true" /><conta estrutural="417180140000000" nivel="" exclusao="true" /><conta estrutural="417280110000000" nivel="" exclusao="true" /><conta estrutural="417280120000000" nivel="" exclusao="true" /><conta estrutural="417180150000000" nivel="" exclusao="true" /><conta estrutural="417180610000000" nivel="" exclusao="true" /><conta estrutural="417280130000000" nivel="" exclusao="true" /><conta estrutural="417580100000000" nivel="" exclusao="true" /><conta estrutural="917000000000000" nivel="" exclusao="false" /><conta estrutural="917180120000000" nivel="" exclusao="true" /><conta estrutural="917180130000000" nivel="" exclusao="true" /><conta estrutural="917180140000000" nivel="" exclusao="true" /><conta estrutural="917280110000000" nivel="" exclusao="true" /><conta estrutural="917280120000000" nivel="" exclusao="true" /><conta estrutural="917180150000000" nivel="" exclusao="true" /><conta estrutural="917180610000000" nivel="" exclusao="true" /><conta estrutural="917280130000000" nivel="" exclusao="true" /><conta estrutural="917580100000000" nivel="" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 21, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="416410100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="916410100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="416410300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="916410300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="419220120000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="919220120000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="419220640000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="919220640000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="419440000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="919440000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="419991100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="919991100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="419999930000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="919999930000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 21, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="416400110000000" nivel="" exclusao="false" /><conta estrutural="416400310000000" nivel="" exclusao="false" /><conta estrutural="419220120000000" nivel="" exclusao="false" /><conta estrutural="419901110000000" nivel="" exclusao="false" /><conta estrutural="419909920000000" nivel="" exclusao="false" /><conta estrutural="916400110000000" nivel="" exclusao="false" /><conta estrutural="916400310000000" nivel="" exclusao="false" /><conta estrutural="919220120000000" nivel="" exclusao="false" /><conta estrutural="919901110000000" nivel="" exclusao="false" /><conta estrutural="919909920000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 21, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="416400110000000" nivel="" exclusao="false" /><conta estrutural="416400310000000" nivel="" exclusao="false" /><conta estrutural="419220120000000" nivel="" exclusao="false" /><conta estrutural="419901110000000" nivel="" exclusao="false" /><conta estrutural="419909920000000" nivel="" exclusao="false" /><conta estrutural="916400110000000" nivel="" exclusao="false" /><conta estrutural="916400310000000" nivel="" exclusao="false" /><conta estrutural="919220120000000" nivel="" exclusao="false" /><conta estrutural="919901110000000" nivel="" exclusao="false" /><conta estrutural="919909920000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 21, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="416400110000000" nivel="" exclusao="false" /><conta estrutural="416400310000000" nivel="" exclusao="false" /><conta estrutural="419220120000000" nivel="" exclusao="false" /><conta estrutural="419901110000000" nivel="" exclusao="false" /><conta estrutural="419909920000000" nivel="" exclusao="false" /><conta estrutural="916400110000000" nivel="" exclusao="false" /><conta estrutural="916400310000000" nivel="" exclusao="false" /><conta estrutural="919220120000000" nivel="" exclusao="false" /><conta estrutural="919901110000000" nivel="" exclusao="false" /><conta estrutural="919909920000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 22, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="414000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="914000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="415000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="915000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="416000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="916000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="416410100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="916410100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="416410300000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="916410300000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="419000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="919000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="419220120000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="919220120000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="419220640000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="919220640000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="419440000000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="919440000000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="419991100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="919991100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="419999930000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="919999930000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 22, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="414000000000000" nivel="" exclusao="false" /><conta estrutural="415000000000000" nivel="" exclusao="false" /><conta estrutural="416000000000000" nivel="" exclusao="false" /><conta estrutural="416400110000000" nivel="" exclusao="true" /><conta estrutural="416400310000000" nivel="" exclusao="true" /><conta estrutural="419000000000000" nivel="" exclusao="false" /><conta estrutural="419220120000000" nivel="" exclusao="true" /><conta estrutural="419901110000000" nivel="" exclusao="true" /><conta estrutural="419909920000000" nivel="" exclusao="true" /><conta estrutural="914000000000000" nivel="" exclusao="false" /><conta estrutural="915000000000000" nivel="" exclusao="false" /><conta estrutural="916000000000000" nivel="" exclusao="false" /><conta estrutural="916400110000000" nivel="" exclusao="true" /><conta estrutural="916400310000000" nivel="" exclusao="true" /><conta estrutural="919000000000000" nivel="" exclusao="false" /><conta estrutural="919220120000000" nivel="" exclusao="true" /><conta estrutural="919901110000000" nivel="" exclusao="true" /><conta estrutural="919909920000000" nivel="" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 22, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="414000000000000" nivel="" exclusao="false" /><conta estrutural="415000000000000" nivel="" exclusao="false" /><conta estrutural="416000000000000" nivel="" exclusao="false" /><conta estrutural="416400110000000" nivel="" exclusao="true" /><conta estrutural="416400310000000" nivel="" exclusao="true" /><conta estrutural="419000000000000" nivel="" exclusao="false" /><conta estrutural="419220120000000" nivel="" exclusao="true" /><conta estrutural="419901110000000" nivel="" exclusao="true" /><conta estrutural="419909920000000" nivel="" exclusao="true" /><conta estrutural="914000000000000" nivel="" exclusao="false" /><conta estrutural="915000000000000" nivel="" exclusao="false" /><conta estrutural="916000000000000" nivel="" exclusao="false" /><conta estrutural="916400110000000" nivel="" exclusao="true" /><conta estrutural="916400310000000" nivel="" exclusao="true" /><conta estrutural="919000000000000" nivel="" exclusao="false" /><conta estrutural="919220120000000" nivel="" exclusao="true" /><conta estrutural="919901110000000" nivel="" exclusao="true" /><conta estrutural="919909920000000" nivel="" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 22, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="414000000000000" nivel="" exclusao="false" /><conta estrutural="415000000000000" nivel="" exclusao="false" /><conta estrutural="416000000000000" nivel="" exclusao="false" /><conta estrutural="416400110000000" nivel="" exclusao="true" /><conta estrutural="416400310000000" nivel="" exclusao="true" /><conta estrutural="419000000000000" nivel="" exclusao="false" /><conta estrutural="419220120000000" nivel="" exclusao="true" /><conta estrutural="419901110000000" nivel="" exclusao="true" /><conta estrutural="419909920000000" nivel="" exclusao="true" /><conta estrutural="914000000000000" nivel="" exclusao="false" /><conta estrutural="915000000000000" nivel="" exclusao="false" /><conta estrutural="916000000000000" nivel="" exclusao="false" /><conta estrutural="916400110000000" nivel="" exclusao="true" /><conta estrutural="916400310000000" nivel="" exclusao="true" /><conta estrutural="919000000000000" nivel="" exclusao="false" /><conta estrutural="919220120000000" nivel="" exclusao="true" /><conta estrutural="919901110000000" nivel="" exclusao="true" /><conta estrutural="919909920000000" nivel="" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 23, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="411000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="911000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="412000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="912000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="413000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="913000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="413210100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="913210100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="413210200000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="913210200000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="413210300000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="913210300000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="413210400000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="913210400000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="413210500000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="913210500000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="413299900000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="913299900000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="140000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="914000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="415000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="915000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="416000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="916000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="416410100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="916410100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="416410300000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="916410300000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="419000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="919000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="419220120000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="919220120000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="419220640000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="919220640000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="419440000000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="919440000000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="419991100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="919991100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="419999930000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="919999930000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="471000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="971000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="472000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="972000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="473000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="973000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="473210100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="973210100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="473210200000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="973210200000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="473210300000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="973210300000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="473210400000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="973210400000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="473210500000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="973210500000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="473299900000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="973299900000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="474000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="974000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="475000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="975000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="476000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="976000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="476410100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="976410100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="476410300000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="976410300000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="479000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="979000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="479220120000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="979220120000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="479220640000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="979220640000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="479440000000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="979440000000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="479991100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="979991100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="479999930000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="979999930000000" nivel="" exclusao="true" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="notin" valor="1800,1801,1802,0051,0050" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 25, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="413210100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="913210100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="413210200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="913210200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="413210300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="913210300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="413210400000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="913210400000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="413210500000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="913210500000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="413299900000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="913299900000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="416410100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="916410100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="416410300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="916410300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="419220120000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="919220120000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="419220640000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="919220640000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="419440000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="919440000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="419991100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="919991100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="419999930000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="919999930000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="473210100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="973210100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="473210200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="473210200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="973210200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="473210300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="973210300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="473210400000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="973210400000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="473210500000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="973210500000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="473299900000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="973299900000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="476410100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="976410100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="476410300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="976410300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="479220120000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="979220120000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="479220640000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="979220640000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="479440000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="979440000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="479991100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="979991100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="479999930000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="979999930000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="1800,1801,1802,0051,0050" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 27, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="421000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="921000000000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="notin" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 27, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="421000000000000" nivel="" exclusao="false" /><conta estrutural="921000000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 27, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="421000000000000" nivel="" exclusao="false" /><conta estrutural="921000000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 27, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="421000000000000" nivel="" exclusao="false" /><conta estrutural="921000000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 28, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="423000000000000" nivel="" exclusao="false" /><conta estrutural="923000000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 28, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="423000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="923000000000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="notin" valor="1800,1801,0051,0050" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 28, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="423000000000000" nivel="" exclusao="false" /><conta estrutural="923000000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 28, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="423000000000000" nivel="" exclusao="false" /><conta estrutural="923000000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 30, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="422110100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="482110100000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="notin" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 30, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="422180110000000" nivel="" exclusao="false" /><conta estrutural="922180110000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 30, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="422180110000000" nivel="" exclusao="false" /><conta estrutural="922180110000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 30, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="422180110000000" nivel="" exclusao="false" /><conta estrutural="922180110000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 31, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="422180120000000" nivel="" exclusao="false" /><conta estrutural="942218012000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 31, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="422110200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="482110200000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="notin" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 31, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="422180120000000" nivel="" exclusao="false" /><conta estrutural="942218012000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 31, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="422180120000000" nivel="" exclusao="false" /><conta estrutural="942218012000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 32, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="422000000000000" nivel="" exclusao="false" /><conta estrutural="422180110000000" nivel="" exclusao="true" /><conta estrutural="422180120000000" nivel="" exclusao="true" /><conta estrutural="922000000000000" nivel="" exclusao="false" /><conta estrutural="922180110000000" nivel="" exclusao="true" /><conta estrutural="922180120000000" nivel="" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 32, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="422000000000000" nivel="" exclusao="false" /><conta estrutural="422180110000000" nivel="" exclusao="true" /><conta estrutural="422180120000000" nivel="" exclusao="true" /><conta estrutural="922000000000000" nivel="" exclusao="false" /><conta estrutural="922180110000000" nivel="" exclusao="true" /><conta estrutural="922180120000000" nivel="" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 32, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="422000000000000" nivel="" exclusao="false" /><conta estrutural="422180110000000" nivel="" exclusao="true" /><conta estrutural="422180120000000" nivel="" exclusao="true" /><conta estrutural="922000000000000" nivel="" exclusao="false" /><conta estrutural="922180110000000" nivel="" exclusao="true" /><conta estrutural="922180120000000" nivel="" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 32, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="422000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="422110100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="422110200000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="482000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="482110100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="482110200000000" nivel="" exclusao="true" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="notin" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 34, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="424140000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="424220000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="424320000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="424415000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="424415100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="484140000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="484220000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="484320000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="484415000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="484415100000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="notin" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 34, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="424181000000000" nivel="" exclusao="false" /><conta estrutural="424281000000000" nivel="" exclusao="false" /><conta estrutural="424381000000000" nivel="" exclusao="false" /><conta estrutural="424481000000000" nivel="" exclusao="false" /><conta estrutural="924181000000000" nivel="" exclusao="false" /><conta estrutural="924281000000000" nivel="" exclusao="false" /><conta estrutural="924381000000000" nivel="" exclusao="false" /><conta estrutural="924481000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 34, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="424181000000000" nivel="" exclusao="false" /><conta estrutural="424281000000000" nivel="" exclusao="false" /><conta estrutural="424381000000000" nivel="" exclusao="false" /><conta estrutural="424481000000000" nivel="" exclusao="false" /><conta estrutural="924181000000000" nivel="" exclusao="false" /><conta estrutural="924281000000000" nivel="" exclusao="false" /><conta estrutural="924381000000000" nivel="" exclusao="false" /><conta estrutural="924481000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 34, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="424181000000000" nivel="" exclusao="false" /><conta estrutural="424281000000000" nivel="" exclusao="false" /><conta estrutural="424381000000000" nivel="" exclusao="false" /><conta estrutural="424481000000000" nivel="" exclusao="false" /><conta estrutural="924181000000000" nivel="" exclusao="false" /><conta estrutural="924281000000000" nivel="" exclusao="false" /><conta estrutural="924381000000000" nivel="" exclusao="false" /><conta estrutural="924481000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 35, 2021, '
<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
  <contas>
    <conta estrutural="424000000000000" nivel="" exclusao="false" />
    <conta estrutural="424181000000000" nivel="" exclusao="true"  />
    <conta estrutural="424281000000000" nivel="" exclusao="true"  />
    <conta estrutural="424381000000000" nivel="" exclusao="true"  />
    <conta estrutural="424481000000000" nivel="" exclusao="true"  />
    <conta estrutural="924000000000000" nivel="" exclusao="false" />
    <conta estrutural="924181000000000" nivel="" exclusao="true"  />
    <conta estrutural="924281000000000" nivel="" exclusao="true"  />
    <conta estrutural="924381000000000" nivel="" exclusao="true"  />
    <conta estrutural="924481000000000" nivel="" exclusao="true"  />
  </contas>
  <orgao operador="in" valor="" id="orgao"/>
  <unidade operador="in" valor="" id="unidade"/>
  <funcao operador="in" valor="" id="funcao"/>
  <subfuncao operador="in" valor="" id="subfuncao"/>
  <programa operador="in" valor="" id="programa"/>
  <projativ operador="in" valor="" id="projativ"/>
  <recurso operador="in" valor="" id="recurso"/>
  <recursocontalinha numerolinha="" id="recursocontalinha"/>
  <observacao valor=""/><desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 35, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="924000000000000" nivel="" exclusao="false" /><conta estrutural="424181000000000" nivel="" exclusao="true" /><conta estrutural="424281000000000" nivel="" exclusao="true" /><conta estrutural="424381000000000" nivel="" exclusao="true" /><conta estrutural="424481000000000" nivel="" exclusao="true" /><conta estrutural="924000000000000" nivel="" exclusao="false" /><conta estrutural="924181000000000" nivel="" exclusao="true" /><conta estrutural="924281000000000" nivel="" exclusao="true" /><conta estrutural="924381000000000" nivel="" exclusao="true" /><conta estrutural="924481000000000" nivel="" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 35, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="924000000000000" nivel="" exclusao="false" /><conta estrutural="424181000000000" nivel="" exclusao="true" /><conta estrutural="424281000000000" nivel="" exclusao="true" /><conta estrutural="424381000000000" nivel="" exclusao="true" /><conta estrutural="424481000000000" nivel="" exclusao="true" /><conta estrutural="924000000000000" nivel="" exclusao="false" /><conta estrutural="924181000000000" nivel="" exclusao="true" /><conta estrutural="924281000000000" nivel="" exclusao="true" /><conta estrutural="924381000000000" nivel="" exclusao="true" /><conta estrutural="924481000000000" nivel="" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 35, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="424000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="424140000000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="424220000000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="424320000000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="424415000000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="424415100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="484000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="484140000000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="484220000000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="484320000000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="484415000000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="484415100000000" nivel="" exclusao="true" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="notin" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 37, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="429200000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="429300000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="429400000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="489200000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="489300000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="489400000000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="notin" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 37, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="429200000000000" nivel="" exclusao="false" /><conta estrutural="429300000000000" nivel="" exclusao="false" /><conta estrutural="429400000000000" nivel="" exclusao="false" /><conta estrutural="929200000000000" nivel="" exclusao="false" /><conta estrutural="929300000000000" nivel="" exclusao="false" /><conta estrutural="929400000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 37, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="429200000000000" nivel="" exclusao="false" /><conta estrutural="429300000000000" nivel="" exclusao="false" /><conta estrutural="429400000000000" nivel="" exclusao="false" /><conta estrutural="929200000000000" nivel="" exclusao="false" /><conta estrutural="929300000000000" nivel="" exclusao="false" /><conta estrutural="929400000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 37, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="429200000000000" nivel="" exclusao="false" /><conta estrutural="429300000000000" nivel="" exclusao="false" /><conta estrutural="429400000000000" nivel="" exclusao="false" /><conta estrutural="929200000000000" nivel="" exclusao="false" /><conta estrutural="929300000000000" nivel="" exclusao="false" /><conta estrutural="929400000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 38, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="429100000000000" nivel="" exclusao="false" /><conta estrutural="429900000000000" nivel="" exclusao="false" /><conta estrutural="929100000000000" nivel="" exclusao="false" /><conta estrutural="929900000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 38, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="429100000000000" nivel="" exclusao="false" /><conta estrutural="429900000000000" nivel="" exclusao="false" /><conta estrutural="929100000000000" nivel="" exclusao="false" /><conta estrutural="929900000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 38, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="429100000000000" nivel="" exclusao="false" /><conta estrutural="429900000000000" nivel="" exclusao="false" /><conta estrutural="929100000000000" nivel="" exclusao="false" /><conta estrutural="929900000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 38, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="429100000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="429900000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="489100000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="489900000000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="notin" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 40, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="422000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="422110100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="422110200000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="424000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="429100000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="429900000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="482000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="482110100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="482110200000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="484000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="489100000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="489900000000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="1800,1802,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 41, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="421000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="423000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="422110100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="422110200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="429200000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="429300000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="429400000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="481000000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="489300000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="482110100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="482110200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="489200000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="489300000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="489400000000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 45, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="331000000000000" nivel="3" exclusao="false" /><conta estrutural="331910000000000" nivel="5" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 45, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="331000000000000" nivel="3" exclusao="false" /><conta estrutural="331910000000000" nivel="5" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 45, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="331000000000000" nivel="3" exclusao="false" /><conta estrutural="331910000000000" nivel="5" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 45, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="331000000000000" nivel="3" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="notin" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 46, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="332000000000000" nivel="3" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="notin" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 46, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="332000000000000" nivel="3" exclusao="false" /><conta estrutural="332910000000000" nivel="5" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 46, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="332000000000000" nivel="3" exclusao="false" /><conta estrutural="332910000000000" nivel="5" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 46, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="332000000000000" nivel="3" exclusao="false" /><conta estrutural="332910000000000" nivel="5" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 47, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="333000000000000" nivel="3" exclusao="false" /><conta estrutural="333910000000000" nivel="5" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 47, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="333000000000000" nivel="3" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="notin" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 47, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="333000000000000" nivel="3" exclusao="false" /><conta estrutural="333910000000000" nivel="5" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 47, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="333000000000000" nivel="3" exclusao="false" /><conta estrutural="333910000000000" nivel="5" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 49, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="331000000000000" nivel="3" exclusao="false" indicador=""/>
  <conta estrutural="333000000000000" nivel="3" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 50, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="332000000000000" nivel="3" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 52, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="344000000000000" nivel="3" exclusao="false" /><conta estrutural="344910000000000" nivel="5" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 52, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="344000000000000" nivel="3" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="notin" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 52, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="344000000000000" nivel="3" exclusao="false" /><conta estrutural="344910000000000" nivel="5" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 52, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="344000000000000" nivel="3" exclusao="false" /><conta estrutural="344910000000000" nivel="5" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 54, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="345906600000000" nivel="7" exclusao="false" /><conta estrutural="345916600000000" nivel="7" exclusao="true" /><conta estrutural="345909266000000" nivel="9" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 54, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="345906600000000" nivel="7" exclusao="false" indicador=""/>
  <conta estrutural="345909266000000" nivel="9" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="notin" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 54, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="345906600000000" nivel="7" exclusao="false" /><conta estrutural="345916600000000" nivel="7" exclusao="true" /><conta estrutural="345909266000000" nivel="9" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 54, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="345906600000000" nivel="7" exclusao="false" /><conta estrutural="345916600000000" nivel="7" exclusao="true" /><conta estrutural="345909266000000" nivel="9" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 55, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="345906400000000" nivel="7" exclusao="false" indicador=""/>
  <conta estrutural="345909264000000" nivel="9" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="notin" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 55, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="345906400000000" nivel="7" exclusao="false" /><conta estrutural="345916400000000" nivel="7" exclusao="true" /><conta estrutural="345909264000000" nivel="9" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 55, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="345906400000000" nivel="7" exclusao="false" /><conta estrutural="345916400000000" nivel="7" exclusao="true" /><conta estrutural="345909264000000" nivel="9" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 55, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="345906400000000" nivel="7" exclusao="false" /><conta estrutural="345916400000000" nivel="7" exclusao="true" /><conta estrutural="345909264000000" nivel="9" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 56, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="345906300000000" nivel="7" exclusao="false" indicador=""/>
  <conta estrutural="345909263000000" nivel="9" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="notin" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 56, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="345906300000000" nivel="7" exclusao="false" /><conta estrutural="345909263000000" nivel="9" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 56, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="345906300000000" nivel="7" exclusao="false" /><conta estrutural="345909263000000" nivel="9" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 56, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="345906300000000" nivel="7" exclusao="false" /><conta estrutural="345909263000000" nivel="9" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 57, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="345000000000000" nivel="3" exclusao="false" indicador=""/>
  <conta estrutural="345906600000000" nivel="7" exclusao="true" indicador=""/>
  <conta estrutural="345906400000000" nivel="7" exclusao="true" indicador=""/>
  <conta estrutural="345906300000000" nivel="7" exclusao="true" indicador=""/>
  <conta estrutural="345909263000000" nivel="9" exclusao="true" indicador=""/>
  <conta estrutural="345909264000000" nivel="9" exclusao="true" indicador=""/>
  <conta estrutural="345909266000000" nivel="9" exclusao="true" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="notin" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 57, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="345000000000000" nivel="3" exclusao="false" /><conta estrutural="345906600000000" nivel="7" exclusao="true" /><conta estrutural="345906400000000" nivel="7" exclusao="true" /><conta estrutural="345906300000000" nivel="7" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 57, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="345000000000000" nivel="3" exclusao="false" /><conta estrutural="345906600000000" nivel="7" exclusao="true" /><conta estrutural="345906400000000" nivel="7" exclusao="true" /><conta estrutural="345906300000000" nivel="7" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 57, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="345000000000000" nivel="3" exclusao="false" /><conta estrutural="345906600000000" nivel="7" exclusao="true" /><conta estrutural="345906400000000" nivel="7" exclusao="true" /><conta estrutural="345906300000000" nivel="7" exclusao="true" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 58, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="346000000000000" nivel="3" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="notin" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 58, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="346000000000000" nivel="3" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 58, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="346000000000000" nivel="3" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 58, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="346000000000000" nivel="3" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 60, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="399000000000000" nivel="3" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 60, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="399000000000000" nivel="3" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 60, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="399000000000000" nivel="3" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 60, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="399000000000000" nivel="3" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 61, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="344000000000000" nivel="3" exclusao="false" indicador=""/>
  <conta estrutural="345000000000000" nivel="3" exclusao="false" indicador=""/>
  <conta estrutural="345906300000000" nivel="7" exclusao="true" indicador=""/>
  <conta estrutural="345906600000000" nivel="7" exclusao="true" indicador=""/>
  <conta estrutural="345906400000000" nivel="7" exclusao="true" indicador=""/>
  <conta estrutural="345909263000000" nivel="9" exclusao="true" indicador=""/>
  <conta estrutural="345909264000000" nivel="9" exclusao="true" indicador=""/>
  <conta estrutural="345909266000000" nivel="9" exclusao="true" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 62, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="345906300000000" nivel="7" exclusao="false" indicador=""/>
  <conta estrutural="345906400000000" nivel="7" exclusao="false" indicador=""/>
  <conta estrutural="345906600000000" nivel="7" exclusao="false" indicador=""/>
  <conta estrutural="345909263000000" nivel="9" exclusao="false" indicador=""/>
  <conta estrutural="345909264000000" nivel="9" exclusao="false" indicador=""/>
  <conta estrutural="345909266000000" nivel="9" exclusao="false" indicador=""/>
  <conta estrutural="346000000000000" nivel="3" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 68, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="441110000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 68, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="443930171 00000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="441119900000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="441210000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="441230000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="441240000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="441250000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="441310000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="441330000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="441340000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="441350000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="441410000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="442110000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="442130000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="442140000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="442150000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="442210000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="442520200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="442610000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="442630000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="442640000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="442650000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="443110100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="443119900000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="443130100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="443140100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="443150100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="443210100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="443310100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="443319900000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="443330100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="443340100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="443350100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="443410100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="443510100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="443530100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="443540100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="443550100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="443910170000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="443930170000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="443930171000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="445110000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="445210000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="446110000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="446130000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="446140000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="446150000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="446210000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="446230000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="446240000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="446250000000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 68, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="441110000000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="0400" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 68, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="441110000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 69, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="341110000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="341130000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="341140000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="341150000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="341210000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="341310000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="341330000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="341340000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="341350000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="341410000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="341810000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="341830000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="341840000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="341850000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="341910000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="342110000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="342130000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="342140000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="342150000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="342210000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="342520200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="342610000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="342630000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="342640000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="342650000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="343110100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="343140100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="343130100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="343150100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="343310100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="343210100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="343330100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="343340100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="343350100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="343410100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="343510100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="343530100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="343540100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="343550100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="343910170000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="343930170000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="343930171000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="345110000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="345210000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="346110000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="346130000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="346140000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="346150000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="349110000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="349130000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="349140000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="349150000000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 71, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
    <contas>
        <conta estrutural="212110100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212130100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212140100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212150100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212210100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218410000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218430000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218440000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218450000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222110100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222130100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222140100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222150100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222210100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228310000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228330000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228340000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228350000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212110201000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212110298000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212110300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212130201000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212130298000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212139900000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212140201000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212140298000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212140300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212150201000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212150298000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212150300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212510100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212510200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212530100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212530200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212540100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212540200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212550000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222110200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222110300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222130200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222139900000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222140200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222140300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222150200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222150300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222510100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222510200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222530000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222540000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222550000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212210200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212210300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212610100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212610200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222210200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222210300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222610100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222610200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212130400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212130500000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222130400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222130500000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212310100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212310200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212330100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212330200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212340100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212340200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212350000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212510300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212510400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212530300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212530400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212540300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212540400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110102000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213210102000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213210202000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222310000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222330000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222340000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222350000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222510300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222510400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223110102000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223210102000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212410000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212610300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212610400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222410000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222610300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222610400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="214111200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="214131200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="214210300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="214240300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="214310300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="214350300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="224110200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="224130200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="224210100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="224240100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="224310100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="224350100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211410102000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211410600000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211420200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211430102000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211430600000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211440600000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211450600000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221410100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221420200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221430101000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211410700000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211430700000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221410200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221430200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221410300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110103000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110303000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213210103000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213210203000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223110103000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223210103000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218110000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218130000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218140000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218150000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228110000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228130000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228140000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228150000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211110402000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211110502000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211110700000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211210402000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211210502000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211210700000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211310302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211310402000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110502000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110602000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110702000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110802000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213111100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221110302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221110402000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221110700000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221210202000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221210302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221310202000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221310302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223110402000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223110502000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223110602000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223110702000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223111100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="853700000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222910100000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212910100000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212910200000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212810200000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212830200000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212840200000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="222810200000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="222830000000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="222840000000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="222850000000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="222910200000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212810100000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212830100000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212840100000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="222810100000000" nivel="" exclusao="true" indicador=""/>
    </contas>
    <orgao operador="in" valor="" id="orgao"/>
    <unidade operador="in" valor="" id="unidade"/>
    <funcao operador="in" valor="" id="funcao"/>
    <subfuncao operador="in" valor="" id="subfuncao"/>
    <programa operador="in" valor="" id="programa"/>
    <projativ operador="in" valor="" id="projativ"/>
    <recurso operador="in" valor="" id="recurso"/>
    <recursocontalinha numerolinha="" id="recursocontalinha"/>
    <observacao valor=""/>
    <desdobrarlinha valor="false"/>
</filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 71, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
    <contas>
        <conta estrutural="212110100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212130100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212140100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212150100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212210100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218410000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218430000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218440000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218450000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222110100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222130100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222140100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222150100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222210100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228310000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228330000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228340000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228350000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212110201000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212110298000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212110300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212130201000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212130298000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212139900000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212140201000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212140298000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212140300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212150201000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212150298000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212150300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212510100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212510200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212530100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212530200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212540100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212540200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212550000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222110200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222110300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222130200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222139900000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222140200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222140300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222150200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222150300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222510100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222510200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222530000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222540000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222550000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212210200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212210300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212610100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212610200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222210200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222210300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222610100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222610200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212130400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212130500000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222130400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222130500000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212310100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212310200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212330100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212330200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212340100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212340200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212350000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212510300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212510400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212530300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212530400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212540300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212540400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110102000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213210102000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213210202000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222310000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222330000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222340000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222350000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222510300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222510400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223110102000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223210102000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212410000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212610300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212610400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222410000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222610300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222610400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="214111200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="214131200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="214210300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="214240300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="214310300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="214350300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="224110200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="224130200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="224210100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="224240100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="224310100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="224350100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211410102000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211410600000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211420200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211430102000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211430600000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211440600000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211450600000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221410100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221420200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221430101000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211410700000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211430700000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221410200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221430200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221410300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110103000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110303000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213210103000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213210203000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223110103000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223210103000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218110000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218130000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218140000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218150000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228110000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228130000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228140000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228150000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211110402000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211110502000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211110700000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211210402000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211210502000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211210700000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211310302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211310402000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110502000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110602000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110702000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110802000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213111100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221110302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221110402000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221110700000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221210202000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221210302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221310202000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221310302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223110402000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223110502000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223110602000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223110702000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223111100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="853700000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222910100000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212910100000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212910200000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212810200000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212830200000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212840200000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="222810200000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="222830000000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="222840000000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="222850000000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="222910200000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212810100000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212830100000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212840100000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="222810100000000" nivel="" exclusao="true" indicador=""/>
    </contas>
    <orgao operador="in" valor="" id="orgao"/>
    <unidade operador="in" valor="" id="unidade"/>
    <funcao operador="in" valor="" id="funcao"/>
    <subfuncao operador="in" valor="" id="subfuncao"/>
    <programa operador="in" valor="" id="programa"/>
    <projativ operador="in" valor="" id="projativ"/>
    <recurso operador="in" valor="" id="recurso"/>
    <recursocontalinha numerolinha="" id="recursocontalinha"/>
    <observacao valor=""/>
    <desdobrarlinha valor="false"/>
</filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 71, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
    <contas>
        <conta estrutural="212110100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212130100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212140100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212150100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212210100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218410000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218430000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218440000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218450000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222110100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222130100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222140100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222150100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222210100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228310000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228330000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228340000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228350000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212110201000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212110298000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212110300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212130201000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212130298000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212139900000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212140201000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212140298000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212140300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212150201000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212150298000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212150300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212510100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212510200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212530100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212530200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212540100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212540200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212550000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222110200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222110300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222130200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222139900000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222140200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222140300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222150200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222150300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222510100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222510200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222530000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222540000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222550000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212210200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212210300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212610100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212610200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222210200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222210300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222610100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222610200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212130400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212130500000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222130400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222130500000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212310100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212310200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212330100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212330200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212340100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212340200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212350000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212510300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212510400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212530300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212530400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212540300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212540400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110102000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213210102000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213210202000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222310000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222330000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222340000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222350000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222510300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222510400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223110102000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223210102000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212410000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212610300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212610400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222410000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222610300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222610400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="214111200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="214131200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="214210300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="214240300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="214310300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="214350300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="224110200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="224130200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="224210100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="224240100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="224310100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="224350100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211410102000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211410600000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211420200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211430102000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211430600000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211440600000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211450600000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221410100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221420200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221430101000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211410700000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211430700000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221410200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221430200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221410300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110103000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110303000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213210103000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213210203000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223110103000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223210103000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218110000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218130000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218140000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218150000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228110000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228130000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228140000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228150000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211110402000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211110502000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211110700000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211210402000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211210502000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211210700000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211310302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211310402000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110502000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110602000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110702000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110802000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213111100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221110302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221110402000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221110700000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221210202000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221210302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221310202000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221310302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223110402000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223110502000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223110602000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223110702000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223111100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="853700000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222910100000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212910100000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212910200000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212810200000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212830200000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212840200000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="222810200000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="222830000000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="222840000000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="222850000000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="222910200000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212810100000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212830100000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212840100000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="222810100000000" nivel="" exclusao="true" indicador=""/>
    </contas>
    <orgao operador="in" valor="" id="orgao"/>
    <unidade operador="in" valor="" id="unidade"/>
    <funcao operador="in" valor="" id="funcao"/>
    <subfuncao operador="in" valor="" id="subfuncao"/>
    <programa operador="in" valor="" id="programa"/>
    <projativ operador="in" valor="" id="projativ"/>
    <recurso operador="in" valor="" id="recurso"/>
    <recursocontalinha numerolinha="" id="recursocontalinha"/>
    <observacao valor=""/>
    <desdobrarlinha valor="false"/>
</filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 71, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
    <contas>
        <conta estrutural="212110100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212130100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212140100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212150100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212210100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218410000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218430000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218440000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218450000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222110100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222130100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222140100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222150100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222210100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228310000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228330000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228340000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228350000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212110201000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212110298000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212110300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212130201000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212130298000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212139900000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212140201000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212140298000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212140300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212150201000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212150298000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212150300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212510100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212510200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212530100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212530200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212540100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212540200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212550000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222110200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222110300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222130200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222139900000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222140200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222140300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222150200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222150300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222510100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222510200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222530000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222540000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222550000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212210200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212210300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212610100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212610200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222210200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222210300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222610100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222610200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212130400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212130500000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222130400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222130500000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212310100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212310200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212330100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212330200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212340100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212340200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212350000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212510300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212510400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212530300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212530400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212540300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212540400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110102000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213210102000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213210202000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222310000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222330000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222340000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222350000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222510300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222510400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223110102000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223210102000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212410000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212610300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="212610400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222410000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222610300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222610400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="214111200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="214131200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="214210300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="214240300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="214310300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="214350300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="224110200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="224130200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="224210100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="224240100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="224310100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="224350100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211410102000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211410600000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211420200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211430102000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211430600000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211440600000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211450600000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221410100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221420200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221430101000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211410700000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211430700000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221410200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221430200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221410300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110103000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110303000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213210103000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213210203000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223110103000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223210103000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218110000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218130000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218140000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="218150000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228110000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228130000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228140000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="228150000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211110402000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211110502000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211110700000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211210402000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211210502000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211210700000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211310302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="211310402000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110502000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110602000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110702000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213110802000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="213111100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221110302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221110402000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221110700000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221210202000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221210302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221310202000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="221310302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223110402000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223110502000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223110602000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223110702000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="223111100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="853700000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="222910100000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212910100000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212910200000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212810200000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212830200000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212840200000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="222810200000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="222830000000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="222840000000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="222850000000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="222910200000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212810100000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212830100000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="212840100000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="222810100000000" nivel="" exclusao="true" indicador=""/>
    </contas>
    <orgao operador="in" valor="" id="orgao"/>
    <unidade operador="in" valor="" id="unidade"/>
    <funcao operador="in" valor="" id="funcao"/>
    <subfuncao operador="in" valor="" id="subfuncao"/>
    <programa operador="in" valor="" id="programa"/>
    <projativ operador="in" valor="" id="projativ"/>
    <recurso operador="in" valor="" id="recurso"/>
    <recursocontalinha numerolinha="" id="recursocontalinha"/>
    <observacao valor=""/>
    <desdobrarlinha valor="false"/>
</filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 74, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="111000000000000" nivel="" exclusao="false" /><conta estrutural="114000000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 74, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="111110100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="111110200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="111111900000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="111113000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="111115000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="111210100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="111210200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="111210300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="111310000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="111330000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="111340000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="111350000000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="notin" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 74, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="111000000000000" nivel="" exclusao="false" /><conta estrutural="114000000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 74, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="111000000000000" nivel="" exclusao="false" /><conta estrutural="114000000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 75, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter> <contas> <conta estrutural="622130700000000" nivel="" exclusao="false" indicador=""/> <conta estrutural="631300000000000" nivel="" exclusao="false" indicador=""/> <conta estrutural="632100000000000" nivel="" exclusao="false" indicador=""/> <conta estrutural="632700000000000" nivel="" exclusao="false" indicador=""/> </contas> <orgao operador="in" valor="" id="orgao"/> <unidade operador="in" valor="" id="unidade"/> <funcao operador="in" valor="" id="funcao"/> <subfuncao operador="in" valor="" id="subfuncao"/> <programa operador="in" valor="" id="programa"/> <projativ operador="in" valor="" id="projativ"/> <recurso operador="in" valor="" id="recurso"/> <fonterecurso operador="in" valor="" id="fonterecurso"/> <complemento operador="in" valor="" id="complemento"/> <recursocontalinha numerolinha="" id="recursocontalinha"/> <observacao valor=""/> <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 75, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="622130700000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="631300000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="632100000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="632700000000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="notin" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 75, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter> <contas> <conta estrutural="622130700000000" nivel="" exclusao="false" indicador=""/> <conta estrutural="631300000000000" nivel="" exclusao="false" indicador=""/> <conta estrutural="632100000000000" nivel="" exclusao="false" indicador=""/> <conta estrutural="632700000000000" nivel="" exclusao="false" indicador=""/> </contas> <orgao operador="in" valor="" id="orgao"/> <unidade operador="in" valor="" id="unidade"/> <funcao operador="in" valor="" id="funcao"/> <subfuncao operador="in" valor="" id="subfuncao"/> <programa operador="in" valor="" id="programa"/> <projativ operador="in" valor="" id="projativ"/> <recurso operador="in" valor="" id="recurso"/> <fonterecurso operador="in" valor="" id="fonterecurso"/> <complemento operador="in" valor="" id="complemento"/> <recursocontalinha numerolinha="" id="recursocontalinha"/> <observacao valor=""/> <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 75, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter> <contas> <conta estrutural="622130700000000" nivel="" exclusao="false" indicador=""/> <conta estrutural="631300000000000" nivel="" exclusao="false" indicador=""/> <conta estrutural="632100000000000" nivel="" exclusao="false" indicador=""/> <conta estrutural="632700000000000" nivel="" exclusao="false" indicador=""/> </contas> <orgao operador="in" valor="" id="orgao"/> <unidade operador="in" valor="" id="unidade"/> <funcao operador="in" valor="" id="funcao"/> <subfuncao operador="in" valor="" id="subfuncao"/> <programa operador="in" valor="" id="programa"/> <projativ operador="in" valor="" id="projativ"/> <recurso operador="in" valor="" id="recurso"/> <fonterecurso operador="in" valor="" id="fonterecurso"/> <complemento operador="in" valor="" id="complemento"/> <recursocontalinha numerolinha="" id="recursocontalinha"/> <observacao valor=""/> <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 76, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="218810000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="218830000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="218840000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="218850000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="228810000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="228830000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="228840000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="228850000000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="notin" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 77, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
    <contas>
        <conta estrutural="112410100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112410200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112410300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112430100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112430200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112430300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112440100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112440200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112440300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112450100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112450200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112450300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="114110100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="114110200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="114110300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="114111500000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="114119900000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="114200000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="114300000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121110301000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121110302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121130300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121140301000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121140302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121140303000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121140304000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121150301000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121150302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121150303000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121150304000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121310100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121310200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121310300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121310400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112910300000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="112920300000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="112930300000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="112940300000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="112950300000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="114910000000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="121119903000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="121139900000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="121149900000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="121159900000000" nivel="" exclusao="true" indicador=""/>
    </contas>
    <orgao operador="in" valor="" id="orgao"/>
    <unidade operador="in" valor="" id="unidade"/>
    <funcao operador="in" valor="" id="funcao"/>
    <subfuncao operador="in" valor="" id="subfuncao"/>
    <programa operador="in" valor="" id="programa"/>
    <projativ operador="in" valor="" id="projativ"/>
    <recurso operador="in" valor="" id="recurso"/>
    <recursocontalinha numerolinha="" id="recursocontalinha"/>
    <observacao valor=""/>
    <desdobrarlinha valor="false"/>
</filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 77, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
    <contas>
        <conta estrutural="112410100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112410200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112410300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112430100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112430200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112430300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112440100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112440200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112440300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112450100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112450200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112450300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="114110100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="114110200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="114110300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="114111500000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="114119900000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="114200000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="114300000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121110301000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121110302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121130300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121140301000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121140302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121140303000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121140304000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121150301000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121150302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121150303000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121150304000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121310100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121310200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121310300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121310400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112910300000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="112920300000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="112930300000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="112940300000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="112950300000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="114910000000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="121119903000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="121139900000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="121149900000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="121159900000000" nivel="" exclusao="true" indicador=""/>
    </contas>
    <orgao operador="in" valor="" id="orgao"/>
    <unidade operador="in" valor="" id="unidade"/>
    <funcao operador="in" valor="" id="funcao"/>
    <subfuncao operador="in" valor="" id="subfuncao"/>
    <programa operador="in" valor="" id="programa"/>
    <projativ operador="in" valor="" id="projativ"/>
    <recurso operador="in" valor="" id="recurso"/>
    <recursocontalinha numerolinha="" id="recursocontalinha"/>
    <observacao valor=""/>
    <desdobrarlinha valor="false"/>
</filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 77, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
    <contas>
        <conta estrutural="112410100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112410200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112410300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112430100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112430200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112430300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112440100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112440200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112440300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112450100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112450200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112450300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="114110100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="114110200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="114110300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="114111500000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="114119900000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="114200000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="114300000000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121110301000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121110302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121130300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121140301000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121140302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121140303000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121140304000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121150301000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121150302000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121150303000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121150304000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121310100000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121310200000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121310300000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="121310400000000" nivel="" exclusao="false" indicador=""/>
        <conta estrutural="112910300000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="112920300000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="112930300000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="112940300000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="112950300000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="114910000000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="121119903000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="121139900000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="121149900000000" nivel="" exclusao="true" indicador=""/>
        <conta estrutural="121159900000000" nivel="" exclusao="true" indicador=""/>
    </contas>
    <orgao operador="in" valor="" id="orgao"/>
    <unidade operador="in" valor="" id="unidade"/>
    <funcao operador="in" valor="" id="funcao"/>
    <subfuncao operador="in" valor="" id="subfuncao"/>
    <programa operador="in" valor="" id="programa"/>
    <projativ operador="in" valor="" id="projativ"/>
    <recurso operador="in" valor="" id="recurso"/>
    <recursocontalinha numerolinha="" id="recursocontalinha"/>
    <observacao valor=""/>
    <desdobrarlinha valor="false"/>
</filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 77, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="112410100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112410200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112410300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112419900000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112430100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112430300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112439900000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112440100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112440100 00000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112440200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112440300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112449900000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112450100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112450200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112450300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112459900000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="112910300000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="112930300000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="112940300000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="112950300000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="113510800000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="114110100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="114110200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="114110300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="114119900000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="114200000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="114300000000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="114910100000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="114910200000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="114910300000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="114910400000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="114919900000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="121110301000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121110302000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121110399000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121119700000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121119903000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="121119999000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="121130300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121139700000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121139903000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="121139999000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="121140301000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121140302000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121140303000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121140304000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121140399000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121149700000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121149903000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="121149999000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="121150301000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121150302000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121150303000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121150304000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121150399000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121159700000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121159903000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="121159999000000" nivel="" exclusao="true" indicador=""/>
  <conta estrutural="121310100000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121310200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121310300000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121310400000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121319800000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="121319900000000" nivel="" exclusao="true" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="notin" valor="1800,1801,0050,0051" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 82, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="422110200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="922110200000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 83, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="343110200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="343210200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="343410200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="343510200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="343530200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="343540200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="343550200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="443110200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="443210200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="443410200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="443510200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="443540200000000" nivel="" exclusao="false" indicador=""/>
  <conta estrutural="443550200000000" nivel="" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="" id="funcao"/>
 <subfuncao operador="in" valor="" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 90, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="499900000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 90, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="499900000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 90, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="499900000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 90, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="499900000000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 91, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="522130100000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 91, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="522130100000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 91, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="522130100000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 91, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="522130100000000" nivel="" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 92, 2021, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="399000000000000" nivel="3" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 92, 2023, '<?xml version="1.0" encoding="ISO-8859-1"?>
<filter>
 <contas>
  <conta estrutural="399000000000000" nivel="3" exclusao="false" indicador=""/>
 </contas>
 <orgao operador="in" valor="" id="orgao"/>
 <unidade operador="in" valor="" id="unidade"/>
 <funcao operador="in" valor="99" id="funcao"/>
 <subfuncao operador="in" valor="997" id="subfuncao"/>
 <programa operador="in" valor="" id="programa"/>
 <projativ operador="in" valor="" id="projativ"/>
 <recurso operador="in" valor="" id="recurso"/>
 <fonterecurso operador="in" valor="" id="fonterecurso"/>
 <complemento operador="in" valor="" id="complemento"/>
 <recursocontalinha numerolinha="" id="recursocontalinha"/>
 <observacao valor=""/>
 <desdobrarlinha valor="false"/>
</filter>
');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 92, 2019, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="399000000000000" nivel="3" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqfiltropadrao (o132_sequencial, o132_orcparamrel, o132_orcparamseq, o132_anousu, o132_filtro) values (nextval('orcparamelementospadrao_o132_sequencial_seq'), 271, 92, 2020, '<?xml version="1.0" encoding="ISO-8859-1"?><filter><contas><conta estrutural="399000000000000" nivel="3" exclusao="false" /></contas><orgao operador="in" valor="" id="orgao"/><unidade operador="in" valor="" id="unidade"/><funcao operador="in" valor="" id="funcao"/><subfuncao operador="in" valor="" id="subfuncao"/><programa operador="in" valor="" id="programa"/><projativ operador="in" valor="" id="projativ"/><recurso operador="in" valor="" id="recurso"/><recursocontalinha numerolinha="" id="recursocontalinha"/><observacao valor=""/><desdobrarlinha valor="false"/></filter>');
insert into orcparamseqcoluna (o115_sequencial, o115_anousu, o115_descricao, o115_tipo, o115_valoresdefault, o115_nomecoluna, o115_formula, o115_origem, o115_relatorio) values (nextval('orcparamseqcoluna_o115_sequencial_seq'), 2021, 'Bal Ver Saldo Final Acumulado', 1, '', 'saldo_final_acumulado', '', 0, 271);
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 82, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 82, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 82, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 82, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 82, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 82, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 81, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 81, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 81, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 81, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 81, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 81, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 87, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 87, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 87, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 87, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 87, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 87, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 88, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 88, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 88, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 88, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 88, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 88, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqcoluna (o115_sequencial, o115_anousu, o115_descricao, o115_tipo, o115_valoresdefault, o115_nomecoluna, o115_formula, o115_origem, o115_relatorio) values (nextval('orcparamseqcoluna_o115_sequencial_seq'), 2022, 'RP - Processado', 1, '', 'inscricao_rp_processado', '', 0, 271);
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 93, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 93, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 93, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 93, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 93, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 93, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqcoluna (o115_sequencial, o115_anousu, o115_descricao, o115_tipo, o115_valoresdefault, o115_nomecoluna, o115_formula, o115_origem, o115_relatorio) values (nextval('orcparamseqcoluna_o115_sequencial_seq'), 2022, 'RP - Saldo dos RP inscritos como processados no exercício', 1, '', 'saldo_rp_processado', '', 0, 271);
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 93, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 93, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 93, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 93, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 93, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 93, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqcoluna (o115_sequencial, o115_anousu, o115_descricao, o115_tipo, o115_valoresdefault, o115_nomecoluna, o115_formula, o115_origem, o115_relatorio) values (nextval('orcparamseqcoluna_o115_sequencial_seq'), 2021, 'Bal Rec - Arrecadado acumulado', 1, '', 'arrecadado_acumulado', '', 0, 271);
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 23, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 38, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 37, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 36, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 35, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 34, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 32, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 31, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 30, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 28, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 27, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 22, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 21, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 19, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 18, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 17, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 16, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 15, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 14, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 13, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 11, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 10, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 8, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 7, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 6, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 5, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 4, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 3, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 12, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 1, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 2, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 9, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 20, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 26, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 29, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 33, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 39, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 42, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 23, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 27, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 38, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 37, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 36, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 35, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 34, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 32, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 31, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 30, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 28, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 22, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 21, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 19, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 18, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 17, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 16, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 15, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 14, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 13, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 11, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 10, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 8, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 7, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 6, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 5, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 4, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 3, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 12, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 1, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 2, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 9, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 20, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 26, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 29, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 33, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 39, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 42, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 23, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 38, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 37, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 36, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 35, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 34, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 32, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 31, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 30, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 28, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 27, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 22, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 21, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 19, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 18, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 17, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 16, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 15, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 14, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 13, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 11, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 10, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 8, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 7, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 6, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 5, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 4, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 3, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 12, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 1, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 2, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 9, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 20, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 26, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 29, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 33, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 39, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 42, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 42, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 38, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 37, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 36, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 35, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 34, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 32, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 31, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 30, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 28, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 27, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 22, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 21, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 19, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 18, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 17, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 16, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 15, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 14, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 13, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 11, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 10, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 8, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 7, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 6, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 5, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 4, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 3, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 12, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 1, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 2, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 9, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 20, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 23, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 26, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 29, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 33, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 39, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 42, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 35, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 38, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 37, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 36, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 34, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 32, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 31, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 30, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 28, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 27, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 22, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 21, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 19, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 18, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 17, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 16, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 15, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 14, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 13, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 11, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 10, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 8, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 7, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 6, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 5, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 4, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 3, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 12, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 1, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 2, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 9, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 20, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 23, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 26, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 29, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 33, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 39, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 42, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 38, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 37, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 36, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 35, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 34, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 32, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 31, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 30, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 28, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 27, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 22, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 21, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 19, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 18, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 17, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 16, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 15, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 14, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 13, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 11, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 10, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 8, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 7, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 6, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 5, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 4, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 3, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 12, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 1, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 2, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 9, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 20, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 23, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 26, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 29, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 33, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 39, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 24, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 24, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 24, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 24, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 24, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 24, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 25, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 25, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 25, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 25, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 25, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 25, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 40, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 40, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 40, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 40, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 40, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 40, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 41, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 41, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 41, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 41, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 41, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 41, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 43, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 43, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 43, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 43, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 43, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 43, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqcoluna (o115_sequencial, o115_anousu, o115_descricao, o115_tipo, o115_valoresdefault, o115_nomecoluna, o115_formula, o115_origem, o115_relatorio) values (nextval('orcparamseqcoluna_o115_sequencial_seq'), 2021, 'Bal Deps - Total de Créditos (Dot Atualizada)', 1, '', 'total_creditos', '', 0, 271);
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 92, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 92, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 92, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 92, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 92, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 92, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqcoluna (o115_sequencial, o115_anousu, o115_descricao, o115_tipo, o115_valoresdefault, o115_nomecoluna, o115_formula, o115_origem, o115_relatorio) values (nextval('orcparamseqcoluna_o115_sequencial_seq'), 2011, 'Valor', 1, '', 'valor', '', 0, 271);
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 67, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 80, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 79, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 89, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 65, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 70, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 67, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 80, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 79, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 89, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 65, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 70, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 67, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 80, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 79, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 89, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 65, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 70, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 67, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 80, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 79, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 89, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 65, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 70, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 67, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 80, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 79, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 89, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 65, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 70, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 67, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 80, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 79, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 89, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 65, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 70, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 66, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 66, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 66, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 66, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 66, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 66, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqcoluna (o115_sequencial, o115_anousu, o115_descricao, o115_tipo, o115_valoresdefault, o115_nomecoluna, o115_formula, o115_origem, o115_relatorio) values (nextval('orcparamseqcoluna_o115_sequencial_seq'), 2021, 'RP liquidados', 1, '', 'liquidacoes_rp', '', 0, 271);
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 6, 11, '');
insert into orcparamseqcoluna (o115_sequencial, o115_anousu, o115_descricao, o115_tipo, o115_valoresdefault, o115_nomecoluna, o115_formula, o115_origem, o115_relatorio) values (nextval('orcparamseqcoluna_o115_sequencial_seq'), 2020, 'Previsão Atualizada', 1, '', 'previsao_atualizada', '', 0, 271);
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 9, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 2, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 1, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 12, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 23, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 20, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 26, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 29, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 90, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 10, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 8, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 7, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 6, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 5, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 4, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 3, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 38, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 37, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 36, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 35, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 34, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 32, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 31, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 30, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 28, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 27, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 22, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 21, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 19, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 18, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 17, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 16, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 15, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 14, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 11, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 13, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 33, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 39, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 42, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 9, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 2, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 1, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 12, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 23, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 20, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 26, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 29, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 90, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 8, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 7, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 6, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 5, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 4, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 3, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 38, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 37, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 36, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 35, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 34, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 32, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 31, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 30, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 28, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 27, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 22, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 21, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 19, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 18, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 17, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 16, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 15, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 14, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 11, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 10, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 13, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 33, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 39, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 42, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 9, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 2, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 1, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 12, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 23, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 20, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 26, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 29, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 90, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 8, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 7, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 6, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 5, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 4, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 3, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 38, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 37, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 36, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 35, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 34, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 32, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 31, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 30, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 28, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 27, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 22, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 21, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 19, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 18, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 17, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 16, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 15, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 14, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 11, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 10, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 13, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 33, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 39, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 42, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 9, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 2, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 1, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 12, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 23, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 20, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 26, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 29, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 90, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 8, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 7, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 6, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 5, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 4, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 3, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 13, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 30, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 38, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 37, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 36, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 35, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 34, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 32, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 31, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 28, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 27, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 17, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 22, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 21, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 19, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 18, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 16, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 15, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 14, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 11, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 10, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 33, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 39, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 42, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 9, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 2, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 1, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 12, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 23, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 20, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 26, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 90, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 8, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 7, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 6, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 5, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 4, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 3, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 38, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 37, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 36, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 35, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 34, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 32, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 31, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 30, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 28, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 27, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 22, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 21, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 19, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 18, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 17, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 16, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 15, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 14, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 13, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 11, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 10, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 29, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 33, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 39, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 42, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 9, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 2, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 1, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 12, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 23, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 20, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 26, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 90, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 8, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 7, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 6, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 5, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 4, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 3, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 38, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 37, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 36, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 35, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 34, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 32, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 31, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 30, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 28, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 27, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 22, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 21, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 19, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 18, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 17, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 16, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 15, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 14, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 13, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 11, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 10, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 29, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 33, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 39, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 42, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 24, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 24, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 24, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 24, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 24, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 24, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 25, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 25, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 25, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 25, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 25, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 25, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 40, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 40, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 40, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 40, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 40, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 40, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 41, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 41, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 41, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 41, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 41, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 41, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 43, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 43, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 43, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 43, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 43, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 43, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqcoluna (o115_sequencial, o115_anousu, o115_descricao, o115_tipo, o115_valoresdefault, o115_nomecoluna, o115_formula, o115_origem, o115_relatorio) values (nextval('orcparamseqcoluna_o115_sequencial_seq'), 2021, 'Bal Ver Saldo Anterior Acumulado', 1, '', 'saldo_anterior_acumulado', '', 0, 271);
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 76, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 72, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 73, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 74, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 78, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 75, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 77, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 71, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 78, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 76, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 72, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 73, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 74, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 75, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 77, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 71, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 78, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 76, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 72, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 73, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 74, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 75, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 77, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 71, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 78, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 76, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 72, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 73, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 74, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 75, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 77, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 71, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 78, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 76, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 72, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 73, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 74, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 75, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 77, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 71, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 78, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 76, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 72, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 73, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 74, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 75, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 77, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 71, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqcoluna (o115_sequencial, o115_anousu, o115_descricao, o115_tipo, o115_valoresdefault, o115_nomecoluna, o115_formula, o115_origem, o115_relatorio) values (nextval('orcparamseqcoluna_o115_sequencial_seq'), 2021, 'Bal Desp - Pago Acumulado', 1, '', 'pago_acumulado', '', 0, 271);
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 4, 11, '');
insert into orcparamseqcoluna (o115_sequencial, o115_anousu, o115_descricao, o115_tipo, o115_valoresdefault, o115_nomecoluna, o115_formula, o115_origem, o115_relatorio) values (nextval('orcparamseqcoluna_o115_sequencial_seq'), 2021, 'Bal Desp - Liquidado Acumulado', 1, '', 'liquidado_acumulado', '', 0, 271);
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 3, 11, '');
insert into orcparamseqcoluna (o115_sequencial, o115_anousu, o115_descricao, o115_tipo, o115_valoresdefault, o115_nomecoluna, o115_formula, o115_origem, o115_relatorio) values (nextval('orcparamseqcoluna_o115_sequencial_seq'), 2021, 'Bal Ver Saldo Final Acumulado', 1, '', 'saldo_final_acumulado', '', 0, 271);
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 69, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 85, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 86, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 84, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 84, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 68, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 69, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 83, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 83, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 68, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 69, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 83, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 83, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 85, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 86, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 84, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 85, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 86, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 84, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 85, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 68, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 69, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 85, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 86, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 83, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 84, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 85, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 86, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 84, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 68, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 69, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 86, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 68, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 83, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 68, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 69, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 71, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 73, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 78, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 74, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 75, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 77, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 75, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 72, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 71, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 74, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 73, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 78, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 75, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 77, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 76, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 72, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 71, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 74, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 73, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 78, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 75, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 77, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 76, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 72, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 71, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 74, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 73, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 78, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 75, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 77, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 76, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 72, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 71, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 73, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 78, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 74, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 76, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 77, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 76, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 72, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 71, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 73, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 78, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 74, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 75, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 77, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 76, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 72, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqcoluna (o115_sequencial, o115_anousu, o115_descricao, o115_tipo, o115_valoresdefault, o115_nomecoluna, o115_formula, o115_origem, o115_relatorio) values (nextval('orcparamseqcoluna_o115_sequencial_seq'), 2022, 'RP - Pagamento de RP não processado', 1, '', 'pagamento_rp_nao_processado', '', 0, 271);
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 7, 11, '');
insert into orcparamseqcoluna (o115_sequencial, o115_anousu, o115_descricao, o115_tipo, o115_valoresdefault, o115_nomecoluna, o115_formula, o115_origem, o115_relatorio) values (nextval('orcparamseqcoluna_o115_sequencial_seq'), 2021, 'Bal Desl - Emp Liquido Acumulado', 1, '', 'empenhado_liquido_acumulado', '', 0, 271);
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 2, 11, '');
insert into orcparamseqcoluna (o115_sequencial, o115_anousu, o115_descricao, o115_tipo, o115_valoresdefault, o115_nomecoluna, o115_formula, o115_origem, o115_relatorio) values (nextval('orcparamseqcoluna_o115_sequencial_seq'), 2022, 'RP - Pagamento de RP processado', 1, '', 'pagamento_rp_processado', '', 0, 271);
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 60, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 44, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 59, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 47, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 46, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 57, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 56, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 55, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 54, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 45, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 63, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 58, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 52, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 48, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 51, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 53, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 49, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 50, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 61, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 62, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 11, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 64, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 5, 11, '');
insert into orcparamseqcoluna (o115_sequencial, o115_anousu, o115_descricao, o115_tipo, o115_valoresdefault, o115_nomecoluna, o115_formula, o115_origem, o115_relatorio) values (nextval('orcparamseqcoluna_o115_sequencial_seq'), 2021, 'Bal Ver Saldo Final Acumulado', 1, '', 'saldo_final_acumulado', '', 0, 271);
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 91, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 6, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 91, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 7, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 91, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 8, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 91, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 9, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 91, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 10, '');
insert into orcparamseqorcparamseqcoluna (o116_sequencial, o116_codseq, o116_codparamrel, o116_orcparamseqcoluna, o116_ordem, o116_periodo, o116_formula) values (nextval('orcparamseqorcparamseqcoluna_o116_sequencial_seq'), 91, 271, currval('orcparamseqcoluna_o115_sequencial_seq'), 1, 11, '');
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
        DB::connection()->getPdo()->exec(<<<SQL
delete from orcparamseqfiltroorcamento where o133_orcparamrel = 271;
delete from orcparamrelperiodos where o113_orcparamrel = 271;
delete from orcparamseqfiltropadrao where o132_orcparamrel = 271;
delete from orcparamseqorcparamseqcolunavalor where o117_orcparamseqorcparamseqcoluna in (
    select o116_sequencial from orcparamseqorcparamseqcoluna where o116_codparamrel = 271
);
delete from orcparamseqorcparamseqcoluna where o116_codparamrel = 271;
delete from orcparamseqorcparamseqcoluna where o116_codparamrel = 271;
delete from orcparamseq where o69_codparamrel = 271;
delete from orcparamrel where o42_codparrel = 271;
SQL
        );
    }
}
