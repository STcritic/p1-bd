<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Proposal extends Model
{
    protected $fillable = [
        'announcement_admin_id',
        'reference',
        'service_slug',
        'service_title',
        'client_name',
        'client_contact',
        'status',
        'form_data',
        'notes',
        'expires_at',
    ];

    protected $casts = [
        'form_data'  => 'array',
        'expires_at' => 'date',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(AnnouncementAdmin::class, 'announcement_admin_id');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'rascunho'   => 'Rascunho',
            'enviado'    => 'Enviado',
            'negociacao' => 'Em negociação',
            'aceite'     => 'Aceite',
            'recusado'   => 'Recusado',
            'expirado'   => 'Expirado',
            default      => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'enviado'    => 'blue',
            'negociacao' => 'orange',
            'aceite'     => 'green',
            'recusado'   => 'red',
            default      => 'grey',
        };
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast()
            && ! in_array($this->status, ['aceite', 'recusado'], true);
    }

    public static function statuses(): array
    {
        return [
            'rascunho'   => 'Rascunho',
            'enviado'    => 'Enviado',
            'negociacao' => 'Em negociação',
            'aceite'     => 'Aceite',
            'recusado'   => 'Recusado',
            'expirado'   => 'Expirado',
        ];
    }
}
