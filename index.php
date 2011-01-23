<?php
include 'config.php';
//Before anything, verify if logged in
if("login"==$_GET['action'])
{
	$uri = "index.php?auth=".md5($_POST['password']."minilog");
	header("Location: $uri");
}

if(md5($password."minilog")!=$_GET['auth'])
{
echo "<html><head><title>Log in</title><meta name=\"viewport\" content=\"width=device-width\"></head><body>\n";
echo "<form name=\"loginform\" action=\"index.php?action=login\" method=\"post\">\n";
echo "<label for=\"entrytext\">Password:</label>\n";
echo "<input type=\"password\" name=\"password\"></input><br />\n";
echo "<input type=\"submit\" value=\"Log In\"/></form></body></html>";
exit;
}

?>

<html>
<head>
<title>minilog</title>
<meta name="viewport" content="width=device-width"/> 
</head>
<body>

<?php
//open xml
$entxml = simplexml_load_file($entryxml) or die("Failed opening $entryxml: error was '$php_errormsg'");
$tagxml = simplexml_load_file($tagxmlfile) or die("Failed opening $tagxmlfile: error was '$php_errormsg'");
$viewingtags[0]=-1;

//FUNCTIONS
function tagit($passedtagxml,$entry,$entryid)
{
	preg_match_all('/(^|\s)#(\w+)/',$entry,$tags);
	foreach($tags[2] as $tag)
	{
		if($passedtagxml->$tag)
		{
			//echo "\ntag $tag already exists!\n";
			$passedtagxml->$tag->addChild('index',$entryid);
		}
		else
		{
			//echo "\ntag $tag is new!\n";
			$passedtagxml->addChild($tag);
			$passedtagxml->$tag->addChild('index',$entryid);				
		}
	}
}
//END FUNCTIONS
//PROCESSING
if ("add" == $_GET['action']) //adding an entry to xml
{
	if(empty($_POST['entrytext']))
	{
		echo "<div class=\"error\">No entry found, did you put anything in the entry box?</div>";
	}
	else
	{
		$newentry = $entxml->addChild('entry');
		$newentry->addChild('text',$_POST['entrytext']);
		$newentry->addChild('date',date($dateformat));
		$dom = new DOMDocument('1.0');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($entxml->asXML());
		$dom->save($entryxml);
	
		//begin processing for tags
		$tagcount = substr_count($_POST['entrytext'], '#');
		if($tagcount > 0)
		{
			tagit($tagxml,$_POST['entrytext'],$dom->getElementsByTagName("entry")->length-1);			
			$dom = new DOMDocument('1.0');
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
			$dom->loadXML($tagxml->asXML());
			$dom->save($tagxmlfile);		
		}
	}
}
if ("tag" == $_GET['action'] && isset($_GET['tag']))  //if viewing specific tag entries
{
	 array_pop($viewingtags);
	 foreach($tagxml->$_GET['tag']->index as $index)
	 {
	 	array_push($viewingtags,intval($index));
	 }
	 rsort($viewingtags);
}
if ("viewtags" == $_GET['action'])  //if usr wants to see all tags
{
	$link="index.php?auth=".$_GET['auth']."&action=tag&tag=";
	$arcount=0;
	foreach($tagxml->children() as $tag)
	{
		$count=0;
		foreach($tag->index as $index)
		{
			$count++;
		}
		
		$tagarray[$arcount]['num']=$count;
		$tagarray[$arcount]['name']=$tag->getName();
		$arcount++;
	}
	rsort($tagarray);
	foreach($tagarray as $tag)
	{
		echo "<a href=\"".$link.$tag['name']."\">".$tag['name']."</a>(".$tag['num'].") ";
	}
	echo "\n<br />\n";
	
	
}
//END PROCESSING
//FORM 
?>
<a href="index.php?auth=<?php echo $_GET['auth'];?>">Home</a> - <a href="index.php?auth=<?php echo $_GET['auth'];?>&action=viewtags">View Tags</a><br />
<form name="entryform" action="index.php?auth=<?php echo $_GET['auth'];?>&action=add" method="post">
<label for="entrytext">Entry:</label><br />
<textarea name="entrytext" rows="5" cols="30"></textarea><br />
<input type="submit" value="Add Entry"/>
</form>
<?php if(-1!=$viewingtags[0]): //if viewing 1 tag ?>
<p>Entries for Tag: <strong>#<?php echo $_GET['tag']; ?></strong></p>
<?php foreach($viewingtags as $tagindex): ?>

<div class="entry">
	<div class="entrytxt"><?php echo $entxml->entry[$tagindex]->text; ?></div>
	<span class="date"><?php echo $entxml->entry[$tagindex]->date; ?></span>
</div>
<? endforeach; ?>

<?php else: //if standard viewing entries ?>
<?php 
//END FORM
	$dom = new DOMDocument('1.0');
	$dom->preserveWhiteSpace = false;
	$dom->formatOutput = true;
	$dom->loadXML($entxml->asXML());
	$numentries = $dom->getElementsByTagName("entry")->length-1;
?>
<?php for($i=$numentries;$i>-1;$i--): ?>

<div class="entry">
	<div class="entrytxt"><?php echo $entxml->entry[$i]->text; ?></div>
	<span class="date"><?php echo $entxml->entry[$i]->date; ?></span>
</div>
<?php endfor; ?>
<?php endif; ?>

</body>
</html>
