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
 * Class Sample
 *
 * @category Library
 * @package  Gmllt\PromParser
 * @author   Gilles Miraillet <g.miraillet@gmail.com>
 * @license  https://github.com/gmllt/prometheus-parser/LICENSE proprietary
 * @link     https://github.com/gmllt/prometheus-parser
 */
class Sample
{
    /**
     * Name
     *
     * @var string
     */
    protected string $name = '';

    /**
     * Labels
     *
     * @var array
     */
    protected array $labels = [];

    /**
     * Value
     *
     * @var float
     */
    protected float $value = 0.0;

    /**
     * Sample constructor.
     *
     * @param string $name   Name
     * @param array  $labels Label
     * @param float  $value  Value
     */
    public function __construct(string $name = '', array $labels = [], float $value = 0.0)
    {
        $this->name = $name;
        $this->labels = $labels;
        $this->value = $value;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name Name
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get labels
     *
     * @return array
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * Set labels
     *
     * @param array $labels Labels
     *
     * @return self
     */
    public function setLabels(array $labels): self
    {
        $this->labels = $labels;
        return $this;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * Set value
     *
     * @param float $value Value
     *
     * @return self
     */
    public function setValue(float $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * To string
     *
     * @return string
     */
    public function __toString(): string
    {
        $result = '';
        $result .= $this->getName();
        $labelString = '';
        $labels = $this->getLabels();
        if (!empty($labels)) {
            $labelString .= '{';
            foreach ($labels as $name => &$value) {
                $value = "$name=\"$value\"";
            }
            $labelString .= implode(',', $labels);
            $labelString .= '}';
        }
        $result .= $labelString;
        $result .= ' ' . $this->getValue();
        $result .= "\n";
        return $result;
    }
}
