@extends('layouts.app')

@section('title', $title ?? 'Panel de Catequista')

@section('page-title', $title ?? 'Panel de Catequista')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Catequista</li>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <h4>{{ $title ?? 'Panel de Catequista' }}</h4>
        <p>{{ $message ?? 'Bienvenido al panel de catequista.' }}</p>
    </div>
</div>
@endsection