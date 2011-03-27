<?php

require_once("Email.php");
require_once("SignupGadget.php");

/**
 * Luokka, jonka avulla l�hetet��n ilmoittautumisvahvistusviesti
 * k�ytt�j�lle.
 *
 *
 * @author Mikko Koski (mikko.koski@tkk.fi)
 *
 * HUOM! EI viel� k�yt�ss� (13.3.2008) -mikko
 */
class ConfirmationMail{

	var $debugger;	// CommonTools::initializeCommonObjects ei toimisi, jos t�m� olisi private
	
	private $message;
	private $email;
	private $signupGadget;
	private $answers;

	/**
	 * Luo uuden k�ytt�j�lle l�hetett�v�n vahvistusviestin
	 * @param string message Viesti k�ytt�j�lle
	 * @param string email K�ytt�j�n email
	 * @param SignupGadget signupgadget Tiedot ilmosta
	 * @param array answers vastaukset masiinaan
	 */
	public function ConfirmationMail($message, Email $email, $signupGadget, $answers = null){
		CommonTools::initializeCommonObjects($this);

		$this->message = $message;
		$this->email = $email;
		$this->signupGadget = $signupGadget;
		$this->answers = $answers;

		if(!is_a($email, 'Email')){
			$this->debugger->error("email parameter must be an instance of Email class");
		}	

		// Tulostetaan ilmoittautumisen vahvistus vain jos k�ytt�j�n id on annettu
		if($answers != null){
			$questions = $this->signupGadget->getAllQuestions();

			$this->message .= "\n\n*** Ilmoittautumisesi tiedot ***\n\n";

			foreach($answers as $answer){
				// Gets answer to question
				if(is_a($answer, "Answer")){
					$this->message .= $answer->getQuestion()->getQuestion() . ": " . $answer->getReadableAnswer() . "\n";
				} else {
					// T�h�n joku virhe?
				}
			}
		}
	}

	/**
	 * L�hett�� k�ytt�j�lle vahvistusviestin ilmoittautumisesta
	 *
	 */
	public function send(){	
		$email = $this->email->getAddress();
		$subject = "Ilmoittautumisen vahvistus: ". $this->signupGadget->getTitle();
		$message = $this->message;
		$from = "From: Ilmomasiina <ilmomasiina@ik.tky.fi.invalid>";

		// Just quick testing die("Email: $email, Subject: $subject, Message: $message, $from");

		mail($email, $subject, $message, $from);
	}

	public function getMessage(){
		return $this->message;
	}
}

?>
