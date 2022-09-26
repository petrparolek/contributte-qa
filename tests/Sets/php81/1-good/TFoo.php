<?php declare(strict_types = 1);

namespace Tests;

/**
 * @mixin IFoobar
 */
trait TFoo
{

	protected mixed $foobar;

	protected function contextFoobar(): void
	{
		$this->interfaceMethod('foo', 3);
	}

}
