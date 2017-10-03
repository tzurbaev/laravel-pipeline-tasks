<?php

namespace Zurbaev\PipelineTasks\Exceptions;

class PipelineTaskFailedException extends \RuntimeException
{
    const EXCEPTION_THROWN = 100;
    const REJECTED = 101;

    /**
     * Failed pipe name.
     *
     * @var string
     */
    protected $pipeName;

    /**
     * PipelineTaskFailed constructor.
     *
     * @param string          $pipeName
     * @param string          $message
     * @param int             $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $pipeName, $message = '', $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->pipeName = $pipeName;
    }

    /**
     * Get the failed pipe name.
     *
     * @return string
     */
    public function getPipeName()
    {
        return $this->pipeName;
    }

    /**
     * Create new exception instance with EXCEPTION_THROWN status.
     *
     * @param string     $name
     * @param \Exception $e
     *
     * @return static
     */
    public static function exceptionThrown(string $name, \Exception $e)
    {
        return new static(
            $name,
            'Pipe "'.$name.'" failed due to exception (message: "'.$e->getMessage().'", code: '.$e->getCode().').',
            static::EXCEPTION_THROWN,
            $e
        );
    }

    /**
     * Create new exception instance with REJECTED status.
     *
     * @param string $name
     *
     * @return static
     */
    public static function rejected(string $name)
    {
        return new static(
            $name,
            'Pipe "'.$name.'" has been rejected.',
            static::REJECTED
        );
    }
}
