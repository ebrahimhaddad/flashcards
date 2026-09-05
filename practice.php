<?php
include 'config.php';

$editor = current_editor(); // 'user' if not logged in, or the editor's email

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book = $_POST['buch'];
    $lesson = $_POST['lektion'];
    $shuffle = $_POST['shuffle'];
    $checkVerb = $_POST['verb'] ?? "yes";
    $lang = $_POST['language'];
    // $editor no longer comes from $_POST
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $book = $_GET['buch'] ?? "ECL Band 1";
    $lesson = $_GET['lektion'] ?? "1";
    $shuffle = $_GET['shuffle'] ?? "yes";
    $checkVerb = $_GET['verb'] ?? "yes";
    $lang = $_GET['language'] ?? "de";
} else {
    $book = "ECL Band 1";
    $lesson = "1";
    $shuffle = "yes";
    $checkVerb = "yes";
    $lang = 'de';
}

if ($checkVerb == "no") $verb = " AND `COL 2`='(Verb)'";
else $verb = "";

$query = "SELECT * FROM `woerter_txt` WHERE `COL 7` = ? AND `COL 8` = ? AND `language` = ?" . $verb;


$stmt = mysqli_prepare($db, $query);
mysqli_stmt_bind_param($stmt, 'sss', $book, $lesson, $lang);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

echo "<script>jsarray = Array(); pageIndex = 0; statt = 0;";
$c = 0;
// Adds words to the 'jsarray' array
while ($r = mysqli_fetch_array($result)) {
    echo "jsarray[$c]=" . json_encode($r, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) . ";";
    $c++;
}
// Shuffle the words in the array
if ($shuffle == "yes") echo "jsarray.sort(() => Math.random() - 0.5);";
$c--;
echo "pageNum=$c;</script>";

$coverStmt = mysqli_prepare($db, "SELECT * FROM `bookscovers` WHERE `bookname` = ?;");
mysqli_stmt_bind_param($coverStmt, 's', $book);
mysqli_stmt_execute($coverStmt);
$urlResult = mysqli_stmt_get_result($coverStmt);
$url = mysqli_fetch_array($urlResult);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="practice.css">

    <title>Ebrahim&apos;s Flash Cards - <?= htmlspecialchars($book) ?></title>
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--<link href="/bootstrap/bootstrap.min.css" rel="stylesheet">-->

</head>

<body style="background-image: url('img/books/<?= htmlspecialchars($url[1]) ?>');">
    <div class="loading-container">
        <img src="img/books/<?= htmlspecialchars($url[1]); ?>" alt="<?= htmlspecialchars($book) ?>">
    </div>
    <script>
        const loadingContainer = document.querySelector('.loading-container');

        function hideLoading() {
            loadingContainer.classList.add('hide');
        }
        window.onload = function() {
            setTimeout(hideLoading, 1500);
        }
        document.onkeydown = function(e) {
            const tag = e.target.tagName;
            const isEditableField = (tag === 'TEXTAREA' || tag === 'INPUT') && !e.target.readOnly;

            if (isEditableField) return; // let arrow keys behave normally while typing

            switch (e.keyCode) {
                case 39:
                    next();
                    break;
                case 37:
                    prev();
                    break;
                case 38:
                    play();
                    break;
            }
        }
    </script>
    <div class="container-fluid min-vh-100 d-flex align-items-center">
        <form method="POST" id="myForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_COOKIE['csrf_token']) ?>">
            <div class="row w-100">
                <!-- Left Column -->
                <div class="col-md-6 colbg d-flex justify-content-center align-items-center py-1">
                    <div class="column-content text-center">
                        <h2 class="bg-light m-1">Ebrahim's Flash Cards</h2>
                        <?php
                        if ($lesson == "-") $title = $book;
                        else $title = $book . " - " . $lesson;
                        // for Editors
                        if ($editor !== null && $editor !== "user") $readOnly = "";
                        else $readOnly = "readonly";
                        ?>
                        <h3 class="bg-light m-1"><?= htmlspecialchars($title) ?></h3>
                        <input type="button" class="btn btn-success btn-lg m-1" id="lockart" value="روشن ماندن آرتیکل و ترجمه" onclick="lockArtikel()"></br>
                        <input type="button" class="btn btn-success btn-lg m-1" id="art" value="Artikel?" onclick="showArtikel()">
                        <textarea id="word" rows="2" class="text-center" name="word" <?= $readOnly ?>></textarea>
                        <input type="button" class="btn btn-success btn-lg m-1" id="answer" value="ترجمه" multiline onclick="showTranslate()">
                        <textarea id="translate" rows="2" class="text-center" name="translate" placeholder="Bedeutung" <?= $readOnly ?> style="direction: rtl;"></textarea>
                        <textarea id="beispiel" rows="3" class="text-center" name="beispiel" title="Beispiel" placeholder="Beispiel" <?= $readOnly ?>></textarea>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="direction: rtl;">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">دیدگاه دریافت شد</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                دیدگاهتان را دریافت کردیم. از همکاری شما صمیمانه سپاسگزاریم.
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-6 colbg d-flex justify-content-center align-items-center py-1">
                    <div class="column-content text-center">
                        <input type="button" id="prev" class="btn btn-success btn-lg" style="margin: 1px" value="< قبلی">
                        <input type="button" id="next" class="btn btn-success btn-lg" value="بعدی >">
                        <p id="serie" class="bg-light m-1">-</p>
                        <input type="button" class="btn btn-success btn-lg" style="margin: 1px" value="تعویض زبان" onclick="refresh()">
                        <?php
                        if ($editor !== null && $editor !== "user") {
                            echo '<input type="hidden" name = "buch" value="' . htmlspecialchars($book) . '">';
                            echo '<input type="hidden" name = "lektion" value="' . htmlspecialchars($lesson) . '">';
                            echo '<input type="hidden" name = "shuffle" value="' . htmlspecialchars($shuffle) . '">';
                            echo '<input type="hidden" name = "verb" value="' . htmlspecialchars($checkVerb) . '">';
                            echo '<textarea id="body" name="body" rows="3" class="text-center" title="Kommentieren" placeholder="Ihren Kommentar"></textarea>';
                            echo '<button name="comment" class="btn btn-warning btn-lg" data-bs-toggle="modal" data-bs-target="#exampleModal">Kommentieren</button>';
                        }

                        echo "<script>
                            document.getElementById('myForm').addEventListener('submit', function(event) {
                                event.preventDefault();

                                fetch('api_comment.php', {
                                    method: 'POST',
                                    body: new FormData(this)
                                });
                            });
                        </script>";
                        ?>
                        <p class="bg-light m-1">
                            <a href="cards.php">More vocabularies</a>
                        </p>
                        <p class="bg-light m-1">
                            <a href="index1.php">صفحه اصلی</a>
                        </p>
                        <p class="bg-light m-1" id="hint">
                            برای شنیدن تلفظ روی واژه یا ترجمه کلیک کنید یا فلش بالا را بفشارید
                        </p>
                        <p>
                            <?php
                            if ($url[2] != null)
                                echo '<a href="' . htmlspecialchars($url[2]) . '" class="btn btn-lg" style="background-color:rgb(255,215,0); margin: 1px" target="_blank">..:: خرید کتاب ::..</a>';
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Bootstrap 5.3.3 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!--<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>-->
    <script src="practice.js"></script>

    <?php
    if ($editor !== null && $editor !== "user") echo "<script>lockArtikel()</script>";
    ?>

</body>

</html>