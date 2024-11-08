<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    background-color: #f0f2f5;
}

.login-container {
    width: 100%;
    max-width: 400px;
    background-color: #fff;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    text-align: center;
}

.login-container h2 {
    margin-bottom: 1rem;
    font-size: 1.5rem;
    color: #333;
}

.login-container label {
    display: block;
    text-align: left;
    margin-bottom: 0.5rem;
    font-weight: bold;
    color: #333;
}

.login-container input {
    width: 100%;
    padding: 0.5rem;
    margin-bottom: 1rem;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.login-container button {
    width: 100%;
    padding: 0.75rem;
    background-color: #007bff;
    border: none;
    border-radius: 4px;
    color: #fff;
    font-size: 1rem;
    cursor: pointer;
}

.login-container button:hover {
    background-color: #0056b3;
}

.signup-link {
    margin-top: 1rem;
    font-size: 0.9rem;
    color: #555;
}

.signup-link a {
    color: #007bff;
    text-decoration: none;
}

.signup-link a:hover {
    text-decoration: underline;
}
</style>
<body>
<div class="login-container">
        <h2>Login</h2>

        @if(session()->has('success')) 
        <div class="alert alert-success alert-dismissable fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"
            aria-label="Close"></button>
        </div>
        @endif

        @if(session()->has('success')) 
        <div class="alert alert-danger alert-dismissable fade show" role="alert">
            {{ session('loginError') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"
            aria-label="Close"></button>
        </div>
        @endif

        <form action="/login" method="post">
            @csrf
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" 
            placeholder="name@example.com" autofocus required value="{{ old('email') }}">
            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Password" required>
            
            <button type="submit">Login</button>
            <p class="signup-link">Don't have an account? <a href="#">Sign up</a></p>
        </form>
    </div>
</body>
</html>