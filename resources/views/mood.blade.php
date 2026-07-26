@extends('layouts.app')
@section('title', ucfirst($mood) . ' İçerikleri')

@section('content')
<livewire:movie-grid :mood="$mood" />
@endsection
