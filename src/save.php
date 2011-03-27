<?php

/* Requirements */ 
require_once("classes/Configurations.php");
require_once("classes/Page.php");
require_once("classes/Debugger.php");
require_once("classes/Database.php");
require_once("classes/CommonTools.php");
require_once("classes/User.php");
require_once("classes/SignupGadget.php");
require_once("classes/Answer.php");
require_once("classes/ConfirmationMail.php");
require_once("classes/Email.php");

/* Implementations of the most critical classes */
$configurations		= new Configurations();
$page				= new Page();
$debugger			= new Debugger();
$database			= new Database();

/* The code */

/*
 * Vastauksia tallennettaessa lomakkeesta saadaan tiedot kysymysten id-
 * numeroista pilkuilla erotettuina siten, ett� checkboxit, joilla voi olla 
 * monta vastausta (sama id moneen kertaan) on id:n per�ss� viel� 
 * viivalla eroitettu numero. T�m�n lis�ksi lomakkeelta saadaan vastaukset
 * kysymyksiin. Kysymyksien tunnisteina on id. Esim:
 * 
 * 11: Mikko Koski
 * 12: mikko.koski@tkk.fi
 * 13: Normaali ruokavalio
 * 14-0: Tykk��n kahvista
 * 14-1: Tykk��n suklaasta
 * 14-2: Tykk��n p�hkin�st�
 * 15: Punaviini
 * ilmo_id: 56
 * user_id: 143
 * question_ids: 11,12,13,14-0,14-1,14-2,15
 */
 
$signupId = CommonTools::POST("signupid");
$userId = CommonTools::POST("userid");
$user = new User($signupId);

$debugger->debug("UserID: " + $userId, "save.php");

$signupGadget = new SignupGadget($signupId);

$questions = $signupGadget->getAllQuestions();

$answers = array();

// This is for debugging. It's easy to see from here the variable names that 
// the form sends
$debugger->listDefinedPostAndGetVars("save.php");

foreach($questions as $question){
	
	$answer = null;
	
	// Checkbox is a bit more complicated
	if($question->getType() == "checkbox"){
		$answer = parseCheckboxAnswer($question);
	} else {
		$answer = parseNormalAnswer($question);
	}
	array_push($answers, $answer);
}

// All answers are now in the answers array. Let's check that user answered 
// to all required questions
$notAnsweredRequired = getNotAnsweredRequiredIds($answers);

if(count($notAnsweredRequired) > 0){
	$debugger->error("Et vastannut kaikkiin pakollisiin kysymyksiin", "save.php");
} else {
	foreach($answers as $answer){
		$answer->insertToDatabase();
	}
}

// Get the email
$email = "";
foreach($answers as $answer){
	if($answer->getQuestion()->getType() == "email"){
		$email = $answer->getAnswer();
	}
}

// Send email
if($signupGadget->getSendConfirmation()){
	$confirmationMail = new ConfirmationMail($signupGadget->getConfirmationMessage(), new Email($email), $signupGadget, $answers);
	$confirmationMail->send();
}

$user->setConfirmed(true);
$user->destroySessionId();

header("Location: " . $configurations->webRoot . "signup/$signupId");


function parseCheckboxAnswer($question){
	global $userId, $debugger;
	
	// Checkbox's question id is in format 14-0, 14-1, 14-2
	$checkboxAnswers = array();
	for($i = 0; $i < count($question->getOptions()); $i++){
		$checkboxFromPost = CommonTools::POST($question->getId() . "-" . $i);
		if($checkboxFromPost != null && $checkboxFromPost != ""){
			$debugger->debug("Checkbox value ".$question->getId() . "-" . $i.": " . $checkboxFromPost, "parseChechboxAnswer");
			array_push($checkboxAnswers, $checkboxFromPost);
		}
	}
	return new Answer($checkboxAnswers, $userId, $question);
}

function parseNormalAnswer($question){
	global $userId;

	$answerFromPost = CommonTools::POST($question->getId());
	return new Answer($answerFromPost, $userId, $question);
}

/**
 * Gets ids of questions that user hasn't answer although they are required
 */
function getNotAnsweredRequiredIds($answers){
	$notAnsweredRequired = array();
	foreach($answers as $answer){
		if($answer->isRequired() && $answer->isEmpty()){
			array_push($notAnsweredRequired, $answer->getSignupId());
		}
	}
	return $notAnsweredRequired;
}

?>
