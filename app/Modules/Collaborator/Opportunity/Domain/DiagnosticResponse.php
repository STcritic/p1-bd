<?php

namespace App\Modules\Collaborator\Opportunity\Domain;

use Illuminate\Database\Eloquent\Model;

class DiagnosticResponse extends Model
{
    protected $table = 'diagnostic_responses';

    protected $fillable = [
        'diagnostic_session_id', 'opportunity_id',
        'group_key', 'question_key', 'question_label', 'answer_value',
    ];

    protected $casts = [
        'answer_value' => 'array',
    ];

    public function session()
    {
        return $this->belongsTo(DiagnosticSession::class, 'diagnostic_session_id');
    }

    public function opportunity()
    {
        return $this->belongsTo(Opportunity::class);
    }

    /** Return the answer as a plain scalar when applicable. */
    public function scalarValue(): mixed
    {
        $v = $this->answer_value;
        if (is_array($v) && count($v) === 1) return $v[0];
        return $v;
    }
}
