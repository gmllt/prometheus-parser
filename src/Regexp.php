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

/**
 * Class Regexp
 *
 * @category Library
 * @package  Gmllt\PromParser
 * @author   Gilles Miraillet <g.miraillet@gmail.com>
 * @license  https://github.com/gmllt/prometheus-parser/LICENSE proprietary
 * @link     https://github.com/gmllt/prometheus-parser
 */
abstract class Regexp
{

    /**
     * Get metric name regexp
     *
     * @return string
     */
    public static function metricName(): string
    {
        return '(?<metric_name>[a-zA-Z_:][a-zA-Z0-9_:]*)';
    }

    /**
     * Get metric value regexp
     *
     * @return string
     */
    public static function metricValue(): string
    {
        return '(?<metric_value>[-+]?([0-9]*[.])?[0-9]+([eE][-+]?\d+)?)';
    }

    /**
     * Get label name regexp
     *
     * @return string
     */
    public static function labelName(): string
    {
        return '(?<label_name>[a-zA-Z_][a-zA-Z0-9_]*)';
    }

    /**
     * Get label value regexp
     *
     * @return string
     */
    public static function labelValue(): string
    {
        return '"(?<label_value>[^"]*)"';
    }

    /**
     * Get label definition regexp
     *
     * @return string
     */
    public static function labelDefinition(): string
    {
        return '(?<label_definition>' . self::labelName() . '=' . self::labelValue() . ')';
    }

    /**
     * Get label group regexp
     *
     * @return string
     */
    public static function labelGroup(): string
    {
        return '(?<label_group>{(' . self::labelDefinition() . ',?)+})?';
    }

    /**
     * Get metric regexp
     *
     * @return string
     */
    public static function metric(): string
    {
        return self::metricName() . self::labelGroup() . '[\s]+' . self::metricValue();
    }

    /**
     * Get metric group regexp
     *
     * @return string
     */
    public static function metricGroup(): string
    {
        return '(?<metric_group>(' . self::metric() . '\n?)+)';
    }

    /**
     * Get help regexp
     *
     * @return string
     */
    public static function help(): string
    {
        return '#[\s]HELP[\s]+' . self::metricName() . '[\s]+(?<metric_help>[^\n]*)';
    }

    /**
     * Get type regexp
     *
     * @return string
     */
    public static function type(): string
    {
        return '#[\s]TYPE[\s]+(' . self::metricName() . '[\s]+)?(?<metric_type>(' . implode(
                '|',
                Family::getAvailableTypes()
            ) . '))';
    }

    /**
     * Get metric definition regexp
     *
     * @return string
     */
    public static function metricDefinition(): string
    {
        return preg_replace(
            '~\?<[a-z_]+>~',
            '',
            '(' . self::help() . '\n)?' . self::type() . '\n' . self::metricGroup()
        );
    }
}
