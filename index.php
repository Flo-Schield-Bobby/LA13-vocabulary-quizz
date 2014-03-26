<?php
session_start();

mysql_connect("127.0.0.1","root", "root");

mysql_select_db("la13");

if(isset($_GET['mode']))
    $mode = $_GET['mode'];
else
    $mode = "french";

if(isset($_POST['idQuestion']))
    $result = true;
else
    $result = false;

if(isset($_POST['search'])){
    $search = $_POST['search'];
    $search = preg_replace("#\\\\#", "", $search );
}
else
    $search = false;

if(isset($_GET['week']))
    $week = $_GET['week'];
else
    $week = "all";

if(isset($_GET['words']))
    $words = true;
else
    $words = false;

//pseudo management
if(!isset($_COOKIE['currentUser']) && isset($_POST['pseudo']))
{
    $pseudo = mysql_real_escape_string(htmlspecialchars($_POST['pseudo']));
    $data = mysql_fetch_array(mysql_query("SELECT count(*) as nb FROM la13_user WHERE pseudoUser='".$pseudo."';"));
    $nb = $data['nb'];

    if($nb==0)
        mysql_query("INSERT INTO la13_user(pseudoUser) VALUES('".$pseudo."');");

    setcookie('currentUser', $pseudo, (time() + 3600*24*365));
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>LA13 Training</title>
    <link href="style.css?4" rel="stylesheet" type="text/css" />
    <link href="./design/favicon.png" rel="icon" type="image/png" />
</head>
<body>
    <div id="header">
        <div style="text-align:center;position:relative;top:55px;"><a href="index.php"><img src="design/logo.png" alt="LA13 Training" title="LA13 Training"/><span class="titre">LA13 UTC</span></a></div>
        <?php
        if(!isset($_COOKIE['currentUser']) and !isset($_POST['pseudo']))
        { 
        ?>
        </div>
        <div id="main">
            <form name='choosePseudo' action="index.php" method="post">
                <p>Votre pseudo <input type='text' name='pseudo'  maxlength="20"  class="inputAnswer" style='width:300px;height:30px;font-size:20px;' id='pseudo' value=''/></p>
                <p><i>Ce pseudo permet &agrave; l'application de ne vous poser que les questions auxquelles vous avez les moins r&eacute;pondues..</i></p>
            </form>
        <?php
        }
        else 
        {
            if(isset($_COOKIE['currentUser']))
                $pseudo =$_COOKIE['currentUser'];

            if($result)
                echo "<body onLoad=\"document.getElementById('submitOk').focus();\">";
            else
                echo "<body onLoad=\"document.getElementById('answer').focus();\">";

            if(isset($_GET['reset']))
            {
                $_SESSION['goodAnswers']=0;
                $_SESSION['totalAnswers']=0;
            }
            
            if(isset($_SESSION['totalAnswers']) and $_SESSION['totalAnswers']!=0)
                $score = number_format ($_SESSION['goodAnswers']/ $_SESSION['totalAnswers'] *100,2);
            else
                $score = "-";
            ?>

            <div id="info">
                <p> <?php echo $pseudo;?></p>
                <p> <?php echo $score;?>%<?php if($score!="-") echo " (".$_SESSION['goodAnswers']."/".$_SESSION['totalAnswers'].")";?></p>
            </div>
            <span style="position:absolute; top:170px; left: 160px;margin:0px;"><a class="top_links" href="index.php?mode=<?php echo $mode;?>">Training</a></span>
            <span style="position:absolute; top:170px; left: 320px;margin:0px;"><a class="top_links" href="index.php?words&mode=<?php echo $mode;?>">Words</a></span>
            <span style="position:absolute; top:90px; right: 20px;margin:0px;"><a href="index.php?reset&mode=<?php echo $mode;?>"><input type="button" value="Reset your score" class="myButton"/></a></span>
            <span style="position:absolute; top:150px; right: 20px;margin:0px;">
                <form name='result' method='POST' action='index.php?mode=<?php echo $mode;?>' style='margin:auto;text-align:center;'>
                    <input type="text" name="search" id="search" value="Search..." onfocus="if(this.value=='Search...') this.value='';"/>
                    <input type='submit' name='submit' id='submitSearch' value=' '/>    
                </form></span>
            </div>
            <div id="main">
                <?php
                if($words)
                {
                ?>
                <span class="titleText">Words list</span>
                <?php
                if($week!="all")
                    $conditionWeek = " week=$week ";
                else
                    $conditionWeek = "1=1 ";

                $dataR = mysql_query("SELECT english, french, help FROM la13_words WHERE $conditionWeek ORDER BY week, idWord;");
                $nb = mysql_affected_rows();

                if($nb>1)
                    echo "<i style='color:#666;'>&nbsp;-&nbsp;".$nb."&nbsp;words</i>";
                else
                    echo "<i style='color:#666;'>&nbsp;-&nbsp;".$nb."&nbsp;word</i>";
                ?>
                <span style="margin-left:30px;"class="titleText">Week:&nbsp;</span>
                <form style="display:inline;margin:0px;" id="weekForm"  name="weekForm" method='GET' action='index.php'>  
                    <select name="week" onchange="this.form.submit();" id="selectWeek">
                        <OPTION VALUE="all" <?php if($week=="all") echo "selected='selected';"?>>All</OPTION>
                        <?php
                        $dataR2 = mysql_query("SELECT DISTINCT week FROM la13_words ORDER BY week;");

                        while($data2=mysql_fetch_array($dataR2))
                        {
                            if($week==$data2["week"]) echo "<OPTION VALUE='$data2[week]' selected='selected'>$data2[week]</OPTION>";    
                            else  echo "<OPTION VALUE='$data2[week]'>$data2[week]</OPTION>";    
                        }
                        ?>
                    </select>
                    <input type="hidden" name="mode" value="<?php echo $mode;?>"/>
                    <input type="hidden" name="words" value="1"/>
                </form><br/>
                <?php
                while($data=mysql_fetch_array($dataR))
                {
                    echo "<p style='margin-bottom:-10px;'><b>".$data['english'].":</b>&nbsp;".$data['french']."&nbsp;<i style='color:#666;'>".$data['help']."</i></p>"; 
                }
                echo "<br/><br/>";
            }
            else
                if($search)
                    {?>
                <p><span class="titleText">Your search:&nbsp;</span><?php echo $search;?>
                    <?php
                    $dataR = mysql_query("SELECT english, french, help FROM la13_words WHERE english like '%".mysql_real_escape_string($search)."%' OR french like '%".mysql_real_escape_string($search)."%' ORDER BY english;");
                    $nb = mysql_affected_rows();

                    if($nb>1)
                        echo "<i style='color:#666;'>&nbsp;-&nbsp;".$nb."&nbsp;results</i></p>";
                    else
                        echo "<i style='color:#666;'>&nbsp;-&nbsp;".$nb."&nbsp;result</i></p>";

                    while($data=mysql_fetch_array($dataR))
                    {
                        echo "<p style='margin-bottom:-10px;'><b>".$data['english'].":</b>&nbsp;".$data['french']."&nbsp;<i style='color:#666;'>".$data['help']."</i></p>";     
                    }
                }
                else
                    if(!$result)
                    {
                        ?>
                        <span class="titleText">Mode:&nbsp;</span><input type='radio' name='mode' value="english" id="en" style="cursor:pointer;" onchange="document.location='index.php?week=<?php echo $week;?>&mode='+this.value;" <?php if($mode == "english") echo "checked='checked'";?>/>
                        <label style="cursor:pointer;" for="en">English</label> 
                        <input type='radio' style="cursor:pointer;"  name='mode' value="french" id="fr"  onchange="document.location='index.php?week=<?php echo $week;?>&mode='+this.value;" <?php if($mode == "french") echo "checked='checked'";?>/>
                        <label style="cursor:pointer;" for="fr">French</label>
                        <span style="margin-left:30px;" class="titleText">Week:&nbsp;</span>
                        <form style="display:inline;margin:0px;" name="weekForm" id="weekForm" method='GET' action='index.php'>
                            <select style ="width:180px;" name="week" onchange="this.form.submit();" id="selectWeek">
                                <OPTION VALUE="all" <?php if($week=="all") echo "selected='selected';"?>>All</OPTION>
                                <OPTION VALUE="median" <?php if($week=="median") echo "selected='selected';"?>>Median (week 1-5)</OPTION>
                                <OPTION VALUE="final" <?php if($week=="final") echo "selected='selected';"?>>Final (week 7-14)</OPTION>
                                <?php
                                $dataR = mysql_query("SELECT DISTINCT week FROM la13_words ORDER BY week;");

                                while($data=mysql_fetch_array($dataR))
                                {
                                    if($week==$data["week"]) echo "<OPTION VALUE='$data[week]' selected='selected'>$data[week]</OPTION>";   
                                    else  echo "<OPTION VALUE='$data[week]'>$data[week]</OPTION>";  
                                }
                                ?>
                            </select>
                            <input type="hidden" name="mode" value="<?php echo $mode;?>"/>
                        </form>
                        <?php
                        $data = mysql_fetch_array(mysql_query("SELECT count(*) AS nb FROM la13_answer JOIN la13_user USING(idUser) WHERE pseudoUser='$pseudo';"));
                        $nb = $data['nb'];

                        if($week=="final")
                            $conditionWeek = " week>6 ";
                        else if($week=="median")
                            $conditionWeek = " week< 6 ";
                        else if($week!="all")
                            $conditionWeek = " week=$week "; 
                        else
                            $conditionWeek = "1=1 ";
                        if($nb>0)
                        {
                            $data = mysql_fetch_array(mysql_query("SELECT count(*) as nb FROM (SELECT idWord FROM la13_words WHERE $conditionWeek AND NOT EXISTS (SELECT * FROM la13_answer JOIN la13_user USING(idUser) WHERE pseudoUser='$pseudo' AND la13_words.idWord=la13_answer.idWord )) tab"));
                            $nbNotUsed = $data['nb'];

                            if($nbNotUsed>0)
                                $data = mysql_fetch_array(mysql_query("SELECT idWord, english, french, help FROM la13_words WHERE $conditionWeek AND NOT EXISTS (SELECT * FROM la13_answer JOIN la13_user USING(idUser) WHERE pseudoUser='$pseudo' AND la13_words.idWord=la13_answer.idWord ) ORDER BY RAND() LIMIT 1;"));
                            else
                            {
                                $data = mysql_fetch_array(mysql_query("SELECT min(nb) as nbUsed FROM ( SELECT count(*) AS nb FROM la13_answer JOIN la13_user USING(idUser) JOIN la13_words USING(idWord) WHERE $conditionWeek AND pseudoUser='$pseudo' GROUP BY idWord) tab;"));
                                $minUsed = $data['nbUsed'];         
                                $data = mysql_fetch_array(mysql_query("SELECT idWord, english, french, help FROM la13_words WHERE $conditionWeek AND NOT EXISTS (SELECT idWord FROM la13_answer JOIN la13_user USING(idUser) WHERE pseudoUser='$pseudo' AND la13_words.idWord=la13_answer.idWord GROUP BY idWord HAVING count(*)>$minUsed) ORDER BY RAND() LIMIT 1;"));
                            }
                        }
                        else
                        {
                            $data = mysql_fetch_array(mysql_query("SELECT idWord, english, french, help FROM la13_words WHERE $conditionWeek ORDER BY RAND() LIMIT 1;"));
                        }

                        if($mode=="english")
                            $question =$data['english'];
                        else
                            $question =$data['french'];

                        $id =$data['idWord'];
                        $help = $data['help'];
                        ?>
                        <p><form name='test' action="index.php?week=<?php echo $week;?>&mode=<?php echo $mode;?>" method="POST">
                            <span id="question" style="margin-top:-20px;font-size:40px;display:inline-block;max-width:750px;"><?php echo $question;?></span><br/> 
                            <input type="text" name="answer" id="answer" value="" class="inputAnswer" autocomplete="off"/><br/>
                            <p style="text-align:center;width:750px;"><i style="color:#555"><?php echo $help;?></i></p>
                            <p style="text-align:center;margin-left:-150px;"><input type="submit" id="submit" name="submit" class="myButton" style="margin-top:20px;font-size:50px;width:300px;height:100px; border-radius:10px;border:1px solid black;" value="Answer"/></p>
                            <input type="hidden" name="idQuestion" value="<?php echo $id;?>"/>
                            <img id='iconAnswer'  style="right:70px;" src='design/question.png' alt='question' title='question'/>
                        </form></p>
                        <p style="text-align:center;margin:auto;width:700px;"><i style="color:#555;font-size:14px;margin-right:150px;">Vous pouvez utiliser les abr&eacute;viations courantes telles que  sb, sth, qqn ou qqch...</i></p>
                        <?php }
                        else
                        {
                            $answer = htmlspecialchars($_POST['answer']);
                            $id = $_POST['idQuestion'];
                            $data = mysql_fetch_array(mysql_query("SELECT english, french FROM la13_words WHERE idWord=$id LIMIT 1;"));
                           
                            if($mode=="english")
                            {
                                $question = $data['english'];
                                $goodAnswer = $data['french'];
                            }
                            else
                            {
                                $question = $data['french'];        
                                $goodAnswer = $data['english'];
                            }

                            //acceptation des abreviations...
                            $answer = preg_replace("#\\\\#", "", $answer);
                            $answer = preg_replace("/(^sth[^a-z])|([^a-z]sth$)|([^a-z]sth[^a-z])/", " something ", $answer);
                            $answer = preg_replace("/(^qqch[^a-z])|([^a-z]qqch$)|([^a-z]qqch[^a-z])/", " quelque chose ", $answer);
                            $answer = preg_replace("/(^sb's[^a-z])|([^a-z]sb's$)|([^a-z]sb's[^a-z])/", " somebody's ", $answer);
                            $answer = preg_replace("/(^sb[^a-z])|([^a-z]sb$)|([^a-z]sb[^a-z])/", " somebody ", $answer);
                            $answer = preg_replace("/(^qqn[^a-z])|([^a-z]qqn$)|([^a-z]qqn[^a-z])/", " quelqu'un ", $answer);
                            $answer = preg_replace("/(^qqun[^a-z])|([^a-z]qqun$)|([^a-z]qqun[^a-z])/", " quelqu'un ", $answer);
                            $answer = preg_replace("/(^som's[^a-z])|([^a-z]som's$)|([^a-z]som's[^a-z])/", " someone's ", $answer);  
                            $answer = preg_replace("/(^som[^a-z])|([^a-z]som$)|([^a-z]som[^a-z])/", " someone ", $answer);      
                            $answer_f = preg_replace("/(^someone[^a-z])|([^a-z]someone$)|([^a-z]someone[^a-z])/", " somebody ", $answer);   
                            $answer_f = preg_replace("/(^I'm[^a-z])|([^a-z]I'm$)|([^a-z]I'm[^a-z])/", " I am ", $answer_f);
                            $answer_f = preg_replace("/(^i'm[^a-z])|([^a-z]i'm$)|([^a-z]i'm[^a-z])/", " I am ", $answer_f);
                            $answer_f = preg_replace("/(^It's[^a-z])|([^a-z]It's$)|([^a-z]It's[^a-z])/", " It is ", $answer_f); 
                            $answer_f = preg_replace("/(^it's[^a-z])|([^a-z]it's$)|([^a-z]it's[^a-z])/", " it is ", $answer_f);
                            $answer_f = preg_replace("/(^He's[^a-z])|([^a-z]He's$)|([^a-z]He's[^a-z])/", " He is ", $answer_f); 
                            $answer_f = preg_replace("/(^he's[^a-z])|([^a-z]he's$)|([^a-z]he's[^a-z])/", " he is ", $answer_f);
                            $answer_f = preg_replace("/(n't$)|(n't[^a-z])/", " not ", $answer_f );  
                            $answer_f = preg_replace("/(^She's[^a-z])|([^a-z]She's$)|([^a-z]She's[^a-z])/", " She is ", $answer_f); 
                            $answer_f = preg_replace("/(^she's[^a-z])|([^a-z]she's$)|([^a-z]she's[^a-z])/", " she is ", $answer_f); 
                            $answer_f = preg_replace("/(^I've[^a-z])|([^a-z]I've$)|([^a-z]I've[^a-z])/", " I have ", $answer_f);    
                            $answer_f = preg_replace("/(^i've[^a-z])|([^a-z]i've$)|([^a-z]i've[^a-z])/", " I have ", $answer_f);    
                            $answer_f = preg_replace("/(^I'll[^a-z])|([^a-z]I'll$)|([^a-z]I'll[^a-z])/", " I will ", $answer_f);
                            $answer_f = preg_replace("/(^i'll[^a-z])|([^a-z]i'll$)|([^a-z]i'll[^a-z])/", " I will ", $answer_f);
                            $answer_f = preg_replace("/(^what's[^a-z])|([^a-z]what's$)|([^a-z]what's[^a-z])/", " what is ", $answer_f);
                            $answer_f = preg_replace("/[,;:!.?\-*$%]/", "", $answer_f);
                            $answer_f = preg_replace ("/\s+/", " ", $answer_f);

                            $goodAnswer_f = $goodAnswer;
                            $goodAnswer_f = preg_replace("/[,;:!.?\-]/", "", $goodAnswer_f);

                            $ok = (strtoupper(trim($answer_f)) == strtoupper(trim($goodAnswer_f))) || (strtoupper(trim($answer_f)) == strtoupper(trim($goodAnswer))) || (strtoupper(trim($answer)) == strtoupper(trim($goodAnswer)))|| (strtoupper(trim($answer)) == strtoupper(trim($goodAnswer_f)));

                            if($ok)
                            {
                                mysql_query("INSERT INTO la13_answer VALUES((SELECT idUser FROM la13_user WHERE pseudoUser='$pseudo'),$id,1);");
                                $color = "#20ac20";
                                $image="right";
                                if(isset($_SESSION['totalAnswers']))
                                {
                                    $_SESSION['goodAnswers']++;
                                    $_SESSION['totalAnswers']++;
                                }
                                else
                                {
                                    $_SESSION['goodAnswers']=1;
                                    $_SESSION['totalAnswers']=1;
                                }
                            }
                            else
                            {
                                mysql_query("INSERT INTO la13_answer VALUES((SELECT idUser FROM la13_user WHERE pseudoUser='$pseudo'), $id,0);");
                                $color = "#d70505";
                                $image="wrong";
                                if(isset($_SESSION['totalAnswers']))
                                {
                                    $_SESSION['totalAnswers']++;
                                }
                                else
                                {
                                    $_SESSION['goodAnswers']=0;
                                    $_SESSION['totalAnswers']=1;
                                }
                            }
                            echo "<span style='margin-top:50px;display:inline-block;vertical-align:top;margin-bottom:20px;'><span style='vertical-align:top;width:220px;' class='titleText'>Question:&nbsp;</span><span style='display:inline-block;max-width:600px;'>".$question;
                            echo "</span></span><br/><span style='display:inline-block;vertical-align:top;margin-bottom:20px;'><span style='vertical-align:top;width:220px;' class='titleText'>Correct Answer:&nbsp;</span><span style=display:inline-block;max-width:600px;'>".$goodAnswer."</span></span>";
                            if($mode=="french" and ( strstr($_SERVER['HTTP_USER_AGENT'],'MSIE') or strstr($_SERVER['HTTP_USER_AGENT'], 'Chrome') or strstr($_SERVER['HTTP_USER_AGENT'], 'Firefox')))
                            {
                                ?>
                                <span id="listenButton" alt="Listen" title="Listen" onclick="playAudio();">&nbsp;</span>
                                <img src="./design/listen_h.png" style="position:absolute;top:-100000px;"/>     
                                <audio id="speech"  rel="noreferrer" src="audio.php?words=<?php echo $goodAnswer;?>"  replay="replay">Your browser does not support the audio element.</audio>
                                <?php
                            }
                            echo "<br/><span style='display:inline-block;vertical-align:top;'margin-top:20px;'><span class='titleText' style='width:220px;vertical-align:top;color:$color;'>Your Answer:&nbsp;</span><span style='display:inline-block;max-width:600px;'>".$answer;
                            echo "</span></span><br/><form name='resultForm' method='POST' action='index.php?week=".$week."&mode=".$mode."' style='margin:auto;text-align:center;'><input type='submit' name='submitOk' class='myButton' style=' margin-top:60px;margin-left:-150px;font-size:50px;width:300px;height:100px; border-radius:10px;border:1px solid black; ' id='submitOk' value=' OK '/></form>";
                            echo "<img id='iconAnswer' src='design/$image.png' alt='$image' title='$image'/>";
                        }
                    }
                    ?>
                </div>
                <div id="footer">
                    <?php
                    //affichage du nombre total de réponses
                    $data = mysql_fetch_array(mysql_query('SELECT COUNT(*) AS nb FROM la13_answer'));
                    echo '<p style="position:absolute; left: 20px;margin-top:8px;"><i style="color:#eee;font-size:12px;">' . $data['nb'] . ' questions r&eacute;pondues cette ann&eacute;e (record: 77k en 2012/2013)</i></p>';
    // ------- from siteduzero
    // ÉTAPE 1 : on vérifie si l'IP se trouve déjà dans la table.
    // Pour faire ça, on n'a qu'à compter le nombre d'entrées dont le champ "ip" est l'adresse IP du visiteur.
                    $retour = mysql_query('SELECT COUNT(*) AS nbre_entrees FROM connectes WHERE ip=\'' . $_SERVER['REMOTE_ADDR'] . '\'');
                    $donnees = mysql_fetch_array($retour);

    if ($donnees['nbre_entrees'] == 0) // L'IP ne se trouve pas dans la table, on va l'ajouter.
    {
        mysql_query('INSERT INTO connectes VALUES(\'' . $_SERVER['REMOTE_ADDR'] . '\', ' . time() . ')');
    }
    else // L'IP se trouve déjà dans la table, on met juste à jour le timestamp.
    {
        mysql_query('UPDATE connectes SET timestamp=' . time() . ' WHERE ip=\'' . $_SERVER['REMOTE_ADDR'] . '\'');
    }
    // -------
    // ÉTAPE 2 : on supprime toutes les entrées dont le timestamp est plus vieux que 5 minutes.
    // On stocke dans une variable le timestamp qu'il était il y a 5 minutes :
    $timestamp_5min = time() - (60 * 5); // 60 * 5 = nombre de secondes écoulées en 5 minutes
    mysql_query('DELETE FROM connectes WHERE timestamp < ' . $timestamp_5min);
    // -------
    // ÉTAPE 3 : on compte le nombre d'IP stockées dans la table. C'est le nombre de visiteurs connectés.
    $retour = mysql_query('SELECT COUNT(*) AS nbre_entrees FROM connectes');
    $donnees = mysql_fetch_array($retour);

    // Ouf ! On n'a plus qu'à afficher le nombre de connectés !
    if( $donnees['nbre_entrees'] >1)
        echo '<p style="margin:auto;text-align:center;margin-top:8px;"><i style="color:#eee;font-size:14px;">' . $donnees['nbre_entrees'] . ' utilisateurs connect&eacute;s</i></p>';
    else
        echo '<p style="margin:auto;text-align:center;margin-top:8px;"><i style="color:#eee;font-size:14px;">' . $donnees['nbre_entrees'] . ' utilisateur connect&eacute;</i></p>';
    ?>
    <span style="float:right; margin-right:10px; font-size:10px;color:#eee;margin-top:-15px;"><a href="http://valentin-hervieu.fr/projects">Valentin Hervieu</a> - UTC - <?php echo date("Y");?></span>
</div>
<script type='text/javascript'>
function playAudio()
{
    var audio = document.getElementById('speech');
    audio.currentTime = 0;
    audio.play();
}
</script>   
</body>
</html>