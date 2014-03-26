<?php
session_start();
mysql_connect("127.0.0.1","root", "root");
mysql_select_db("la13");

if(isset($_POST['english']))
	$add = true;
else
	$add = false;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Document sans titre</title>
</head>

<?php
echo "<body onLoad=\"document.forms['add'].elements['english'].focus()\">";
if($add)
{
	$english = mysql_real_escape_string(trim($_POST['english']));
	$english = str_replace("\\", "", $english);

	$french = mysql_real_escape_string(trim($_POST['french']));
	$french = str_replace("\\", "", $french );
	$help = mysql_real_escape_string(trim($_POST['indic']));
	$help  = str_replace("\\", "", $help );
	$week = intval($_POST['week']);
	if($english!="" and $french!="")
	{
		mysql_query("INSERT INTO la13(english, french, help) VALUES(\"$english\", \"$french\", \"$help\")");
		
		mysql_query("INSERT INTO la13_words(english, french, help, week) VALUES(\"$english\", \"$french\", \"$help\", $week)");
		echo "Element ajout&eacute;.";
	}
	else
		echo "<span style='color:red;'>Erreur lors de l'ajout</span>";
}
	?>
    <h3>Add a word/expression</h3>
    <form name="add" action="add_words.php" method="POST">
    <label for="english">English </label><input type="text" name="english" style="width:300px;" id="english"/><br/>
    <label for="french">French </label><input type="text" name="french" style="width:300px;" id="french"/><br/>
    <label for="indic">Help </label><input type="text" name="indic" style="width:300px;" id="indic"/><br/>
    <label for="week">Week </label><input type="text" name="week" style="width:300px;" id="week"/><br/>
    <input type="submit" name="submit" value="Add"/>
    </form>

</body>
</html	