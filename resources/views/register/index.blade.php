<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <title>Registration Page</title>
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

.register-container {
    width: 100%;
    max-width: 400px;
    background-color: #fff;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    text-align: center;
}

.register-container h2 {
    margin-bottom: 1rem;
    font-size: 1.5rem;
    color: #333;
}

.register-container label {
    display: block;
    text-align: left;
    margin-bottom: 0.5rem;
    font-weight: bold;
    color: #333;
}

.register-container input {
    width: 100%;
    padding: 0.5rem;
    margin-bottom: 1rem;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.register-container button {
    width: 100%;
    padding: 0.75rem;
    background-color: #007bff;
    border: none;
    border-radius: 4px;
    color: #fff;
    font-size: 1rem;
    cursor: pointer;
}

.register-container button:hover {
    background-color: #0056b3;
}

.login-link {
    margin-top: 1rem;
    font-size: 0.9rem;
    color: #555;
}

.login-link a {
    color: #007bff;
    text-decoration: none;
}

.login-link a:hover {
    text-decoration: underline;
}
</style>
<body>
    <div class="register-container">
        <h2>Register</h2>
        <form action="/register" method="post">
            @csrf
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" class="@error('name')is-invalid @enderror" 
            placeholder="Name" required value="{{ old('nama') }}">
            
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="@error('email')is-invalid @enderror" 
            placeholder="Email" required value="{{ old('email') }}">
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="@error('password')is-invalid @enderror" 
            placeholder="Password" required>
            
            <button type="submit">Register</button>
            <p class="login-link">Already have an account? <a href="#">Login</a></p>
        </form>
    </div>
</body>
</html>
