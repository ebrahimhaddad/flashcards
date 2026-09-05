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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--<link href="/bootstrap/bootstrap.min.css" rel="stylesheet">-->

    <title>Ebrahim's Flashcards</title>
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Online free flashcards, Ebrahim's Flashcards">
    <meta name="author" content="Ebrahim Haddad">

</head>

<body class="bg-info">
    <div class="container">
        <div class="row">
            <div class="col-lg">
                <h2 class="h2 bg-primary text-white" style="margin-top: 1rem; text-align: center;">What is the <b>Ebrahim's Flashcards</b>?</h2>
                <p>Flashcards are powerful tools for memorizing new information. Our flashcards provide a convenient way to review key vocabulary
                    and phrases in preparation for your German language lessons.</p>
                <p><kbd class="bg-primary">Ebrahim's Flashcards</kbd> offers a unique resource specifically designed for learners of German. With our flashcards, you can:</p>
                <ul>
                    <li>Review important words and phrases from your German Training Books</li>
                    <li>See definitions and examples to help solidify your understanding</li>
                    <li>Prepare yourself before starting a lesson and get a head start on your learning journey.</li>
                </ul>
                <p> But what makes our flashcards truly special is the community of volunteers who are working together to add new words to the project. Our dedicated team of volunteers is committed to creating flashcards that support the Leitner technique, a proven method for improving vocabulary retention. This means that you'll have access to a constantly growing library of words and phrases, all carefully curated and organized to help you learn more efficiently.</p>
                <p> To support our volunteers, we're also launching a new panel that will help you track your progress and repeat words in a repetitive manner. This panel will store the words you've learned and use them to generate customized review sessions, ensuring that you stay on track and make steady progress towards fluency.</p>
                <div class="card">
                    <p class="text-danger card-body" style="text-shadow: 2px 1px 6px black;"><b>Join our community today and start using <b>Ebrahim's Flashcards</b> to take your German language skills to the next level!</b></p>
                </div>
            </div>
            <div class="col-lg">
                <div class="row" style="margin-top: 2rem;">
                    <a class="btn btn-success btn-lg" href="cards.php">Use free Flashcards now!</a>
                </div>
                <div class="row" style="margin-top: 2rem;  margin-bottom: 1rem;">
                    <a class="btn btn-success btn-lg disabled" href="login.php">Join as a volunteer <span class="text-danger">♥</span></a>
                </div>
                <div class="row" style="margin-top: 2rem;  margin-bottom: 1rem;">
                    <a class="btn btn-success btn-lg" href="index1.php">فارسی</a>
                </div>
                <div class="row" style="margin-top: 2rem;  margin-bottom: 1rem;">
                    <div class="card bg-info" style="width:33%">
                        <div class="card-header">All Words</div>
                        <div class="card-body">
                            <h4 class="card-title"><?= $allWords; ?></h4>
                        </div>
                    </div>
                    <div class="card bg-info" style="width:33%">
                        <div class="card-header">Verb</div>
                        <div class="card-body">
                            <h4 class="card-title"><?= $verbWords; ?></h4>
                        </div>
                    </div>
                    <div class="card bg-info" style="width:33%">
                        <div class="card-header">Adjective</div>
                        <div class="card-body">
                            <h4 class="card-title"><?= $adjWords; ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest compiled JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>