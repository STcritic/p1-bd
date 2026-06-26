@extends('announcements.layout')
@section('title', 'Gestão de anúncios')

@section('content')
<main class="announcement-dashboard">
    <header class="announcement-admin-header">
        <div>
            <a class="announcement-admin-logo" href="{{ route('home') }}"><img src="{{ asset('assets/img/logo/logo.png') }}" alt="Business Diversity"></a>
            <div><span class="eyebrow">GESTÃO DO WEBSITE</span><h1>Anúncios de abertura</h1></div>
        </div>
        <div class="announcement-admin-user">
            <span>{{ $admin->name }}</span>
            <form method="POST" action="{{ route('announcements.logout') }}">@csrf<button type="submit">Sair</button></form>
        </div>
    </header>

    <div class="announcement-admin-shell">
        @if (session('status'))
            <div class="alert-success" role="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert-error" role="alert">Há campos por corrigir. Veja as mensagens no formulário.</div>
        @endif

        <section class="announcement-admin-grid">
            <form method="POST" action="{{ route('announcements.store') }}" class="announcement-panel announcement-form">
                @csrf
                <div class="announcement-panel-heading">
                    <span class="eyebrow">NOVO ANÚNCIO</span>
                    <h2>Criar anúncio sobreposto</h2>
                    <p>Para manter o site leve, imagens, vídeos e documentos grandes devem estar em plataformas externas. Cole aqui o link de YouTube, Facebook, Google Drive, Vimeo, imagem externa ou documento.</p>
                </div>

                <label><span>Título *</span><input name="title" value="{{ old('title') }}" required maxlength="140">@error('title')<small>{{ $message }}</small>@enderror</label>
                <label><span>Mensagem</span><textarea name="body" rows="4" maxlength="1200">{{ old('body') }}</textarea>@error('body')<small>{{ $message }}</small>@enderror</label>

                <div class="field-row">
                    <label>
                        <span>Tipo de media</span>
                        <select name="media_type">
                            <option value="none">Sem media</option>
                            <option value="image" @selected(old('media_type') === 'image')>Imagem externa</option>
                            <option value="video" @selected(old('media_type') === 'video')>Vídeo externo</option>
                            <option value="document" @selected(old('media_type') === 'document')>Documento externo</option>
                        </select>
                        @error('media_type')<small>{{ $message }}</small>@enderror
                    </label>
                    <label>
                        <span>Link externo do media</span>
                        <input type="url" name="media_url" value="{{ old('media_url') }}" placeholder="https://youtube.com/..., https://drive.google.com/...">
                        <small>Não carregue ficheiros no site. Use links de plataformas intermediárias.</small>
                        @error('media_url')<small>{{ $message }}</small>@enderror
                        @error('media')<small>{{ $message }}</small>@enderror
                    </label>
                </div>

                <div class="announcement-external-note">
                    <strong>Recomendado:</strong>
                    <span>Vídeos: YouTube, Facebook, Vimeo ou Google Drive.</span>
                    <span>Documentos grandes: Google Drive/Docs, OneDrive ou página externa.</span>
                    <span>Imagens: link público optimizado ou plataforma/CDN externa.</span>
                </div>

                <div class="field-row">
                    <label><span>Texto do botão</span><input name="button_label" value="{{ old('button_label') }}" placeholder="Ex.: Saiba mais">@error('button_label')<small>{{ $message }}</small>@enderror</label>
                    <label><span>Link do botão</span><input type="url" name="button_url" value="{{ old('button_url') }}" placeholder="https://...">@error('button_url')<small>{{ $message }}</small>@enderror</label>
                </div>

                <div class="field-row">
                    <label><span>Prioridade</span><input type="number" name="priority" min="1" max="99" value="{{ old('priority', 10) }}"><small>Quanto menor, mais prioridade tem.</small>@error('priority')<small>{{ $message }}</small>@enderror</label>
                    <label><span>Publicar de</span><input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}">@error('starts_at')<small>{{ $message }}</small>@enderror</label>
                </div>

                <label><span>Publicar até</span><input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}">@error('ends_at')<small>{{ $message }}</small>@enderror</label>

                <div class="announcement-check-row">
                    <label><input type="checkbox" name="is_active" value="1" checked> <span>Activar ao guardar</span></label>
                    <label><input type="checkbox" name="show_once_per_session" value="1" checked> <span>Mostrar uma vez por sessão</span></label>
                </div>

                <button class="button button-primary" type="submit">Criar anúncio <span>→</span></button>
            </form>

            <section class="announcement-panel">
                <div class="announcement-panel-heading">
                    <span class="eyebrow">ANÚNCIOS</span>
                    <h2>Activos e arquivados</h2>
                    <p>O site mostra primeiro o anúncio activo com maior prioridade.</p>
                </div>

                <div class="announcement-admin-list">
                    @forelse ($announcements as $announcement)
                        @php
                            $mediaUrl = $announcement->mediaUrl();
                            $embedUrl = $announcement->mediaEmbedUrl();
                        @endphp
                        <article class="announcement-admin-item">
                            <div>
                                <span @class(['announcement-status', 'is-active' => $announcement->is_active])>{{ $announcement->is_active ? 'Activo' : 'Inactivo' }}</span>
                                <h3>{{ $announcement->title }}</h3>
                                <p>{{ \Illuminate\Support\Str::limit($announcement->body, 130) ?: 'Sem mensagem adicional.' }}</p>
                                <small>Prioridade {{ $announcement->priority }} · {{ $announcement->media_type === 'none' ? 'sem media' : $announcement->mediaPlatform() }}</small>
                            </div>
                            @if ($mediaUrl)
                                <div class="announcement-admin-media">
                                    @if ($announcement->media_type === 'image')
                                        <img src="{{ $mediaUrl }}" alt="">
                                    @elseif ($embedUrl)
                                        <iframe src="{{ $embedUrl }}" loading="lazy" allowfullscreen></iframe>
                                    @else
                                        <a href="{{ $mediaUrl }}" target="_blank" rel="noopener">Abrir media externo ↗</a>
                                    @endif
                                </div>
                            @endif
                            <div class="announcement-admin-actions">
                                <form method="POST" action="{{ route('announcements.toggle', $announcement) }}">@csrf @method('PATCH')<button type="submit">{{ $announcement->is_active ? 'Desactivar' : 'Activar' }}</button></form>
                                <form method="POST" action="{{ route('announcements.destroy', $announcement) }}" onsubmit="return confirm('Apagar este anúncio?')">@csrf @method('DELETE')<button class="danger" type="submit">Apagar</button></form>
                            </div>
                        </article>
                    @empty
                        <div class="announcement-empty">Ainda não há anúncios criados.</div>
                    @endforelse
                </div>
            </section>
        </section>

        @if ($admin->is_master)
            <section class="announcement-panel announcement-access-panel">
                <div class="announcement-panel-heading">
                    <span class="eyebrow">ACESSOS SECUNDÁRIOS</span>
                    <h2>Gerir passwords de apoio</h2>
                    <p>A conta master pode criar acessos secundários para pessoas autorizadas a gerir anúncios.</p>
                </div>

                <form method="POST" action="{{ route('announcements.admins.store') }}" class="announcement-access-form">
                    @csrf
                    <label><span>Nome</span><input name="name" required></label>
                    <label><span>Email</span><input type="email" name="email" required></label>
                    <label><span>Palavra-passe</span><input type="password" name="password" required minlength="8"></label>
                    <label><span>Confirmar</span><input type="password" name="password_confirmation" required minlength="8"></label>
                    <button class="button button-primary" type="submit">Criar acesso</button>
                </form>

                <div class="announcement-access-list">
                    @foreach ($admins as $access)
                        <article>
                            <div><strong>{{ $access->name }}</strong><span>{{ $access->email }}{{ $access->is_master ? ' · master' : '' }}</span></div>
                            @unless ($access->is_master)
                                <form method="POST" action="{{ route('announcements.admins.destroy', $access) }}" onsubmit="return confirm('Apagar este acesso secundário?')">@csrf @method('DELETE')<button type="submit">Apagar</button></form>
                            @endunless
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</main>
@endsection
