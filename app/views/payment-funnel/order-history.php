<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#fffbfa">
    <meta name="robots" content="noindex, nofollow">
    <title>Order History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/icons.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark"></nav>
    <script type="module" src="/js/nav.js"></script>

    <!-- Container -->
    <div class="container">
        <h1 class="my-5">Order History</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Total excl VAT</th>
                    <th>Number of Items</th>
                    <th>Total incl VAT</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>
                            <?= $order->getOrderId() ?>
                        </td>
                        <td>
                            <?= $order->getOrderDateAsDMY(); ?>
                        </td>
                        <td>
                            <?= "€ " . number_format($order->getTotalBasePrice(), 2) ?>
                        </td>
                        <td>
                            <?= $order->getTotalItemCount(); ?>
                        </td>
                        <td>
                            <?= "€ " . number_format($order->getTotalPrice(), 2) ?>
                        </td>
                        <td>
                            <a href="/sendTicketOfOrder?orderId=<?= $order->getOrderId() ?>" class="btn btn-primary">Send
                                Ticket</a>
                            <a href="/sendInvoiceOfOrder?orderId=<?= $order->getOrderId() ?>" class="btn btn-primary">Send
                                Invoice</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>


        <script src="/js/accountmanager.js"></script>
        <footer class="foot row bottom"></footer>
        <script type="application/javascript"
            src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
            crossorigin="anonymous"></script>

        <script type="module" src="/js/foot.js"></script>
</body>

</html>