<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <meta name="theme-color" content="#fffbfa">
  <meta name="robots" content="noindex, nofollow">
  <title>Visit Haarlem - Provide Email</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <link rel="stylesheet" href="/css/main.css">
  <link rel="stylesheet" href="/css/icons.css">
</head>


<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark"></nav>
  <script type="module" src="/js/nav.js"></script>

    <h2 class="mb-4">Provide your email</h2>
    <form method="POST" action="sendEmail">
      <div class="form-group">
        <label for="emailField">Your email</label>
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="far fa-envelope"></i></span>
          </div>
          <input id="emailField" type="email" class="form-control" placeholder="Email.." required>
        </div>
      </div>
      <button id="sendEmail" type="button" class="btn btn-success" onclick="resetPassword()">Send Email</button>
    </form>
  </div>

  <script src="../js/resetpassword.js"></script>

  <footer class="foot row bottom"></footer>
  <script type="application/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
    crossorigin="anonymous"></script>

  <script type="module" src="/js/foot.js"></script>
</body>

</html>