<?php 
// Add this at the top of your view file
$cart = $cart ?? null;
$isLoggedIn = $isLoggedIn ?? false;
$hasItems = $cart && !empty($cart->getCartItems());
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#fffbfa">
    <meta name="robots" content="noindex, nofollow">
    <title>Haarlem Festival Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/icons.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark"></nav>
    <script type="module" src="/js/nav.js"></script>

    <!-- Container -->
    <section class="h-100 h-custom">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-10">
                    <button class="btn btn-secondary float-end my-2 <?php if (!$isLoggedIn) echo "disabled"; ?>" style="width: 9em;" onclick="showOrderHistory()">
                        My Order History
                    </button>

                    <!-- Progress Bar -->
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: 50%;"></div>
                    </div>

                    <!-- Cart Items -->
                    <div class="timeline">
                        <?php if ($isLoggedIn && $hasItems) {
                            foreach ($cart->getCartItems() as $cartItem) {
                                $id = $cartItem->getTicketLinkId(); var_dump($id); ?>
                                <div class="timeline-item">
                                    <div id="cart-item-<?= $id ?>" class="card p-3">
                                        <div class="card-header">
                                            <h5 class="card-title">
                                                <?= $cartItem->getEventName() ?> -
                                                <?= $cartItem->getTicketName() ?>
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <h6 class="card-subtitle mb-2 text-muted">
                                                Price per ticket: <strong>&euro; <?= number_format($cartItem->getFullTicketPrice(), 2, '.'); ?></strong>
                                                (&euro; <?= number_format($cartItem->getBasePrice(), 2, '.'); ?>
                                                + &euro; <?= number_format($cartItem->getVatAmount(), 2, '.'); ?> VAT)
                                            </h6>
                                            <div class="quantity-control mt-3">
                                                <?php if ($isLoggedIn) { ?>
                                                    <button id="cart-item-remove-<?= $id ?>" class="btn btn-danger">-</button>
                                                <?php } ?>
                                                <span id="cart-item-counter-<?= $id ?>" class="fw-bold mx-2">
                                                    <?= $cartItem->getQuantity() ?>
                                                </span>
                                                <?php if ($isLoggedIn) { ?>
                                                    <button id="cart-item-add-<?= $id ?>" class="btn btn-success">+</button>
                                                <?php } ?>
                                                <?php if ($isLoggedIn) { ?>
                                                    <button id="cart-item-delete-<?= $id ?>" class="btn btn-danger ms-3">DELETE</button>
                                                <?php } ?>
                                                <span id="cart-item-unit-price-<?= $id ?>" class="d-none">
                                                    <?= $cartItem->getFullTicketPrice() ?>
                                                </span>
                                                <span id="cart-item-total-price-<?= $id ?>" class="price ms-auto">
                                                    &euro; <?= number_format($cartItem->getTotalFullPrice(), 2, '.'); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                        } else { ?>
                            <div class="alert alert-info" role="alert">
                                <?= $isLoggedIn ? 'Your cart is empty.' : 'Please log in to view your cart and purchase tickets for the Haarlem Festival.' ?>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- Checkout Section -->
                    <?php if ($isLoggedIn && $hasItems) { ?>
                        <h4 id="total" class="total-price">Total price: &euro;
                            <?= number_format($cart->getTotalPrice(), 2, '.'); ?>
                        </h4>
                        <button class="btn btn-primary checkout-btn" onclick="checkout()">Check out</button>
                        <br>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>

    <footer class="foot row bottom"></footer>
    <script type="application/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="/js/cartcontroller.js"></script>
    <script type="module" src="/js/foot.js"></script>
</body>

</html>