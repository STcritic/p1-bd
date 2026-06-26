<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'announcement_admin_id',
    'title',
    'body',
    'media_type',
    'media_path',
    'media_original_name',
    'media_url',
    'button_label',
    'button_url',
    'is_active',
    'show_once_per_session',
    'priority',
    'starts_at',
    'ends_at',
])]
class Announcement extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'show_once_per_session' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'priority' => 'integer',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(AnnouncementAdmin::class, 'announcement_admin_id');
    }

    public function scopeVisible(Builder $query): Builder
    {
        $now = now();

        return $query
            ->where('is_active', true)
            ->where(function (Builder $query) use ($now): void {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function (Builder $query) use ($now): void {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            });
    }

    public function mediaUrl(): ?string
    {
        if ($this->media_url) {
            return $this->media_url;
        }

        if ($this->media_path) {
            return Storage::disk('public')->url($this->media_path);
        }

        return null;
    }

    public function mediaEmbedUrl(): ?string
    {
        $url = $this->media_url;

        if (! $url) {
            return null;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = (string) parse_url($url, PHP_URL_PATH);
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        if (str_contains($host, 'youtube.com')) {
            $id = $query['v'] ?? null;

            if (! $id && preg_match('~/shorts/([^/?]+)~', $path, $matches)) {
                $id = $matches[1];
            }

            if (! $id && preg_match('~/embed/([^/?]+)~', $path, $matches)) {
                $id = $matches[1];
            }

            return $id ? 'https://www.youtube.com/embed/'.rawurlencode($id) : null;
        }

        if (str_contains($host, 'youtu.be')) {
            $id = trim($path, '/');

            return $id ? 'https://www.youtube.com/embed/'.rawurlencode($id) : null;
        }

        if (str_contains($host, 'vimeo.com') && preg_match('~/(\d+)~', $path, $matches)) {
            return 'https://player.vimeo.com/video/'.$matches[1];
        }

        if (str_contains($host, 'facebook.com') && $this->media_type === 'video') {
            return 'https://www.facebook.com/plugins/video.php?href='.rawurlencode($url).'&show_text=false&width=900';
        }

        if (str_contains($host, 'drive.google.com')) {
            $id = $query['id'] ?? null;

            if (! $id && preg_match('~/file/d/([^/]+)~', $path, $matches)) {
                $id = $matches[1];
            }

            return $id ? 'https://drive.google.com/file/d/'.rawurlencode($id).'/preview' : null;
        }

        if (str_contains($host, 'docs.google.com')) {
            return preg_replace('~/edit.*$~', '/preview', $url) ?: $url;
        }

        return null;
    }

    public function mediaPlatform(): string
    {
        $url = $this->media_url;

        if (! $url && $this->media_path) {
            return 'ficheiro local antigo';
        }

        $host = strtolower((string) parse_url((string) $url, PHP_URL_HOST));

        return match (true) {
            str_contains($host, 'youtube.com'), str_contains($host, 'youtu.be') => 'YouTube',
            str_contains($host, 'vimeo.com') => 'Vimeo',
            str_contains($host, 'facebook.com') => 'Facebook',
            str_contains($host, 'drive.google.com') => 'Google Drive',
            str_contains($host, 'docs.google.com') => 'Google Docs',
            $host !== '' => $host,
            default => 'sem media',
        };
    }
}
