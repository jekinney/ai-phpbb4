@extends('layouts.app')

@section('content')
    <livewire:messages.inbox :activeTab="$activeTab ?? 'inbox'" />
@endsection
