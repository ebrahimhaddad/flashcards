<!DOCTYPE html>
<html>
<?php
   include 'config.php';
   session_start();

   function split_string_by_hyphen($inp, $num){
    // Splits a string into 2 sub strings broken by hyphen character
    if($num == 1){
      return substr($inp, 0, strpos($inp, '-') - 1);
    }elseif($num == 2){
      return substr($inp, strpos($inp, '-') + 2);
    }else{
      return 'error';
    }
  }
   
   if(isset($_POST['sendmessage'])){
        $fri = $_POST['lektion'];    
        $book = split_string_by_hyphen($fri, 1);
        $lesson = split_string_by_hyphen($fri, 2);
        $query = "SELECT * FROM `woerter_txt` WHERE `COL 7` = '$book' AND `COL 8` = '$lesson'";
        $result = mysqli_query($db, $query);
        echo "<script>jsarray = Array(); pageIndex=0; statt = 0;</script>";
        $c = 0;
        while ($r = mysqli_fetch_array($result)) {
            echo "<script>jsarray[$c]=" . json_encode($r) . ";</script>";
            $c++;
        }
        $c--;
        echo "<script>pageNum=$c;</script>";
   }else{
        $fri = "Wortschatz A2 - 8";    
        $book = split_string_by_hyphen($fri, 1);
        $lesson = split_string_by_hyphen($fri, 2);
        $query = "SELECT * FROM `woerter_txt` WHERE `COL 7` = '$book' AND `COL 8` = '$lesson'";
        $result = mysqli_query($db, $query);
        echo "<script>jsarray = Array(); pageIndex=0; statt = 0;</script>";
        $c = 0;
        // Adds words to the 'jsarray' array
        while ($r = mysqli_fetch_array($result)) {
            echo "<script>jsarray[$c]=" . json_encode($r) . ";</script>";
            $c++;
        }
        $c--;
        echo "<script>pageNum=$c;</script>"; 
   }
?>
    <head>
        <title>Ebrahim&apos;s Flash Cards</title>
        <link rel="stylesheet" href="practice.css">
    </head>
    <body style="background-image: url('<?= $book; ?>.jpg');">
        <div class="loading-container">
            <img src="<?= $book; ?>.jpg" alt="<?= $book; ?>">
        </div>
        <script>
            const loadingContainer =document.querySelector('.loading-container');
            
            function hideLoading(){
                loadingContainer.classList.add('hide');
            }
            
            window.onload = function(){
                setTimeout(hideLoading, 1500);
            }
        </script>
        <div id = "box">
            <h2>Ebrahim's Flash Cards</h2>
            <h3><?= $fri ?></h3>
            <input type="button" id="lockart" value="&#128274;" onclick="lockArtikel()">
            <input type="button" id="art" value="Artikel?" onclick="showArtikel()">
            <textarea id = "word" rows="2" style = "text-align: center;" disabled></textarea>
            <input type="button" id="answer" value="Übersetzen" multiline onclick="showTranslate()">
            <textarea id = "translate" rows="2" style = "text-align: center;" disabled></textarea>

            <input type="button" id="prev" class="controls" value="&lt;&lt;" onclick="prev()">
            <input type="button" id="next" class="controls" value="&gt;&gt;" onclick="next()">
        
            <input type="button" id="next" class="controls" value="&#8635;" onclick="refresh()">
            <br>
            <a href="cards.php">More vocabularies</a>
        </div>
        <script>
            showWord();
            lockArt = 0;
            
            function showWord(){
                a = '';
                if(jsarray[pageIndex][3]!= '') a = ', ' + jsarray[pageIndex][3];
                if(statt == 0)
                    document.getElementById("word").value = jsarray[pageIndex][2] + a;
                else
                    document.getElementById("word").value = jsarray[pageIndex][5]+' '+jsarray[pageIndex][4];
            }
            
            function showArtikel(){
                if (jsarray[pageIndex][1]!=null) document.getElementById("art").value = jsarray[pageIndex][1];
            }

            function lockArtikel(){
                if(lockArt == 0){
                    lockArt = 1;
                    document.getElementById("lockart").style.backgroundColor = "pink";
                    showArtikel();
                    showTranslate();
                }else{
                    lockArt = 0;
                    document.getElementById("lockart").style.backgroundColor = "#E0E0E0";
                }
            }

            function showTranslate(){
                if(statt == 0)
                    document.getElementById("translate").value = jsarray[pageIndex][5]+' '+jsarray[pageIndex][4];
                else
                    document.getElementById("translate").value = jsarray[pageIndex][2] + a;
            }

            function next(){
                if(++pageIndex>pageNum)pageIndex = 0;
                reset();
                showWord();
                if(lockArt == 1){
                    showArtikel();
                    showTranslate();
                }
            }

            function prev(){
                if(--pageIndex<0)pageIndex = pageNum;
                reset();
                showWord();
                if(lockArt == 1){
                    showArtikel();
                    showTranslate();
                }
            }

            function refresh(){
                if(++statt == 2) statt = 0;
                reset();
                showWord();
            }

            function reset(){
                if(lockArt == 1){
                    showArtikel();
                    showTranslate();
                }else{
                    document.getElementById("art").value = 'Artikel?';
                    document.getElementById("translate").value = '';
                }
            }
        </script>
    </body>
</html>