<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#fffbfa">
    <meta name="robots" content="noindex, nofollow">
    <title>Visit Haarlem - Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/icons.css">
</head>

<body onload="">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark"></nav>
    <script type="module" src="/js/nav.js"></script>

    <!-- Account management container -->
    <section class="h-100 h-custom">
        <div class="container py-5 h-100">   
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-10">
                    <h2 class="m-5">Manage your Account</h2>

                    <!--Pop-up message-->
                    <div id="popup">
                    </div>
                    
                    <!-- Account management form -->
                    <div class="card card-registration card-registration-2" style="border-radius: 15px;">
                        <div class="card-body p-0">
                            <div class="row g-0">
                                <div class="col-lg-6">
                                    <div class="p-5">

                                        <h3 class="fw-normal mb-5">Personal Information</h3>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-4 pb-2">
                                                <div class="form-outline">
                                                    <label class="form-label" for="firstName">First Name*</label>    
                                                    <input type="text" id="firstName" class="form-control form-control-lg" value=<?php echo $user->getFirstName()?> onchange=enableSaveChanges() /> 
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-4 pb-2">
                                                <div class="form-outline">
                                                    <label class="form-label" for="lastName">Last Name*</label>
                                                    <input type="text" id="lastName" class="form-control form-control-lg" value=<?php echo $user->getLastName()?> onchange=enableSaveChanges() />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="mb-4 pb-2">
                                                <div class="form-outline">
                                                    <label class="form-label" for="phoneNumber">Phone Number*</label>
                                                    <input type="text" id="phoneNumber" class="form-control form-control-lg" value=<?php echo $user->getPhoneNumber()?> onchange=enableSaveChanges() />
                                                </div>
                                                <div class="form-outline">
                                                    <label class="form-label" for="dateOfBirth">Date of Birth*</label>
                                                    <input type="date" id="dateOfBirth" class="form-control form-control-lg" value=<?php echo $user->getDateOfBirthAsString()?> onchange=enableSaveChanges() /> 
                                                </div>
                                                
                                            </div>
                                        </div>

                                        <div class="row">
                                        <h3 class="fw-normal mb-5">Account Information</h3>
                                        </div>

                                        <div class="row">
                                            <div class="form-outline">
                                                <label class="form-label" for="email">E-mail*</label>
                                                <input type="email" id="email" class="form-control form-control-lg" value=<?php echo $user->getEmail()?> onchange=enableSaveChanges() />
                                            </div>
                                            <div class="form-outline">
                                                <label class="form-label" for="password">New password</label>
                                                <input type="password" id="password" class="form-control form-control-lg" />
                                            </div>
                                            <div class="form-outline">
                                                <label class="form-label" for="passwordConfirm">Enter current password to confirm</label>
                                                <input type="password" id="passwordConfirm" class="form-control form-control-lg" onchange=enableSaveChanges() />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="p-5">
                                
                                        <h3 class="fw-normal mb-5">Address Details</h3>

                                        <div class="row">
                                            <div class="col-md-6 mb-4 pb-2">
                                                <div class="form-outline form-white pb-2">
                                                    <label class="form-label" for="streetName">Street Name*</label>
                                                    <input type="text" id="streetName" class="form-control form-control-lg" value=<?php echo $user->getAddress()->getStreetName()?> onchange=enableSaveChanges() />
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-4 pb-2">
                                                <div class="form-outline form-white">
                                                    <label class="form-label" for="houseNumber">House Nr*</label>
                                                    <input type="text" id="houseNumber" class="form-control form-control-lg" value=<?php echo $user->getAddress()->getHouseNumber()?> onchange=enableSaveChanges() />
                                                </div>
                                            </div>
                                        </div>
    

                                        <div class="row">
                                            <div class="col-md-6 mb-4 pb-2">
                                                <div class="form-outline form-white">
                                                    <label class="form-label" for="postalCode">Postal Code*</label>
                                                    <input type="text" id="postalCode" class="form-control form-control-lg" value=<?php echo $user->getAddress()->getPostalCode()?> onchange=enableSaveChanges() />
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-4 pb-2">
                                                <div class="form-outline form-white pb-2">
                                                    <label class="form-label" for="city">Place / City*</label>
                                                    <input type="text" id="city" class="form-control form-control-lg" value=<?php echo $user->getAddress()->getCity()?> onchange=enableSaveChanges() />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            
                                            <div class="col-md-6 mb-4 pb-2">
                                                <div class="form-outline form-white pb-2">
                                                    <label class="form-label" for="country">Country*</label>
                                                    <input type="text" id="country" class="form-control form-control-lg" value=<?php echo $user->getAddress()->getCountry()?> onchange=enableSaveChanges() />
                                                </div>
                                            </div>
                                        </div>                                         

                                        <div id="errorBox" class="p-2">
                                            <ul id="errorList">

                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <button id="saveChangesButton" type="submit" class="btn btn-primary" disabled onclick=updateAccount()>Save Changes</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class=col-2>
                    <button class="btn btn-lg btn-light" onclick=logout()>Log out</button>
                </div>
            </div>
        </div>
    </section>
    
    <script src="/js/accountmanager.js"></script>
    <footer class="foot row bottom"></footer>
    <script type="application/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

    <script type="module" src="/js/foot.js"></script>
</body>

</html>