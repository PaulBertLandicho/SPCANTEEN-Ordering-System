@extends('layouts.layout')

@section('content')
@section('css', 'css/user.css')
@section('title', 'SPCanteen - User')
<div class="container">
    @yield('content1')
    @include('layouts.components.user.user_navbar')
</div>
@endsection