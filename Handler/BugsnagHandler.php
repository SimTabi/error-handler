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
	public static $SEVERITY_MAP = array(
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
	public function handleError(ErrorException $error, Metadata $metadata)
	{
		$metadataArr  = array_merge($metadata->getMetadata(), $metadata->getTags());
		$groupingHash = $this->calculateGroupingHash($metadata);

		$this->presetFromMetadata($metadata);
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
	public function handleException(\Exception $exception, Metadata $metadata)
	{
		$metadataArr  = array_merge($metadata->getMetadata(), $metadata->getTags());
		$groupingHash = $this->calculateGroupingHash($metadata);

		$this->presetFromMetadata($metadata);
		$this->notifyException($exception, $metadataArr, self::$SEVERITY_MAP[$metadata->getSeverity()]);

		return $groupingHash;
	}

	/** {@inheritdoc} */
	public function handleEvent($event, Metadata $metadata)
	{
		$metadataArr  = array_merge($metadata->getMetadata(), $metadata->getTags());
		$groupingHash = $this->calculateGroupingHash($metadata);

		$this->presetFromMetadata($metadata);
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

		$groupingHash                 = md5($dataToHash);
		$metadataArr['grouping_hash'] = $groupingHash;

		return $groupingHash;
	}

	/**
	 * @param Metadata $metadata
	 */
	protected function presetFromMetadata(Metadata $metadata)
	{
		$this->setReleaseStage($metadata->getStage());
		$this->setProjectRoot($metadata->getAppRootDir());
		$this->setAppVersion($metadata->getAppVersion());
	}

}
