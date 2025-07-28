@extends('layouts.app')

@section('content')
    <livewire:messages.compose :replyTo="$replyTo ?? null" :recipient="$recipient ?? null" />
@endsection
