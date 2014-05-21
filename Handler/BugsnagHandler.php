<?php

namespace prgTW\ErrorHandler\Handler;

use prgTW\ErrorHandler\Error\ErrorException;
use prgTW\ErrorHandler\Metadata\Metadata;

/**
 * @codeCoverageIgnore
 */
class BugsnagHandler extends \Bugsnag_Client implements HandlerInterface
{
	/** {@inheritdoc} */
	public function handleError(ErrorException $error, Metadata $metadata = null)
	{
		$metadataArr = array_merge($metadata->getMetadata(), $metadata->getTags());
		$this->setReleaseStage($metadata->getStage());
		$this->setProjectRoot($metadata->getAppRootDir());
		$this->setAppVersion($metadata->getAppVersion());
		$this->setContext($error->getContext());
		$this->notifyError(ErrorException::$phpErrors[$error->getCode()], $error->getMessage(), $metadataArr, $error->getSeverity());
	}

	/** {@inheritdoc} */
	public function handleException(\Exception $exception, Metadata $metadata = null)
	{
		$metadataArr = array_merge($metadata->getMetadata(), $metadata->getTags());
		$this->setReleaseStage($metadata->getStage());
		$this->setProjectRoot($metadata->getAppRootDir());
		$this->setAppVersion($metadata->getAppVersion());
		$this->notifyException($exception, $metadataArr);
	}

	/** {@inheritdoc} */
	public function handleEvent($eventName, $message, Metadata $metadata = null)
	{
		$metadataArr = array_merge($metadata->getMetadata(), $metadata->getTags());
		$this->setReleaseStage($metadata->getStage());
		$this->setProjectRoot($metadata->getAppRootDir());
		$this->setAppVersion($metadata->getAppVersion());
		$this->notifyError($eventName, $message, $metadataArr, ErrorException::translateSeverity(E_USER_NOTICE));
	}

	/** {@inheritdoc} */
	public function shutdownHandler()
	{
		// ErrorHandler handles on-shutdown errors already
	}

}
