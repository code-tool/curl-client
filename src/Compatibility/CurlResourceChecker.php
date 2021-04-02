<?php
declare(strict_types=1);

namespace Http\Client\Curl\Compatibility;

final class CurlResourceChecker
{
    public static function isCurlResource($resource): bool
    {
        return \is_resource($resource) || self::isCurlHandle($resource);
    }

    public static function isCurlHandle($resource): bool
    {
        return PHP_MAJOR_VERSION > 7 && $resource instanceof \CurlHandle;
    }
}
