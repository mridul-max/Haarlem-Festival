<?php require("../Config.php"); ?>
<html>

<head>
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 16px;
            line-height: 1.5;
            color: #333333;
            background-color: #f7f7f7;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            font-weight: bold;
            margin-top: 0;
            margin-bottom: 20px;
        }

        p {
            margin-top: 0;
            margin-bottom: 20px;
        }

        a.button {
            color: #ffffff;
            text-decoration: none;
            background-color: #007bff;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
        }

        a.button:hover {
            background-color: #0069d9;
        }

        .email {
            color: #333333;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Reset Your Password</h1>
        <p>Dear <?= $user->getFirstName() ?>,</p>
        <p>We received a request to reset your password for The Festival website. If you did not initiate this request, please disregard this message.</p>
        <p>If you did request a password reset, please click the link below to reset your password. This link will expire in 24 hours, so please act promptly.</p>
    </div>
    <div class="container" style="margin-top: 20px;">
        <p>For security reasons, please do not share this link with anyone else. If you have any questions or concerns, please contact our support team at <span class="email">team@haarlem.kfigura.nl</span></p>
        <a href="<?= $hostname ?>/updatePassword?token=<?= $reset_token ?>&email=<?= $email ?>" class="button">Reset Your Password</a>
    </div>
    <div class="container" style="margin-top: 20px;">
        <p>Thank you,<br>The Festival Website Team</p>
    </div>
</body>

</html>