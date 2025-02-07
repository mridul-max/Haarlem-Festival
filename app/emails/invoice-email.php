<!--Author: Vedat-->
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Your invoice for the Haarlem Festival
        
    </title>
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
    <p>Dear
        <?= $order->getCustomer()->getFirstName() ?>
        <?= $order->getCustomer()->getLastName() ?>,
    </p>
    <p>Thank you for your purchasing tickets for the Haarlem Festival. We're excited to have you join us for this year's event</p>

    <br>

    <p>Please find attached a PDF of your invoice for your order.
    </p>

    <p>Kind regards,</p>
    <p>The Festival Team</p>
</body>

</html>