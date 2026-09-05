@extends('layouts.app')

@section('title', 'Semua Tugas')
@section('page-title', 'Semua Tugas')
@section('page-subtitle', 'Seluruh tugas mandiri, rutin, penugasan, dan pengelolaan sosmed hari ini')

@section('sidebar')
    @include('components.sidebar-sosmed')
@endsection

@section('content')
    <x-task-all-table :tasks="$tasks" role="sosmed" />
@endsection
