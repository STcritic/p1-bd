<?php

namespace App\Modules\Collaborator\Opportunity\Decision;

use App\Modules\Collaborator\Opportunity\Domain\Opportunity;

/**
 * DecisionEngine — evaluates config-driven rules against the opportunity context
 * and produces a scored, annotated output used by the ProposalBuilder.
 *
 * No hardcoded logic — all rules live in config/decision_rules.php.
 * Adding new rules never requires code changes.
 */
final class DecisionEngine
{
    private array $rules;
    private array $weights;
    private array $defaults;
    private array $thresholds;

    public function __construct()
    {
        $this->rules      = config('decision_rules.rules', []);
        $this->weights    = config('decision_rules.score_weights', []);
        $this->defaults   = config('decision_rules.score_defaults', []);
        $this->thresholds = config('decision_rules.risk_thresholds', []);
    }

    /**
     * Evaluate all matching rules and persist the result on the opportunity.
     */
    public function evaluate(Opportunity $opportunity): array
    {
        $context   = $opportunity->context_snapshot ?? [];
        $slug      = $opportunity->service_slug;

        $scores    = $this->defaults;
        $arguments = [];
        $tags      = $opportunity->tags ?? [];
        $risks     = [];
        $timelineNotes = [];
        $firedRules    = [];

        foreach ($this->rules as $rule) {
            // Check service scope
            if (! empty($rule['services']) && ! in_array($slug, $rule['services'])) {
                continue;
            }

            if (! $this->conditionsMatch($rule['conditions'] ?? [], $context)) {
                continue;
            }

            $firedRules[] = $rule['id'];
            $actions = $rule['actions'] ?? [];

            // Apply actions
            if (isset($actions['set_score_dimension'])) {
                foreach ($actions['set_score_dimension'] as $dim => $delta) {
                    $scores[$dim] = min(100, ($scores[$dim] ?? 0) + $delta);
                }
            }

            if (isset($actions['add_argument'])) {
                $arguments[] = $actions['add_argument'];
            }

            if (isset($actions['add_timeline_note'])) {
                $timelineNotes[] = $actions['add_timeline_note'];
            }

            if (isset($actions['add_tag'])) {
                $tag = $actions['add_tag'];
                if (! in_array($tag, $tags)) $tags[] = $tag;
            }

            if (isset($actions['set_complexity'])) {
                // Store for proposal builder
                $scores['_complexity_override'] = $actions['set_complexity'];
            }

            if (isset($actions['flag_risk'])) {
                $risks[] = $actions['flag_risk'];
            }
        }

        $totalScore = $this->computeTotal($scores);
        $riskLevel  = $this->riskLevel($totalScore, $risks);

        $result = [
            'total'           => $totalScore,
            'dimensions'      => $scores,
            'risk_level'      => $riskLevel,
            'risk_flags'      => $risks,
            'arguments'       => $arguments,
            'timeline_notes'  => $timelineNotes,
            'tags'            => $tags,
            'fired_rules'     => $firedRules,
            'evaluated_at'    => now()->toIso8601String(),
        ];

        $opportunity->update([
            'score_data' => $result,
            'tags'       => $tags,
        ]);

        return $result;
    }

    /**
     * Return only the arguments applicable to the current context.
     */
    public function buildArguments(Opportunity $opportunity): array
    {
        return $opportunity->score_data['arguments'] ?? [];
    }

    /**
     * Return timeline notes to be appended to proposal assumptions.
     */
    public function buildTimelineNotes(Opportunity $opportunity): array
    {
        return $opportunity->score_data['timeline_notes'] ?? [];
    }

    // ── Condition evaluation ──────────────────────────────────────────────────

    private function conditionsMatch(array $conditions, array $context): bool
    {
        foreach ($conditions as $condition) {
            if (! $this->evaluateCondition($condition, $context)) return false;
        }
        return true;
    }

    private function evaluateCondition(array $condition, array $context): bool
    {
        [$field, $operator, $expected] = [
            $condition['field'],
            $condition['operator'],
            $condition['value'] ?? null,
        ];

        $actual = $context[$field] ?? null;

        return match ($operator) {
            'eq'         => $actual == $expected,
            'neq'        => $actual != $expected,
            'in'         => in_array($actual, (array) $expected),
            'not_in'     => ! in_array($actual, (array) $expected),
            'gt'         => is_numeric($actual) && $actual > $expected,
            'gte'        => is_numeric($actual) && $actual >= $expected,
            'lt'         => is_numeric($actual) && $actual < $expected,
            'lte'        => is_numeric($actual) && $actual <= $expected,
            'contains'   => is_string($actual) && str_contains(strtolower($actual), strtolower($expected)),
            'starts_with'=> is_string($actual) && str_starts_with(strtolower($actual), strtolower($expected)),
            'filled'     => ! empty($actual),
            'empty'      => empty($actual),
            default      => false,
        };
    }

    // ── Scoring ───────────────────────────────────────────────────────────────

    private function computeTotal(array $scores): int
    {
        $weighted = 0.0;
        $totalWeight = 0.0;

        foreach ($this->weights as $dim => $weight) {
            $weighted    += ($scores[$dim] ?? 50) * $weight;
            $totalWeight += $weight;
        }

        return $totalWeight > 0 ? (int) round($weighted / $totalWeight) : 50;
    }

    private function riskLevel(int $score, array $riskFlags): string
    {
        $base = match (true) {
            $score >= $this->thresholds['high']   => 'alto',
            $score >= $this->thresholds['medium']  => 'médio',
            default                                => 'baixo',
        };

        // Having explicit risk flags always elevates to at least medium
        if (! empty($riskFlags) && $base === 'baixo') return 'médio';
        return $base;
    }
}
