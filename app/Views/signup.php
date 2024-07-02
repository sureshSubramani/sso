{% load static %}
<!DOCTYPE html>
<html data-bs-theme="light" lang="en-US" dir="ltr">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>Admin | Register</title>
    <link
      rel="apple-touch-icon"
      sizes="180x180"
      href="{% static 'assets/img/falcon.png' %}"
    />
    <link
      rel="icon"
      type="image/png"
      sizes="32x32"
      href="{% static 'assets/img/falcon.png' %}"
    />
    <link
      rel="icon"
      type="image/png"
      sizes="16x16"
      href="{% static 'assets/img/falcon.png' %}"
    />
    <link
      rel="shortcut icon"
      type="image/x-icon"
      href="{% static 'assets/img/favicons/favicon.ico' %}"
    />
    <link
      rel="manifest"
      href="{% static 'assets/img/favicons/manifest.json' %}"
    />
    <meta
      name="msapplication-TileImage"
      content="{% static 'assets/img/favicons/mstile-150x150.png' %}"
    />
    <meta name="theme-color" content="#ffffff" />
    <script src="{% static 'assets/js/config.js' %}"></script>
    <script src="vendors/simplebar/simplebar.min.js"></script>

    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link
      href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap"
      rel="stylesheet"
    />
    <link
      href="{% static 'vendors/simplebar/simplebar.min.css' %}"
      rel="stylesheet"
    />
    <link
      href="{% static 'assets/css/theme-rtl.min.css' %}"
      rel="stylesheet"
      id="style-rtl"
    />
    <link
      href="{% static 'assets/css/theme.min.css' %}"
      rel="stylesheet"
      id="style-default"
    />
    <link
      href="{% static 'assets/css/user-rtl.min.css' %}"
      rel="stylesheet"
      id="user-style-rtl"
    />
    <link
      href="{% static 'assets/css/user.min.css' %}"
      rel="stylesheet"
      id="user-style-default"
    />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
  </head>
  <body>
    <main class="main" id="top">
      <div class="container-fluid" data-layout="container">
        <div class="row flex-center min-vh-100 py-6">
          <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 col-xxl-4">
            <div class="card">
              <div class="card-body p-4 p-sm-5">
                {% if error %}
                <span class="text-danger is_message">{{ error }} </span>
                {% endif %}
                <div class="row flex-between-center mb-2">
                  <div class="col-auto">
                    <h5>Register</h5>
                  </div>
                  <div class="col-auto fs--1 text-600">
                    <span class="mb-0 undefined">or</span>
                    <span><a href="{% url 'login' %}">Login</a></span>
                  </div>
                </div>

                <form
                  role="signup"
                  method="post"
                  action="{% url 'signup' %}"
                  class="text-start"
                  id="signupForm"
                  autocomplete="off"
                >
                  {% csrf_token %}
                  <div class="mb-3">
                    <label class="form-label" for="username">UserName</label>
                    <input
                      class="form-control"
                      type="text"
                      name="username"
                      id="username"
                      placeholder="Enter Username"
                    />
                  </div>
                  <div class="mb-3">
                    <label class="form-label" for="email">Email Address</label>
                    <input
                      class="form-control"
                      type="email"
                      name="email"
                      id="email"
                      placeholder="Enter Email"
                    />
                  </div>
                  <div class="row gx-2">
                    <div class="mb-3 col-sm-6">
                      <label class="form-label" for="password">Password</label>
                      <input
                        class="form-control password"
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Enter Password"
                      />
                    </div>
                    <div class="mb-3 col-sm-6">
                      <label class="form-label" for="conpassword"
                        >Confirm Password</label
                      >
                      <input
                        class="form-control"
                        type="password"
                        name="conpassword"
                        id="conpassword"
                        placeholder="Confirm Password"
                      />
                    </div>
                  </div>
                  <div class="mb-3">
                    <span class="is_msg"></span>
                    <button
                      class="btn btn-primary d-block w-100 mt-3"
                      type="submit"
                      name="signup"
                      id="signup"
                    >
                      Register &nbsp;<span style="position: absolute"></span>
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <script>
      $(document).ready(function () {
        $("#signupForm").submit(function (e) {
          e.preventDefault();
          var username = $("#username").val().trim();
          var email = $("#email").val().trim();
          var password = $("#password").val().trim();
          var conpassword = $("#conpassword").val().trim();
          var csrftoken = jQuery("[name=csrfmiddlewaretoken]").val().trim();
          var form = $("#signupForm");
          if (username === undefined || username === null || username === "") {
            $("#signup span").addClass(
              "spinner-grow text-light spinner-grow-sm"
            );
            setTimeout(function () {
              $("#signup span").removeClass(
                "spinner-grow text-light spinner-grow-sm"
              );
              $(".is_msg")
                .html("Username is required.")
                .addClass("text-danger text-center");
            }, 500);
          } else if (email === undefined || email === null || email === "") {
            $("#signup span").addClass(
              "spinner-grow text-light spinner-grow-sm"
            );
            setTimeout(function () {
              $("#signup span").removeClass(
                "spinner-grow text-light spinner-grow-sm"
              );
              $(".is_msg")
                .html("Email is required.")
                .addClass("text-danger text-center");
            }, 500);
          } else if ( password === undefined || password === null || password === "" ) {
            $("#signup span").addClass(
              "spinner-grow text-light spinner-grow-sm"
            );
            setTimeout(function () {
              $("#signup span").removeClass(
                "spinner-grow text-light spinner-grow-sm"
              );
              $(".is_msg")
                .html("Password is required.")
                .addClass("text-danger text-center");
            }, 500);
          } else if ( conpassword === undefined || conpassword === null || conpassword === "" ) {
            $("#signup span").addClass(
              "spinner-grow text-light spinner-grow-sm"
            );
            setTimeout(function () {
              $("#signup span").removeClass(
                "spinner-grow text-light spinner-grow-sm"
              );
              $(".is_msg")
                .html("Confirm password is required.")
                .addClass("text-danger text-center");
            }, 500);
          } else {

            if (password === conpassword) {
              $.ajax({
                method: form.attr("method"),
                url: form.attr("action"),
                //contentType: "application/json",
                //dataType: "json",
                data: {
                  csrfmiddlewaretoken: csrftoken,
                  username: $.trim($("#username").val()),
                  email: $.trim($("#email").val()),
                  password: $.trim($("#password").val()),
                  conpassword: $.trim($("#conpassword").val())
                },
                beforeSend: function (e) {
                  $("#signup span").addClass(
                    "spinner-grow text-light spinner-grow-sm"
                  );
                },
                success: function (res) {
                  console.log(res);
                  //alert(res); return false;
                  if (res != "is_success") {
                    setTimeout(function () {
                      $("#signup span").removeClass(
                        "spinner-grow text-light spinner-grow-sm"
                      );
                      $(".is_msg")
                        .html(res)
                        .addClass("text-danger text-center");
                    }, 500);
                  } else {
                    $(".is_msg")
                      .html("Successfully register with us..")
                      .removeClass("text-danger")
                      .addClass("text-success");
                    setTimeout(function () {
                      $("#signup span").removeClass(
                        "spinner-grow text-light spinner-grow-sm"
                      );
                      window.location = window.location;
                    }, 1000);
                  }
                },
                error: function (res) {
                  console.log(res);
                },
              });
            }else{
              $("#signup span").addClass("spinner-grow text-light spinner-grow-sm");
            setTimeout(function () {
              $("#signup span").removeClass("spinner-grow text-light spinner-grow-sm");
              $(".is_msg").html("Password is not matched.").addClass("text-danger text-center");
            }, 500);
            }
          }
        });

        $("#signupForm").on("keypress", "input", function () {
          $("#signupForm input").each(function () {
            if ($(this).val()) $(".is_msg ").empty();
          });
        });

        $("#signupForm").on("keyup", ".password", function () {
          //alert($(this).val().trim());
          var csrftoken = jQuery("[name=csrfmiddlewaretoken]").val().trim();
          if($(this).val().trim() != ''){
            $.ajax({
              method: 'POST',
              url: "{% url 'is_password_strength' %}",
              data: {
                csrfmiddlewaretoken: csrftoken,
                password: $(this).val().trim()
              },
              beforeSend: function (e) {
                $("#signup span").addClass(
                  "spinner-grow text-light spinner-grow-sm"
                );
              },
              success: function (res) {
                console.log(res);
                setTimeout(function () {
                  $("#signup span").removeClass(
                    "spinner-grow text-light spinner-grow-sm"
                  );
                  $(".is_msg").html(res).addClass("text-danger text-center");
                }, 500);
              }
            });
          }
        });

      });

      $(document).ready(function () {
        $(document).keypress(function (e) {
          // Check if the pressed key is Enter (keyCode 13)
          if (e.which === 13) {
            // Trigger the click event on the button with the ID 'view_salary'
            $("#signup").click();
          }
        });

      });
    </script>
  </body>
</html>
