<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name=”robots” content="index, follow">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/main_no_editor.css">
    <link rel="stylesheet" href="/css/icons.css">
    <title>Visit Haarlem - Buy Pass</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark"></nav>
    <div class="container">
        <div class="row">
            <h1 class="mx-auto text-center">Buy a pass</h1>
        </div>
        <div class="row g-2" id="master">
            <div class="card col-5 g-1 p-1">
                <div class="row">
                    <div class="col-12">
                        <h2>Event</h2>
                        <select id="event-type" class="form-select">
                            <option value="0" disabled> === Select an event === </option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h2>Pass Type</h2>
                        <select id="pass-type" class="form-select">
                        </select>
                    </div>
                </div>
            </div>
            <div class="card col-5 px-1" id="details-thing">
                <h2>Details</h2>
                <div class="row">
                    <div class="col-12">
                        <h3 class="d-inline">Day</h3>
                        <p class="d-inline" id="event-date"></p>
                    </div>
                    <div class="col-12">
                        <h3 class="d-inline">Price</h3>
                        <p id="event-price" class="price d-inline"></p>
                    </div>
                </div>
                <div class="row">
                    <button id="buy-pass" class="btn btn-primary w-50 my-2 mx-auto">Add to cart</button>
                </div>
            </div>
        </div>
    </div>
    <footer class="foot row bottom"></footer>
    <script type="application/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script type="module" src="/js/nav.js"></script>
    <script type="application/javascript" src="/js/cart.js"></script>
    <script type="module" src="/js/foot.js"></script>
    <script type="application/javascript" src="/js/textpage.js"></script>
    <script type="application/javascript" src="/js/buypass.js"></script>
</body>

</html>