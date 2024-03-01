<?php

if(!class_exists('Path'))
{

	/**
	 * A utility for manipulating paths
	 * History<br />
	 * 2006.08.16 - psyjoniz - initial revision<br />
	 * @package utils
	 * @author psyjoniz (jesse@psy-core.com)
	 * @version 0.00
	 */

	class Path {
		
		/**
		 * Does everything to a path to make sure it's valid based on server OS. <br />
		 * For example, on *nix, this method ensures a slash are at the beginning *and* <br />
		 * end of your path.
		 * History<br />
		 * 2006.08.16 - psyjoniz - initial revision<br />
		 * @author psyjoniz (jesse@psy-core.com)
		 * @param string $path The path you want to repair
		 * @return string The repaired path
		 * @since Version 0.00 alpha
		 * @version 0.00 alpha
		 */
		static function repair($path){
			if(substr($path,-1,1) != '/'){
				$path = $path.'/';
			}
			//if(strpos(strtolower(PHP_OS),'win') === false){
			if(false === DetectOS::isWindows()){
				if(substr($path,0,1) != '/'){
					$path = '/'.$path;
				}
			}
			return $path;
		}
		
		/* 2006.11.28 - psy */
		static function getFileType($location) {
			$tmp = explode('\.', $location);
			$type = StringUtil::makeSafe($tmp[ ( count($tmp) - 1 ) ]);
			$return = array('type' => $type);
			return $return;
		}
		
		/* 2006.11.28 - psy */
		static function getFileName($location) {
			//Debug::analyze($location);
			$tmp = explode('/', $location);
			$fullname = $tmp[ ( count($tmp) - 1 ) ];
			//Debug::analyze($fullname);
			$tmp2 = explode('\.', $fullname);
			$name = trim($tmp2[0]);
			$return = array('fullname' => $fullname, 'name' => $name);
			//Debug::analyze($return);
			return $return;
		}
		
		/* 2007.01.18 - psyjoniz - if it doesn't find the file right away, churn through include_path dir's - if we find it there, we return the filename as a string otherwise return false. */
		/* 2007.5.8 - psy - added a check into sup psy-core directory */
		static function findFile($filename) {
			//echo('Path::findFile(): $filename: ' . $filename . '<br />' . chr(10));
			if(false === $filename) {
				return $filename; //if we are coming in with a false value for filename spit it right back out
			}
			if(file_exists($filename)) {
				return $filename; //duhhhhhhhhh
			}
			$found = array();
			$sep = DetectOS::isWindows() ? ';' : ':';
			$dirs = explode($sep, ini_get('include_path'));
			$count_dirs = count($dirs);
			//echo('Path::findFile(): $dirs:<pre>' . print_r($dirs, true) . '</pre>' . chr(10));
			for($x = 0; $x < $count_dirs; $x++) {
				$cur_name = $dirs[$x] . $filename;
				//echo('Path::findFile(): $cur_name: ' . $cur_name . '<br />' . chr(10));
				if(file_exists($cur_name)) {
					$found[] = $cur_name;
				} else {
					$cur_name = $dirs[$x] . '/' . $filename;
					//echo('Path::findFile(): $cur_name: ' . $cur_name . '<br />' . chr(10));
					if(file_exists($cur_name)) {
						$found[] = $cur_name;
					} else {
						//try looking in a relative site dir
						//--except we don't have access to what site we are on when in here..
						//try looking in a relative psy-core dir
						$psycoreDir = $dirs[$x] . '/psy-core/' . $filename;
						if(file_exists($psycoreDir)) {
							$found[] = $psycoreDir;
						}
					}
				}
			}
			//echo('Path::findFile(): $found:<pre>' . print_r($found, true) . '</pre>' . chr(10));
			$count_found = count($found);
			if($count_found == 0) {
				return false; //gosh, all that hard work and the file is nowhere to be found.  did you bother to check the case?  you suck.  no file for you.  :P
			}
			if($count_found == 1) {
				return $found[0];
			}
			//ok, if we found multiple, whatever, just return the first one......
			//if/when we run into a scenario where this is undesireable, we'll deal with it then EOL
			return $found[0];
		}

		static function getBaseDir()
		{
			return $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['PHP_SELF']);
		}
		
	}

}
