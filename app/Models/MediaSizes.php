<?php

namespace App\Models;

class MediaSizes
{
    /*
     * @var integer|null
     */
    public $x;
    /*
     * @var integer|null
     */
    public $y;
    /*
     * @var string
     */
    public $xUnit = 'px';
    /*
     * @var string
     */
    public $yUnit = 'px';

    public function __construct($x, $y)
    {
        $intX = (int)$x;
        if ($intX) {
            $this->x = $intX;
            $this->setUnit((string)$x, $this->xUnit);
        }

        $intY = (int)$y;
        if ($intY) {
            $this->y = $intY;
            $this->setUnit((string)$y, $this->yUnit);
        }
    }

    private function setUnit(string $value, &$unitVar)
    {
        if (strpos($value, 'px') !== false) {
            $unitVar = 'px';
        }
        if (strpos($value, '%') !== false) {
            $unitVar = '%';
        }
    }
}
