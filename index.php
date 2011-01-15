<?php
//FUNCTIONS

//END FUNCTIONS
//CONFIG
$entryxml = 'testentries.xml';
$tagxml = 'tag.xml';
$dateformat = "Y.m.d H:i:s"; //must use php date format: http://www.php.net/manual/en/function.date.php
//open xml

$entxml = simplexml_load_file($entryxml) or die("Failed opening $entryxml: error was '$php_errormsg'"); ;
//$tagxml = simplexml_load_file($tagxml) or die("Failed opening $tagxml: error was '$php_errormsg'"); ;;
//END CONFIG
//PROCESSING
if ("add" == $_GET['action'])
{
	echo "woo add";
	$newentry = $entxml->addChild('entry');
	$newentry->addChild('text',$_POST['entrytext']);
	$newentry->addChild('date',date($dateformat));
	$dom = new DOMDocument('1.0');
	$dom->preserveWhiteSpace = false;
	$dom->formatOutput = true;
	$dom->loadXML($entxml->asXML());
	//Save XML to file - remove this and following line if save not desired
	$dom->save($entryxml);

}
if ("tag" == $_GET['action'] && isset($_GET['tag']))
{
	 echo "woo tag";
}
//END PROCESSING
//FORM
?>
<form name="entryform" action="index.php?action=add" method="post">
<label for="entrytext">Entry:</label><br />
<textarea name="entrytext" rows="5" cols="30"></textarea><br />
<input type="submit" value="Add Entry"/>
</form>
<?php
//END FORM
//DISPLAY
foreach($entxml->entry as $entry)
{
	echo "<p>entry: ".$entry->text."</p>\n";
	echo "<p>date: ".$entry->date."</p>\n";
}
//END DISPLAY
?>
