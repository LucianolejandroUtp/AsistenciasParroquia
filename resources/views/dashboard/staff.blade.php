@extends('layouts.app')

@section('title', $title ?? 'Panel de Personal de Apoyo')

@section('page-title', $title ?? 'Panel de Personal de Apoyo')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Personal de Apoyo</li>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <h4>{{ $title ?? 'Panel de Personal de Apoyo' }}</h4>
        <p>{{ $message ?? 'Bienvenido al panel de staff.' }}</p>
    </div>
</div>
@endsection