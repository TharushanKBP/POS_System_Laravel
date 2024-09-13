<!DOCTYPE html>
<html>

<style>
    body {
        font-family: Arial, sans-serif;
    }

    h1 {
        color: #333;
    }
</style>


<head>
    <title>Your Reset Password</title>
</head>

<body>
    <h1>Hello, {{ $data['name'] }}</h1>
    <p>This is an example email from {{ $data['sender'] }}.</p>
    <p>Thank you for choosing us!</p>
</body>

</html>