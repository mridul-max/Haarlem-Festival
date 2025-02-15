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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8.4.5/dist/css/swiper.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark"></nav>
    <script type="module" src="/js/nav.js"></script>

    <!-- Container -->
    <div class="container">
        <h1 class="mb-4">Order History</h1>

        <!-- Order Cards -->
        <div class="swiper order-cards">
            <div class="swiper-wrapper">
                <?php foreach ($orders as $order): ?>
                    <div class="swiper-slide order-card">
                        <div class="row">
                            <div class="col-md-8">
                                <h5>Order ID: <?= $order->getOrderId() ?></h5>
                                <p><strong>Order Date:</strong> <?= $order->getOrderDateAsDMY(); ?></p>
                                <p><strong>Total excl VAT:</strong> <?= "€ " . number_format($order->getTotalBasePrice(), 2) ?></p>
                                <p><strong>Number of Items:</strong> <?= $order->getTotalItemCount(); ?></p>
                                <p><strong>Total incl VAT:</strong> <?= "€ " . number_format($order->getTotalPrice(), 2) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>

        <!-- Festive Progress Bar -->
        <div class="festive-progress">
            <i class="fas fa-gift festive-icon"></i>
            <h3>Your Festival Journey</h3>
            <div class="progress">
                <div class="progress-bar" role="progressbar" style="width: 75%;" aria-valuenow="75" aria-valuemin="0"
                    aria-valuemax="100"></div>
            </div>
            <p class="mt-2">You're 75% through your festival experience! Keep enjoying the fun.</p>
        </div>
    </div>

    <footer class="foot row bottom"></footer>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>
    <script type="module" src="/js/foot.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@8.4.5/dist/js/swiper.min.js"></script>
    <script>
        const swiper = new Swiper('.order-cards', {
            slidesPerView: 1,
            spaceBetween: 30,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    </script>
</body>

</html>






<style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .container {
            margin-top: 2rem;
        }

        .order-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
            padding: 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .order-card h5 {
            color: #333;
            font-weight: 600;
        }

        .order-card p {
            color: #666;
            margin-bottom: 0.5rem;
        }

        .order-card .btn {
            margin-right: 0.5rem;
        }

        .festive-progress {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .festive-progress h3 {
            color: #ff6f61;
            font-weight: 700;
        }

        .progress-bar {
            background: linear-gradient(90deg, #ff6f61, #ffcc00);
            border-radius: 20px;
            height: 20px;
        }

        .festive-icon {
            color: #ff6f61;
            font-size: 2rem;
            margin-bottom: 1rem;
        }
    </style>