<?php

/**
 * 2013.12.20 - Jesse L Quattlebaum (psyjoniz@gmail.com) (https://github.com/psyjoniz/code_sample__Timer)
 * A class for timing things in code.
 */

class Timer {

	private $aTimers = array();

	/**
	 * constructor with optional start timer
	 *
	 * @param string $sTimer timer name
	 *
	 * @return void
	 */
	public function __construct($sTimer = null) {
		if(null !== $sTimer)
		{
			$this->start($sTimer);
		}
	}

	/**
	 * start a timer
	 *
	 * @param string $sTimer timer name
	 *
	 * @return void
	 */
	public function start($sTimer = null) {
		if(null === $sTimer)
		{
			throw new Exception('Timer name invalid');
		}
		$this->aTimers[$sTimer] = array(
			'start'   => $this->getTimestamp(),
			'stop'    => null,
			'elapsed' => null,
		);
	}

	/**
	 * stop a timer
	 *
	 * @param string $sTimer timer name
	 *
	 * @return array
	 */
	public function stop($sTimer = null) {
		if(null === $sTimer)
		{
			throw new Exception('Timer name invalid');
		}
		if(false === is_array($this->aTimers[$sTimer]))
		{
			throw new Exception('Timer does not exist: ' . $sTimer);
		}
		$this->aTimers[$sTimer]['stop'] = $this->getTimestamp();
		$this->aTimers[$sTimer]['elapsed'] = $this->getElapsed($sTimer);
		return $this->aTimers[$sTimer];
	}

	/**
	 * get timestamp
	 *
	 * @return decimal
	 */
	private function getTimestamp() {
		return microtime(true);
	}

	/**
	 * get elapsed time for a timer
	 *
	 * @param string $sTimer timer name
	 *
	 * @return decimal
	 */
	private function getElapsed($sTimer = null) {
		if(null === $sTimer)
		{
			throw new Exception('Timer name invalid');
		}
		if(false === is_array($this->aTimers[$sTimer]))
		{
			throw new Exception('Timer does not exist: ' . $sTimer);
		}
		return $this->aTimers[$sTimer]['stop'] - $this->aTimers[$sTimer]['start'];
	}

}

