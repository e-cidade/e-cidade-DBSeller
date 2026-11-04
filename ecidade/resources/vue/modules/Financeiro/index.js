import DDR from "./Contabilidade/ContaCorrente/Implantacao/DDR.vue";
import VinculoAutomatico from "./Contabilidade/PlanoContas/Mapeamento/VinculoAutomatico.vue";
import ApropriacaoDecimoFerias from "./Contabilidade/Apropriacao/DecimoFerias/ApropriacaoDecimoFerias.vue";
import RelatorioBalanceteReceitaRecurso from "./Contabilidade/Relatorios/Balancetes/ReceitaRecurso";
import RelatorioBalanceteVerificacao from "./Contabilidade/Relatorios/Balancetes/Verificacao";
import RelatorioBalanceteVerificacaoInformacaoComplementar
    from "./Contabilidade/Relatorios/Balancetes/InformacaoComplementar";
import DeliberacaoV285Modelo5 from "./Contabilidade/Relatorios/Deliberacao/V285/Modelo5";
import LancamentoManual from "./Contabilidade/Lancamento/Manual/Manutencao";
import SigfisUnidadeGestora from './Contabilidade/Tce/RJ/Sigfis/UnidadeGestora/UnidadeGestora';
import MapeamentoEmpenhoRPConta from './Contabilidade/MapeamentoEmpenhoRPConta/MapeamentoEmpenhoRPConta';
import EmissaoMSC from './Contabilidade/MSC/Emissao';
import MapeamentoEmpenhoRPManual from './Contabilidade/MapeamentoEmpenhoRPConta/MapeamentoEmpenhoRPManual';

// contabilidade relatorios legais
import LrfEmissao from '@modules/Financeiro/Contabilidade/Relatorios/Lrf'
import PorRecurso from "./Contabilidade/ContaCorrente/Cadastro/PorRecurso.vue";

// contabilidade relatorios conferência
import SuperavitPorRecurso from '@modules/Financeiro/Contabilidade/Relatorios/Conferencia/Superavit';

//tesouraria
import DecendioReceita from './Tesouraria/Decendio/DecendioReceita';
import DecendioReceitaPorDesdobramento from './Tesouraria/Decendio/DecendioReceitaDesdobramento';
import DecendioPercentuais from './Tesouraria/Decendio/Percentuais';
import DecendioSlips from './Tesouraria/Decendio/Slips';
import DecendioRelatorio from './Tesouraria/Decendio/Relatorio';
import CoberturaRecursoExtra from "./Tesouraria/Slips/Gerar/CoberturaRecursoExtra";

// orcamento
import RecursoConvereVinculo from './Orcamento/Recursos/Relatorios/ConfereVinculo';

export default function (app) {
    app.component('implantacao_ddr', DDR);
    app.component('plano-contas-vinculo-automatico', VinculoAutomatico);
    app.component('apropriacao-decimo-ferias', ApropriacaoDecimoFerias);
    app.component('cobertura_recurso_extra', CoberturaRecursoExtra);
    app.component('relatorio-balancete-receita-recurso', RelatorioBalanceteReceitaRecurso);
    app.component('relatorio-balancete-verificacao', RelatorioBalanceteVerificacao);
    app.component('relatorio-balancete-verificacao-informacao-complementar', RelatorioBalanceteVerificacaoInformacaoComplementar);
    app.component('deliberacao-v285-modelo-5', DeliberacaoV285Modelo5);
    app.component('lancamento-manual', LancamentoManual);
    app.component('sigfis-unidade-gestora', SigfisUnidadeGestora);
    app.component('mapeamento-empenho-rp-conta', MapeamentoEmpenhoRPConta);
    app.component('mapeamento-empenho-rp-manual', MapeamentoEmpenhoRPManual);
    app.component('emissao-msc', EmissaoMSC);
    app.component('recurso-confere-vinculo', RecursoConvereVinculo);
    app.component('cadastro_por_recurso', PorRecurso);
    app.component('lrf-emissao', LrfEmissao);

    app.component('decendio-receita', DecendioReceita);
    app.component('decendio-receita-desdobramento', DecendioReceitaPorDesdobramento);
    app.component('decendio-percentuais', DecendioPercentuais);
    app.component('decendio-slips', DecendioSlips);
    app.component('decendio-relatorio', DecendioRelatorio);
    app.component('superavit-por-recurso', SuperavitPorRecurso)
}
