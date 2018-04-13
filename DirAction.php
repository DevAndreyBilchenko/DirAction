<?php

class DirAction {
	private $debug = false;
	private $skip_list = [
		'.',
		'..'
	];

	/**
	 * Копирует директорию в новое место
	 *
	 * @param       string  $from           Откуда брать директорию
	 * @param       string  $to             Куда перемещать директорию
	 * @param       int     $override_rule  Что делать при совпадении (1 перезапись, 2 пропуск)
	 * @throws      \Exception
	 *
	 * @return      boolean Состояние удача/неудача
	 */
	public function move($from, $to, $override_rule) {
		$this->canStart($from, $to, $override_rule);
		$this->readAndCopy($from, $to, $override_rule);

		return true;
	}

	/**
	 * Проверяет все ли аргументы верны
	 *
	 * @param       string  $from           Откуда брать директорию
	 * @param       string  $to             Куда перемещать директорию
	 * @param       int     $override_rule  Что делать при совпадении (1 перезапись, 2 пропуск)
	 * @throws      \Exception
	 *
	 */
	private function canStart($from, $to, $override_rule) {
		if (!file_exists($from)) {
			throw new \Exception("Directory \"from\" does not exist");
		}

		if (!file_exists($to)) {
			throw new \Exception("Directory \"to\" does not exist");
		}

		if ($override_rule !== 1 && $override_rule !== 2) {
			throw new \Exception("Override rule must be 1 or 2");
		}
	}

	private function checkSkipList($target) {
		$result = true;

		foreach ($this->skip_list as $skip_item) {
			if ($target == $skip_item) {
				$result = false;
			}
		}

		return $result;

	}

	private function readAndCopy($from, $to, $override_rule) {

		if ($handle = opendir($from)) {

			while (false !== ($file = readdir($handle))) {
				$_from = $from.'/'.$file;
				$_to   = $to.'/'.$file;

				if (is_dir($_from) && $this->checkSkipList($file)) {
					if ($this->debug) echo 'Is dir '.$file.'<br>';
					if (!file_exists($_to)) { // Если директория не существует создаем.
						mkdir($_to);
						if ($this->debug)  echo 'Create dir '.$file.'<br>';
					}
					$this->readAndCopy($_from, $_to, $override_rule);
				} else if (!is_dir($_from)) { // Если файл
					if ($this->debug)  echo 'Is file '.$file.'<br>';
					if ($this->debug)  echo 'Copy to '.$_to.'<br>';
					if ($override_rule == 1) copy($_from, $_to);
				}
			}

			closedir($handle);
		}
	}
}

