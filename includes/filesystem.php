<?php

/**
* @package Canvas
*/

/**
* @description FileSystem is used to handle interactions with the file system.
*/

class FileSystem extends SkyBlueObject {

    /*
    * @description   Class constructor
    * @param string  The file to buffer
    * @return string The file contents
    */
    
    function buffer($file) {
        if (!file_exists($file)) return null;
        ob_start();
        include($file);
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }
    
    /*
    * @description  Reads a SkyBlueCanvas config ini file
    * @param        The file path
    * @return       An INI array
    */
    
    function read_config($file) {
        $ini = array();
        $tmp = FileSystem::buffer($file);
        if (!empty($tmp)) {
            $lines = explode("\n", trim($tmp));
            foreach ($lines as $line) {
                $bits = explode("=", $line);
                if (count($bits) != 2) continue;
                $left  = trim($bits[0]);
                $right = trim($bits[1]);
                $isArray = false;
                if (substr($left, strlen($left)-2, strlen($left)-1) == "[]") {
                    $isArray = true;
                }
                if ($isArray) {
                    $left = str_replace("[]", "", $left);
                    $ini[$left][] = $right;
                }
                else {
                    $ini[$left] = $right;
                }
            }
        }
        return $ini;
    }
    
    /*
    * @description   Writes the provided string data to the specified file
    * @param string  The file to write
    * @param string  The string contents of the file
    * @param string  The file mode
    * @return bool   Whether or not the file was written
    */
    
    function write_file($file, $str, $mode='w+') {
        if (file_exists($file) && !is_writable($file)) return false;
        $fp = fopen($file, $mode);
        if (!$fp) return false;
        if (!fwrite($fp, $str)) return false;
        fclose($fp);
        return true;
    }
    
    /*
    * @description   Reads the contents of a file
    * @param string  The file to read
    * @return string The file contents
    */
    
    function read_file($file) {
        if (!file_exists($file) || !is_readable($file)) return null;
        $str = "";
        $fp = fopen($file, 'r+');
        if (!$fp) return false;
        if (filesize($file) > 0) {
            $str = fread($fp, filesize($file));
        }
        return $str;
    }
    
    /*
    * @description   Moves/renames a file
    * @param string  The original file location
    * @param string  The desitination location of the file
    * @return bool   Whether or not the file was moved
    */
    
    function move_file($from, $to) {
        if (!file_exists($to)) {
            return rename($from, $to);
        }
        return false;
    }
    
    /*
    * @description  Lists all files in a directory
    * @param string The directory to list
    * @param bool   Whether or not to list sub-directories
    * @param array  Files already listed (for recursion)
    * @return array An array of the files in the directory
    */
    
    function list_files($dir, $recurse=0, $files=array()) {
        ini_set('max_execution_time', 10);
        if (!is_dir($dir)) {
            die ("$dir is not a valid directory in " . __METHOD__);
        }
        if ($root = @opendir($dir)) {
            while ($file = readdir($root)) {
                if ($file{0} == '.') continue;
                if (is_dir($dir.$file)) {
                    if ($recurse == 0) continue;
                    $files = array_merge(
                        $files, 
                        FileSystem::list_files(
                            $dir.$file.'/', 
                            $recurse
                        )
                    );
                } 
                else {
                    array_push($files, $dir.$file);
                }
            }
        }
        sort($files);
        return $files;
    }
    
    /*
    * @description  Lists all directories in a directory
    * @param string The directory to list
    * @param bool   Whether or not to list sub-directories
    * @param array  Directories already listed (for recursion)
    * @return array An array of the directories in the root directory
    */
    
    function list_dirs($dir, $recurse=1, $dirs=array()) {
        ini_set('max_execution_time', 10);
        if (!is_dir($dir)) {
            die ("$dir is not a valid directory in " . __METHOD__);
        }
        if ($root = @opendir($dir)) {
            while ($file = readdir($root)) {
                if ($file{0}=='.') continue;
                if (is_dir($dir.$file)) {
                    $dirs[] = $dir.$file.'/';
                    if ($recurse == 1) {
                        $dirs = array_merge(
                            $dirs, 
                            FileSystem::list_dirs(
                            $dir.$file.'/', 
                            $recurse)
                        );
                    }
                } 
                else {
                    continue;
                }
            }
        }
        return $dirs;
    }
    
    /*
    * @description  Copies a directory and all enclosed files
    * @param string The directory to copy
    * @param string The location to copy to
    * @return bool  Whether or not the directory was copied
    */
    
    function copy_dir($srcdir, $dstdir) {
        if (!is_dir($srcdir))
            return false;
        if (!is_dir($dstdir))
            mkdir($dstdir);

        if ($curdir = opendir($srcdir)) {
            while ($file = readdir($curdir)) {
                if ($file{0} != '.') {
                    $srcfile = $srcdir. '/' . $file;
                    $dstfile = $dstdir . '/' . $file;
                    if (is_file($srcfile)) {
                        if (copy($srcfile, $dstfile)) {
                            touch($dstfile, filemtime($srcfile));
                            chmod($dstfile, 0777);
                        }
                        else
                            return false;
                    }
                    else if (is_dir($srcfile)) {
                        if (!FileSystem::copy_dir($srcfile, $dstfile))
                            return false;
                    }
                }
            }
            closedir($curdir);
        }
        return true;
    }
    
    /*
    * @description  Copies a file
    * @param string The file to copy
    * @param string The location to copy to
    * @return bool  Whether or not the file was copied
    */
    
    function copy_file($from, $to) {
        if (!file_exists($from) || file_exists($to)) {
            return false;
        }
        return copy($from, $to);
    }
    
    /*
    * @description  Deletes a file
    * @param string The file to delete
    * @return bool  Whether or not the file was deleted
    */
    
    function delete_file($file) {
        if (!is_dir($file)) {
            return unlink($file);
        }
        return false;
    }
    
    /*
    * @description  Deletes all the files in a directory, including
    *               optionally the directory itself.
    * @param string Name of directory to delete files from
    * @param bool   True => delete the directory also (default: false)
    * @return bool  Whether or not the file was deleted
    */
    
    function delete_files($dir, $delDir = false) {
        if (!$dh = @opendir($dir))
            return;

        while (false !== ($obj = readdir($dh))) {
            if($obj == '.' || $obj == '..')
                continue;
    
            if (!@unlink($dir . '/' . $obj))
                FileSystem::delete_files($dir.'/'.$obj, true);
        }
    
        closedir($dh);
    
        if ($delDir)
            @rmdir($dir);
    } 
    
    /*
    * @description   Deletes the contents of a directory and, optionally, the directory.
    * @param string  The directory to delete
    * @return bool   Whether or not the directory was deleted
    */
    
    function delete_dir($dir, $contentsOnly=true) {
        if (!is_dir($dir)) {
            return false;
        }
        $files = FileSystem::list_files($dir, true);
        for ($i=0; $i<count($files); $i++) {
            unlink($files[$i]);
        }
        $subdirs = FileSystem::list_dirs($dir);
        rsort($subdirs);
        for ($i=0; $i<count($subdirs); $i++) {
            rmdir($subdirs[$i]);
        }
        if (!$contentsOnly)
            return rmdir($dir);
    }
    
    /*
    * @description   Creates a directory
    * @param string  The directory to create
    * @return bool   Whether or not the directory was created
    */
    
    function make_dir($dir) {
        if (is_dir($dir)) {
            return false;
        }
        return mkdir($dir, 0775);
    }
    
    /*
    * @description   Gets the last modified time of the file
    * @param string  The file name
    * @return int    The Unix time of the last modification
    */
    
    function file_time($file) {
        if (file_exists($file)) {
            return filectime($file);
        }
        return null;
    }
    
    /*
    * @description   Parses an ini file in PHP ini format
    * @param string  The ini file name
    * @return array  An associative array of the ini settings
    */
    
    function parse_config_file($file) {
        return parse_ini_file($file);
    }
    
    /*
    * @description   Updates the file modified time to the current time
    * @param string  The file name
    * @return bool   Whether or not the file time was updated
    */
    
    function touch_file($file) {
        return touch($file);
    }
    
    /*
    * @description   Determines if a file is writable
    * @param string  The file name
    * @return bool   Whether or not the file is writable
    */
    
    function writable($file) {
        return is_writable($file);
    }
    
    /*
    * @description   Determines if a file is readable
    * @param string  The file name
    * @return bool   Whether or not the file is readable
    */
    
    function readable($file) {
        return is_readable($file);
    }
    
    /*
    * @description   Makes a file name file system safe (no spaces or illegal chars)
    * @param string  The file name
    * @return string Safe file name with no spaces or special chars
    */
    
    function safe_file_name($name) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-.";
        for ($i=0; $i<strlen($name); $i++) {
            if (strpos($chars, $name{$i}) === false) {
                $name{$i} = "_";
            }
        }
        return $name;
    }
    
    /*
    * @description   Gets the mime type of the file
    * @param string  The file path
    * @return string The file's mime type
    */
    
    function file_type($file) {
        if (function_exists("mime_content_type")) {
            return mime_content_type($filename);
        }

        $disabled = ini_get('disabled_functions');
        $disarr = explode(',', $disabled);
        if (!in_array('exec', $disarr)) {
            $type = trim(exec('file -bi ' . $file, $out, $rv));
            if ($rv == 0)
                return $type;
        }

        preg_match("|\.([a-z0-9]{2,4})$|i", $file, $fileSuffix);

        switch(strtolower($fileSuffix[1])) {
        case "js":
            return "application/x-javascript";

        case "json":
            return "application/json";

        case "jpg": case "jpeg": case "jpe":
            return "image/jpeg";

        case "png":
        case "gif":
        case "bmp":
        case "tiff":
            return "image/".strtolower($fileSuffix[1]);

        case "css" :
            return "text/css";

        case "xml" :
            return "application/xml";

        case "doc": case "docx":
            return "application/msword";

        case "xls": case "xlt": case "xlm": case "xld":
	    case "xla": case "xlc": case "xlw": case "xll":
            return "application/vnd.ms-excel";

        case "ppt": case "pps":
            return "application/vnd.ms-powerpoint";

        case "rtf":
            return "application/rtf";

        case "pdf":
            return "application/pdf";

        case "html":  case "htm": case "php":
            return "text/html";

        case "txt":
            return "text/plain";

        case "mpeg": case "mpg": case "mpe":
            return "video/mpeg";

        case "mp3":
            return "audio/mpeg3";

        case "wav":
            return "audio/wav";

        case "aiff": case "aif":
            return "audio/aiff";

        case "avi" :
            return "video/msvideo";

        case "wmv" :
            return "video/x-ms-wmv";

        case "mov" :
            return "video/quicktime";

        case "zip" :
            return "application/zip";

        case "tar" :
            return "application/x-tar";

        case "swf" :
            return "application/x-shockwave-flash";

        default :
            return "unknown/" . $fileSuffix[1];
        }
    }
    
    /*
    * @description   Gets the file extension
    * @param string  The file path
    * @return string The file extension
    */
    
    function file_ext($file) {
	    $path_info = pathinfo($file);
        if (isset($path_info['extension'])) {
            return $path_info['extension'];
        }
        return end(explode('.', $file));
    }
    
    /*
    * @description   Gets the file permissions
    * @param string  The file path
    * @return string The file permissions in format 0777
    */
    
    function file_perms($file) {
        if (!file_exists($file)) {
            return '000';
        }
        return substr(decoct(fileperms($file)), 3);
    }
    
    /*
    * @description Gets the Effective User ID of the PHP Engine
    * @return int  The Effective User ID of the PHP Engine
    */
    
    function process_uid() {
		return posix_geteuid();
	}
	
    /*
    * @description Gets the Effective Group ID of the PHP Engine
    * @return int  The Effective User Group of the PHP Engine
    */
	
	function process_gid() {
		return posix_getegid();
	}
	
    /*
    * @description  Gets Group Info of the PHP Engine
    * @return array The Group Info of the PHP Engine
    */
	
	function process_info() {
		return posix_getgrgid(posix_getegid());
	}
	
    /*
    * @description  Gets Groups members of the PHP Engine
    * @return array The Group members of the PHP Engine
    */
	
	function process_members() {
		$info = FileSystem::process_info();
		if (isset($info['members'])) {
			return $info['members'];
		}
		return array();
	}
	
    /*
    * @description   Gets name of the file owner
    * @return string The name of the file owner
    */
	
	function file_owner($file) {
		$info = FileSystem::file_info($file);
		if (is_array($info)) {
			if (isset($info['name'])) {
				return $info['name'];
			}
			else if (isset($info['uid'])) {
				return $info['uid'];
			}
		}
		return null;
	}
	
    /*
    * @description Gets User ID of the file owner
    * @return int  The user ID of the file owner
    */
	
	function file_uid($file) {
		$info = FileSystem::file_info($file);
		if (is_array($info)) {
			if (isset($info['uid'])) {
				return $info['uid'];
			}
		}
		return null;
	}
	
    /*
    * @description Gets Group ID of the file owner
    * @return int  The user Group of the file owner
    */
	
	function file_group($file) {
		$info = FileSystem::file_info($file);
		if (is_array($info) && isset($info['gid'])) {
			return $info['gid'];
		}
		return null;
	}
	
    /*
    * @description  Gets Info array of the file owner
    * @return array The Info array of the file owner
    */
	
	function file_info($file) {
		return posix_getpwuid(fileowner($file));
	}
    
}

?>