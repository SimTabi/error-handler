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
		$metadataArr = $this->prepareForSending($metadata);

		$this->setContext($error->getContext());
		$this->notifyError(
			ErrorException::$phpErrors[$error->getCode()],
			$error->getMessage(),
			$metadataArr,
			self::$SEVERITY_MAP[$metadata->getSeverity()]
		);

		return $metadataArr['grouping_hash'];
	}

	/** {@inheritdoc} */
	public function handleException(\Exception $exception, Metadata $metadata)
	{
		$metadataArr = $this->prepareForSending($metadata);

		$this->notifyException($exception, $metadataArr, self::$SEVERITY_MAP[$metadata->getSeverity()]);

		return $metadataArr['grouping_hash'];
	}

	/** {@inheritdoc} */
	public function handleEvent($event, Metadata $metadata)
	{
		$metadataArr = $this->prepareForSending($metadata);

		$this->notifyError('event', $event, $metadataArr, self::$SEVERITY_MAP[$metadata->getSeverity()]);

		return $metadataArr['grouping_hash'];
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

		$groupingHash = md5($dataToHash);
		$metadata->addMetadatum('grouping_hash', $groupingHash);

		return $groupingHash;
	}

	/**
	 * @param Metadata $metadata
	 *
	 * @return array
	 */
	protected function prepareForSending(Metadata $metadata)
	{
		$this->calculateGroupingHash($metadata);
		$metadataArr = array_merge($metadata->getTags(), $metadata->getMetadata());

		$this->setReleaseStage($metadata->getStage());
		$this->setProjectRoot($metadata->getAppRootDir());
		$this->setAppVersion($metadata->getAppVersion());

		return $metadataArr;
	}

}
