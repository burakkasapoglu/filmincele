@extends('layouts.app')
@section('title', 'Profili Düzenle — Filmincele')
@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <a href="{{ url('/profil') }}" class="text-gray-400 hover:text-white text-sm transition mb-6 inline-block">← Profile Dön</a>
    <livewire:edit-profile />
</div>
@endsection
