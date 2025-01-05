<?php

namespace Napse\BundleMaker\Helper;

/**
 * Helper class for processing bundle names and related transformations.
 */
class BundleNameHelper
{
    /**
     * Converts a PascalCase string to kebab-case.
     *
     * @param string $string The input string in PascalCase.
     * @return string The converted string in kebab-case.
     */
    public static function pascalCaseToKebabCase(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $string));
    }

    /**
     * Generates the composer package name from the full bundle name.
     *
     * @param string $fullBundleName The full bundle name with namespace (e.g., Napse\DemoBundle).
     * @return string The composer package name in the format 'vendor/bundle-name'.
     */
    public static function generateComposerName(string $fullBundleName): string
    {
        $parts = explode('\\', $fullBundleName);
        $vendor = strtolower(array_shift($parts));
        $bundle = self::pascalCaseToKebabCase(end($parts));

        return "{$vendor}/{$bundle}";
    }

    /**
     * Parses the full bundle name into namespace and class name.
     *
     * @param string $fullBundleName The full bundle name with namespace (e.g., Napse\DemoBundle).
     * @return array An array containing the namespace and class name.
     */
    public static function parseBundleName(string $fullBundleName): array
    {
        $parts = explode('\\', $fullBundleName);
        $className = array_pop($parts);
        $namespace = implode('\\', $parts) . '\\' . $className;

        return [$namespace, $className];
    }

    /**
     * Generates the path name from the full bundle name.
     *
     * @param string $fullBundleName The full bundle name with namespace (e.g., Napse\DemoBundle).
     * @return string The generated path name in kebab-case (e.g., napse-demo-bundle).
     */
    public static function generatePathName(string $fullBundleName): string
    {
        // Remove backslashes and convert CamelCase to kebab-case
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', str_replace('\\', '', $fullBundleName)));
    }
}
