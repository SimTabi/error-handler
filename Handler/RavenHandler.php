<?php

namespace prgTW\ErrorHandler\Handler;

use prgTW\ErrorHandler\Error\ErrorException;
use prgTW\ErrorHandler\Error\Severity;
use prgTW\ErrorHandler\Metadata\Metadata;

/**
 * @codeCoverageIgnore
 */
class RavenHandler extends \Raven_Client implements HandlerInterface
{
	/** @var array */
	public static $SEVERITY_MAP = array(
		Severity::DEBUG     => \Raven_Client::DEBUG,
		Severity::INFO      => \Raven_Client::INFO,
		Severity::NOTICE    => \Raven_Client::INFO,
		Severity::WARNING   => \Raven_Client::WARNING,
		Severity::ERROR     => \Raven_Client::ERROR,
		Severity::CRITICAL  => \Raven_Client::ERROR,
		Severity::ALERT     => \Raven_Client::ERROR,
		Severity::EMERGENCY => \Raven_Client::ERROR,
	);

	/** {@inheritdoc} */
	public function __construct($optionsOrDsn = null, $options = array())
	{
		$options['auto_log_stacks'] = true;

		parent::__construct($optionsOrDsn, $options);
	}

	/** {@inheritdoc} */
	public function handleError(ErrorException $error, Metadata $metadata)
	{
		$options                     = $this->prepareOptions($metadata);
		$options['extra']['context'] = $error->getContext();

		$eventId = $this->captureException($error, $options);
		$eventId = $this->getIdent($eventId);

		return $eventId;
	}

	/** {@inheritdoc} */
	public function handleException(\Exception $exception, Metadata $metadata)
	{
		$options = $this->prepareOptions($metadata);

		$eventId = $this->captureException($exception, $options);
		$eventId = $this->getIdent($eventId);

		return $eventId;
	}

	/** {@inheritdoc} */
	public function handleEvent($event, Metadata $metadata)
	{
		$options = $this->prepareOptions($metadata);

		$eventId = $this->captureMessage($event, array(), $options, true);
		$eventId = $this->getIdent($eventId);

		return $eventId;
	}

	/**
	 * @param Metadata $metadata
	 *
	 * @return array
	 */
	protected function prepareOptions(Metadata $metadata)
	{
		$options = array(
			'tags'  => array_merge_recursive($metadata->getTags(), array(
				'releaseStage' => $metadata->getStage(),
				'appVersion'   => $metadata->getAppVersion(),
			)),
			'extra' => array_merge_recursive($metadata->getMetadata(), array(
				'appRootDir'   => $metadata->getAppRootDir(),
				'releaseStage' => $metadata->getStage(),
				'appVersion'   => $metadata->getAppVersion(),
			)),
			'level' => self::$SEVERITY_MAP[$metadata->getSeverity()],
		);

		$groupingHash = $this->calculateGroupingHash($metadata);
		if ($groupingHash)
		{
			$options['checksum'] = $groupingHash;
		}

		return $options;
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
