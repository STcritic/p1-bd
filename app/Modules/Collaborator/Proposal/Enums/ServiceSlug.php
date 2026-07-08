<?php

namespace App\Modules\Collaborator\Proposal\Enums;

enum ServiceSlug: string
{
    case RecrutamentoSeleccao          = 'recrutamento-seleccao';
    case GestaoDesempenho              = 'gestao-desempenho';
    case CarreiraSuccessao             = 'carreira-sucessao';
    case AvaliacaoClassificacaoCargos  = 'avaliacao-classificacao-cargos';
    case PerfilComportamental          = 'perfil-comportamental';
    case PoliticasProcedimentos        = 'politicas-procedimentos';
    case RemuneracaoBeneficios         = 'remuneracao-beneficios';
    case FormacaoDesenvolvimento       = 'formacao-desenvolvimento';
    case AssessoriaOutsourcingRh       = 'assessoria-outsourcing-rh';
    case DigitalizacaoRhEndomarketing  = 'digitalizacao-rh-endomarketing';
}
