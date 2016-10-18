<!DOCTYPE html>
<html>
<head>
    <title>Monitor</title>

    <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

    <style>
        html, body {
            height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            display: table;
            font-weight: 100;
            font-family: 'Lato';
        }

        .container {
            text-align: center;
            display: table-cell;
            vertical-align: middle;
        }

        .content {
            text-align: center;
            display: inline-block;
        }

        .title {
            font-size: 96px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="content">
        <div class="title" style="margin-bottom: 85%;">Hello {{ $user->nickname }} !</div>
        <img src="{{ $user->headimgurl }}" alt="" style="width: 150px;height: 150px;border-radius: 75px;">
        <br>
        <br>
        <a href="{{ $url }}" style="text-decoration: none ;color: black">点击进入</a>
    </div>
</div>
</body>
</html>
