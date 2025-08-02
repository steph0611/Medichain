<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verify Your Email</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; margin:0; padding:20px;">
    <div style="max-width:600px; margin:0 auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
        <h2 style="color:#333;">Welcome!</h2>
        <p style="font-size:16px; color:#555;">
            Thank you for registering. Please verify your email address by clicking the button below:
        </p>

        <p style="text-align:center; margin:30px 0;">
            <a href="{{ $verifyUrl }}" style="background:#28a745; color:#fff; padding:12px 25px; text-decoration:none; font-weight:bold; border-radius:5px; display:inline-block;">
                Verify Email
            </a>
        </p>

        <p style="font-size:14px; color:#777;">
            If the button doesn’t work, copy and paste this link into your browser:<br>
            <a href="{{ $verifyUrl }}" style="color:#28a745;">{{ $verifyUrl }}</a>
        </p>
    </div>
</body>
</html>
