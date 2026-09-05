@extends('layouts.app')

@section('title', 'Semua Tugas')
@section('page-title', 'Semua Tugas Hari Ini')
@section('page-subtitle', 'Gabungan seluruh tugas yang harus kamu selesaikan hari ini')

@section('sidebar')
    @include('components.sidebar-ob')
@endsection

@section('content')
    <x-task-all-table :tasks="$tasks" role="ob" />
@endsection
