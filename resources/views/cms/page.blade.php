@extends('layouts.main')

@section('title', $page->seo_title ?? $page->title)

@section('content')
    @if($page->content)
        @foreach($page->content as $block)
            <x-dynamic-component :component="'cms.' . $block['type']" :data="$block['data']" />
        @endforeach
    @endif
@endsection