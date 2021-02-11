<?php

/**
 * Proprietary License
 *
 * Copyright (c) 2019-2021 Gilles MIRAILLET
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS
 * OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * PHP Version 7.4
 *
 * @category Library
 * @package  Gmllt\PromParser
 * @author   Gilles Miraillet <g.miraillet@gmail.com>
 * @license  https://github.com/gmllt/prometheus-parser/LICENSE proprietary
 * @link     https://github.com/gmllt/prometheus-parser
 */

namespace Gmllt\PromParser;

use Exception;
use Gmllt\PromParser\Builder\FamilyBuilder;
use Gmllt\PromParser\Builder\SampleBuilder;
use Prometheus\MetricFamilySamples;

/**
 * Class Parser
 *
 * @category Library
 * @package  Gmllt\PromParser
 * @author   Gilles Miraillet <g.miraillet@gmail.com>
 * @license  https://github.com/gmllt/prometheus-parser/LICENSE proprietary
 * @link     https://github.com/gmllt/prometheus-parser
 */
class Parser
{

    /**
     * Parse string
     *
     * @param string $parsed String to parse
     *
     * @return MetricFamilySamples[]
     * @throws Exception
     */
    public static function parse(string $parsed): array
    {
        $families = [];
        $extractedFamilies = self::extractFamilies($parsed);
        foreach ($extractedFamilies as $extractedFamily) {
            $help = self::extractHelp($extractedFamily);
            $type = self::extractType($extractedFamily);
            $extractedSamples = self::extractMetricSamples($extractedFamily);
            $availableLabels = self::extractAvailableLabels($extractedSamples);
            $name = self::extractNameFromSamples($extractedSamples);
            if ($type === Family::TYPE_HISTOGRAM) {
                $name = preg_replace('~_(bucket|count|sum)$~', '', $name);
                // remove 'le' key from histogram
                foreach ($availableLabels as $key => $value) {
                    if ($value == 'le') {
                        unset($availableLabels[$key]);
                    }
                }
            }
            if (null !== $help && null !== $type && null !== $name) {
                $currentFamily = FamilyBuilder::buildFromArray(
                    [
                        FamilyBuilder::FIELD_NAME => $name,
                        FamilyBuilder::FIELD_HELP => $help,
                        FamilyBuilder::FIELD_TYPE => $type,
                        FamilyBuilder::FIELD_LABELS => $availableLabels,
                    ]
                );
                // create samples
                $currentSamples = [];
                foreach ($extractedSamples as $key => $extractedSample) {
                    $labels = self::extractLabels($extractedSample);
                    $currentName = self::extractNameFromSamples([$extractedSample]);
                    $value = self::extractValue($extractedSample);
                    if (null === $value) {
                        continue;
                    }
                    $currentSamples[] = SampleBuilder::buildFromArray(
                        [
                            SampleBuilder::FIELD_NAME => $currentName,
                            SampleBuilder::FIELD_LABELS => $labels,
                            SampleBuilder::FIELD_VALUE => $value,
                        ]
                    );
                }
                $currentFamily->setSamples($currentSamples);
                $families[] = $currentFamily;
            }
        }
        array_walk(
            $families,
            function (&$family) {
                if ($family instanceof Family) {
                    $family = $family->toPrometheusMetricFamilySamples();
                }
            }
        );
        return $families;
    }

    /**
     * Parse file
     *
     * @param string $file File to parse
     *
     * @return MetricFamilySamples[]
     * @throws Exception
     */
    public static function parseFile(string $file): array
    {
        $string = file_get_contents($file);
        return self::parse($string);
    }

    /**
     * Parse url
     *
     * @param string $url Url to parse
     *
     * @return MetricFamilySamples[]
     * @throws Exception
     */
    public static function parseUrl(string $url): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $string = curl_exec($ch);
        curl_close($ch);
        return self::parse($string);
    }

    /**
     * Extract families
     *
     * @param string $parsed String to parse
     *
     * @return array
     */
    protected static function extractFamilies(string $parsed): array
    {
        $matches = [];
        preg_match_all('~' . Regexp::metricDefinition() . '~m', $parsed, $matches);
        return $matches[0] ?? [];
    }

    /**
     * Extract help
     *
     * @param string $parsed String to parse
     *
     * @return string|null
     */
    protected static function extractHelp(string $parsed): ?string
    {
        $matches = [];
        preg_match('~' . Regexp::help() . '~', $parsed, $matches);
        return $matches['metric_help'] ?? null;
    }

    /**
     * Extract type
     *
     * @param string $parsed String to parse
     *
     * @return string|null
     */
    protected static function extractType(string $parsed): ?string
    {
        $matches = [];
        preg_match('~' . Regexp::type() . '~', $parsed, $matches);
        return $matches['metric_type'] ?? null;
    }

    /**
     * Extract metric samples
     *
     * @param string $parsed String to parse
     *
     * @return array
     */
    protected static function extractMetricSamples(string $parsed): array
    {
        $matches = [];
        preg_match_all('~' . Regexp::metricGroup() . '~', $parsed, $matches);
        $result = explode(PHP_EOL, $matches[0][0] ?? '');
        // remove empty
        foreach ($result as $key => $value) {
            if (empty(trim($value))) {
                unset($result[$key]);
            }
        }
        return $result;
    }

    /**
     * Extract available labels from samples
     *
     * @param array $samples Extracted samples as an array of strings
     *
     * @return array
     */
    protected static function extractAvailableLabels(array $samples): array
    {
        $labels = [];
        foreach ($samples as $sample) {
            $matches = [];
            preg_match_all('~' . Regexp::labelDefinition() . '~', $sample, $matches);
            $labels = array_unique(array_merge($labels, $matches['label_name']));
        }
        return $labels;
    }

    /**
     * Extract name from samples
     *
     * @param array $samples Extracted samples as an array of strings
     *
     * @return string|null
     */
    protected static function extractNameFromSamples(array $samples): ?string
    {
        foreach ($samples as $sample) {
            $matches = [];
            preg_match('~^' . Regexp::metricName() . '~', $sample, $matches);
            if (isset($matches['metric_name'])) {
                return $matches['metric_name'];
            }
        }
        return null;
    }

    /**
     * Extract labels
     *
     * @param string $extractedSample Extracted sample as string
     *
     * @return array
     */
    protected static function extractLabels(string $extractedSample): array
    {
        $labels = [];
        $matches = [];
        preg_match_all('~' . Regexp::labelDefinition() . '~', $extractedSample, $matches);
        $labelNames = $matches['label_name'] ?? [];
        $labelValues = $matches['label_value'] ?? [];
        foreach ($labelNames as $key => $labelName) {
            if (!isset($labelValues[$key])) {
                continue;
            }
            $labelValue = $labelValues[$key];
            if (empty($labelName)) {
                continue;
            }
            $labels[$labelName] = $labelValue;
        }
        return $labels;
    }

    /**
     * Extract value
     *
     * @param string $extractedSample Extracted sample as string
     *
     * @return float|null
     */
    protected static function extractValue(string $extractedSample): ?float
    {
        $matches = [];
        preg_match('~' . Regexp::metricValue() . '$~', $extractedSample, $matches);
        if (isset($matches['metric_value'])) {
            return floatval($matches['metric_value']);
        }
        return null;
    }
}
