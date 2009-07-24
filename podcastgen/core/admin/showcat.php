<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella
# http://podcastgen.sourceforge.net
# 
# This is Free Software released under the GNU/GPL License.
############################################################

########### Security code, avoids cross-site scripting (Register Globals ON)
if (isset($_REQUEST['absoluteurl']) OR isset($_REQUEST['amilogged']) OR isset($_REQUEST['theme_path'])) { exit; } 
########### End

### Check if user is logged ###
	if ($amilogged != "true") { exit; }
###

include ("$absoluteurl"."components/xmlparser/loadparser.php");
include ("$absoluteurl"."core/admin/readXMLcategories.php");

if (file_exists("$absoluteurl"."categories.xml") AND isset($parser->document->category)) {

	///// DETERMINE NEW PODCAST OR EDIT PODCAST MODE
	if (isset($_GET['do']) AND $_GET['do']=="edit") { //if edit mode

		$preselectcat = "yes"; //this variable will preselect categories assigned to an episode

	} else {
		$preselectcat = "no";
	}
	/////

	// define variables
	$arr = NULL;
	$arrid = NULL;
	$n = 0;

	foreach($parser->document->category as $singlecategory)
	{
		//echo $singlecategory->id[0]->tagData."<br>";
		//echo $singlecategory->description[0]->tagData;

		$arr[] .= $singlecategory->description[0]->tagData;
		$arrid[] .= $singlecategory->id[0]->tagData;
		$n++;
	}



	$PG_mainbody .= '<label for="category">'.$L_category.'*</label><br />
		<span class ="admin_hints">'.$L_categoryfieldhint.'</span><br />';
	if ($preselectcat == "yes") {
		$PG_mainbody .= '<span class ="admin_hints">'.$L_catpreselected.'</span><br />';
	}
	$PG_mainbody .= '<br /><select name="category[]"';

	if ($n<5) { //height of the category form
		$PG_mainbody .= 'size="'.$n.'" ';
	} else {

		$PG_mainbody .= 'size="5" '; //standard height if more than 5 categories
	}

	$PG_mainbody .=  'multiple id="category"  onchange="checkMaxSelected(this, 3, \''.$L_nummaxcat.'\');">'; // 3 = max category number... if u change this value, you should also change php code in other files...


	natcasesort($arr); // Natcasesort orders more naturally and is different from "sort", which is case sensitive

	$firstselect = 0; //value to determine the first category of the form, which will be selected by default

	foreach ($arr as $key => $val) {
		//$PG_mainbody .= "cat[" . $key . "] = " . $val . "<br>";

		if ($firstselect == "0" AND $preselectcat != "yes") { //pre-select the first category (except in edit mode which is set in this var: $preselectcat == "yes")

		$PG_mainbody .= '<option value=\'' . $arrid[$key] . '\' selected>' . $val .'</option>';
	}
	else { // other arrays not pre-selected
		$PG_mainbody .= '<option value=\'' . $arrid[$key] . '\' ';

		if ($preselectcat == "yes") {

			if ($text_category1 == $arrid[$key] OR $text_category2 == $arrid[$key] OR $text_category3 == $arrid[$key]) {

				$PG_mainbody .= 'selected';

			}
		}	
		$PG_mainbody .= '>' . $val .'</option>';
	}

	$firstselect++; //increment 

}

$PG_mainbody .= '</select><br /><br /><br />';

} //if xml categories file doesn't exist
else
{
	$PG_mainbody .= '<p><b>'.$L_catfileerror.'</b></p>';

	$PG_mainbody .= '
		<form action="?p=admin&amp;do=categories&amp;action=add" method="POST" enctype="multipart/form-data" name="categoryform" id="categoryform" onsubmit="return submitForm();">

		<br /><br />
		<label for="addcategory"><b>'.$L_addnewcat.'</b></label><br />
		<input name="addcategory" id="addcategory" type="text" size="50" maxlength="255" ><br />

		<input type="submit" value="'.$L_add.'" onClick="showNotify(\''.$L_adding.'\');">
		';
}



?>