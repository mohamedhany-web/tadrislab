<?php

namespace App\Support;

/**
 * Gates for TADRIS LAB product modules (config/platform.php → modules.*.enabled).
 * Soft-disable only — code and tables remain for later re-enable.
 */
final class PlatformModules
{
    public static function enabled(string $key): bool
    {
        $value = config("platform.modules.{$key}.enabled");

        if ($value === null) {
            // Unknown keys default to enabled so KEEP features are not accidentally blocked.
            return true;
        }

        return (bool) $value;
    }

    public static function disabled(string $key): bool
    {
        return ! self::enabled($key);
    }

    /**
     * True if every listed module is enabled.
     */
    public static function allEnabled(string ...$keys): bool
    {
        foreach ($keys as $key) {
            if (! self::enabled($key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * True if at least one listed module is enabled.
     */
    public static function anyEnabled(string ...$keys): bool
    {
        foreach ($keys as $key) {
            if (self::enabled($key)) {
                return true;
            }
        }

        return false;
    }
}
