@extends('layouts.user')

@section('page', 'profile')
@section('content1')
<div class="content">
    <div class="profile-container">
        <span id="profile-txt">PROFILE</span>
    </div>
    <div class="user-container">
        <div class="user-content">
            <div class="user-info">
                <img id="user-profile" src="{{ asset('images/profile/' . Auth::user()->image) }}" alt="Profile Picture">
                <div class="user-profile-edit">
                    <a id="user-edit" href="{{ route('user.edit', ['id' => Auth::user()->id])}}">
                        <iconify-icon id="user-edit-icon" icon="akar-icons:edit"></iconify-icon>
                    </a>
                </div>
            </div>
        </div>
        <span id="name-txt" title="{{ Auth::user()->name }}" style="display:inline-block; max-width:28ch; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; vertical-align:middle;">{{ Auth::user()->name }}</span>
        <span id="role-txt">{{Auth::user()->role->name}}</span>
    </div>
    <div class="user-btns">
        <div class="profile-btn">
            <a class="option-link" href="favorite">
                <iconify-icon id="fav-icon" icon="material-symbols:favorite"></iconify-icon>
                <span id="fav-txt">Your Favorite</span>
                <iconify-icon id="fav-btn" icon="ic:baseline-greater-than"></iconify-icon>
            </a>
        </div>
        <div class="profile-btns">
            <a class="option-link" href="history">
                <iconify-icon id="history-icon" icon="bi:clock-history"></iconify-icon>
                <span id="history-txt">History</span>
                <iconify-icon id="history-btn" icon="ic:baseline-greater-than"></iconify-icon>
            </a>
        </div>
        <div class="profile-btns">
            <a class="option-link" href="#">
                <iconify-icon id="about-icon" icon="mdi:about-circle-outline"></iconify-icon>
                <span id="about-txt">About</span>
                <iconify-icon id="about-btn" icon="ic:baseline-greater-than"></iconify-icon>
            </a>
        </div>
        <div class="profile-btns">
            <form action="/logout" method="POST">
                @csrf
                <button type="submit" class="logout-link">
                    <iconify-icon id="logout-icon" icon="mdi-light:logout"></iconify-icon>
                    <span id="logout-txt">Logout</span>
                    <iconify-icon id="logout-btn" icon="ic:baseline-greater-than"></iconify-icon>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection