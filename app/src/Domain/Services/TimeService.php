<?php

namespace App\Domain\Services;

use App\Domain\Exceptions\ExceptionUnknownDelayMode;
use Psr\Log\LoggerInterface;

class TimeService
{
    public const DELAY_MODE_DAY = 'DAY';
    public const DELAY_MODE_HOUR = 'HOUR';
    public const DELAY_MODE_MINUTE = 'MINUTE';
    public const DELAY_MODE_SECOND = 'SECOND';

    public function __construct(private LoggerInterface $logger) {}

    /**
     * calculate the remaining time
     * 
     * @param int $time - checking time, only Unix time
     * 
     * @param string $typeTime - checking time mode. Use TimeService constants
     * 
     * @param int $delayTime - delay time in $typeTime
     * 
     * @return int returns the time in $typeTime
     */
    public function getRemainingTime(int $time, string $typeTime, int $delayTime): int
    {
        $pastTime = $this->timeFromMode($typeTime, time() - $time);

        $remainingDelay = $delayTime - $pastTime;

        $this->logger->info('remaining time', [
            'checking time' => $time,
            'type time' => $typeTime,
            'delay time' => $delayTime,
            'pastTime' => $pastTime,
            'remainingDelay' => $remainingDelay,
        ]);
        return $remainingDelay;
    }

    private function timeToMode(string $mode, $time): int
    {
        switch ($mode) {
            case self::DELAY_MODE_DAY:
                return $time * 60 * 60 * 24;
            case self::DELAY_MODE_HOUR:
                return $time * 60 * 60;
            case self::DELAY_MODE_MINUTE:
                return $time * 60;
            case self::DELAY_MODE_SECOND:
                return $time;
            default:
                throw new ExceptionUnknownDelayMode($mode);
        }
    }

    private function timeFromMode(string $mode, $time): int
    {
        switch ($mode) {
            case self::DELAY_MODE_DAY:
                return floor($time / 60 / 60 / 24);
            case self::DELAY_MODE_HOUR:
                return floor($time / 60 / 60);
            case self::DELAY_MODE_MINUTE:
                return floor($time / 60);
            case self::DELAY_MODE_SECOND:
                return $time;
            default:
                throw new ExceptionUnknownDelayMode($mode);
        }
    }
}
