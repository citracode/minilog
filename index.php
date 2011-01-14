<?php
//CONFIG
$entryxml = 'testentries.xml';
$tagxml = 'tag.xml';
$dateformat = "Y.m.d H:i:s"; //must use php date format: http://www.php.net/manual/en/function.date.php
if (!file_exists($entryxml)) exit("Failed to open $entryxml.");
$entxml = simplexml_load_file($entryxml);
//if (!file_exists($tagxml)) exit("Failed to open $tagxml.");
//$tagxml = simplexml_load_file($tagxml);
//END CONFIG

//FUNCTIONS

//END FUNCTIONS

//PROCESSING
if ("add" == $_GET['action'])
{
	 echo "woo add";
}
if ("tag" == $_GET['action'])
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

//END DISPLAY
foreach($entxml->entry as $entry)
{
	echo "<p>entry: ".$entry->text."</p>\n";
	echo "<p>date: ".$entry->date."</p>\n";
}

?>
