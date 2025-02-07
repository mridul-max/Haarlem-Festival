<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Visit Haarlem - Employee Ticket Scanning Result</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/icons.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark"></nav>
    <script type="module" src="/js/nav.js"></script>

    <div class="container">
        <div class="row mt-5">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Ticket Scanning Result</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-<?php echo $ticket->getIsScanned() ? 'danger' : 'success'; ?> text-center" role="alert">
                            <?php if ($ticket->getIsScanned() == 0) : ?>
                                <h4 class="alert-heading">Ticket Scanned!</h4>
                                <p>The ticket with ID
                                    <?php echo $ticket->getTicketId(); ?> has been successfully scanned.
                                </p>
                            <?php else : ?>
                                <h4 class="alert-heading">Ticket Already Scanned!</h4>
                                <p>The ticket with ID
                                    <?php echo $ticket->getTicketId(); ?> has already been scanned.
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="text-center">
                            <a href="/festival" class="btn btn-primary">Back to Festival</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="application/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

    <footer class="foot row bottom">
    </footer>
    <script type="application/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

    <script type="module" src="/js/foot.js"></script>
</body>

</html>