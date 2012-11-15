<?php
	class Server
	{
		protected $allowIPs = array();
		protected $denyIPs = array();
		protected $whitelist = false;
		protected $serverName = "Unnamed Chiori Framework Server";
		
		protected $firstCall = true;
		
		function __construct()
		{
			// TODO: Remove
			$this->serverName = "Apple Bloom Framework Server #1";
		}
		
		public function banIP($addr)
		{
			$this->denyIPs[] = $ipaddr;
		}
		
		public function unbanIP($addr)
		{
			foreach ( $this->denyIPs as $key => $val )
			{
				if ( strtolower($val) == strtoupper($addr) )
					unset ( $this->denyIPs[$key] );
			}
		}
		
		public function setWhitelist($bool)
		{
			if ( typeof($bool) != "Boolean" )
				return false;
			
			$this->whitelist = $bool;
		}
		
		public function getServerName()
		{
			return $this->serverName;
		}
		
		public function runSource( $source )
		{
			if ( empty( $source ) )
				return false;
			
			foreach (getFramework()->getConfig()->getArray("aliases", CONFIG_SITE) as $key => $val)
			{
				$keys[] = "%" . $key . "%";
				$vals[] = $val;
			}
			
			$source = str_replace($keys, $vals, $source);
			
			/*
			$disallowed = array("_", "__qca", "__cs_rr", "fc", "__utma", "__utmb", "__utmc", "__utmz", "argv", "argc", "erl");
			foreach ($GLOBALS as $key => $val)
			{
				if (strtolower($key) == $key && !in_array($key, $disallowed)) $keys .= ", \$" . $key;
			}
			
			$new_source = "global " . substr($keys, 2) . "; ?>" . $source . "<? ";
			
			$disallowed = array("GLOBALS", "_POST", "_GET", "_SERVER", "_FILES", "_COOKIE");
			$vars = array();
			foreach($GLOBALS as $k => $v)
			{
				if (!in_array($k, $disallowed))
					$vars[] = "$".$k;
			}
			return "global ".  join(",", $vars).";";
			*/
				
			$return = eval($new_source);
				
			if ( $return === false && ( $e = error_get_last() ) )
			{
				throw new RuntimeException($e["message"] . " on line " . $e["line"], 500);
				exit;
			}
		}
		
		// Logging System
		
		public function sendException( string $msg, $level = LOG_ERR )
		{
			$this->rawData( $msg, $level );
		}
		
		// TODO: Initate connection with daemon process and send information.
		
		function Debug($msg){$this->rawData($msg, LOG_DEBUG);}
		function Debug1($msg){$this->rawData($msg, LOG_DEBUG1);}
		function Debug2($msg){$this->rawData($msg, LOG_DEBUG2);}
		function Debug3($msg){$this->rawData($msg, LOG_DEBUG3);}
		function Info($msg){$this->rawData($msg, LOG_INFO);}
		function Notice($msg){$this->rawData($msg, LOG_NOTICE);}
		function Warning($msg){$this->rawData($msg, LOG_WARNING);}
		function Error($msg){$this->rawData($msg, LOG_ERR);}
		function Critical($msg){$this->rawData($msg, LOG_CRIT);}
		function Alert($msg){$this->rawData($msg, LOG_ALERT);}
		function Emergency($msg){$this->rawData($msg, LOG_EMERG);}
		
		private function getSeconds()
		{
			$mill_sec = round(microtime(true) - date("U"), 4);
			return (date("s") + $mill_sec) . str_repeat("0", 6 - strlen($mill_sec));
		}
		
		public function getLogLevelName ( $level )
		{
			switch ( $level )
			{
				case LOG_DISABLED:	$level = "Disabled "; break;
				case LOG_DEBUG3:	$level = "Debug 3  "; break;
				case LOG_DEBUG2:	$level = "Debug 2  "; break;
				case LOG_DEBUG1:	$level = "Debug 1  "; break;
				case LOG_DEBUG:		$level = "Debug    "; break;
				case LOG_INFO:		$level = "Info     "; break;
				case LOG_NOTICE:	$level = "Notice   "; break;
				case LOG_WARNING:	$level = "Warning  "; break;
				case LOG_ERR:		$level = "Error    "; break;
				case LOG_CRIT:		$level = "Critical "; break;
				case LOG_ALERT:		$level = "Alert    "; break;
				default:			$level = "Unknown  "; break;
			}
				
			return $level;
		}
		
		public function rawData ($message, $level = LOG_DEBUG)
		{
			// TODO: Add a Better Logger System.
				
			$log = "";
			$length = 100;
				
			if ( $this->firstCall )
				$log .= "\n\n<Log Message>" . str_repeat(" ", $length - 13) . "     <Time>        <Level>   <Line> <File>\n";
				
			$this->firstCall = false;
				
			$op = array();
				
			do
			{
				if ( strlen($message) > $length )
				{
					$op[] = substr($message, 0, $length);
					$message = substr($message, $length);
				}
				else
				{
					$op[] = $message;
					$message = "";
				}
			}
			while (!empty($message));
				
			$bt = debug_backtrace();
			$bt = $bt[1];
			$arr = array(date("h:i:") . $this->getseconds(), basename($bt["file"]), $bt["line"], $level);
				
			$log .= Colors::translateAlternateColors($op[0]) . Colors::RESET . str_repeat(" ", $length - strlen($op[0])) . " --> " . $arr[0] . " " . $this->getLogLevelName($arr[3]) . " " . $arr[2] . str_repeat(" ", 6 - strlen($arr[2])) . " " . $arr[1] . "\n";
				
			if (count($op) > 1)
			{
				for ($x=1;$x<=count($op);$x++)
				{
				if (!empty($op[$x]))
					$log .= Colors::translateAlternateColors($op[$x]) . Colors::RESET . "\n";
				}
				}
					
				if ($handle = fopen("/var/log/chiori.log", "a"))
				{
						fwrite($handle, $log);
						fclose($handle);
				}
				}
	}