@php
    $kit = fn (string $path): string => asset('assets/'.$path);
    $openBook = $latestBooks->first();
    $queue = $latestBooks->take(8)->values();
    $initials = fn (string $name): string => collect(explode(' ', $name))->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('');
@endphp
<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ config('app.supported_locales.'.app()->getLocale().'.dir', 'ltr') }}" data-type="software" data-radius="soft" data-ground="sky" data-buttons="arrow" data-motion>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>{{ __('BooksWeb: a shared PDF library for readers') }}</title>
<meta name="description" content="{{ __('Read and download free PDF books, or upload your own for an admin to review and publish.') }}">
<meta property="og:type" content="website">
<meta property="og:title" content="{{ __('BooksWeb: a shared PDF library for readers') }}">
<meta property="og:description" content="{{ __('Free PDF books, each one checked by an admin before it goes public.') }}">
<meta property="og:image" content="{{ asset('share.jpg') }}">
<meta name="twitter:card" content="summary_large_image">
<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<meta name="theme-color" content="#f8f8f8">
<!-- <link rel="canonical" href="https://[the client's domain]/"> -->
<meta name="site-kind" content="software">
<meta name="page-type" content="software">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Geist:wght@400..700&family=Geist+Mono:wght@400..500{{ app()->getLocale() === 'ar' ? '&family=Cairo:wght@400..700' : '' }}&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ $kit('system.css') }}">
<link rel="stylesheet" href="{{ $kit('ui/cards.css') }}">
<link rel="stylesheet" href="{{ $kit('motion/motion.css') }}">
<link rel="stylesheet" href="{{ $kit('layout/layout.css') }}">
<style>
:root {
  --p-bg: #f8f8f8; --p-surface: #eeeeee; --p-ink: #0a0a0a; --p-muted: #6a6a6a;
  --p-accent: #1f6b3a; --p-accent-ink: #ffffff; --p-action: #1f6b3a; --p-action-ink: #ffffff;
  --p-inv-bg: #0a0a0a; --p-inv-surface: #171717; --p-inv-ink: #f8f8f8; --p-inv-muted: #939393;
  --p-inv-accent: #1f6b3a; --p-inv-accent-ink: #ffffff; --p-inv-action: #1f6b3a; --p-inv-action-ink: #ffffff;
  --font-display: "Geist", system-ui, sans-serif;
  --font-text: "Geist", system-ui, sans-serif;
  --font-mono: "Geist Mono", ui-monospace, "SFMono-Regular", Menlo, Consolas, monospace;
  --display-weight: 500;
  --display-tracking: -0.035em;
  --display-tracking-xl: -0.045em;
}
</style>
<link rel="stylesheet" href="{{ $kit('ui/base.css') }}">
<link rel="stylesheet" href="{{ $kit('ui/window/fragment.css') }}">
<link rel="stylesheet" href="{{ $kit('ui/doc/fragment.css') }}">
<link rel="stylesheet" href="{{ $kit('ui/checklist/fragment.css') }}">
<link rel="stylesheet" href="{{ $kit('ui/notify/fragment.css') }}">
<link rel="stylesheet" href="{{ $kit('ui/search/fragment.css') }}">
<script>document.documentElement.classList.add('m-enter');setTimeout(()=>document.documentElement.classList.add('m-enter-done'),700)</script>
<script>document.documentElement.classList.add('mo-js');setTimeout(()=>document.documentElement.classList.add('mo-safe'),2500)</script>
<link rel="modulepreload" href="{{ $kit('moments/moments.js') }}">
<link rel="modulepreload" href="{{ $kit('moments/lib.js') }}">
<link rel="modulepreload" href="{{ $kit('moments/line-carries-colour/moment.js') }}">
<style>
  /* hero window: the reader's view of the library, drawn as live HTML */
  .hero__window { background: radial-gradient(120% 80% at 20% 0%, #dfe9e2, transparent 60%), radial-gradient(90% 70% at 90% 100%, #cfe0d6, transparent 60%), #e6ece8; }
  .lib-win { background: #fff; color: #16181a; border-radius: 12px; overflow: hidden; font: 400 14px/1.4 var(--font-text); text-align: left; box-shadow: 0 0 0 1px rgb(0 0 0 / .07), 0 24px 60px -28px rgb(0 0 0 / .45); }
  .lib-win__bar { display: flex; align-items: center; gap: 10px; height: 40px; padding: 0 14px; border-bottom: 1px solid rgb(0 0 0 / .07); font-size: 13px; color: #5d6268; }
  .lib-win__bar b { color: #16181a; font-weight: 500; } .lib-win__bar .sp { flex: 1; }
  .lib-win__dots { display: flex; gap: 6px; } .lib-win__dots i { width: 10px; height: 10px; border-radius: 50%; background: #dcdfe2; }
  .lib-win__body { display: grid; grid-template-columns: 260px 1fr; min-height: 420px; }
  .lib-list { border-right: 1px solid rgb(0 0 0 / .07); padding: 10px; display: grid; align-content: start; gap: 2px; }
  .lib-list .q { height: 34px; margin-bottom: 6px; border-radius: 9px; background: rgb(0 0 0 / .04); display: flex; align-items: center; padding: 0 10px; color: #6b7076; font-size: 13px; }
  .lib-list .t { display: grid; grid-template-columns: 26px 1fr; gap: 1px 10px; padding: 8px; border-radius: 9px; font-size: 13px; }
  .lib-list .t.is-on { background: rgb(31 107 58 / .08); }
  .lib-list .t i { grid-row: 1 / span 2; width: 26px; height: 36px; border-radius: 2px 4px 4px 2px; }
  .lib-list .t b { font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .lib-list .t span { color: #6b7076; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .lib-open { display: grid; grid-template-columns: 112px 1fr; gap: 18px; padding: 20px 22px; align-content: start; }
  .lib-open__cover { width: 112px; height: 164px; border-radius: 3px 8px 8px 3px; padding: 12px 10px; color: #fff; display: flex; flex-direction: column; justify-content: flex-start; font: 500 13px/1.2 var(--font-text); }
  @media (max-width: 1279px) { .lib-open { grid-template-columns: 84px 1fr; } .lib-open__cover { width: 84px; height: 122px; } .lib-open__cover small { display: none; } }
  .lib-open__cover small { font-size: 13px; margin-top: 4px; }
  .hero--centred .hero__lead { color: var(--ink); }
  .close__word { line-height: 1.12; font-size: min(calc((100vw - 2 * var(--gut)) / 4.62), 277px); }
  @media (max-width: 639px) { .hero--centred .pill { justify-self: stretch; justify-content: center; } }
  @media (max-width: 1279px) { .lib-list .t:nth-of-type(n+4) { display: none; } }
  #reading .frame--split > .frame__img { object-position: 50% 60%; }
  @media (max-width: 1023px) { #reading .frame--split { --ratio: 50%; } }
  @media (max-width: 639px) { #reading .frame--split { --ratio: 42%; } .adm__rows p:nth-child(n+4) { display: none; } #questions .faq__item:nth-child(n+6) { display: none; } #questions .span-5 > p { display: none; } .adm__grid strong { font-size: 22px; } }
  @media (min-width: 640px) and (max-width: 1279px) { #steps { --h: 2; } }
  .lib-open__meta { display: grid; gap: 8px; align-content: start; }
  .lib-open__meta h4 { margin: 0; font: 500 20px/1.2 var(--font-text); letter-spacing: -0.01em; }
  .lib-open__meta p { margin: 0; color: #5d6268; font-size: 13px; }
  .lib-facts { display: flex; flex-wrap: wrap; gap: 6px; }
  .lib-facts span { height: 24px; padding: 0 9px; border-radius: 99px; background: rgb(0 0 0 / .05); display: inline-flex; align-items: center; font-size: 13px; color: #3e4247; }
  .lib-acts { display: flex; gap: 8px; margin-top: 4px; }
  .lib-acts span { white-space: nowrap; height: 32px; padding: 0 10px; border-radius: 8px; display: inline-flex; align-items: center; font-size: 13px; font-weight: 500; background: #1f6b3a; color: #fff; }
  .lib-acts span + span { background: rgb(0 0 0 / .06); color: #16181a; }
  @media (max-width: 639px) {
    .lib-win__body { grid-template-columns: 1fr; } .lib-list { display: none; }
    .lib-open { grid-template-columns: 72px 1fr; gap: 12px; padding: 14px; }
    .lib-open__cover { width: 72px; height: 106px; padding: 8px 6px; font-size: 13px; } .lib-open__cover small { display: none; }
    .lib-open__meta h4 { font-size: 16px; } .lib-open__meta > p:last-of-type { display: none; }
    .lib-win__bar .sp ~ span { display: none; }
  }

  /* the review queue: app-frame recipe */
  .app.ui { display: grid; grid-template-columns: minmax(0, 5fr) minmax(0, 7fr); margin-top: var(--s-7); background: var(--u-bg); border: 1px solid var(--u-border); border-radius: var(--u-r); box-shadow: var(--u-shadow); }
  .app > .app__pane { position: relative; min-width: 0; min-height: 0; overflow: hidden; }
  .app > .app__pane + .app__pane { border-left: 1px solid var(--u-line); }
  .app > .app__pane > .ui-panel { width: 100%; border: 0; border-radius: 0; box-shadow: none; }
  .app > .app__pane::after { content: ""; position: absolute; inset: auto 0 0 0; height: 64px; background: linear-gradient(transparent, var(--u-bg)); pointer-events: none; }
  .app .ui-doc { padding: 24px; }
  @media (min-width: 1024px) and (max-width: 1279px) { .app.ui { grid-template-columns: minmax(0, 4fr) minmax(0, 8fr); } }
  @media (max-width: 1023px) {
    .app.ui { grid-template-columns: minmax(0, 1fr); grid-template-rows: auto minmax(0, 1fr); }
    .app > .app__pane--list { max-height: 252px; }
    .app > .app__pane--list::after { display: none; }
    .app .ui-win__side { display: none; }
    .app .ui-win__body { grid-template-columns: minmax(0, 1fr); }
    .app > .app__pane + .app__pane { border-left: 0; border-top: 1px solid var(--u-line); }
  }
  @media (max-width: 639px) {
    .app.ui { grid-template-rows: minmax(0, 1fr); }
    .app > .app__pane--list { display: none; }
    .app > .app__pane + .app__pane { border-top: 0; }
  }

  #steps .panel { --p-h: 300px; }
  @media (max-width: 639px) { #steps .panel { --p-h: 180px; } }
  

  /* the admin panel's numbers, drawn as live HTML */
  .adm { height: 100%; display: flex; flex-direction: column; background: #171717; color: #ececec; border-radius: 12px; overflow: hidden; font: 400 14px/1.4 var(--font-text); box-shadow: 0 0 0 1px rgb(255 255 255 / .08); }
  .adm__bar { display: flex; align-items: center; gap: 10px; height: 40px; padding: 0 16px; background: #1f1f1f; font-size: 13px; color: #a3a3a3; }
  .adm__bar b { color: #fff; font-weight: 500; }
  .adm__grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1px; background: rgb(255 255 255 / .08); }
  .adm__grid div { background: #171717; padding: 18px 20px; }
  .adm__grid span { display: block; font-size: 13px; color: #a3a3a3; }
  .adm__grid strong { font: 500 30px/1.15 var(--font-display); letter-spacing: -0.02em; font-variant-numeric: tabular-nums; }
  .adm__rows { padding: 6px 20px 16px; flex: 1; }
  .adm__rows p { display: flex; justify-content: space-between; gap: 12px; margin: 0; padding: 11px 0; border-bottom: 1px solid rgb(255 255 255 / .08); font-size: 13px; color: #d4d4d4; }
  .adm__rows p:last-child { border-bottom: 0; }
  .adm__rows em { font-style: normal; color: #86d19f; }
  .menu-card .panel { --p-h: 200px; }
  .menu-card .panel .menu-mini { position: absolute; inset: 24px 24px auto 24px; display: grid; gap: 6px; font-size: 13px; }
  .menu-mini span { display: flex; justify-content: space-between; gap: 8px; padding: 8px 10px; border-radius: 8px; background: #fff; box-shadow: 0 0 0 1px rgb(0 0 0 / .06); }
  .menu-mini em { font-style: normal; color: var(--muted); }

  /* language switcher in the top bar */
  .lang-switch { display: inline-flex; align-items: center; gap: 6px; }
  .lang-switch .icon { width: 16px; height: 16px; }

  /* Arabic: Cairo font, no Latin letter-spacing, mirrored layout details */
  html[lang="ar"] { --font-display: "Cairo", "Geist", system-ui, sans-serif; --font-text: "Cairo", "Geist", system-ui, sans-serif; --display-tracking: 0; --display-tracking-xl: 0; }
  html[lang="ar"] .lib-open__meta h4 { letter-spacing: 0; }
  [dir="rtl"] .lib-win { text-align: right; }
  [dir="rtl"] .lib-list { border-right: 0; border-left: 1px solid rgb(0 0 0 / .07); }
  [dir="rtl"] .app > .app__pane + .app__pane { border-left: 0; border-right: 1px solid var(--u-line); }
  @media (max-width: 1023px) { [dir="rtl"] .app > .app__pane + .app__pane { border-right: 0; } }
  [dir="rtl"] .m-arrow { transform: scaleX(-1); }
</style>
</head>
<body>
<!-- type: software (a shared PDF library with member uploads and admin review)
     deal: hero centred, nav nav-cards, close close-word, footer footer-statement, rhythm dark-band, buttons arrow, mark dots,
     palette mint-pink-accounts, type geist, ground sky, effect silk-folds (hero window), card grounds fluted-glass (#steps)
     moments: line-carries-colour on #admins (the dark band) -->
<header class="nav">
  <div class="wrap nav__bar">
    <a class="skip-link" href="#top">{{ __('Skip to content') }}</a>
    <a class="nav__logo" href="#top"><span class="mark" data-mark="dots" aria-hidden="true"></span>BooksWeb</a>
    <nav class="nav__links" aria-label="{{ __('Main') }}">
      <div class="nav__item">
        <a class="nav__link" href="#review">{{ __('Review') }} <svg class="chev" viewBox="0 0 12 12" aria-hidden="true"><path d="M3 4.5l3 3 3-3" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
        <div class="nav__menu">
          <div class="wrap grid">
            <a class="menu-card span-4" href="#review">
              <div class="panel"><div class="menu-mini" aria-hidden="true">@foreach ($queue->take(3) as $book)<span>{{ Str::limit($book->title, 26) }}<em>{{ __('Pending') }}</em></span>@endforeach</div></div>
              <span class="t-card">{{ __('Review queue') }}</span>
              <span class="t-small muted">{{ __('Every member upload waits here for an admin') }}</span>
            </a>
            <a class="menu-card span-4" href="#steps">
              <div class="panel"><div class="menu-mini" aria-hidden="true"><span>{{ __('Title, author, category') }}<em>{{ __('Done') }}</em></span><span>{{ __('PDF, up to 50 MB') }}<em>{{ __('Done') }}</em></span><span>{{ __('Cover image') }}<em>{{ __('Optional') }}</em></span></div></div>
              <span class="t-card">{{ __('Upload steps') }}</span>
              <span class="t-small muted">{{ __('From a PDF on your disk to a public book page') }}</span>
            </a>
            <a class="menu-card span-4" href="#admins">
              <div class="panel"><div class="menu-mini" aria-hidden="true"><span>{{ __('Books') }}<em>{{ $stats['books'] }}</em></span><span>{{ __('Categories') }}<em>{{ $stats['categories'] }}</em></span><span>{{ __('Members') }}<em>{{ $stats['members'] }}</em></span></div></div>
              <span class="t-card">{{ __('Admin panel') }}</span>
              <span class="t-small muted">{{ __('Books, categories and members in one place') }}</span>
            </a>
          </div>
        </div>
      </div>
      <div class="nav__item"><a class="nav__link" href="#steps">{{ __('Steps') }}</a></div>
      <div class="nav__item"><a class="nav__link" href="#reading">{{ __('Reading') }}</a></div>
      <div class="nav__item"><a class="nav__link" href="#admins">{{ __('Admins') }}</a></div>
      <div class="nav__item"><a class="nav__link" href="#questions">{{ __('Questions') }}</a></div>
    </nav>
    <div class="nav__actions">
      <x-language-switcher class="nav__link" />
      @auth
        <a class="nav__link" href="{{ route('my-books.index') }}">{{ __('My books') }}</a>
      @else
        <a class="nav__link" href="{{ route('login') }}">{{ __('Sign in') }}</a>
      @endauth
      <a class="btn btn--primary" href="{{ route('books.index') }}">{{ __('Find a book') }}</a>
    </div>
    <button class="nav__toggle" type="button" aria-expanded="false" aria-label="{{ __('Menu') }}">
      <svg width="18" height="18" viewBox="0 0 18 18" aria-hidden="true"><path d="M2 5h14M2 13h14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
    </button>
  </div>
</header>

<main id="top">
  <section class="hero hero--centred" data-enter aria-labelledby="hero-h">
    <div class="wrap"><div class="hero-text">
      <div class="hero__c">
        <a class="pill" href="{{ route('books.index') }}"><span>{{ __(':books books in :categories categories', ['books' => $stats['books'], 'categories' => $stats['categories']]) }}</span><span class="muted">{{ __('See the shelves') }}</span></a>
        <h1 id="hero-h" aria-label="{{ __('A shared library of free PDF books') }}"><span class="ly-mark" aria-hidden="true"></span> {{ __('A shared library of free PDF books') }}</h1>
        <p class="hero__lead">{{ __('Read online or download any book without an account. Members upload PDFs, and an admin checks each one before it goes public.') }}</p>
        <div class="btn-row">
          <a class="btn btn--primary" href="{{ route('books.index') }}">{{ __('Find a book') }}<svg class="m-arrow" viewBox="0 0 12 12" aria-hidden="true"><path class="m-arrow__stem" d="M1 6h8"/><path class="m-arrow__head" d="M4.5 2.5 8 6l-3.5 3.5"/></svg></a>
          <a class="btn btn--secondary" href="{{ route('my-books.create') }}">{{ __('Submit a book') }}</a>
        </div>
      </div>
      <div class="hero__window" data-fx="silk-folds" data-fx-seed="7" role="img" aria-label="{{ __('The BooksWeb library with :title open and its download buttons', ['title' => $openBook?->title ?? __('a book')]) }}">
        <div class="ly-piece lib-win ui" aria-hidden="true">
          <div class="lib-win__bar"><span class="lib-win__dots"><i></i><i></i><i></i></span><span><b>{{ __('Library') }}</b> · {{ __('Latest') }}</span><span class="sp"></span><span>{{ trans_choice(':count book|:count books', $stats['books']) }}</span></div>
          <div class="lib-win__body">
            <div class="lib-list">
              <div class="q">{{ __('Search title or author') }}</div>
              @foreach ($latestBooks->take(5) as $book)
                @php([$from, $to] = $book->coverColors())
                <div @class(['t', 'is-on' => $loop->first])><i style="background: linear-gradient(to bottom, {{ $to }}, {{ $from }} 60%)"></i><b>{{ $book->title }}</b><span>{{ $book->author }}</span></div>
              @endforeach
            </div>
            @if ($openBook)
              @php([$from, $to] = $openBook->coverColors())
              <div class="lib-open" data-ly-steps="900">
                <div class="lib-open__cover" style="background: linear-gradient(to bottom, {{ $from }} 55%, {{ $to }})">{{ $openBook->title }}<small>{{ $openBook->author }}</small></div>
                <div class="lib-open__meta">
                  <h4>{{ $openBook->title }}</h4>
                  <p>{{ __('by') }} {{ $openBook->author }}</p>
                  <div class="lib-facts" data-step>
                    @if ($openBook->category)<span>{{ $openBook->category->name }}</span>@endif
                    @if ($openBook->pages)<span>{{ __(':count pages', ['count' => $openBook->pages]) }}</span>@endif
                    @if ($openBook->language)<span>{{ $openBook->language }}</span>@endif
                  </div>
                  <p data-step dir="auto">{{ Str::limit($openBook->description, 150) }}</p>
                  <div class="lib-acts" data-step><span>{{ __('Download PDF') }}</span><span>{{ __('Read online') }}</span></div>
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div></div>
  </section>

  <section class="section" id="review" aria-labelledby="review-h">
    <div class="wrap">
      <div class="grid">
        <h2 class="span-5" id="review-h">{{ __('A review queue for every upload') }}</h2>
        <p class="span-6 start-7">{{ __("A member's book lands here as pending. An admin opens the PDF, checks the title and category, then approves it or rejects it with a reason.") }} <a class="link" href="#steps">{{ __('See the three steps') }}</a></p>
      </div>
      <div class="ui app fill" role="img" aria-label="{{ __('The admin review queue with pending book requests beside the open request') }}" data-ui-pause-host>
        <div class="app__pane app__pane--list">
          <div class="ui ui-panel ui-win" data-ui="window" data-open="{{ __('Pending') }}" data-work="{{ __('Reviewing') }}" data-done="{{ __('Approved') }}">
            <div class="ui-win__bar">
              <span class="ui-dots" aria-hidden="true"><i></i><i></i><i></i></span>
              <span class="ui-win__crumb">{{ __('Requests') }} <i>/</i> <b>{{ __('Pending') }}</b></span>
              <span class="ui-spacer"></span>
              <span class="ui-win__search" aria-hidden="true"><svg class="ui-icon" viewBox="0 0 16 16"><circle cx="7" cy="7" r="4.5"/><path d="m10.5 10.5 3 3"/></svg><span class="ui-win__ph">{{ __('Search') }}</span></span>
            </div>
            <div class="ui-win__body">
              <nav class="ui-win__side" aria-hidden="true">
                <span class="ui-win__nav is-on">{{ __('Pending') }}<em>{{ max($stats['pending'], 1) }}</em></span>
                <span class="ui-win__nav">{{ __('Approved') }}<em>{{ $stats['books'] }}</em></span>
                <span class="ui-win__nav">{{ __('Rejected') }}</span>
              </nav>
              <div class="ui-win__main">
                <div class="ui-win__cols" aria-hidden="true"><span>{{ __('Author') }}</span><span>{{ __('Book') }}</span><span>{{ __('Status') }}</span><span>{{ __('Age') }}</span></div>
                <ul class="ui-win__rows">
                  @foreach ($queue as $book)
                    <li class="ui-win__row" data-row><span class="ui-avatar" data-tone="{{ $loop->iteration % 5 + 1 }}">{{ $initials($book->author) }}</span><span class="ui-win__who">{{ $book->author }}</span><span class="ui-win__sub">{{ $book->title }}</span><span @class(['ui-win__st', 'is-done' => $loop->index >= 4]) data-st>{{ $loop->index < 4 ? __('Pending') : __('Approved') }}</span><span class="ui-win__age">{{ __(':countm', ['count' => ($loop->index + 1) * 7]) }}</span></li>
                  @endforeach
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="app__pane app__pane--open" data-ui-loop>
          <div class="ui ui-doc" data-ui="doc">
            <article class="ui-doc__page">
              <div class="ui-doc__label">{{ __('Request from a member · PDF attached') }}</div>
              <div class="ui-doc__title">{{ $openBook?->title ?? __('New book') }}{{ __(', ') }}{{ $openBook?->author }}</div>
              <div class="ui-doc__text">{{ __('Category') }} <mark class="ui-doc__mark is-on">{{ $openBook?->category?->name ?? __('Not set') }}</mark>{{ __(', ') }}{{ __(':count pages', ['count' => $openBook?->pages ?? __('unknown')]) }}{{ __(', ') }}{{ __('written in :language', ['language' => $openBook?->language ?? __('English')]) }}.</div>
              <div class="ui-doc__text" dir="auto">{{ Str::limit($openBook?->description ?? '', 180) }}</div>
              <div class="ui-doc__text">{{ __('The member can edit a rejected request and') }} <mark class="ui-doc__mark" data-kind="{{ __('Rule') }}" data-msg="{{ __('Any edit sends the request back to pending.') }}" data-where="{{ __("Member's book page") }}">{{ __('send it again') }}</mark> {{ __('for review.') }}</div>
            </article>
            <aside class="ui-doc__note ui-panel is-on" aria-live="polite">
              <div class="ui-doc__kind"><span class="ui-doc__dot"></span><span data-doc-kind>{{ __('Check') }}</span></div>
              <div class="ui-doc__msg" data-doc-msg>{{ __('Category matches the book. Ready to approve.') }}</div>
              <div class="ui-doc__where" data-doc-where>{{ __('Admin review') }}</div>
            </aside>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section" id="steps" style="--hm: 2" aria-labelledby="steps-h">
    <div class="wrap">
      <div class="section-head">
        <h2 id="steps-h">{{ __('A book goes public in 3 steps') }}</h2>
      </div>
      <div class="grid fill">
        <article class="card card--row card--open span-4">
          <span class="step-num">1</span>
          <h3>{{ __('Upload the PDF') }}</h3>
          <p>{{ __('Add the title, author and category, then attach the PDF and a cover if you have one.') }}</p>
          <div class="panel" data-fx="fluted-glass" data-fx-seed="21" data-fx-params='{"drift":0}'>
            <div class="stage stage--fade-b"><div class="at-rise fit" style="--fit-max:320px;--ax:max(24px, 50% - 160px);--ay:40px;--ay-sm:24px" data-ui-loop>
              <div class="ui ui-panel ui-checks" data-ui="checklist">
                <div class="ui-checks__head"><div class="ui-title">{{ __('Submit a book') }}</div><span class="ui-spacer"></span><div class="ui-meta" data-checks-count>{{ __(':done of :total', ['done' => 2, 'total' => 5]) }}</div></div>
                <div class="ui-checks__bar"><i data-checks-bar style="--k:0.4"></i></div>
                <ul class="ui-checks__list">
                  <li class="ui-check is-done"><span class="ui-check__box"><svg viewBox="0 0 16 16"><path d="m4.5 8.2 2.3 2.3 4.7-5"/></svg></span><span class="ui-check__label">{{ __('Title and author') }}</span></li>
                  <li class="ui-check is-done"><span class="ui-check__box"><svg viewBox="0 0 16 16"><path d="m4.5 8.2 2.3 2.3 4.7-5"/></svg></span><span class="ui-check__label">{{ __('Category') }}</span></li>
                  <li class="ui-check"><span class="ui-check__box"><svg viewBox="0 0 16 16"><path d="m4.5 8.2 2.3 2.3 4.7-5"/></svg></span><span class="ui-check__label">{{ __('Description') }}</span></li>
                  <li class="ui-check"><span class="ui-check__box"><svg viewBox="0 0 16 16"><path d="m4.5 8.2 2.3 2.3 4.7-5"/></svg></span><span class="ui-check__label">{{ __('PDF file, up to 50 MB') }}</span></li>
                  <li class="ui-check"><span class="ui-check__box"><svg viewBox="0 0 16 16"><path d="m4.5 8.2 2.3 2.3 4.7-5"/></svg></span><span class="ui-check__label">{{ __('Cover image') }}</span></li>
                </ul>
              </div>
            </div></div>
          </div>
        </article>
        <article class="card card--row card--open span-4">
          <span class="step-num">2</span>
          <h3>{{ __('An admin reviews it') }}</h3>
          <p>{{ __('You see the decision on your books page. A rejection comes with a reason you can fix.') }}</p>
          <div class="panel" data-fx="fluted-glass" data-fx-seed="34" data-fx-params='{"drift":0}'>
            <div class="stage stage--fade-b"><div class="at-rise fit" style="--fit-max:320px;--ax:max(24px, 50% - 160px);--ay:40px;--ay-sm:24px">
              <div class="ui ui-notify" data-ui="notify">
                <ul class="ui-notify__list" data-notify-list>
                  <li class="ui-note"><span class="ui-note__ico"><svg class="ui-icon" viewBox="0 0 16 16"><path d="m3.5 8.4 2.8 2.8 6.2-6.4"/></svg></span><span class="ui-note__text"><span class="ui-note__title">{{ __('Request approved') }}<time>{{ __('now') }}</time></span><span class="ui-note__body">{{ __(':title is public', ['title' => $queue->get(1)?->title ?? __('Your book')]) }}</span></span></li>
                  <li class="ui-note"><span class="ui-note__ico"><svg class="ui-icon" viewBox="0 0 16 16"><path d="M4.5 4.5l7 7M11.5 4.5l-7 7"/></svg></span><span class="ui-note__text"><span class="ui-note__title">{{ __('Request rejected') }}<time>{{ __(':counth', ['count' => 2]) }}</time></span><span class="ui-note__body">{{ __('Reason: pages 40 to 52 are missing') }}</span></span></li>
                  <li class="ui-note"><span class="ui-note__ico"><svg class="ui-icon" viewBox="0 0 16 16"><circle cx="8" cy="8" r="5.5"/><path d="M8 5v3l2 1.5"/></svg></span><span class="ui-note__text"><span class="ui-note__title">{{ __('Sent for review') }}<time>{{ __(':countd', ['count' => 1]) }}</time></span><span class="ui-note__body">{{ __('Edited request back to pending') }}</span></span></li>
                </ul>
              </div>
            </div></div>
          </div>
        </article>
        <article class="card card--row card--open span-4">
          <span class="step-num">3</span>
          <h3>{{ __('Readers find it') }}</h3>
          <p>{{ __('The book shows in search, in its category and on the home page as newly added.') }}</p>
          <div class="panel" data-fx="fluted-glass" data-fx-seed="58" data-fx-params='{"drift":0}'>
            <div class="stage stage--fade-b"><div class="at-rise fit" style="--fit-max:320px;--ax:max(24px, 50% - 160px);--ay:40px;--ay-sm:24px">
              <div class="ui ui-panel ui-search" data-ui="search">
                <div class="ui-search__field">
                  <svg class="ui-icon" viewBox="0 0 16 16" aria-hidden="true"><circle cx="7" cy="7" r="4.5"/><path d="m10.5 10.5 3 3"/></svg>
                  <span class="ui-search__q"><span data-search-q>{{ Str::lower(Str::words($queue->get(1)?->title ?? 'habits', 1, '')) }}</span><i class="ui-search__caret"></i></span>
                </div>
                <div class="ui-search__group">{{ __('Books') }}</div>
                <ul class="ui-search__list" data-search-list>
                  @foreach ($queue->take(3) as $book)
                    <li @class(['ui-search__row', 'is-sel' => $loop->first])><span class="ui-search__ico"><svg class="ui-icon" viewBox="0 0 16 16"><path d="M4 2.5h5l3 3v8H4z"/><path d="M9 2.5v3h3"/></svg></span><span class="ui-search__t">{{ Str::limit($book->title, 24) }}</span><span class="ui-search__m">PDF</span></li>
                  @endforeach
                </ul>
              </div>
            </div></div>
          </div>
        </article>
      </div>
    </div>
  </section>

  <section class="section" id="questions" aria-labelledby="questions-h">
    <div class="wrap">
      <div class="grid">
        <div class="span-5 stack-4">
          <h2 id="questions-h">{{ __('Questions') }}</h2>
          <p>{{ __('What members ask before their first upload.') }}</p>
          <a class="link" href="{{ route('sign_up') }}">{{ __('Create a free account') }}</a>
        </div>
        <div class="faq span-6 start-7">
          <div class="faq__item"><h3>{{ __('Do I need an account to read a book?') }}</h3><p>{{ __('No. Anyone can open or download a published book. You only sign up to upload.') }}</p></div>
          <div class="faq__item"><h3>{{ __('Which book files can I upload?') }}</h3><p>{{ __('The book as one PDF of up to 50 MB, and a cover as JPG, PNG or WEBP of up to 4 MB.') }}</p></div>
          <div class="faq__item"><h3>{{ __('Who sees my book while it is pending?') }}</h3><p>{{ __('Only you and the admins. It joins the library on the day an admin approves it.') }}</p></div>
          <div class="faq__item"><h3>{{ __('What happens to a rejected book?') }}</h3><p>{{ __('The reason shows on your books page. Fix the request and save it; it goes back to pending.') }}</p></div>
          <div class="faq__item"><h3>{{ __('Can I change a book after approval?') }}</h3><p>{{ __('Only an admin can. You can still delete your own book from your books page.') }}</p></div>
          <div class="faq__item"><h3>{{ __('How many books can I submit?') }}</h3><p>{{ __('There is no limit. Each book is its own request and is reviewed on its own.') }}</p></div>
        </div>
      </div>
    </div>
  </section>
  <section class="section" id="reading" aria-labelledby="reading-h">
    <div class="wrap">
      <div class="grid fill">
        <div class="span-5 stack-6">
          <h2 id="reading-h">{{ __('Read in the browser or keep the PDF') }}</h2>
          <p class="t-lead">{{ __("Every book page has two buttons. Read online opens the PDF in a new tab; Download saves it under the book's title.") }}</p>
          <ul class="stack-3">
            <li>{{ __('No account needed to read or download') }}</li>
            <li>{{ __('A download count on every book') }}</li>
            <li>{{ __('A drawn cover for books uploaded without one') }}</li>
          </ul>
          <a class="link" href="{{ route('books.index') }}">{{ __('See the newest books') }}</a>
        </div>
        <div class="frame frame--split frame--photo span-6 start-7">
          <img class="frame__img" src="{{ $kit('img/photo-f195053383-960.jpg') }}" srcset="{{ $kit('img/photo-f195053383-500.jpg') }} 500w, {{ $kit('img/photo-f195053383-960.jpg') }} 960w" sizes="(max-width: 639px) 100vw, 960px" width="960" height="722" alt="{{ __('A reading corner with full bookshelves, a small round table and chairs on a rug') }}" loading="lazy" decoding="async" data-source="stand-in" data-subject="reading room">
        </div>
      </div>
    </div>
  </section>

  <section class="section ground-inverse" id="admins" aria-labelledby="admins-h" data-moment="line-carries-colour" data-moment-opts='{"from": "base"}'>
    <div class="wrap">
      <div class="grid fill">
        <div class="span-5 stack-6">
          <h2 id="admins-h">{{ __('Admin panel for books, categories and members') }}</h2>
          <p class="t-lead">{{ __('Admins sign in on their own page. A book they upload is public at once; they also rename categories and remove members.') }}</p>
          <a class="link" href="{{ route('admin.login') }}">{{ __('Open the admin sign-in') }}</a>
        </div>
        <div class="span-6 start-7">
          <div class="adm ui" role="img" aria-label="{{ __("The admin dashboard with today's library numbers") }}">
            <div class="adm__bar"><b>{{ __('Dashboard') }}</b><span>· {{ __('BooksWeb admin') }}</span></div>
            <div class="adm__grid">
              <div><span>{{ __('Published books') }}</span><strong>{{ number_format($stats['books']) }}</strong></div>
              <div><span>{{ __('Pending requests') }}</span><strong>{{ number_format($stats['pending']) }}</strong></div>
              <div><span>{{ __('Members') }}</span><strong>{{ number_format($stats['members']) }}</strong></div>
              <div><span>{{ __('Downloads') }}</span><strong>{{ number_format($stats['downloads']) }}</strong></div>
            </div>
            <div class="adm__rows">
              @foreach ($categories->take(8) as $category)
                <p><span>{{ $category->name }}</span><em>{{ trans_choice(':count book|:count books', $category->books_count) }}</em></p>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

</main>

<footer class="footer closing footer--statement g2m" id="contact" aria-labelledby="close-h">
  <div class="wrap">
    <div class="closing__cta close--word">
      <div class="section-head--split">
        <div><h2 id="close-h">{{ __('Find your next book') }}</h2><p class="t-lead">{{ __(':count books, free to read and download.', ['count' => $stats['books']]) }}</p></div>
        <a class="btn btn--primary" href="{{ route('books.index') }}">{{ __('Find a book') }}<svg class="m-arrow" viewBox="0 0 12 12" aria-hidden="true"><path class="m-arrow__stem" d="M1 6h8"/><path class="m-arrow__head" d="M4.5 2.5 8 6l-3.5 3.5"/></svg></a>
      </div>
      <p class="close__word" aria-hidden="true">BooksWeb</p>
    </div>
    <div class="closing__links">
      <div class="grid">
        <div class="span-4 stack-4">
          <a class="nav__logo" href="#top"><span class="mark" data-mark="dots" aria-hidden="true"></span>BooksWeb</a>
          <p class="footer__lede">{{ __('A PDF library its members fill.') }}</p>
        </div>
        <div class="span-2 footer__col"><h3>{{ __('Library') }}</h3><ul><li><a href="{{ route('books.index') }}">{{ __('All books') }}</a></li><li><a href="#reading">{{ __('Reading') }}</a></li></ul></div>
        <div class="span-2 footer__col"><h3>{{ __('Members') }}</h3><ul><li><a href="{{ route('sign_up') }}">{{ __('Sign up') }}</a></li><li><a href="{{ route('login') }}">{{ __('Sign in') }}</a></li></ul></div>
        <div class="span-2 footer__col"><h3>{{ __('Uploads') }}</h3><ul><li><a href="#steps">{{ __('Steps') }}</a></li><li><a href="#review">{{ __('Review') }}</a></li></ul></div>
        <div class="span-2 footer__col"><h3>{{ __('Admins') }}</h3><ul><li><a href="#admins">{{ __('Admin panel') }}</a></li><li><a href="#questions">{{ __('Questions') }}</a></li></ul></div>
      </div>
      <div class="footer__legal">
        <span>&copy; {{ date('Y') }} BooksWeb</span>
        <x-language-switcher />
        <a class="credits" href="{{ asset('credits.html') }}">{{ __('Photographs via Wikimedia Commons') }}</a>
      </div>
    </div>
  </div>
</footer>

<script type="module" src="{{ $kit('system.js') }}"></script>
<script type="module" src="{{ $kit('motion/motion.js') }}"></script>
<script type="module" src="{{ $kit('layout/layout.js') }}"></script>
</body>
</html>
