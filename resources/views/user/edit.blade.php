@extends('layouts.layout')

@section('content')
@section('css', 'css/user.css')
<div class="content">
    <div class="user-edit-header">
        <a href="{{ route('profile') }}" class="user-edit-icon">
            <iconify-icon icon="tabler:arrow-back-up"></iconify-icon>
        </a>
        <h5>Edit Profile</h5>
    </div>

    <div class="user-edit-inputs">
        <form id="edit-form" action="{{ route('process.edit', ['id' => $user->id ]) }}" method="POST" data-orig-email="{{ $user->email }}">
            @csrf
            <div class="user-edit-container">
                <div class="user-edit-name">
                    <label for="name">Name</label>
                    <input class="edit-user-fields" type="text" name="name" value="{{ old('name', $user->name) }}">
                </div>
                <div class="user-edit-email">
                    <label for="email">Email</label>
                    <input class="edit-user-fields" type="email" name="email" value="{{ old('email', $user->email) }}">
                </div>
                <div class="user-edit-old-password">
                    <label for="oldpassword">Old Password</label>
                    <input class="edit-user-fields" type="password" name="oldpassword">
                </div>
                <div class="user-edit-new-password">
                    <label for="password">New Password</label>
                    <input class="edit-user-fields" type="password" name="password">
                </div>
                <div class="user-edit-confirm-password">
                    <label for="password_confirmation">Confirm New Password</label>
                    <input class="edit-user-fields" type="password" name="password_confirmation">
                </div>
                <div class="user-edit-btn">
                    <input class="edit-user-btn" type="submit" name="submit" value="Submit">
                </div>
            </div>
        </form>

        <script>
            // Allow updating individual fields. Do not require current password client-side.
            (function() {
                const form = document.getElementById('edit-form');
                if (!form) return;
                form.addEventListener('submit', function(e) {
                    const newpwd = (form.querySelector('input[name="password"]') || {}).value || '';
                    const conf = (form.querySelector('input[name="password_confirmation"]') || {}).value || '';

                    // If changing password, ensure confirmation matches
                    if ((newpwd.trim() !== '' || conf.trim() !== '') && newpwd !== conf) {
                        e.preventDefault();
                        alert('New password and confirmation do not match.');
                        return false;
                    }

                    // Allow submission for any other changes without requiring old password client-side
                });
            })();
        </script>
    </div>

    {{-- SweetAlert2 notifications for success, error, and validation messages (rendered via hidden DOM nodes) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('success'))
    <div id="flash-success" data-msg="{{ session('success') }}" style="display:none"></div>
    @endif

    @if(session('error'))
    <div id="flash-error" data-msg="{{ session('error') }}" style="display:none"></div>
    @endif

    @if($errors->any())
    <div id="flash-errors" style="display:none">
        @foreach($errors->all() as $err)
        <div class="flash-error-item">{{ $err }}</div>
        @endforeach
    </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            try {
                const s = document.getElementById('flash-success');
                const e = document.getElementById('flash-error');
                const errs = document.getElementById('flash-errors');

                if (s && s.dataset && s.dataset.msg) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: s.dataset.msg
                    });
                }
                if (e && e.dataset && e.dataset.msg) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: e.dataset.msg
                    });
                }
                if (errs) {
                    const items = Array.from(errs.querySelectorAll('.flash-error-item')).map(n => n.textContent || '');
                    if (items.length) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: items.map(i => `<div style="text-align:left">${i}</div>`).join('')
                        });
                    }
                }
            } catch (err) {
                console.error('Error showing notifications', err);
            }
        });
    </script>
</div>
@endsection