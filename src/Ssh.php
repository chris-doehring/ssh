<?php
namespace ChrisDoehring\Ssh;

use Closure;
use Exception;
use Symfony\Component\Process\Process;

class Ssh
{
    /** @var string */
    protected $user;

    /** @var string */
    protected $host;

    /** @var string */
    protected $pathToPrivateKey = '';

    /** @var int|null */
    protected $port;

    /** @var bool */
    protected $enableStrictHostChecking = true;

    /** @var Closure */
    protected $processConfigurationClosure;

    /** @var Closure */
    protected $onOutputClosure;

    /**
     * Ssh constructor.
     * @param string $user
     * @param string $host
     * @param int|null $port
     */
    public function __construct($user, $host, $port = null)
    {
        $this->user = $user;

        $this->host = $host;

        $this->port = $port;
        $this->processConfigurationClosure = static function (Process $process) {
            return null;
        };
        $this->onOutputClosure = static function ($type, $line) {
            return null;
        };
    }

    /**
     * @param mixed ...$args
     * @return static
     */
    public static function create(...$args)
    {
        return new static(...$args);
    }

    /**
     * @param string $pathToPrivateKey
     * @return static
     */
    public function usePrivateKey($pathToPrivateKey)
    {
        $this->pathToPrivateKey = $pathToPrivateKey;

        return $this;
    }

    /**
     * @param int $port
     * @return $this
     * @throws Exception
     */
    public function usePort($port)
    {
        if ($port < 0) {
            throw new Exception('Port must be a positive integer.');
        }
        $this->port = $port;

        return $this;
    }

    /**
     * @param Closure $processConfigurationClosure
     * @return $this
     */
    public function configureProcess(Closure $processConfigurationClosure)
    {
        $this->processConfigurationClosure = $processConfigurationClosure;

        return $this;
    }

    /**
     * @param Closure $onOutput
     * @return $this
     */
    public function onOutput(Closure $onOutput)
    {
        $this->onOutputClosure = $onOutput;

        return $this;
    }

    /**
     * @return $this
     */
    public function enableStrictHostKeyChecking()
    {
        $this->enableStrictHostChecking = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function disableStrictHostKeyChecking()
    {
        $this->enableStrictHostChecking = false;

        return $this;
    }

    /**
     * @param string|array $command
     *
     * @return string
     */
    public function getExecuteCommand($command)
    {
        $commands = $this->wrapArray($command);

        $extraOptions = $this->getExtraSshOptions();

        $commandString = implode(PHP_EOL, $commands);

        $delimiter = 'EOF-SPATIE-SSH';

        $target = $this->getTarget();

        return "ssh {$extraOptions} {$target} 'bash -se' << \\$delimiter".PHP_EOL
            .$commandString.PHP_EOL
            .$delimiter;
    }

    /**
     * @param string|array $command
     *
     * @return Process
     */
    public function execute($command)
    {
        $sshCommand = $this->getExecuteCommand($command);

        return $this->run($sshCommand);
    }

    /**
     * @param string $sourcePath
     * @param string $destinationPath
     * @return string
     */
    public function getDownloadCommand($sourcePath, $destinationPath)
    {
        return "scp {$this->getExtraScpOptions()} {$this->getTarget()}:$sourcePath $destinationPath";
    }

    /**
     * @param string $sourcePath
     * @param string $destinationPath
     * @return Process
     */
    public function download($sourcePath, $destinationPath)
    {
        $downloadCommand = $this->getDownloadCommand($sourcePath, $destinationPath);

        return $this->run($downloadCommand);
    }

    /**
     * @param string $sourcePath
     * @param string $destinationPath
     * @return string
     */
    public function getUploadCommand($sourcePath, $destinationPath)
    {
        return "scp {$this->getExtraScpOptions()} $sourcePath {$this->getTarget()}:$destinationPath";
    }

    /**
     * @param string $sourcePath
     * @param string $destinationPath
     * @return Process
     */
    public function upload($sourcePath, $destinationPath)
    {
        $uploadCommand = $this->getUploadCommand($sourcePath, $destinationPath);

        return $this->run($uploadCommand);
    }

    /**
     * @return string
     */
    protected function getExtraSshOptions()
    {
        $extraOptions = $this->getExtraOptions();

        if (null !== $this->port) {
            $extraOptions[] = "-p {$this->port}";
        }

        return implode(' ', $extraOptions);
    }

    /**
     * @return string
     */
    protected function getExtraScpOptions()
    {
        $extraOptions = $this->getExtraOptions();

        $extraOptions[] = '-r';

        if (null !== $this->port) {
            $extraOptions[] = "-P {$this->port}";
        }

        return implode(' ', $extraOptions);
    }

    /**
     * @return array
     */
    private function getExtraOptions()
    {
        $extraOptions = [];

        if ($this->pathToPrivateKey) {
            $extraOptions[] = "-i {$this->pathToPrivateKey}";
        }

        if (! $this->enableStrictHostChecking) {
            $extraOptions[] = '-o StrictHostKeyChecking=no';
            $extraOptions[] = '-o UserKnownHostsFile=/dev/null';
        }

        return $extraOptions;
    }

    /**
     * @param $arrayOrString
     * @return array
     */
    protected function wrapArray($arrayOrString)
    {
        return (array) $arrayOrString;
    }

    /**
     * @param string $command
     * @return Process
     */
    protected function run($command)
    {
        $process = new Process($command);

        $process->setTimeout(0);

        $processConfigureationClosure = $this->processConfigurationClosure;
        $processConfigureationClosure($process);

        $process->run($this->onOutputClosure);

        return $process;
    }

    /**
     * @return string
     */
    protected function getTarget()
    {
        return "{$this->user}@{$this->host}";
    }
}
