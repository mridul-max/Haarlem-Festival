<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#fffbfa">
    <meta name="robots" content="noindex, nofollow">
    <title>Visit Haarlem Cart</title>
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
                    <?php if (!$shareMode) { ?>
                        <button class="btn btn-secondary float-end my-2 <?php if (!$isLoggedIn) echo "disabled"; ?> " style="width: 9em;" onclick="showOrderHistory()">
                            My Order History
                        </button>
                    <?php } ?>
                    <br>
                    <h2 class="mb-5 mt-5">Shopping Cart <?= $shareMode ? "(Shared)" : "" ?></h2>
                    <!--Pop-up message-->
                    <div id="popup" class="alert d-none"></div>

                    <div class="row">

                        <!-- Cart disclaimer in case there's nothing -->

                        <?php
                        if (!$isCustomerOrVisitor && !$shareMode) {
                            echo "<h4>As an admin or employee, you are not allowed to purchase things.</h4>";
                        } elseif (!$hasStuffInCart && !$shareMode) {
                            echo "<h4>Your cart is empty. Go buy some stuff!</h4>";
                        } else { ?>

                            <!-- Cart items list -->

                            <div class="col-7">
                                <?php
                                $orderItems = $cartOrder->getOrderItems();
                                foreach ($orderItems as $orderItem) {
                                    $id = $orderItem->getTicketLinkId(); ?>
                                    <div id="cart-item-<?= $id ?>" class="card p-3 m-3" style="width: 100%">
                                        <div class="card-header" style="width: 100%">
                                            <h5 class="card-title">
                                                <?= $orderItem->getEventName() ?> -
                                                <?= $orderItem->getTicketName() ?>
                                            </h5>
                                        </div>
                                        <div class="card-body" style="width: 100%">
                                            <h6 class="card-subtitle">
                                                Price per ticket: <strong>&euro; <?= number_format($orderItem->getFullTicketPrice(), 2, '.'); ?></strong>
                                                (&euro; <?= number_format($orderItem->getBasePrice(), 2, '.'); ?>
                                                + &euro; <?= number_format($orderItem->getVatAmount(), 2, '.'); ?> VAT)
                                            </h6>
                                        </div>
                                        <br>
                                        <div style="width: 100%">
                                            <?php if (!$shareMode) { ?>
                                                <button id="cart-item-remove-<?= $id ?>" class="btn btn-light" style="width: 10%">-</button>
                                            <?php } ?>
                                            <span id="cart-item-counter-<?= $id ?>" class="fw-bold mx-2">
                                                <?= $orderItem->getQuantity() ?>
                                            </span>
                                            <?php if (!$shareMode) { ?>
                                                <button id="cart-item-add-<?= $id ?>" class="btn btn-light" style="width: 10%">+</button>
                                            <?php } ?>
                                            <?php if (!$shareMode) { ?>
                                                <button id="order-item-delete-<?= $id ?>" class="btn btn-danger ms-5" style="width: 6em;">DELETE</button>
                                            <?php } ?>
                                            <span id="cart-item-unit-price-<?= $id ?>" class=" d-none float-end">
                                                <?= $orderItem->getFullTicketPrice() ?>
                                            </span>
                                            <span id="cart-item-total-price-<?= $id ?>" class="price float-end">
                                                &euro; <?= number_format($orderItem->getTotalFullPrice(), 2, '.'); ?>
                                            </span>
                                        </div>
                                        <br>
                                    </div>
                                <?php } ?>
                            </div>

                            <!-- Buttons on the right-->

                            <?php if (!$shareMode) { ?>
                                <div class="col-5 d-grid">

                                    <input type="hidden" id="share-id" value="<?= $cartOrder->getOrderId(); ?>">
                                    <div class="input-group">
                                        <input type=text" class="form-control" readonly id="share-url-text">
                                        <button class="btn btn-primary" onclick="shareMyCart()">Share</button>
                                    </div>
                                </div>
                        <?php }
                        } ?>
                    </div>
                    <br>
                    <br>

                    <!-- Checkout section-->

                    <?php if ($hasStuffInCart) {
                    ?>
                        <h4 id="total">Total price: &euro;
                            <?= number_format($cartOrder->getTotalPrice(), 2, '.'); ?>
                        </h4>
                        <?php if (!$shareMode) { ?>
                            <button class="btn btn-primary <?php if (!$isLoggedIn) echo "disabled"; ?> " onclick="checkout()">Check out</button>
                            <br>
                            <div> <?php if (!$isLoggedIn) echo "Log in to check out your cart."; ?> </div>
                            <br>
                            <h5>Payment method:</h5>
                            <div class="form-check">
                                <input class="form-check-input <?php if (!$isLoggedIn) echo "disabled"; ?> " type="radio" name="paymentMethodRadios" id="method-ideal" value="method-ideal">
                                <label class="form-check-label" for="method-ideal">
                                    iDEAL
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input <?php if (!$isLoggedIn) echo "disabled"; ?> " type="radio" name="paymentMethodRadios" id="method-card" value="method-card">
                                <label class="form-check-label" for="method-card">
                                    Credit Card
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input <?php if (!$isLoggedIn) echo "disabled"; ?> " type="radio" name="paymentMethodRadios" id="method-klarna" value="method-klarna">
                                <label class="form-check-label" for="method-klarna">
                                    Klarna (Pay later)
                                </label>
                            </div>

                        <?php } else { ?>
                            <p>This cart has been shared with you.</p>
                        <?php } ?>
                    <?php
                    }
                    ?>
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