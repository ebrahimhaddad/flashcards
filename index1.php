<?php
include 'config.php';
$allInOneQuery = "SELECT
                COUNT(*) AS all_words,
                SUM(`COL 2` = '(Verb)') AS verb_words,
                SUM(`COL 2` = '(Adj.)') AS adj_words
                FROM woerter_txt WHERE `language` = 'de';";
$allInOneResult = mysqli_query($db, $allInOneQuery);
$counts = mysqli_fetch_array($allInOneResult);
$allWords = $counts['all_words'];
$verbWords = $counts['verb_words'];
$adjWords = $counts['adj_words'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Latest compiled and minified CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>فلش کارت های آبلینگ</title>
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="فلش کارت آلمانی رایگان فلش کارت ابراهیم آبلینگ">
    <meta name="author" content="ابراهیم حداد">

</head>

<body class="bg-info" style="direction:rtl">
    <div class="container">
        <div class="row">
            <div class="col-lg">
                <h2 class="h2 bg-primary text-white" style="margin-top: 1rem; text-align: center;"> با <strong>فلش کارت آ، ب، لینگ</strong> آشنا شوید</h2>
                <p>
                    فلش کارت ها ابزارهایی قدرتمند برای یادگیری اطلاعات تازه هستند. فلش کارت های ما راهی ساده جهت یادگیری لغات کلیدی کتاب های مطرح زبان آلمانی در اختیار شما میگذارند. به نحوی که بتوانید قبل از مطالعه هر درس از کتاب مورد نظر خود، لغات کلیدی آن درس را به خاطر بسپرید.
                </p>
                <p><kbd class="bg-primary"><strong>فلش کارت ابراهیم</strong></kbd> یا به تازگی <kbd class="bg-primary"><strong>فلش کارت آبلینگ</strong></kbd> یک منبع یادگیری لغات است که به طور خاص برای زبان آموزان زبان آلمانی طراحی شده است. با فلش کارت های ما می توانید:</p>
                <ul>
                    <li>لغات و جملات مهم کتاب آلمانی مورد نظر خود را مرور کنید</li>
                    <li>درک خود از هر لغت را با کمک معنی و مثال مناسب تقویت کنید </li>
                    <li>قبل از شروع هر درس خود را برای یادگیری و درک کامل آن آماده کنید</li>
                </ul>
                <p> اما چیزی که فلش کارت های ما را واقعاً خاص می کند، جامعه داوطلبانی است که با هم کار می کنند تا کلمات جدیدی را به پروژه اضافه کنند. تیم داوطلبان ما متشکل از افراد متعهد به توسعه فلش کارت هایی است که از تکنیک لایتنر پشتیبانی می کند که روشی اثبات شده برای بهبود فرآیند به خاطر سپردن واژگان است. این بدان معناست که شما به کتابخانه ای در حال توسعه مداوم از کلمات و عبارات دسترسی خواهید داشت که همگی به دقت تنظیم و سازماندهی شده اند تا به شما کمک کنند کارآمدتر یاد بگیرید.</p>
                <p>برای قدردانی از داوطلبان ما یک پنل کاربری نیز تهیه کرده ایم که ضمن ورود واژگان جدید، واژگان قبلی را بصورت دوره های زمان بندی شده مرور کنند.</p>
                <div class="card">
                    <p class="text-danger card-body" style="text-shadow: 2px 1px 6px black;"><b>همین حالا به جامعه در حال توسعه داوطلبان بپیوندید تا با <b>فلش کارت آبلینگ</b> مهارت های زبان آلمانی خود را به سطوح بالاتری ارتقا دهید!</b></p>
                </div>
            </div>
            <div class="col-lg">
                <div class="row" style="margin-top: 2rem;">
                    <a class="btn btn-success btn-lg" style="direction:rtl;" href="cards.php">از فلش کارت های رایگان ما استفاده کن!</a>
                </div>
                <div class="row" style="margin-top: 2rem;  margin-bottom: 1rem;">
                    <a class="btn btn-success btn-lg" style="direction:rtl;" href="login.php">به عنوان داوطلب با ما همراه شو <span class="text-danger">♥</span></a>
                </div>
                <div class="row" style="margin-top: 2rem;  margin-bottom: 1rem;">
                    <a class="btn btn-success btn-lg" href="index.php">English</a>
                </div>
                <div class="row" style="margin-top: 2rem;  margin-bottom: 1rem;">
                    <div class="card bg-info" style="width:33%">
                        <div class="card-header">کل کلمات</div>
                        <div class="card-body">
                            <h4 class="card-title"><?= $allWords; ?></h4>
                        </div>
                    </div>
                    <div class="card bg-info" style="width:33%">
                        <div class="card-header">فعل</div>
                        <div class="card-body">
                            <h4 class="card-title"><?= $verbWords; ?></h4>
                        </div>
                    </div>
                    <div class="card bg-info" style="width:33%">
                        <div class="card-header">صفت</div>
                        <div class="card-body">
                            <h4 class="card-title"><?= $adjWords; ?></h4>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top: 2rem;  margin-bottom: 1rem;">
                    <a class="btn btn-warning btn-lg" href="https://abeling.ir/book">خرید کتاب</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest compiled JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>