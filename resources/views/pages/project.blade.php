@extends('layouts.app')

@section('content')
<section class="wrap project-detail-page">
    <a class="eyebrow project-back-link" href="{{ route('portfolio') }}"><x-arrow-icon/> Kembali ke portfolio</a>
    <x-portfolio-preview :project="$project"/>
    <div class="project-detail-cta">
        <p>Punya kebutuhan website serupa? Mari bahas arah yang paling sesuai untuk bisnismu.</p>
        <a class="btn btn-primary" href="{{ route('contact') }}">Diskusikan proyek <x-arrow-icon/></a>
    </div>
</section>
@endsection
