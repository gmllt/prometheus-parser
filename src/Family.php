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
use ReflectionClass;

/**
 * Class Family
 *
 * @category Library
 * @package  Gmllt\PromParser
 * @author   Gilles Miraillet <g.miraillet@gmail.com>
 * @license  https://github.com/gmllt/prometheus-parser/LICENSE proprietary
 * @link     https://github.com/gmllt/prometheus-parser
 */
class Family
{
    /**
     * Type 'counter'
     *
     * @var string
     */
    const TYPE_COUNTER = 'counter';

    /**
     * Type 'gauge'
     *
     * @var string
     */
    const TYPE_GAUGE = 'gauge';

    /**
     * Type 'histogram'
     *
     * @var string
     */
    const TYPE_HISTOGRAM = 'histogram';

    /**
     * Type 'summary'
     *
     * @var string
     */
    const _TYPE_SUMMARY = 'summary';

    /**
     * Available types
     *
     * @var string[]
     */
    protected static array $availableTypes = [];

    /**
     * Name
     *
     * @var string
     */
    protected string $name = '';

    /**
     * Type
     *
     * @var string
     */
    protected string $type = '';

    /**
     * Labels
     *
     * @var string[]
     */
    protected array $labels = [];

    /**
     * Help
     *
     * @var string
     */
    protected string $help = '';

    /**
     * Sample
     *
     * @var Sample[]
     */
    protected array $samples = [];

    /**
     * Family constructor.
     *
     * @param string   $name   Name
     * @param string   $type   Type
     * @param string   $help   Help
     * @param string[] $labels Labels
     */
    public function __construct(string $name = '', string $type = '', string $help = '', array $labels = [])
    {
        $this->name = $name;
        $this->type = $type;
        $this->labels = $labels;
        $this->help = $help;
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
     * Get type
     *
     * @return string Type
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param string $type Type
     *
     * @return self
     * @throws Exception
     */
    public function setType(string $type): self
    {
        if (!self::isValidType($type)) {
            throw new Exception(
                "Type '$type' does not appear to be valid. try using ('" . implode(
                    "','",
                    self::getAvailableTypes()
                ) . "')."
            );
        }
        $this->type = $type;
        return $this;
    }

    /**
     * Is type valid
     *
     * @param string $type Type to test
     *
     * @return bool
     */
    public static function isValidType(string $type): bool
    {
        return in_array($type, self::getAvailableTypes());
    }

    /**
     * Get available types
     *
     * @return string[]
     */
    public static function getAvailableTypes(): array
    {
        if (empty(self::$availableTypes)) {
            $startsWith = function ($haystack, $needle) {
                $length = strlen($needle);
                return substr($haystack, 0, $length) === $needle;
            };
            $reflection = new ReflectionClass(self::class);
            $constants = $reflection->getConstants();
            foreach ($constants as $key => $value) {
                if ($startsWith($key, 'TYPE_')) {
                    self::$availableTypes[] = $value;
                }
            }
        }
        return self::$availableTypes;
    }

    /**
     * Get labels
     *
     * @return string[]
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * Set labels
     *
     * @param string[] $labels Labels
     *
     * @return self
     */
    public function setLabels(array $labels): self
    {
        $this->labels = $labels;
        return $this;
    }

    /**
     * Get help
     *
     * @return string
     */
    public function getHelp(): string
    {
        return $this->help;
    }

    /**
     * Set help
     *
     * @param string $help Help
     *
     * @return self
     */
    public function setHelp(string $help): self
    {
        $this->help = $help;
        return $this;
    }

    /**
     * Get samples
     *
     * @return Sample[]
     */
    public function getSamples(): array
    {
        return $this->samples;
    }

    /**
     * Set samples
     *
     * @param Sample[] $samples Samples
     *
     * @return self
     */
    public function setSamples(array $samples): self
    {
        $this->samples = $samples;
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
        //show help
        $result .= '# HELP ' . $this->getName() . ' ' . $this->getHelp() . "\n";
        $result .= '# TYPE ' . $this->getName() . ' ' . $this->getType() . "\n";
        foreach ($this->getSamples() as $sample) {
            $result .= $sample->__toString();
        }
        return $result;
    }
}
