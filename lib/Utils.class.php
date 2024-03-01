<?php

class Utils {

	protected static $oInstance;

	public static function getInstance() {
		if(!self::$oInstance) self::$oInstance = new self();
		return self::$oInstance;
	}

	public static function _() { return self::getInstance(); }

	/* 2019.02.02 - psyjoniz - create random password */
	static function randomPassword($iLength = 20, $bCharactersUpper = true, $bCharactersLower = true, $bNumbers = true, $bSymbols = true) {
		$iIterations = 0;
		$iLength = (!is_numeric($iLength) || trim($iLength) == ''?12:$iLength);
		if($iLength < 4  ) { $iLength = 4;   }
		if($iLength > 250) { $iLength = 250; }
		$bCharactersUpper        = ($bCharactersUpper !== false && $bCharactersUpper !== true ? true : $bCharactersUpper);
		$bCharactersLower        = ($bCharactersLower !== false && $bCharactersLower !== true ? true : $bCharactersLower);
		$bNumbers                = ($bNumbers         !== false && $bNumbers         !== true ? true : $bNumbers        );
		$bSymbols                = ($bSymbols         !== false && $bSymbols         !== true ? true : $bSymbols        );
		$sCharactersUpper        = 'A B C D E F G H I J K L M N O P Q R S T U V W X Y Z';
		$sCharactersLower        = 'a b c d e f g h i j k l m n o p q r s t u v w x y z';
		$sNumbers                = '0 1 2 3 4 5 6 7 8 9';
		$sSymbols                = '! @ # $ % ^ & * ( ) _ + - . , ?';
		$aCharactersUpper        = explode(' ', $sCharactersUpper);
		$aCharactersLower        = explode(' ', $sCharactersLower);
		$aNumbers                = explode(' ', $sNumbers);
		$aSymbols                = explode(' ', $sSymbols);
		$bPasswordSatisfiesRules = false;
		while($bPasswordSatisfiesRules == false){
			$bPasswordSatisfiesRules       = false;
			$bCharactersUpperRuleSatisfied = false;
			$bCharactersLowerRuleSatisfied = false;
			$bNumbersRuleSatisfied         = false;
			$bSymbolsRuleSatisfied         = false;
			shuffle($aCharactersUpper);
			shuffle($aCharactersLower);
			shuffle($aNumbers);
			shuffle($aSymbols);
			$aSourceCharacters = array();
			if($bCharactersUpper) $aSourceCharacters = array_merge($aSourceCharacters, $aCharactersUpper);
			if($bCharactersLower) $aSourceCharacters = array_merge($aSourceCharacters, $aCharactersLower);
			if($bNumbers        ) $aSourceCharacters = array_merge($aSourceCharacters, $aNumbers);
			if($bSymbols        ) $aSourceCharacters = array_merge($aSourceCharacters, $aSymbols);
			shuffle($aSourceCharacters);
			shuffle($aSourceCharacters);
			shuffle($aSourceCharacters);
			shuffle($aSourceCharacters);
			$sPassword = '';
			$iSourceCharactersLength = (count($aSourceCharacters)-1);
			for($iCount = 0; $iCount < $iLength; $iCount++) { $sPassword .= $aSourceCharacters[rand(0,$iSourceCharactersLength)]; }
			if(!$bCharactersUpper) { $bCharactersUpperRuleSatisfied = true; } else { foreach($aCharactersUpper as $sCharacter) { if(false !== strpos($sPassword, $sCharacter)) { $bCharactersUpperRuleSatisfied = true; break; } } }
			if(!$bCharactersLower) { $bCharactersLowerRuleSatisfied = true; } else { foreach($aCharactersLower as $sCharacter) { if(false !== strpos($sPassword, $sCharacter)) { $bCharactersLowerRuleSatisfied = true; break; } } }
			if(!$bNumbers        ) { $bNumbersRuleSatisfied         = true; } else { foreach($aNumbers         as $sNumber   ) { if(false !== strpos($sPassword, $sNumber   )) { $bNumbersRuleSatisfied         = true; break; } } }
			if(!$bSymbols        ) { $bSymbolsRuleSatisfied         = true; } else { foreach($aSymbols         as $sSymbol   ) { if(false !== strpos($sPassword, $sSymbol   )) { $bSymbolsRuleSatisfied         = true; break; } } }
			$iIterations++;
			if($bCharactersUpperRuleSatisfied && $bCharactersLowerRuleSatisfied && $bNumbersRuleSatisfied && $bSymbolsRuleSatisfied) { $bPasswordSatisfiesRules = true; break; }
		}
		return $sPassword;
	}
	
	/* 2007.01.09 - psyjoniz - give back a random string of chars */
	public static function random($sExtraSeed = '') {
		$randomTracker = array();
		$rseed = 'The race does not go to the strong nor to the swift but only the one who endures until the end.  -Petey Pablo' . $sExtraSeed;
		$random = time() . $rseed . time();
		$randomTracker[] = $random;
		$random = md5($random);
		$randomTracker[] = $random;
		$pattern = '/[\d]*/';
		$random = preg_replace($pattern, '', $random);
		$randomTracker[] = $random;
		$random = ( strlen($random) + rand(0,100) );
		$randomTracker[] = $random;
		for($x = 0; $x < $random; $x++) {
			$random .= $rseed;
		}
		$random = md5($random);
		$randomTracker[] = $random;
		for($x = 0; $x < 25; $x++) {
			$random = md5( $random . $randomTracker[rand(0, count($randomTracker))] . $rseed );
			$randomTracker[] = $random;
		}
		$random = md5( $random . $randomTracker[rand(0, count($randomTracker))] . $rseed );
		$randomTracker[] = $random;
		//Debug::analyze($randomTracker, 'randomTracker');
		return $random;
	}

	public static function textToHaha($sText, $sVersion, $sEncryptionKey = 'qLg^*NAgh4DvjbwgilW&') {
		switch($sVersion) {
			case '0.1a' :
				// =============================================================================================== v0.1a - bin2haha(dec2bin($sText))
				if($sText == '') return '';
				$aText = str_split($sText);
				$sBin  = null;
				foreach($aText as $iKey => $sVal) $sBin .= str_pad(decbin(ord($sVal)),7,'0',STR_PAD_LEFT);
				return str_replace('0','H',str_replace('1','A',$sBin)).':'.$sVersion;
			break;
			case '0.2a' :
				// =============================================================================================== v0.2a - bin2haha(dec2bin(encryptedWithStaticSalt($sText)))
				if($sText == '') return '';
				$sText = openssl_encrypt($sText, 'AES256', $sEncryptionKey);
				$aText = str_split($sText);
				$sBin  = null;
				foreach($aText as $iKey => $sVal) $sBin .= str_pad(decbin(ord($sVal)),7,'0',STR_PAD_LEFT);
				return str_replace('0','H',str_replace('1','A',$sBin)).':'.$sVersion;
			break;
			case '0.3a' :
				// =============================================================================================== v0.2a - bin2haha(dec2bin(encryptedWithProvidedSalt($sText)))
				if($sText == '') return '';
				$sText = openssl_encrypt($sText, 'AES256', $sEncryptionKey);
				$aText = str_split($sText);
				$sBin  = null;
				foreach($aText as $iKey => $sVal) $sBin .= str_pad(decbin(ord($sVal)),7,'0',STR_PAD_LEFT);
				return str_replace('0','H',str_replace('1','A',$sBin)).':'.$sVersion;
			break;
			default: return ''; break;
		}
	}

	public static function hahaToText($sHaha, $sVersion, $sEncryptionKey = 'qLg^*NAgh4DvjbwgilW&') {
		switch($sVersion) {
			case '0.1a' :
				// =============================================================================================== v0.1a - dec2bin(haha2bin($sHaha))
				if($sHaha == '') return '';
				if(false === strpos($sHaha,':'.$sVersion)) return 'VERSION ERROR'; //wrong version
				$sHaha = strtoupper(str_replace(':'.$sVersion,'',$sHaha));
				$sBin  = str_replace('H','0',str_replace('A','1',$sHaha));
				$aBin  = self::chunkString($sBin, 7);
				foreach($aBin as $iKey => $sVal) {
					$sText .= chr(bindec($sVal));
				}
				return $sText;
			break;
			case '0.3a' :
				// =============================================================================================== v0.2a - unencryptWithProvidedSalt(bin2dec(unHaha($sHaha)))
				if($sHaha == '') return '';
				if(false === strpos($sHaha,':'.$sVersion)) return 'VERSION ERROR'; //wrong version
				$sHaha = strtoupper(str_replace(':'.$sVersion,'',$sHaha));
				$sBin  = str_replace('H','0',str_replace('A','1',$sHaha));
				$aBin  = self::chunkString($sBin, 7);
				foreach($aBin as $iKey => $sVal) {
					$sText .= chr(bindec($sVal));
				}
				return openssl_decrypt($sText, 'AES256', $sEncryptionKey);
			break;
			case '0.2a' :
				// =============================================================================================== v0.2a - unencryptWithStaticSalt(bin2dec(unHaha($sHaha)))
				if($sHaha == '') return '';
				if(false === strpos($sHaha,':'.$sVersion)) return 'VERSION ERROR'; //wrong version
				$sHaha = strtoupper(str_replace(':'.$sVersion,'',$sHaha));
				$sBin  = str_replace('H','0',str_replace('A','1',$sHaha));
				$aBin  = self::chunkString($sBin, 7);
				foreach($aBin as $iKey => $sVal) {
					$sText .= chr(bindec($sVal));
				}
				return openssl_decrypt($sText, 'AES256', $sEncryptionKey);
			break;
			default: return ''; break;
		}
	}

	public static function chunkString($sString, $iChunkLength) {
		$aString = str_split($sString);
		$iCount  = 1;
		$sChunk  = '';
		$aReturn = array();
		foreach($aString as $iKey => $sVal) {
			$sChunk .= $sVal;
			if($iCount < $iChunkLength) {
				$iCount++;
			} else {
				$aReturn[] = $sChunk;
				$iCount    = 1;
				$sChunk    = '';
			}
		}
		if($sChunk != '') $aReturn[] = $sChunk;
		return $aReturn;
	}

	public static function compileTemplate($sTemplate, $aParams) {
		foreach($aParams as $iKey => $aParam) {
			$sTemplate = str_replace($aParam[0], $aParam[1], $sTemplate);
		}
		return $sTemplate;
	}
}

