<!DOCTYPE html>
<html data-bs-theme="light" lang="en-US" dir="ltr">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>SSO | Login</title>
    <!-- ===============================================-->
    <!--    Favicons-->
    <!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('static/assets/img/favicon.png')?>" />
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('static/assets/img/favicon.png')?>" />
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('static/assets/img/favicon.png')?>" />
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url('static/assets/img/favicons/favicon.ico')?>" />
    <link rel="manifest" href="<?= base_url('static/assets/img/favicons/manifest.json')?>" />
    <meta name="msapplication-TileImage" content="<?= base_url('static/assets/img/favicons/mstile-150x150.png')?>" />
    <meta name="theme-color" content="#ffffff" />
    <script src="<?= base_url('static/assets/js/config.js')?>"></script>
    <script src="vendors/simplebar/simplebar.min.js"></script>

    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap"
        rel="stylesheet" />
    <link href="<?= base_url('vendors/simplebar/simplebar.min.css')?>" rel="stylesheet" />
    <link href="<?= base_url('static/assets/css/theme-rtl.min.css')?>" rel="stylesheet" id="style-rtl" />
    <link href="<?= base_url('static/assets/css/theme.min.css')?>" rel="stylesheet" id="style-default" />
    <link href="<?= base_url('static/assets/css/user-rtl.min.css')?>" rel="stylesheet" id="user-style-rtl" />
    <link href="<?= base_url('static/assets/css/user.min.css')?>" rel="stylesheet" id="user-style-default" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script>
    var isRTL = JSON.parse(localStorage.getItem("isRTL"));
    if (isRTL) {
        var linkDefault = document.getElementById("style-default");
        var userLinkDefault = document.getElementById("user-style-default");
        linkDefault.setAttribute("disabled", true);
        userLinkDefault.setAttribute("disabled", true);
        document.querySelector("html").setAttribute("dir", "rtl");
    } else {
        var linkRTL = document.getElementById("style-rtl");
        var userLinkRTL = document.getElementById("user-style-rtl");
        linkRTL.setAttribute("disabled", true);
        userLinkRTL.setAttribute("disabled", true);
    }
    </script>
</head>

<body>
    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->

    <main class="main" id="top">
        <div class="container-fluid">
            <div class="row min-vh-100 flex-center g-0">
                <div class="col-lg-8 col-xxl-5 py-3 position-relative"><img class="bg-auth-circle-shape"
                        src="<?=base_url()?>/static/assets/img/bg-shape.png" alt="" width="250"><img
                        class="bg-auth-circle-shape-2" src="<?=base_url()?>/static/assets/img/shape-1.png" alt=""
                        width="150">

                    <?php if(@session()->getFlashdata('success')){ ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        <strong>Success!</strong> <?=session()->getFlashdata('success')?>
                    </div>
                    <?php } ?>

                    <?php if(@session()->getFlashdata('error')){ ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        <strong>Error!</strong> <?=session()->getFlashdata('error')?>
                    </div>
                    <?php } ?>
                    <div class="card overflow-hidden z-1">
                        <div class="card-body p-0">
                            <div class="row g-0 h-100">
                                <div class="col-md-5 text-center bg-card-gradient">
                                    <div class="position-relative p-4 pt-md-5 pb-md-7" data-bs-theme="light">
                                        <div class="bg-holder bg-auth-card-shape"
                                            style="background-image:url(<?=base_url()?>/static/assets/img/half-circle.png);">
                                        </div>
                                        <!--/.bg-holder-->
                                        <div class="z-1 position-relative"><a
                                                class="link-light mb-4 font-sans-serif fs-5 d-inline-block fw-bolder"
                                                href="<?=base_url()?>">MEI</a>
                                            <p class="opacity-75 text-white">With the power of Falcon, you can now focus
                                                only on functionaries for your digital products, while leaving the UI
                                                design on us!</p>
                                        </div>
                                    </div>
                                    <div class="mt-3 mb-4 mt-md-4 mb-md-5" data-bs-theme="light">
                                        <p class="mb-0 mt-4 mt-md-5 fs-10 fw-semi-bold text-white opacity-75">Read our
                                            <a class="text-decoration-underline text-white" href="#">terms</a> and <a
                                                class="text-decoration-underline text-white" href="#">conditions </a>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-7 d-flex flex-center">
                                    <div class="p-4 p-md-5 flex-grow-1">
                                        <div class="position-relative mt-4">
                                            <hr>
                                            <div class="divider-content-center">or login through</div>
                                        </div>
                                        <div class="row g-2 mt-2">
                                            <div class="col-sm-12"><a
                                                    class="btn btn-md btn-outline-google-plus btn-md d-block w-100"
                                                    href="https://dev-44uhudulet0f7kxq.us.auth0.com/samlp/hmIaenviRl0HPyVkpparSVEyYlEoSP1z">SSO</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>



    <!-- ===============================================-->
    <!--    End of Main Content-->
    <!-- ===============================================-->

    <script>
    $(document).ready(function() {
        $("#userForm").submit(function(e) {
            e.preventDefault();
            var email = $("#username").val().trim();
            var password = $("#password").val().trim();
            var form = $("#userForm");
            if (email && password) {
                $.ajax({
                    method: 'post',
                    url: '<?=base_url('login')?>',
                    data: {
                        username: $.trim($("#username").val()),
                        password: $.trim($("#password").val()),
                    },
                    beforeSend: function(e) {
                        $("#submit span").addClass(
                            "spinner-grow text-light spinner-grow-sm"
                        );
                    },
                    success: function(res) {
                        //console.log(res); return false;
                        var json = JSON.parse(res);

                        setTimeout(function() {
                            $("#submit span").removeClass(
                                "spinner-grow text-light spinner-grow-sm"
                            );
                            window.location = window.location;
                        }, 200);
                    },
                    error: function(res) {
                        console.log(res);
                    },
                });
            }
        });
    });

    $(document).ready(function() {
        $("#signupForm").submit(function(e) {
            e.preventDefault();
            var username = $("#username").val().trim();
            var email = $("#email").val().trim();
            var password = $("#password").val().trim();
            var form = $("#signupForm");
            if (email === undefined || email === null || email === "") {
                $("#signup span").addClass(
                    "spinner-grow text-light spinner-grow-sm"
                );
                setTimeout(function() {
                    $("#signup span").removeClass(
                        "spinner-grow text-light spinner-grow-sm"
                    );
                    $(".is_msg")
                        .html("Email or username is missing..")
                        .addClass("text-danger text-center");
                }, 500);
            } else if (
                password === undefined ||
                password === null ||
                password === ""
            ) {
                $("#signup span").addClass(
                    "spinner-grow text-light spinner-grow-sm"
                );
                setTimeout(function() {
                    $("#signup span").removeClass(
                        "spinner-grow text-light spinner-grow-sm"
                    );
                    $(".is_msg")
                        .html("Password is missing..")
                        .addClass("text-danger text-center");
                }, 500);
            } else {
                $.ajax({
                    method: form.attr("method"),
                    url: form.attr("action"),
                    //contentType: "application/json",
                    //dataType: "json",
                    data: {
                        username: $.trim($("#username").val()),
                        email: $.trim($("#email").val()),
                        password: $.trim($("#password").val()),
                    },
                    beforeSend: function(e) {
                        $("#signup span").addClass(
                            "spinner-grow text-light spinner-grow-sm"
                        );
                    },
                    success: function(res) {
                        console.log(res); //alert(res); return false;
                        if (res != "is_true") {
                            setTimeout(function() {
                                $("#signup span").removeClass(
                                    "spinner-grow text-light spinner-grow-sm"
                                );
                                $(".is_msg").html(res).addClass(
                                    "text-danger text-center");
                            }, 500);
                        } else {
                            $(".is_msg")
                                .html("Successfully LoggedIn...")
                                .removeClass("text-danger")
                                .addClass("text-success");
                            setTimeout(function() {
                                $("#signup span").removeClass(
                                    "spinner-grow text-light spinner-grow-sm"
                                );
                                window.location = window.location;
                            }, 1000);
                        }
                    },
                    error: function(res) {
                        console.log(res);
                    },
                });
            }
        });
    });

    $(document).ready(function() {
        $(document).keypress(function(e) {
            // Check if the pressed key is Enter (keyCode 13)
            if (e.which === 13) {
                // Trigger the click event on the button with the ID 'view_salary'
                $("#submit").click();
            }
        });
    });
    </script>
</body>

</html>