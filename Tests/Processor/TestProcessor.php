<?php

namespace prgTW\ErrorHandler\Tests\Processor;

use prgTW\ErrorHandler\Metadata\Metadata;
use prgTW\ErrorHandler\Processor\ProcessorInterface;

class TestProcessor implements ProcessorInterface
{
	/** @var bool */
	protected $callbackExecuted = false;

	/** {@inheritdoc} */
	public function process(Metadata $metadata, \Exception $exception)
	{
		$this->callbackExecuted = true;
		$metadata->setAppName('app_name');
		$metadata->setAppRootDir('app_root_dir');
		$metadata->setStage('stage');
		$metadata->setRevision('revision');
		$metadata->setTags(array(
			'tag1' => 1,
			'tag3' => 3,
		));
		$metadata->setTag('tag2', 2);
		$metadata->removeTag('tag3');
	}

	/**
	 * @return boolean
	 */
	public function getCallbackExecuted()
	{
		return $this->callbackExecuted;
	}
}
