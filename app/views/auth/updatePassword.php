<!doctype html>
<html lang="en">

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
  <div class="container">
    <h1 class="text-center">Password Reset</h1>
    <p class="text-center">Please enter your new password below</p>
    <!-- <form class="row g-3 needs-validation" method="post" action="" novalidate> -->
      <div class="col-md-12">
        <label for="new-password" class="form-label">New Password *</label>
        <input type="password" class="form-control" id="new-password" name="newPassword" autocomplete="off" required>
        <div class="invalid-feedback">Please enter your new password.</div>
      </div>
      <div class="col-md-12">
        <label for="confirm-password" class="form-label">Confirm New Password *</label>
        <input type="password" class="form-control" id="confirm-password" name="confirmPassword" autocomplete="off"
          required>
        <div class="invalid-feedback">Please confirm your new password.</div>
      </div>
      <div class="col-md-12">
        <button type="submit" class="btn btn-primary" name="resetPasswordButton"
          onclick="updatePassword()">Submit</button>
      </div>
    <!-- </form> -->
  </div>


  <script src="../js/updatePassword.js"></script>

  <footer class="foot row bottom"></footer>
  <script type="application/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
    crossorigin="anonymous"></script>

  <script type="module" src="/js/foot.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.min.js"></script>

</body>

</html>