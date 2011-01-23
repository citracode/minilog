<html>
<head>
<title>minilog</title>
<meta name="viewport" content="width=device-width"/> 
</head>
<body>
<?php
//FUNCTIONS
function tagit($entry)
{

}
//END FUNCTIONS
//CONFIG
$entryxml = 'testentries.xml';
$tagxmlfile = 'tag.xml';
$dateformat = "Y.m.d H:i:s"; //must use php date format: http://www.php.net/manual/en/function.date.php
$viewingtags[0]=-1;
//open xml

$entxml = simplexml_load_file($entryxml) or die("Failed opening $entryxml: error was '$php_errormsg'"); ;
$tagxml = simplexml_load_file($tagxmlfile) or die("Failed opening $tagxmlfile: error was '$php_errormsg'");
//END CONFIG
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
		//Save XML to file - remove this and following line if save not desired
		$dom->save($entryxml);
	
		//begin processing for tags
		$tagcount = substr_count($_POST['entrytext'], '#');
		if($tagcount > 0)
		{
			//echo "woo add, and there are $tagcount tags in here.";
			preg_match_all('/(^|\s)#(\w+)/',$_POST['entrytext'],$tags);
			foreach($tags[2] as $tag)
			{
				if($tagxml->$tag)
				{
					//echo "\ntag $tag already exists!\n";
					$tagxml->$tag->addChild('index',($dom->getElementsByTagName("entry")->length-1));
				}
				else
				{
					//echo "\ntag $tag is new!\n";
					$tagxml->addChild($tag);
					$tagxml->$tag->addChild('index',($dom->getElementsByTagName("entry")->length-1));
				
				
				}
			}
			$dom = new DOMDocument('1.0');
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
			$dom->loadXML($tagxml->asXML());
			//Save XML to file - remove this and following line if save not desired
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
	$link="index.php?action=tag&tag=";
	$arcount=0;
	foreach($tagxml->children() as $tag)
	{
		$count=0;
		foreach($tag->index as $index)
		{
			$count++;
		}
		$tagarray[$arcount]['name']=$tag->getName();
		$tagarray[$arcount]['num']=$count;
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
<a href="index.php">Home</a> - <a href="index.php?action=viewtags">View Tags</a><br />
<form name="entryform" action="index.php?action=add" method="post">
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
