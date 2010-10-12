<?php

// This enables full error reporting. Comment it when ilmomasiina is in product
//require_once("ErrorReportEnabler.php");

/**
 * Global configurations
 */
class Configurations {
	
	/* * * * USER CONFIGURATIONS BEGIN * * * */
	
	// Debugging
	var $debugMode = false;
	
	// Absolute path to app
	// TODO How do we take care of Windows user and slashes... ?? Haha.. lol.
	var $rootDir = "<add your root here>"; /* Must end to slash (/) */
	
	// Webroot
	var $webRoot = "<add your webroot here>";
	
	// Site template (relatively to root)
	var $template = "templates/template_empty.php";
	
	// How much user should have time to fill the confirmation form (in minutes)
	var $signupTime = 30;
	
	// Administrators email address to where user should contact in case of signup failure
	var $adminEmail = "<admin email>";
	
	//var $timeZone = "Europe/Helsinki";
	/* * * * THIS IS THE END OF USER CONFIGURATIONS! * * * */
	
	
	/* * * * DO NOT EDIT THIS FILE ANY FURTHER! * * * */
	
	















	function Configurations(){
		$invalidConfigurations_array = $this->checkForInvalidConfigurations();
		
		date_default_timezone_set("Europe/Helsinki");
		
		if(count($invalidConfigurations_array) > 0){
			// Some invalid configurations were found.
		
			// Creates error message
			$errorMessage = "Invalid configurations: ";
			foreach($invalidConfigurations_array as $configurationName_string){
				$errorMessage .= $configurationName_string . " ";
			}
			
			// Now we must use die because page is not initialized yet 
			// so the use of debugger->error function is not possible
			die($errorMessage);
		}
		
		// Enable full error reporting if $debugMode is true
		if($this->debugMode){
			require_once("ErrorReportEnabler.php");
		}
		
	}
	
	/**
	 * Checks that the configurations has been made properly
	 * 
	 * @return array of the configurations which has invalid values
	 */
	function checkForInvalidConfigurations(){
		$invalidConfigurationNames = array();
		
		// $DBDatabase
		/* These are not in use because of the DBInterface which replaces all these features
		if(!isset($this->DBDatabase) || $this->DBDatabase == ""){
			array_push($invalidConfigurationNames, "DBDatabase");
		}
		*/
		
		// $rootDir - this must be before templateUpper and lower
		/*
		 * rootDir must be:
		 * - set
		 * - directory
		 * - end to slash (\ for windows, / for unix)
		 */
		/*
		if(!isset($this->rootDir) || !is_dir($this->rootDir) || !substr($this->rootDir, -1, 1) == "/"){
			array_push($invalidConfigurationNames, "rootDir");
		}
		*/
		
		if(!isset($this->debugMode) || !is_bool($this->debugMode)){
			array_push($invalidConfigurationNames, "debugMode");
		}
		
		/*
		if(!isset($this->template) || !file_exists($this->rootDir . $this->template)){
			array_push($invalidConfigurationNames, "template");
		}
		*/
		
		return $invalidConfigurationNames;
	}
}


?>
