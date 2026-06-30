<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Change Password</title>

<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" 
href="{{ asset('css/changepassword.css') }}">

</head>

<body>

<div class="container">

    <div class="title">
        Change Password
    </div>

    <div class="subtitle">
        You must change your password before continuing
    </div>

    <form method="POST"
      action="/change-password">

        @csrf

        <!-- NEW PASSWORD -->
        <div class="form-group">

            <label>New Password</label>

            <div class="input-box">

                <input type="password"
                       name="password"
                       id="password"
                       placeholder="Enter new password"
                       required>

                <button type="button"
                        class="toggle-password"
                        onclick="togglePassword('password', 'passwordIcon')">

                    <i class="fa-solid fa-lock"
                       id="passwordIcon"></i>

                </button>

            </div>

            @error('password')
                <div class="error">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <!-- CONFIRM PASSWORD -->
        <div class="form-group">

            <label>Confirm Password</label>

            <div class="input-box">

                <input type="password"
                       name="password_confirmation"
                       id="password_confirmation"
                       placeholder="Confirm password"
                       required>

                <button type="button"
                        class="toggle-password"
                        onclick="togglePassword('password_confirmation', 'confirmPasswordIcon')">

                    <i class="fa-solid fa-lock"
                       id="confirmPasswordIcon"></i>

                </button>

            </div>

        </div>

        <button type="submit"
                class="save-btn">

            Save Password

        </button>

    </form>

</div>

<script>

/* =========================
   SHOW / HIDE PASSWORD
========================= */

function togglePassword(inputId, iconId){

    const input =
        document.getElementById(inputId);

    const icon =
        document.getElementById(iconId);

    if(input.type === "password"){

        input.type = "text";

        icon.classList.remove("fa-lock");

        icon.classList.add("fa-lock-open");

    }else{

        input.type = "password";

        icon.classList.remove("fa-lock-open");

        icon.classList.add("fa-lock");
    }
}

</script>

</body>
</html>