<!DOCTYPE html>
<html>
    <head>
        <title>Changes to Account</title>
        <style type="text/css">
        /* Add some styles for the email body */
        body {
            font-family: Arial, sans-serif;
            font-size: 16px;
            line-height: 1.5;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        h1 {
            font-size: 24px;
            font-weight: bold;
            color: #003399;
            margin: 0;
            padding: 0;
        }
        p {
            margin: 0 0 10px 0;
            padding: 0;
        }
    </style>
    </head>
    <body>
        <h2>
            Account changes
        </h2>
        <p>
            Dear <?php echo $customer->getFullName(); ?>,
        </p>
        <p>
            Changes have been made to your account.
            <br>
            If you did not make these changes, contact us immediately.
            <br>
            Use your Account number when you contact support: <?php echo $customer->getUserId(); ?>
        </p>
        <p>
            Kind regards,
            <br>
            The Festival Team
        </p>
    </body>
</html>