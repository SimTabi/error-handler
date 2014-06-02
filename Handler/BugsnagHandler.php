<?php

namespace prgTW\ErrorHandler\Handler;

use prgTW\ErrorHandler\Error\ErrorException;
use prgTW\ErrorHandler\Error\Severity;
use prgTW\ErrorHandler\Metadata\Metadata;

/**
 * @codeCoverageIgnore
 */
class BugsnagHandler extends \Bugsnag_Client implements HandlerInterface
{
	/** @var array */
	static $SEVERITY_MAP = array(
		Severity::DEBUG     => 'info',
		Severity::INFO      => 'info',
		Severity::NOTICE    => 'info',
		Severity::WARNING   => 'warning',
		Severity::ERROR     => 'error',
		Severity::CRITICAL  => 'error',
		Severity::ALERT     => 'error',
		Severity::EMERGENCY => 'error',
	);

	/** {@inheritdoc} */
	public function handleError(ErrorException $error, Metadata $metadata = null)
	{
		$metadataArr  = array_merge($metadata->getMetadata(), $metadata->getTags());
		$groupingHash = $this->calculateGroupingHash($metadata);
		if ($groupingHash)
		{
			$metadataArr['grouping_hash'] = $groupingHash;
		}

		$this->setReleaseStage($metadata->getStage());
		$this->setProjectRoot($metadata->getAppRootDir());
		$this->setAppVersion($metadata->getAppVersion());
		$this->setContext($error->getContext());
		$this->notifyError(
			ErrorException::$phpErrors[$error->getCode()],
			$error->getMessage(),
			$metadataArr,
			self::$SEVERITY_MAP[$metadata->getSeverity()]
		);

		return $groupingHash;
	}

	/** {@inheritdoc} */
	public function handleException(\Exception $exception, Metadata $metadata = null)
	{
		$metadataArr  = array_merge($metadata->getMetadata(), $metadata->getTags());
		$groupingHash = $this->calculateGroupingHash($metadata);
		if ($groupingHash)
		{
			$metadataArr['grouping_hash'] = $groupingHash;
		}

		$this->setReleaseStage($metadata->getStage());
		$this->setProjectRoot($metadata->getAppRootDir());
		$this->setAppVersion($metadata->getAppVersion());
		$this->notifyException($exception, $metadataArr, self::$SEVERITY_MAP[$metadata->getSeverity()]);

		return $groupingHash;
	}

	/** {@inheritdoc} */
	public function handleEvent($event, Metadata $metadata = null)
	{
		$metadataArr  = array_merge($metadata->getMetadata(), $metadata->getTags());
		$groupingHash = $this->calculateGroupingHash($metadata);
		if ($groupingHash)
		{
			$metadataArr['grouping_hash'] = $groupingHash;
		}

		$this->setReleaseStage($metadata->getStage());
		$this->setProjectRoot($metadata->getAppRootDir());
		$this->setAppVersion($metadata->getAppVersion());
		$this->notifyError('event', $event, $metadataArr, self::$SEVERITY_MAP[$metadata->getSeverity()]);

		return $groupingHash;
	}

	/**
	 * @param Metadata $metadata
	 *
	 * @return string
	 */
	protected function calculateGroupingHash(Metadata $metadata)
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
