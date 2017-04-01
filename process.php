<?php
	session_start();
	ini_set('max_execution_time', 300);
    set_time_limit(60);
	include_once("FilesMgr.php");
	
	$m_BaseFolder = htmlspecialchars($_POST['l_FolderName']);
	
	$l_ResultArray = getDirContents(htmlspecialchars($_POST['l_FolderName']));
	
	foreach($l_ResultArray as $l_Element)
	{
		if (!is_dir($l_Element))
		{
			$l_PathForCorrection = GetNewPath($l_Element);
			
			$l_CleanTypes = array("CleanOnlyB", "CleanBoth");
			
			foreach ($l_CleanTypes as $l_Type)
				CreateCorrectionFile($l_PathForCorrection, $l_Type, $l_Element);
		}
	}
?>