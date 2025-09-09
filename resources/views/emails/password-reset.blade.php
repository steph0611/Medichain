<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Your Password</h2>
    <p>Click the button below to reset your password:</p>
    <p>
        <a href="{{ $resetUrl }}" style="background:#28a745; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;">Reset Password</a>
    </p>
    <p>If the button doesn’t work, copy and paste this link into your browser:</p>
    <p>{{ $resetUrl }}</p>
</body>
</html>
