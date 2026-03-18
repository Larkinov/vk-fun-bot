<?php
declare(strict_types=1);
namespace App\Infrastructure\Doctrine\Types;

use App\Application\UseCase\Command\Data\LooserData;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

class LooserDataType extends JsonType
{
    public const NAME = 'looser_data';

    public function convertToPHPValue($value, AbstractPlatform $platform): ?LooserData
    {
        $data = parent::convertToPHPValue($value, $platform);
        if ($data === null) return null;

        return new LooserData(
            $data['profiles'] ?? [],
            $data['lastActiveAt'] ?? 0,
            $data['lastWeekActive'] ?? 0,
            $data['lastMonthActive'] ?? 0,
        );
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        if ($value instanceof LooserData) {
            $value = [
                'profiles' => $value->getProfiles(),
                'lastActiveAt' => $value->getLastActive(),
                'lastWeekActive' => $value->getLastWeekActive(),
                'lastMonthActive' => $value->getLastMonthActive(),
            ];
            return parent::convertToDatabaseValue($value, $platform);
        }

        return '{}';
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
