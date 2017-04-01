<?php
	
	function getDirContents($dir, &$results = array())
	{
		$files = scandir($dir);
	 
		foreach($files as $key => $value)
		{
			$path = realpath($dir.DIRECTORY_SEPARATOR.$value);
			
			if(!is_dir($path))
				$results[] = $path;
			else if($value != "." && $value != "..")
			{
				getDirContents($path, $results);
				$results[] = $path;
			}
		}
	 
		return $results;
	}
	
	function GetNewPath($p_Path)
	{
		$l_NewArray = explode("\\", $p_Path);
		
		$l_NewPath = "";
		
		foreach ($l_NewArray as $l_Index => $l_Folder)
		{
			if ($l_Index < (count($l_NewArray) - 1))
				$l_NewPath .= $l_Folder . "/";
		}
		
		return $l_NewPath;
	}
	
	function GetFileName($p_Path)
	{
		$l_NewArray = explode("\\", $p_Path);
		
		$l_Name = "";
		
		foreach ($l_NewArray as $l_Index => $l_File)
		{
			if ($l_Index == (count($l_NewArray) - 1)) ///< Last element of table, so end of path string
				$l_Name = $l_File;
		}
		
		return $l_Name;
	}
	
	/// Needs more work
	function Delete($path)
	{
		if (is_dir($path) === true)
		{
			$files = array_diff(scandir($path), array('.', '..'));

			foreach ($files as $file)
			{
				Delete(realpath($path) . '/' . $file);
			}

			return rmdir($path);
		}
		else if (is_file($path) === true)
			return unlink($path);

		return false;
	}
	
	function CreateCorrectionFile($p_Path, $p_CleanType, $p_File)
	{
		/// Fill path with correction directory
		$l_FileName = GetFileName($p_File);
	   
		/// Allow directory creation if it doesn't already exist
		if (!file_exists($p_Path . $p_CleanType))
			mkdir($p_Path . $p_CleanType, 0777);
	   
		$p_CleanType .= "/";
		$l_FullPath = $p_Path . $p_CleanType . $l_FileName;
		
		$l_Lines = file($p_Path . $l_FileName);
		
		foreach ($l_Lines as $l_LineIndex => $l_Line)
		{
			$l_Lines[$l_LineIndex] = preg_replace("/\(.{1,3}\)/",        "",             $l_Line); ///< All (*)
			$l_Lines[$l_LineIndex] = preg_replace("/\[.{1,3}\]/",        "",             $l_Lines[$l_LineIndex]); 
			$l_Lines[$l_LineIndex] = preg_replace("/<overlap \/>/",      "<overlap>",    $l_Lines[$l_LineIndex]); ///< Attempted formatting
			$l_Lines[$l_LineIndex] = preg_replace("/ er /",              " ",            $l_Lines[$l_LineIndex]); ///< Misc
			$l_Lines[$l_LineIndex] = preg_replace("/ em /",              " ",            $l_Lines[$l_LineIndex]); ///< Misc
			$l_Lines[$l_LineIndex] = preg_replace("/ hr /",              " ",            $l_Lines[$l_LineIndex]); ///< Misc
			$l_Lines[$l_LineIndex] = preg_replace("/ mhm /",             " ",            $l_Lines[$l_LineIndex]); ///< Misc
			$l_Lines[$l_LineIndex] = preg_replace("/ mm /",              " ",            $l_Lines[$l_LineIndex]); ///< Misc
			$l_Lines[$l_LineIndex] = preg_replace("/ um /",              " ",            $l_Lines[$l_LineIndex]); ///< Misc
			$l_Lines[$l_LineIndex] = preg_replace("/ uhm /",             " ",            $l_Lines[$l_LineIndex]); ///< Misc
			$l_Lines[$l_LineIndex] = preg_replace("/=/",                 "",             $l_Lines[$l_LineIndex]); ///< Misc
			$l_Lines[$l_LineIndex] = preg_replace("/\<[^\/A-B<]*\>/",    "",             $l_Lines[$l_LineIndex]); ///< Try to clean all <*> content except <A*>, <B*>, </*>
			$l_Lines[$l_LineIndex] = preg_replace("/<\/F\/>/",           "",             $l_Lines[$l_LineIndex]); ///< Try to clean </F/>
			$l_Lines[$l_LineIndex] = preg_replace("/<\/S>/",             "",             $l_Lines[$l_LineIndex]); ///< Try to clean </S>
			
			if ($p_CleanType == "CleanOnlyB/")
			{
				$l_Lines[$l_LineIndex] = preg_replace("/\<[^\/B](.*)\>\r\n/", "\1", $l_Lines[$l_LineIndex]); ///< Clean all <A> Lines
				$l_Lines[$l_LineIndex] = preg_replace("/<B>/", "", $l_Lines[$l_LineIndex]); ///< Remove all <B>
				$l_Lines[$l_LineIndex] = preg_replace("/<\/B>/", "", $l_Lines[$l_LineIndex]); ///< Remove all </B>
			}
		}
		
		if (!file_exists($l_FullPath))
			file_put_contents($l_FullPath, $l_Lines);
	}
?>