<?php
namespace ECidade\Task\Executor;

use Symfony\Component\Process\Process;
use ECidade\Task\Base;
use ECidade\Task\Closure as TaskClosure;
use ECidade\Task\TaskInterface;

class Background
{
    private $waiting = array();
    private $running = array();
    private $afterRunning;
    private $complete = array();
    private $workerPath = 'bin/background-worker';
    private $limit = 20;
    private $delay = 1000;

    public function __construct()
    {
        $this->afterRunning = new \SplObjectStorage();
    }

    public function add($task, $afterRunning = null)
    {
        if (!is_callable($task) && !($task instanceof Base)) {
            throw new \Exception("Invalid task");
        }

        if (!$task instanceof TaskInterface) {
            $task = new TaskClosure($task);
        }

        if (!empty($afterRunning)) {
            $this->afterRunning->attach($task, $afterRunning);
        }

        $this->waiting[] = $task;
    }

    public function start()
    {
        $this->tick();
    }

    public function complete()
    {
        return $this->complete;
    }

    private function tick()
    {
        do {
            $this->initialize();
            $this->track();
            $this->wait();
        } while (count($this->waiting) || count($this->running));
    }

    private function wait()
    {
        if ($this->throughput()) {
            usleep($this->delay);
        }
    }

    private function initialize()
    {
        foreach ($this->waiting as $key => $task) {
            if ($this->throughput()) {
                break;
            }

            $dataPath = $this->createData($task);
            $job = $this->createWorker($dataPath);
            $job->dataPath = $dataPath;
            $job->start();
            $this->running[] = $job;
            unset($this->waiting[$key]);

            if ($this->afterRunning->contains($task)) {
                $callback = $this->afterRunning->offsetGet($task);
                $this->afterRunning->detach($task);
                $this->afterRunning->attach($job, $callback);
            }
        }
    }

    private function track()
    {
        foreach ($this->running as $key => $process) {
            if (!$process->isTerminated()) {
                continue;
            }

            $task             = unserialize(file_get_contents($process->dataPath));
            $this->complete[] = $task;

            if ($this->afterRunning->contains($process)) {
                call_user_func($this->afterRunning->offsetGet($process), $task);
            }

            unset($this->running[$key]);
            unlink($process->dataPath);
        }
    }

    private function throughput()
    {
        $running = count($this->running);
        return $running > 0 && $running >= $this->limit;
    }

    public function limit($limit)
    {
        if (!is_null($limit)) {
            $this->limit = $limit;
        }

        return $this->limit;
    }

    private function createWorker($dataPath)
    {
        return new Process(["php", $this->workerPath, $dataPath]);
    }

    private function createData($task)
    {
        $path         = tempnam('tmp', 'ecidade_task_');
        $serializable = $task;

        if (!file_put_contents($path, serialize($serializable))) {
            throw new \Exception("Error on create data file");
        }
        return $path;
    }
}
