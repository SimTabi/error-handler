<?php

namespace prgTW\ErrorHandler\Tests\Processor;

use prgTW\ErrorHandler\Metadata\Metadata;
use prgTW\ErrorHandler\Processor\ProcessorInterface;

class TestProcessor implements ProcessorInterface
{
	/** @var bool */
	protected $callbackExecuted = false;

	/** {@inheritdoc} */
	public function process(Metadata $metadata, \Exception $exception = null)
	{
		$this->callbackExecuted = true;
		$metadata->setAppRootDir('app_root_dir');
		$metadata->setStage('stage');
		$metadata->setAppVersion('app_version');
		$metadata->addTags(array(
			'tag1' => 1,
			'tag3' => 3,
		));
		$metadata->addTag('tag2', 2);
		$metadata->removeTag('tag3');
		$metadata->addMetadata(array(
			'metadatum1' => 1,
			'metadatum3' => 3,
		));
		$metadata->addMetadatum('metadatum2', 2);
		$metadata->removeMetadatum('metadatum3');

		$metadata->addCategories(array('category1', 'category2'));
		$metadata->addCategory('category3');
		$metadata->removeCategory('category3');

		$metadata->addGrouping(array(
			'group1' => 'value1',
		));
		$metadata->groupBy('group1', 'value2');
		$metadata->groupBy('group2', 'value2');
	}

	/**
	 * @return boolean
	 */
	public function getCallbackExecuted()
	{
		return $this->callbackExecuted;
	}
}
