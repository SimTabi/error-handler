<?php

namespace prgTW\ErrorHandler\Handler;

use prgTW\ErrorHandler\Error\ErrorException;
use prgTW\ErrorHandler\Metadata\Metadata;

class BugsnagHandler extends \Bugsnag_Client implements HandlerInterface
{
	/** {@inheritdoc} */
	public function handleError(ErrorException $error, Metadata $metadata)
	{
		$metadataArr = array_merge($metadata->getMetadata(), $metadata->getTags());
		$this->setReleaseStage($metadata->getStage());
		$this->setProjectRoot($metadata->getAppRootDir());
		$this->setAppVersion($metadata->getRevision());
		$this->setContext($error->getContext());
		$this->notifyError(ErrorException::$phpErrors[$error->getCode()], $error->getMessage(), $metadataArr, $error->getSeverity());
	}

	/** {@inheritdoc} */
	public function handleException(\Exception $exception, Metadata $metadata)
	{
		$metadataArr = array_merge($metadata->getMetadata(), $metadata->getTags());
		$this->setReleaseStage($metadata->getStage());
		$this->setProjectRoot($metadata->getAppRootDir());
		$this->setAppVersion($metadata->getRevision());
		$this->notifyException($exception, $metadataArr);
	}

	/** {@inheritdoc} */
	public function shutdownHandler()
	{
		// ErrorHandler handles on-shutdown errors already
	}

}
