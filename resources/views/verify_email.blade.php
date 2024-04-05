<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification Confirmation</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            background-color: #000;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 40px;
            text-align: center;
        }

        h1 {
            font-size: 36px;
            margin-bottom: 20px;
        }

        p {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Email Verification Confirmation</h1>
        <p>Congratulations! Your email verification is successful.</p>
        <p>You can now fully access our platform.</p>
        <a href="{{ config('app.frontend_url') . '/login' }}" class="button">Start Exploring</a>
    </div>
</body>

</html>