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
 * @package  Gmllt\PromParser\Builder
 * @author   Gilles Miraillet <g.miraillet@gmail.com>
 * @license  https://github.com/gmllt/prometheus-parser/LICENSE proprietary
 * @link     https://github.com/gmllt/prometheus-parser
 */

namespace Gmllt\PromParser\Builder;

use Gmllt\PromParser\Sample;

/**
 * Class SampleBuilder
 *
 * @category Library
 * @package  Gmllt\PromParser\Builder
 * @author   Gilles Miraillet <g.miraillet@gmail.com>
 * @license  https://github.com/gmllt/prometheus-parser/LICENSE proprietary
 * @link     https://github.com/gmllt/prometheus-parser
 */
class SampleBuilder
{
    /**
     * Field 'name'
     *
     * @var string
     */
    const FIELD_NAME = 'name';

    /**
     * Field 'labels'
     *
     * @var string
     */
    const FIELD_LABELS = 'labels';

    /**
     * Field 'value'
     *
     * @var string
     */
    const FIELD_VALUE = 'value';

    /**
     * Sample
     *
     * @var Sample
     */
    protected Sample $sample;

    /**
     * SampleBuilder constructor.
     */
    public function __construct()
    {
        $this->sample = new Sample();
    }

    /**
     * With name
     *
     * @param string $name Name
     *
     * @return $this
     */
    public function withName(string $name): self
    {
        $this->sample->setName($name);
        return $this;
    }

    /**
     * With labels
     *
     * @param array $labels Labels
     *
     * @return $this
     */
    public function withLabels(array $labels): self
    {
        $this->sample->setLabels($labels);
        return $this;
    }

    /**
     * With value
     *
     * @param float $value Value
     *
     * @return $this
     */
    public function withValue(float $value): self
    {
        $this->sample->setValue($value);
        return $this;
    }

    /**
     * Build
     *
     * @return Sample
     */
    public function build(): Sample
    {
        return $this->sample;
    }

    /**
     * Build from array
     *
     * @param array $array Definition
     *
     * @return Sample
     */
    public static function buildFromArray(array $array): Sample
    {
        return (new self())
            ->withName($array[self::FIELD_NAME] ?? '')
            ->withLabels($array[self::FIELD_LABELS])
            ->withValue($array[self::FIELD_VALUE])
            ->build();
    }
}
