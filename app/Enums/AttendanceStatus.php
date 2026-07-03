<?php
declare(strict_types=1);

namespace App\Enums;

enum AttendanceStatus: string
{
    case PRESENT = 'P';
    case ABSENT = 'A';
    case LATE = 'L';
    case LEAVE = 'LV';

    /**
     * Reserved for future implementation:
     * case HOLIDAY = 'H';
     * case OFFICIAL_DUTY = 'OD';
     */

    /**
     * Get all valid enum values.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }

    /**
     * Get human-readable labels for each case.
     *
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            self::PRESENT->value => 'Present',
            self::ABSENT->value => 'Absent',
            self::LATE->value => 'Late',
            self::LEAVE->value => 'Leave',
            // Reserved: self::HOLIDAY->value => 'Holiday',
            // Reserved: self::OFFICIAL_DUTY->value => 'Official Duty',
        ];
    }
}
