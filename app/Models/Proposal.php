<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Proposal extends Model
{
    protected $fillable = [
        'announcement_admin_id',
        'reference',
        'verification_code',
        'verification_token',
        'service_slug',
        'service_title',
        'client_name',
        'client_contact',
        'status',
        'form_data',
        'notes',
        'expires_at',
        'certified_at',
        'revoked_at',
    ];

    protected $casts = [
        'form_data'    => 'array',
        'expires_at'   => 'date',
        'certified_at' => 'datetime',
        'revoked_at'   => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Proposal $proposal): void {
            if (blank($proposal->verification_code)) {
                $proposal->verification_code = static::newVerificationCode();
            }

            if (blank($proposal->verification_token)) {
                $proposal->verification_token = Str::random(48);
            }

            if ($proposal->certified_at === null) {
                $proposal->certified_at = now();
            }
        });
    }

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

    public function ensureVerification(): void
    {
        if (blank($this->verification_code)) {
            $this->verification_code = static::newVerificationCode();
        }

        if (blank($this->verification_token)) {
            $this->verification_token = Str::random(48);
        }

        if ($this->certified_at === null) {
            $this->certified_at = now();
        }

        if ($this->isDirty(['verification_code', 'verification_token', 'certified_at'])) {
            $this->saveQuietly();
        }
    }

    public function verificationUrl(): ?string
    {
        if (blank($this->verification_token)) {
            return null;
        }

        return route('proposals.verify', $this->verification_token);
    }

    public function verificationQrUrl(): ?string
    {
        if (blank($this->verification_token)) {
            return null;
        }

        return route('proposals.verify.qr', $this->verification_token);
    }

    public function verificationStatus(): string
    {
        if ($this->revoked_at !== null) {
            return 'revoked';
        }

        if ($this->status === 'recusado') {
            return 'inactive';
        }

        if ($this->status === 'expirado' || ($this->expires_at !== null && $this->expires_at->isPast())) {
            return 'expired';
        }

        return 'valid';
    }

    public function verificationStatusLabel(): string
    {
        return match ($this->verificationStatus()) {
            'valid'    => 'Certificada',
            'expired'  => 'Expirada',
            'revoked'  => 'Revogada',
            'inactive' => 'Sem efeito',
            default    => 'Indisponível',
        };
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

    private static function newVerificationCode(): string
    {
        do {
            $code = 'BD-CERT-' . now()->format('Y') . '-' . Str::upper(Str::random(6));
        } while (static::query()->where('verification_code', $code)->exists());

        return $code;
    }
}
