<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verification Required</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-md rounded-lg p-8 max-w-md text-center">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Please Verify Your Email</h2>
        <p class="text-gray-600 mb-6">
            We’ve sent a verification link to your email address.  
            Please check your inbox and click the link to activate your account.
        </p>
        <a href="https://mail.google.com/mail/u/0/#inbox" target="_blank" 
           class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            Open Gmail Inbox
        </a>
        <p class="text-sm text-gray-500 mt-6">
            Didn’t receive the email? Check your spam folder or 
            <a href="/registerC" class="text-blue-500 hover:underline">try registering again</a>.
        </p>
    </div>
</body>
</html>
