@extends('layouts.admin')

@section('content1')
<div class="content1">
    <h1 style="margin-bottom: 20px;">Manage Users</h1>
    <div class="" style="width: 100%; padding-top: 20px;">
        @if ($users->isEmpty())
        <div class="container-empty">
            <img src="images/empties.png" alt="Empty">
            <div class="empty-text">No users found</div>
        </div>
        @else
        <table id="example" class="display nowrap" style="width: 100%; padding-top: 20px;">
            <thead>
                <tr>
                    <th style="text-align: center; background-color: maroon; color: white; padding-left: 30px;">ID</th>
                    <th style="text-align: center; background-color: maroon; color: white; padding-left: 30px;">Name</th>
                    <th style="text-align: center; background-color: maroon; color: white; padding-left: 30px;">Email</th>
                    <th style="text-align: center; background-color: maroon; color: white; padding-left: 30px;">School ID</th>
                    <th style="text-align: center; background-color: maroon; color: white; padding-left: 30px;">Role ID</th>
                    <th style="text-align: center; background-color: maroon; color: white; padding-left: 30px;">Order Count</th>
                    <th style="text-align: center; background-color: maroon; color: white; padding-left: 30px;">Action</th>
                </tr>
            </thead>
            <tbody style="background-color: white;">
                @foreach ($users as $user)
                <tr id="user-row-{{$user->id}}">
                    <td style="text-align: center;">{{$user->id}}</td>
                    <td style="text-align: center;">{{$user->name}}</td>
                    <td style="text-align: center;">{{$user->email}}</td>
                    <td style="text-align: center;">{{$user->school_id}}</td>
                    <td style="text-align: center;">{{$user->role_id}}</td>
                    <td style="text-align: center;">{{$user->totalOrder}}</td>
                    <td style="text-align: center;">
                        <button class="open-modal6" style="font-size: 18px;" data-user-id="{{$user->id}}">
                            <iconify-icon icon="bx:edit" style="color: black;"></iconify-icon>
                        </button>
                        <button style="font-size: 18px;" data-user-id="{{$user->id}}" onclick="deleteUser(this.dataset.userId)">
                            <iconify-icon icon="material-symbols-light:delete-outline" style="color: black;"></iconify-icon>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    <div class="modal_user-edit">
        <form method="POST">
            @csrf
            <div class="manage-user-profile-modal">
                <div class="manage-user-profile">
                    <img id="profile-picture" src="images/profile/default.png" alt="Profile Picture" style="border-radius: 10px;">
                </div>
                <div class="manage-user-edit">
                    <label class="manage-username">Username</label>
                    <input type="text" class="manage-user-info" id="user-name" name="name" value="" placeholder="Username">
                    <label class="manage-email">Email</label>
                    <input type="text" class="manage-user-info" id="user-email" name="email" value="" placeholder="Email">
                    <label class="manage-student-id">School ID</label>
                    <input type="text" class="manage-user-info" id="user-school-id" name="school_id" value="" placeholder="2022-01308">
                </div>
            </div>
            <div class="role" id="user-role">
                <div class="manage-student">
                    <input id="student" type="radio" name="role" value="1" class="manage-user-student">
                    <label for="student">Student</label>
                </div>
                <div class="manage-faculty">
                    <input id="faculty" type="radio" name="role" value="2" class="manage-user-faculty">
                    <label for="faculty">Faculty</label>
                </div>
                <div class="manage-admin">
                    <input id="admin" type="radio" name="role" value="3" class="manage-user-admin">
                    <label for="admin">Admin</label>
                </div>
            </div>
            <div class="manage-save">
                <button id="manage-save">Save</button>
            </div>
        </form>
        <div class="close-modal6">
            <iconify-icon id="close" icon="material-symbols-light:close"></iconify-icon>
        </div>
    </div>
</div>
<script>
    const usermanagementModal = document.querySelector(".modal_user-edit");
    const editForm = usermanagementModal.querySelector("form");

    const openModal6Buttons = document.querySelectorAll(".open-modal6");
    openModal6Buttons.forEach((btn) => {
        btn.addEventListener("click", async (e) => {
            e.preventDefault();

            const userId = btn.dataset.userId;

            const response = await fetch(`/user/${userId}`);
            const userData = await response.json();

            const userRoleInput = document.querySelector("#user-role input[value='" + userData.role_id + "']");

            if (userRoleInput) {
                userRoleInput.checked = true;
            }

            editForm.querySelector("#profile-picture").src = `images/profile/${userData.image}`;
            editForm.querySelector("#user-name").value = userData.name;
            editForm.querySelector("#user-email").value = userData.email;
            editForm.querySelector("#user-school-id").value = userData.school_id;
            editForm.querySelector("#user-role").value = userData.role_id;

            editForm.action = `/user/edit/${userData.id}`;

            usermanagementModal.classList.add("active");
        });
    });

    const closeModal6 = document.querySelector(".close-modal6");
    if (closeModal6) {
        closeModal6.addEventListener("click", () => {
            usermanagementModal.classList.remove("active");
        });
    }

    async function deleteUser(userId) {
        const response = await fetch(`/user/delete/${userId}`);
        const userData = await response.json();

        document.getElementById(`user-row-${userData.id}`).style.display = 'none';
    }
</script>
@endsection