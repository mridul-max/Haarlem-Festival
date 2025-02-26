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
    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark"></nav>
    <script type="module" src="/js/nav.js"></script>

    <!-- Container -->
    <div class="container">
        <h1 class="mb-4">Order History</h1>

        <!-- Check if there are any orders -->
<!-- Check if there are any orders -->
<?php 
$hasOrdersWithItems = false; // Track if at least one order has items
?>

<?php if (!empty($orders)): ?>
    <div class="swiper order-cards">
        <div class="swiper-wrapper">
            <?php foreach ($orders as $order): ?>
                <?php if (!empty($order->getOrderItems())): // Only show orders that have items ?>
                    <?php $hasOrdersWithItems = true; // Set flag to true ?>
                    <div class="swiper-slide order-card" 
                        data-order-id="<?= $order->getOrderId() ?>"
                        data-items='<?= json_encode($order->getOrderItems()) ?>'>
                        <div class="row">
                            <div class="col-md-8">
                                <h5>Order ID: <?= $order->getOrderId() ?></h5>
                                <p><strong>Order Date:</strong> <?= $order->getOrderDateAsDMY(); ?></p>
                                <p><strong>Total:</strong> <?= "€ " . number_format($order->getTotalPrice(), 2) ?></p>
                                <button class="btn btn-sm btn-outline-primary view-details">View Tickets</button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>

    <!-- Show no orders message if all orders were empty -->
    <?php if (!$hasOrdersWithItems): ?>
        <div class="alert alert-warning text-center w-100">
            <i class="fas fa-info-circle"></i> You have no orders yet. 
            <br> <a href="/festival/jazz" class="btn btn-primary mt-2">Browse Events</a>
        </div>
    <?php endif; ?>

<?php else: ?>
    <div class="alert alert-warning text-center w-100">
        <i class="fas fa-info-circle"></i> You have no orders yet. 
        <br> <a href="/festival/jazz" class="btn btn-primary mt-2">Browse Events</a>
    </div>
<?php endif; ?>


        <!-- Order Details Modal -->
        <div class="modal fade" id="orderDetailsModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Order Details - #<span id="modalOrderId"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="orderItemsContainer">
                        <!-- Ticket details will be inserted here -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Festive Progress Bar -->
        <div class="festive-progress mt-5">
            <i class="fas fa-gift festive-icon"></i>
            <h3>Your Festival Journey</h3>
            <div class="progress">
                <div class="progress-bar" role="progressbar" style="width: 75%;" 
                    aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <p class="mt-2">You're 75% through your festival experience! Keep enjoying the fun.</p>
        </div>
    </div>

    <footer class="foot row bottom"></footer>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>
    <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
    <script>
        // Initialize Swiper
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
            breakpoints: {
                768: {
                    slidesPerView: 2
                },
                992: {
                    slidesPerView: 3
                }
            }
        });

        // Handle order details click
        document.querySelectorAll('.order-card').forEach(card => {
            card.addEventListener('click', (e) => {
                if (!e.target.classList.contains('view-details')) return;
                
                const orderId = card.dataset.orderId;
                const items = JSON.parse(card.dataset.items);
                
                document.getElementById('modalOrderId').textContent = orderId;
                const container = document.getElementById('orderItemsContainer');
                container.innerHTML = '';

                if (items.length === 0) {
                    container.innerHTML = `<div class="alert alert-warning">No tickets found for this order.</div>`;
                } else {
                    items.forEach(item => {
                        const itemHTML = `
                            <div class="ticket-item mb-3 p-3 border-bottom">
                                <h6>${item.eventName}</h6>
                                <div class="text-muted small">
                                    <div>Ticket Type: ${item.ticketName}</div>
                                    <div>Quantity: ${item.quantity}</div>
                                    <div>Price: €${item.fullTicketPrice.toFixed(2)} each</div>
                                </div>
                            </div>
                        `;
                        container.insertAdjacentHTML('beforeend', itemHTML);
                    });
                }

                new bootstrap.Modal(document.getElementById('orderDetailsModal')).show();
            });
        });
    </script>

    <style>
        .order-card {
            cursor: pointer;
            transition: transform 0.2s;
            min-height: 200px;
        }

        .order-card:hover {
            transform: translateY(-5px);
            background: #f8f9fa;
        }

        .ticket-item {
            background: #fff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .modal-content {
            border-radius: 15px;
        }

        .modal-header {
            background: #f8f9fa;
            border-bottom: none;
            border-radius: 15px 15px 0 0;
        }

        .progress-bar {
            background: linear-gradient(90deg, #ff6f61, #ffcc00);
            border-radius: 20px;
        }

        .festive-progress {
            background: #fff;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
</body>

</html>
