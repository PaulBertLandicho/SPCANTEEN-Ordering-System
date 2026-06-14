@extends('layouts.layout')

@section('content')
@auth
<p>Set up profile</p>
<a href="/">Home</a>
@else
@section('css', 'css/auth.css')
@section('title', 'SPCanteen - Register')
<div class="container">
    <div class="row">
        <div class="registration-form">
            <div class="logo2">
                <img id="logo" src="/images/SPCanteen.png" alt="SPCanteen.png">
            </div>
            <!--------- Register start -------->
            <div class="form">
                <form action="/register" method="POST">
                    @csrf
                    <div class="input-container">
                        <input type="text" name="name" class="input-field" required>
                        <label>Username</label>
                        <i class="fa-solid fa-user"></i>
                    </div>
                    @error('name')
                    <p style="color: red; margin-left: 20px; position: absolute;">{{$message}}</p>
                    @enderror
                    <div class="input-container">
                        <input type="text" name="email" class="input-field" required>
                        <label>Email</label>
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    @error('email')
                    <p style="color: red; margin-left: 20px; position: absolute;">{{$message}}</p>
                    @enderror
                    <div class="input-container">
                        <input type="text" class="manage-user-info" id="user-school-id" name="school_id" value="" placeholder="2022-01308">
                        <label>School ID</label>
                        <i class="fa fa-address-card"></i>
                    </div>
                    <div class="input-container">
                        <input type="password" id="password" name="password" class="input-field" required>
                        <label>Password</label>
                        <i class="fa-solid fa-eye" onclick="togglePassword(this, 'password')"></i>
                    </div>
                    <div class="input-container">
                        <input type="password" id="password_confirmation" name="password_confirmation" class="input-field" required>
                        <label>Confirm Password</label>
                        <i class="fa-solid fa-eye" onclick="togglePassword(this, 'password_confirmation')"></i>
                    </div>
                    @error('password')
                    <p style="color: red; margin-left: 20px; position: absolute;">{{$message}}</p>
                    @enderror
                    <div class="register">
                        <input type="submit" class="btn" value="REGISTER">
                    </div>
                </form>
                <div class="login">
                    <p id="login-txt">Already have an account? <a id="login-btn" href="/">Login</a></p>
                </div>
            </div>
            <!--------- Register end -------->
        </div>
    </div>
</div>
@endauth
@endsection