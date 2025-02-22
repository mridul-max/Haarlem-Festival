<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#fffbfa">
    <meta name="robots" content="noindex, nofollow">
    <title>Haarlem Festival - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/icons.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark"></nav>
    <script type="module" src="/js/nav.js"></script>

    <!-- Login form -->
    <div class="container py-5 h-100">
        <div class="row p-6 my-4">
            <div class="col-md-6 offset-md-3">
                <h2 class="text-center">Log in to Haarlem Festival</h2>

                <!--Pop-up message-->
                <div id="popup" class="alert alert-danger" role="alert" style="display: none;"></div>

                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <button type="submit" id="loginButton" class="btn btn-primary">
                            <span id="loginText">Login</span>
                            <span id="loginSpinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                        </button>
                    </div>
                    <div class="card-footer" style="width: 100%">
                        <a href="/provideEmail">Forgot password?</a> <br>
                        <p class="mb-1">Don't have an account yet? <a href="/home/register">Register here.</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="foot row bottom"></footer>
    <script type="application/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

    <!-- Include login.js at the end of the body -->
    <script src="/js/login.js"></script>
    <script type="module" src="/js/foot.js"></script>
</body>

</html>
























































































<style>
        body {
            font-family: 'Arial', sans-serif;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            width: 100%;
            max-width: 500px; 
            margin: 0 auto; 
            padding: 2rem; 
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }
        .btn-primary {
            background: linear-gradient(135deg, #ff6f61, #ff3b2f);
            border: none;
            transition: transform 0.3s ease;
        }
        .btn-primary:hover {
            transform: scale(1.05);
        }
        .form-control {
            border-radius: 10px;
            border: 1px solid #ddd;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-control:focus {
            border-color: #ff6f61;
            box-shadow: 0 0 5px rgba(255, 111, 97, 0.5);
        }
        .card-footer a {
            color: #ff6f61;
        }
        .card-footer a:hover {
            color: #ff3b2f;
        }
        .spinner-border {
            vertical-align: middle;
            margin-left: 5px;
        }
    </style>