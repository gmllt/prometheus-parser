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

use Exception;
use Gmllt\PromParser\Family;

/**
 * Class FamilyBuilder
 *
 * @category Library
 * @package  Gmllt\PromParser\Builder
 * @author   Gilles Miraillet <g.miraillet@gmail.com>
 * @license  https://github.com/gmllt/prometheus-parser/LICENSE proprietary
 * @link     https://github.com/gmllt/prometheus-parser
 */
class FamilyBuilder
{
    /**
     * Field 'name'
     *
     * @var string
     */
    public const FIELD_NAME = 'name';
    /**
     * Field 'type'
     *
     * @var string
     */
    public const FIELD_TYPE = 'type';

    /**
     * Field 'help'
     *
     * @var string
     */
    public const FIELD_HELP = 'help';

    /**
     * Field 'labels'
     *
     * @var string
     */
    public const FIELD_LABELS = 'labels';

    /**
     * Family
     *
     * @var Family
     */
    protected Family $family;

    /**
     * FamilyBuilder constructor.
     */
    public function __construct()
    {
        $this->family = new Family();
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
        $this->family->setName($name);
        return $this;
    }

    /**
     * With type
     *
     * @param string $type Type
     *
     * @return $this
     * @throws Exception
     */
    public function withType(string $type): self
    {
        $this->family->setType($type);
        return $this;
    }

    /**
     * With help
     *
     * @param string $help Help
     *
     * @return $this
     */
    public function withHelp(string $help): self
    {
        $this->family->setHelp($help);
        return $this;
    }

    /**
     * With labels
     *
     * @param string[] $labels Labels
     *
     * @return $this
     */
    public function withLabels(array $labels): self
    {
        $this->family->setLabels($labels);
        return $this;
    }

    /**
     * Build
     *
     * @return Family
     */
    public function build(): Family
    {
        return $this->family;
    }

    /**
     * Build from array
     *
     * @param array $array Definition
     *
     * @return Family
     * @throws Exception
     */
    public static function buildFromArray(array $array): Family
    {
        return (new self())
            ->withName($array[self::FIELD_NAME] ?? '')
            ->withType($array[self::FIELD_TYPE] ?? '')
            ->withHelp($array[self::FIELD_HELP] ?? '')
            ->withLabels($array[self::FIELD_LABELS] ?? [])
            ->build();
    }
}
