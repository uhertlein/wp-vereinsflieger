<?php


namespace Diginize\WpVereinsflieger\VereinsfliegerApi\Model;


class VereinsfliegerErrorDto extends VereinsfliegerResponseDto implements IVereinsfliegerErrorDto {

	/** @var string */
	private $error;

	/**
	 * @return string
	 */
	public function getError(): string {
		return $this->error;
	}

}