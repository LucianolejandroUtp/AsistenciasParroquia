@extends('layouts.app')

@section('title', $title ?? 'Panel de Administración')

@section('page-title', $title ?? 'Panel de Administración')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Administración</li>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <h4>{{ $title ?? 'Panel de Administración' }}</h4>
        <p>{{ $message ?? 'Bienvenido al panel de administración.' }}</p>
    </div>
</div>
@endsection