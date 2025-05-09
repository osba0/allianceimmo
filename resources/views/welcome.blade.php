<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{config('app.name')}}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Mulish:200,600" rel="stylesheet">
         <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

        <style>
            @import url('https://fonts.googleapis.com/css2?family=Sofia+Sans+Extra+Condensed:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
            html, body {
                background-color: #f4f4f4f4;
                color: #636b6f;
                font-family: 'Mulish', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
                background: url('assets/images/modern_real_estate_background_images-1746289147296.png') no-repeat center center;
                background-size: cover;
            }
            .full-height {
                height: 100vh;
            }
            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }
            .position-ref {
                position: relative;
            }
            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }
            .content {
                text-align: center;
                background: #ffffffe0;
                padding-left: 15px;
                padding-right: 15px;
                border-radius: 8px;
            }
            .title {
                font-size: 54px;
            }
            .links > a {
                color: #636b6f;
                padding: 0 10px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }
            .m-b-md {
                margin-bottom: 30px;
            }
            .titleHome{
              font-family: 'Sofia Sans Extra Condensed', sans-serif !important;
               font-size: 5rem;
               text-transform: uppercase;
               margin:0;
               font-weight: 500;
            }
            .titleHome span{
                color: #4d81bb;
                font-weight: bold;
            }
            p{
                font-size: 25px;
            }
            .btn-home a{
                font-family: 'Sofia Sans Extra Condensed', sans-serif !important;
                 font-size: 2rem;
            }

            @media (max-width: 767.98px) {
              .titleHome{
                font-size: 2rem;
                margin-top: 20px;
              }
              .logo_nav{
                height: 100px;
              }
              p {
                  font-size: 15px;
                }
            }

        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">

            @if (Route::has('login'))
                <div class="top-right links">
                   
                    @auth
                        <!--a href="{{ url('/home') }}">Home</a-->
                    @else
                        <!--a href="{{ route('login') }}">Login</a-->

                        @if (Route::has('register'))
                            <!--a href="{{ route('register') }}">Register</a-->
                        @endif
                    @endauth
                </div>
            @endif

            <div class="top-right links">
                    <a href="mailto:sakho.bouna20@gmail.com">sakho.bouna20@gmail.com</a>/<a href="tel:+221 77 567-03-62">+221 77 611-78-12</a>
            </div>

            <div class="content">

                <div class="title m-b-md">
                    <h1 class="font-semibold titleHome text-uppercase">{{ config('app.name', 'Laravel') }}<span>.net</span></h1>
                    <img src="{{ url('/assets/images/logo-login.png') }}" class="logo_nav" height="180">
                    <p>Gérer votre agence immobiliére en toute confiance!</p>

                    <div class="text-center btn-home">
                        
                        @auth
                            <a href="{{ url('/home') }}"  class="rounded-0 btn btn-info">Acceder à l'application</a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-0 btn btn-primary btn-lg px-3" >Se connecter ➔</a>

                            @if (Route::has('register'))
                                <!--a href="{{ route('register') }}">Register</a-->
                            @endif
                        @endauth
                    </div>
        

                </div>

                <!--img class="img-fluid" src="{{ url('assets/images/starter.png') }}"-->
                
            </div>
        </div>

    </body>
</html>
