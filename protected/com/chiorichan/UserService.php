<?php
	class UserService
	{
		protected $db;
		
		function __construct()
		{
			$this->db = getFramework()->getConfig()->getDataBase();
		}
		
		public function getUserbyName(string $userName)
		{
			
			
			if ( $userName == null || empty($userName) )
				return null;
			
			
			
		}
	}