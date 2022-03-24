<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- So that mobile will display zoomed in -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- enable media queries for windows phone 8 -->
    <meta name="format-detection" content="telephone=no"> <!-- disable auto telephone linking in iOS -->
    <meta name="format-detection" content="date=no"> <!-- disable auto date linking in iOS -->
    <meta name="format-detection" content="address=no"> <!-- disable auto address linking in iOS -->
    <meta name="format-detection" content="email=no"> <!-- disable auto email linking in iOS -->
    <meta name="author" content="Sebastian Flores">
    <title>User Registration</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <style type="text/css">
        * {
            font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            padding: 0;
            margin: 0;
        }

        .email {
            width: 80%;
            margin: auto;
            border: 1px solid gray;
            border-radius: 1rem;
        }

        .email .header {
            display: flex;
            width: fit-content;
            margin: auto;
            margin-bottom: .5rem;
        }

        .email .header img {
            height: 3.25rem;
        }

        .email .header .app-name {
            display: flex;
            font-size: 2.5rem;
        }

        .email .header .app-name .bold {
            font-weight: 700;
            color: #1ba82f;
        }

        .email .header .app-name .light {
            font-weight: 400;
            color: #0030A8;
        }

        .email .separator-header {
            height: 2px;
            background-color: #1ba82f;
            margin: 1.5rem 0 2.5rem 0;
        }

        .email .footer {
            text-align: center;
            padding: .5rem 0;
            margin-top: 2.5rem;
            color: rgb(235, 235, 235);
            background-color: gray;
            border-radius: 0 0 1rem 1rem;
        }

        .content {
            width: 80%;
            margin: auto;
        }

        .content .greeting {
            font-weight: 700;
        }

        .content .information {
            align-self: center;
            text-align: justify;
        }

        .content .button-container {
            width: fit-content;
            margin: auto;
        }

        .content .button-container button {
            margin: auto;
            margin-top: 2rem;
            padding: 1rem;
            font-size: 1rem;
            background-color: #0030A8;
            border-style: none;
            border-radius: .5rem;
            box-shadow: 0px 0px 4px 0px #3962c9;
        }

        .content .button-container button a {
            color: white;
            text-decoration: none;
        }
    </style>

</head>

<body  style="margin-top: 0; margin-bottom: 0; padding-top: 0; padding-bottom: 0; width: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;" bgcolor="#f0f0f0">
    <div class="email">
        <div class="header">
            <img src="https://i.ibb.co/fxq6Tnx/logo.png" alt="logo" class="logo" />
            <div class="app-name">
                <span class="bold">Eco</span>
                <span class="light">Viewer</span>
            </div>
        </div>
        <div class="separator-header"></div>
        <div class="content">
            <p class="greeting">
                Bienvenido {{$user->getCompleteName()}},
            </p>
            <p class="information">
                has sido registrado en la plataforma Ecoviewer, por favor ingresa al siguiente enlace para ver la información relacionada a tu perfil
            </p>
            <div class="button-container">
                <button class="go-to-app">
                    <a href="http://localhost:3000" class="link">
                        Ir a la plataforma
                    </a>
                </button>
            </div>
        </div>
        <div class="footer">
            © Copyright. Ecoviewer
        </div>
    </div>
</body>
</html>