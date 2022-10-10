<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ url(mix('/css/reset.css')) }}">
    <link rel="stylesheet" href="{{ url(mix('/css/main.css')) }}">
    <link rel="stylesheet" href="{{ $css }}">
    <title>病院管理システム | {{ $title }}</title>
</head>

<body>
    <div class="container">
        <div class="side-menu">
            <div class="side-menu__item">
                <a class="side-menu__btn mt-lg side-menu__link" href="{{ route('home') }}">ホーム</a>
            </div>
            <div class="side-menu__item">
                <a class="side-menu__btn side-menu__link" href="{{ route('business_hour') }}">診察時間編集</a>
            </div>
            <div class="side-menu__item">
                <a class="side-menu__btn side-menu__link" href="{{ route('vacation') }}" ">長期休業設定</a>
            </div>
            <div class="side-menu__item">
                <a class="side-menu__btn side-menu__link" href="{{ route('account.index') }}">アカウント情報</a>
            </div>
            <div class="side-menu__item">
                <a class="side-menu__btn side-menu__link" href="{{ route('hospital') }}">病院登録</a>
            </div>
        </div>

        {{ $slot }}
    </div>
</body>

</html>
