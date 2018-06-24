<?php 

class FileManager 
{
	protected $path = "/home/kde";

	public function getDir($dir = "")
	{
		if (empty($dir))
			$dir = $this->path;	
		if (is_dir($dir))				
			chdir($dir);
		else chdir($this->path);
		$dir_tmp = getcwd();	
		if ( strpos( $dir_tmp, $this->path ) === false)
			$dir_tmp = $this->path;
		
		$ArrayDir 	= [];
		$ArrayFile 	= [];
		foreach (scandir($dir_tmp) as $value) {
			# code...
			if(filetype($dir_tmp . "/$value") === "dir")
				$ArrayDir[] = $value;
			else $ArrayFile[] = $value;
		}		
		$ArrayPath = explode (
			"/",
			preg_replace("/^" . addcslashes($this->path, '/') . "/i", "", $dir_tmp)
		);
		$ArrayPath = array_filter($ArrayPath);
		$limit = 1;
		$tmp_path = [];
		foreach ($ArrayPath as $key => $value) {
			$tmp_path[] = [
				$value,
				$this->path . "/" . implode("/", array_slice($ArrayPath, 0, $limit) )
			];
			$limit++;
		}
		//xprint($tmp_path);
		$res = [
			"Path" 	=> [
				"main" 		=> $this->path,
				"current" 	=> $dir_tmp,
				"bread" 	=> $tmp_path				
				
			],
			"Dir" 	=> $ArrayDir,
			"File" 	=> $ArrayFile
		];
		//xprint($res);
		return $res;
	}
}