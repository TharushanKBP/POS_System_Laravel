<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Reset Password | {{ config('app.name') }}</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}">
    <!-- CoreUI CSS -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css"
        integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog=="
        crossorigin="anonymous" />
</head>

<body class="c-app flex-row align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mx-4">
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <h1>Reset Password</h1>
                            <p class="text-muted">Enter your email and a new password</p>

                            <!-- Email Input -->
                            <div class="input-group mb-3">
                                <label for="email" class="sr-only">Email</label>
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="cil-envelope-open"></i>
                                    </span>
                                </div>
                                <input type="email" id="email" class="form-control @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email', $email) }}" placeholder="Email" required autofocus>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password Input -->
                            <div class="input-group mb-3">
                                <label for="password" class="sr-only">Password</label>
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="cil-lock-locked"></i>
                                    </span>
                                </div>
                                <input type="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password"
                                    placeholder="Password" required>
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirm Password Input -->
                            <div class="input-group mb-4">
                                <label for="password_confirmation" class="sr-only">Confirm Password</label>
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="cil-lock-locked"></i>
                                    </span>
                                </div>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="form-control" placeholder="Confirm Password" required>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-block btn-primary">
                                <i class="fas fa-sync-alt"></i> Reset Password
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CoreUI JS -->
    <script src="{{ mix('js/app.js') }}" defer></script>

</body>

</html>