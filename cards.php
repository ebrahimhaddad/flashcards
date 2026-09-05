<!DOCTYPE html>
<html lang="en">
<?php
include 'config.php';

$editor = current_editor(); // 'user' if not logged in, or the editor's email if a valid JWT cookie is present

// Create an array to store all Books and their lessons
$query = "SELECT `COL 7`, `COL 8`, COUNT(`COL 1`) as card_count 
              FROM `woerter_txt` 
              WHERE language = 'de' 
              GROUP BY `COL 7`, `COL 8` 
              ORDER BY `COL 7`, `COL 8`";
$result = mysqli_query($db, $query);
echo "<script>booksAndLessons = Array();</script>";
$c = 0;
while ($r = mysqli_fetch_array($result)) {
    $lesson_data = array(
        'book' => $r['COL 7'],
        'lesson' => $r['COL 8'],
        'count' => $r['card_count']
    );
    echo "<script>booksAndLessons[$c]=" . json_encode($lesson_data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) . ";</script>";
    $c++;
}
?>

<head>
    <title>Ebrahim's Flash Cards!</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body dir="rtl">
    <div class="container" style="margin:2rem">
        <form action='practice.php' method='POST'>
            <div class="row">
                <div class="col-lg-3"></div>
                <div class="col-lg-6">
                    <h1><img src="../book/img/Logo_AbeLing.png" width="70"> فلش کارت های آبلینگ</h1>
                    <hr>
                    <h2>یک کتاب انتخاب کنید:</h2>

                    <select class="form-select form-select-lg bg-primary" name='buch' onchange='setLektion(this.value)' dir="ltr">
                        <?php
                        $sql = "SELECT * FROM `woerter_txt` WHERE language = 'de' GROUP BY `COL 7` ORDER BY `COL 7`;";
                        $r = mysqli_query($db, $sql);
                        while ($row = mysqli_fetch_array($r)) {
                            echo "<option class='bg-primary' value='" . htmlspecialchars($row[6]) . "'>" . htmlspecialchars($row[6]) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-lg-3"></div>
            </div>
            <div class="row">
                <div class="col-lg-3"></div>
                <div class="col-lg-6">
                    <h2>یک درس یا فصل انتخاب کنید:</h2>

                    <select class="form-select form-select-lg bg-primary" name='lektion' id='lektion' dir="ltr">
                        <!-- Options will add by JS -->
                    </select>
                    <script>
                        setLektion('ECL Band 1');

                        function setLektion(selectedBook) {
                            removeOptions('lektion');
                            var lektion = document.getElementById('lektion');
                            for (var j = 0; j < booksAndLessons.length; j++) {
                                if (selectedBook == booksAndLessons[j].book) {
                                    var opt = document.createElement('option');
                                    opt.value = booksAndLessons[j].lesson;
                                    opt.innerHTML = booksAndLessons[j].lesson + ' (' + booksAndLessons[j].count + ' cards)';
                                    lektion.appendChild(opt);
                                }
                            }
                        }

                        function removeOptions(selectedElement) {
                            var selectElement = document.getElementById(selectedElement);
                            var i, L = selectElement.options.length - 1;
                            for (i = L; i >= 0; i--) {
                                selectElement.remove(i);
                            }
                        }
                    </script>
                </div>
                <div class="col-lg-3"></div>
            </div>
            <div class="row">
                <div class="col-4 col-lg-3"></div>
                <div class="form-check form-switch col-4 col-lg-3" style="margin:1rem 0px 1rem 0px; text-align:center;">
                    <input class="form-check-input" type="checkbox" id="mySwitch" name="verb" value="no">
                    <label class="form-check-label" for="mySwitch" title="فقط فعل ها">فقط فعل ها</label>
                </div>
                <div class="form-check form-switch col-4 col-lg-3" style="margin:1rem 0px 1rem 0px; text-align:center;">
                    <input class="form-check-input" type="checkbox" id="mySwitch2" name="shuffle" value="yes" checked>
                    <label class="form-check-label" for="mySwitch2" title="بر زدن">بُر زدن</label>
                </div>
                <div class="col-4 col-lg-3"></div>
            </div>
            <div class="row">
                <div class="col-lg-3"></div>
                <div class="d-grid gap-2 col-lg-6">
                    <input type='hidden' name='language' value='de'>
                    <input type='submit' class='btn btn-lg bg-success' name='chooseCardSet' value='Start'>
                </div>
                <div class="col-lg-3"></div>
            </div>
        </form>
        <div class="row">
            <a href="index1.php" style="text-align:center; margin-top:1rem;">درباره</a>
        </div>
        <div class="row">
            <a href="https://t.me/ebrahimsflashcards" style="text-align:center; margin-top:1rem;">Telegram</a>
        </div>
        <div class="row">
            <a href="https://abeling.ir/book" style="text-align:center; margin-top:1rem;">خرید کتاب</a>
        </div>
        <div class="row">
            <?php if ($editor === 'user'): ?>
                <a href="login.php" style="text-align:center; margin-top:1rem;">ورود ویراستاران</a>
            <?php else: ?>
                <span style="text-align:center; margin-top:1rem;">خوش آمدید, <?= htmlspecialchars($editor) ?> | <a href="logout.php">خروج</a></span>
            <?php endif; ?>
        </div>
    </div>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>