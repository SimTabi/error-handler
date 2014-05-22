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
		$groupingHash = $this->calculateGroupingHash($metadata);
		if ($groupingHash)
		{
			$metadataArr['grouping_hash'] = $groupingHash;
		}

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
		$groupingHash = $this->calculateGroupingHash($metadata);
		if ($groupingHash)
		{
			$metadataArr['grouping_hash'] = $groupingHash;
		}

		$this->setReleaseStage($metadata->getStage());
		$this->setProjectRoot($metadata->getAppRootDir());
		$this->setAppVersion($metadata->getAppVersion());
		$this->notifyException($exception, $metadataArr);
	}

	/** {@inheritdoc} */
	public function handleEvent($event, Metadata $metadata = null)
	{
		$metadataArr = array_merge($metadata->getMetadata(), $metadata->getTags());
		$groupingHash = $this->calculateGroupingHash($metadata);
		if ($groupingHash)
		{
			$metadataArr['grouping_hash'] = $groupingHash;
		}

		$this->setReleaseStage($metadata->getStage());
		$this->setProjectRoot($metadata->getAppRootDir());
		$this->setAppVersion($metadata->getAppVersion());
		$this->notifyError('event', $event, $metadataArr, ErrorException::translateSeverity(E_USER_NOTICE));
	}

	/** {@inheritdoc} */
	public function shutdownHandler()
	{
		// ErrorHandler handles on-shutdown errors already
	}

	/**
	 * @param Metadata $metadata
	 *
	 * @return string
	 */
	private function calculateGroupingHash(Metadata $metadata)
	{
		$grouping = $metadata->getGrouping();
		if (!$grouping)
		{
			return '';
		}

		$dataToHash = '';

		foreach ($grouping as $name => $value)
		{
			$dataToHash .= sprintf('%s=%s', $name, serialize($value));
		}

		return md5($dataToHash);
	}

}
