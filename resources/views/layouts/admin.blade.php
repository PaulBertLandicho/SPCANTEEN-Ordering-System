@extends('layouts.layout')

@section('content')
@section('css', 'css/admin.css')
@if (Auth::user()->role_id === 3)
@section('title', 'SPCanteen - Admin')
@else
@section('title', 'SPCanteen - Super Admin')
@endif
<div class="container">
    @include('layouts.components.admin.admin_navbar')
    @yield('content1')
</div>
@endsection