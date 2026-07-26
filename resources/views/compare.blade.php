@extends('layouts.app')
@section('title', 'Film Karşılaştır — Filmincele')
@section('content')
<div class="py-8 px-4">
    <livewire:compare-movies :id1="(int) request()->route('id1', 0)" :id2="(int) request()->route('id2', 0)" />
</div>
@endsection
