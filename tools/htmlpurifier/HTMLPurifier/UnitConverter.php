<?php

/**
 * Class for converting between different unit-lengths as specified by
 * CSS.
 */
class HTMLPurifier_UnitConverter
{

    const ENGLISH = 1;
    const METRIC = 2;
    const DIGITAL = 3;

    /**
     * Units information array. Units are grouped into measuring systems
     * (English, Metric), and are assigned an integer representing
     * the conversion factor between that unit and the smallest unit in
     * the system. Numeric indexes are actually magical constants that
     * encode conversion data from one system to the next, with a O(n^2)
     * constraint on memory (this is generally not a problem, since
     * the number of measuring systems is small.)
     */
    protected static $units = array(
        self::ENGLISH => array(
            'px' => 3, // This is as per CSS 2.1 and Firefox. Your mileage may vary
            'pt' => 4,
            'pc' => 48,
            'in' => 288,
            self::METRIC => array('pt', '0.352777778', 'mm'),
        ),
        self::METRIC => array(
            'mm' => 1,
            'cm' => 10,
            self::ENGLISH => array('mm', '2.83464567', 'pt'),
        ),
    );

    /**
     * Minimum bcmath precision for output.
     * @type int
     */
    protected $outputPrecision;

    /**
     * Bcmath precision for internal calculations.
     * @type int
     */
    protected $internalPrecision;

    /**
     * Whether or not BCMath is available.
     * @type bool
     */
    private $bcmath;

    public function __construct($output_precision = 4, $internal_precision = 10, $force_no_bcmath = false)
    {
        $this->outputPrecision = $output_precision;
        $this->internalPrecision = $internal_precision;
        $this->bcmath = !$force_no_bcmath && function_exists('bcmul');
    }

    /**
     * Converts a length object of one unit into another unit.
     * @param HTMLPurifier_Length $length
     *      Instance of HTMLPurifier_Length to convert. You must validate()
     *      it before passing it here!
     * @param string $to_unit
     *      Unit to convert to.
     * @return HTMLPurifier_Length|bool
     * @note
     *      About precision: This conversion function pays very special
     *      attention to the incoming precision of values and attempts
     *      to maintain a number of significant figure. Results are
     *      fairly accurate up to nine digits. Some caveats:
     *          - If a number is zero-padded as a result of this significant
     *            figure tracking, the zeroes will be eliminated.
     *          - If a number contains less than four sigfigs ($outputPrecision)
     *            and this causes some decimals to be excluded, those
     *            decimals will be added on.
     */
    public function convert($length, $to_unit)
    {
        if (!$length->isValid()) {
            return false;
        }

        $n = $length->getN();
        $unit = $length->getUnit();

        if ($n === '0' || $unit === false) {
            return new HTMLPurifier_Length('0', false);
        }

        $state = $dest_state = false;
        foreach (self::$units as $k => $x) {
            if (isset($x[$unit])) {
                $state = $k;
            }
            if (isset($x[$to_unit])) {
                $dest_state = $k;
            }
        }
        if (!$state || !$dest_state) {
            return false;
        }

        // Some calculations about the initial precision of the number;
        // this will be useful when we need to do final rounding.
        $sigfigs = $this->getSigFigs($n);
        if ($sigfigs < $this->outputPrecision) {
            $sigfigs = $this->outputPrecision;
        }

        // BCMath's internal precision deals only with decimals. Use
        // our default if the initial number has no decimals, or increase
        // it by how ever many decimals, thus, the number of guard digits
        // will always be greater than or equal to internalPrecision.
        $log = (int)floor(log(abs($n), 10));
        $cp = ($log < 0) ? $this->internalPrecision - $log : $this->internalPrecision; // internal precision

        for ($i = 0; $i < 2; $i++) {

            // Determine what unit IN THIS SYSTEM we need to convert to
            if ($dest_state === $state) {
                // Simple conversion
                $dest_unit = $to_unit;
            } else {
                // Convert to the smallest unit, pending a system shift
                $dest_unit = self::$units[$state][$dest_state][0];
            }

            // Do the conversion if necessary
            if ($dest_unit !== $unit) {
                $factor = $this->div(self::$units[$state][$unit], self::$units[$state][$dest_unit], $cp);
                $n = $this->mul($n, $factor, $cp);
                $unit = $dest_unit;
            }

            // Output was zero, so bail out early. Shouldn't ever happen.
            if ($n === '') {
                $n = '0';
                $unit = $to_unit;
                break;
            }

            // It was a simple conversion, so bail out
            if ($dest_state === $state) {
                break;
            }

            if ($i !== 0) {
                // Conversion failed! Apparently, the system we forwarded
                // to didn't have this unit. This should never happen!
                return false;
            }

            // Pre-condition: $i == 0

            // Perform conversion to next system of units
            $n = $this->mul($n, self::$units[$state][$dest_state][1], $cp);
            $unit = self::$units[$state][$dest_state][2];
            $state = $dest_state;

            // One more loop around to convert the unit in the new system.

        }

        // Post-condition: $unit == $to_unit
        if ($unit !== $to_unit) {
            return false;
        }

        // Useful for debugging:
        //echo "<pre>n";
        //echo "$n\nsigfigs = $sigfigs\nnew_log = $new_log\nlog = $log\nrp = $rp\n</pre>\n";

        $n = $this->round($n, $sigfigs);
        if (strpos($n, '.') !== false) {
            $n = rtrim($n, '0');
        }
        $n = rtrim($n, '.');

        return new HTMLPurifier_Length($n, $unit);
    }

    /**
     * Returns the number of significant figures in a string number.
     * @param string $n Decimal number
     * @return int number of sigfigs
     */
    public function getSigFigs($n)
    {
        $n = ltrim($n, '0+-');
        $dp = strpos($n, '.'); // decimal position
        if ($dp === false) {
            $sigfigs = strlen(rtrim($n, '0'));
        } else {
            $sigfigs = strlen(ltrim($n, '0.')); // eliminate extra decimal character
            if ($dp !== 0) {
                $sigfigs--;
            }
        }
        return $sigfigs;
    }

    /**
     * Adds two numbers, using arbitrary precision when available.
     * @param string $s1
     * @param string $s2
     * @param int $scale
     * @return string
     */
    private function add($s1, $s2, $scale)
    {
        if ($this->bcmath) {
            return bcadd($s1, $s2, $scale);
        } else {
            return $this->scale((float)$s1 + (float)$s2, $scale);
        }
    }

    /**
     * Multiples two numbers, using arbitrary precision when available.
     * @param string $s1
     * @param string $s2
     * @param int $scale
     * @return string
     */
    private function mul($s1, $s2, $scale)
    {
        if ($this->bcmath) {
            return bcmul($s1, $s2, $scale);
        } else {
            return $this->scale((float)$s1 * (float)$s2, $scale);
        }
    }

    /**
     * Divides two numbers, using arbitrary precision when available.
     * @param string $s1
     * @param string $s2
     * @param int $scale
     * @return string
     */
    private function div($s1, $s2, $scale)
    {
        if ($this->bcmath) {
            return bcdiv($s1, $s2, $scale);
        } else {
            return $this->scale((float)$s1 / (float)$s2, $scale);
        }
    }

    /**
     * Rounds a number according to the number of sigfigs it should have,
     * using arbitrary precision when available.
     * @param float $n
     * @param int $sigfigs
     * @return string
     */
    private function round($n, $sigfigs)
    {
        $new_log = (int)floor(log(abs((float)$n), 10)); // Number of digits left of decimal - 1
        $rp = $sigfigs - $new_log - 1; // Number of decimal places needed
        $neg = $n < 0 ? '-' : ''; // Negative sign
        if ($this->bcmath) {
            if ($rp >= 0) {
                $n = bcadd($n, $neg . '0.' . str_repeat('0', $rp) . '5', $rp + 1);
                $n = bcdiv($n, '1', $rp);
            } else {
                // This algorithm partially depends on the standardized
                // form of numbers that comes out of bcmath.
                $n = bcadd($n, $neg . '5' . str_repeat('0', $new_log - $sigfigs), 0);
                $n = substr($n, 0, $sigfigs + strlen($neg)) . str_repeat('0', $new_log - $sigfigs + 1);
            }
            return $n;
        } else {
            return $this->scale(round((float)$n, $sigfigs - $new_log - 1), $rp + 1);
        }
    }

    /**
     * Scales a float to $scale digits right of decimal point, like BCMath.
     * @param float $r
     * @param int $scale
     * @return string
     */
    private function scale($r, $scale)
    {
        if ($scale < 0) {
            // The f sprintf type doesn't support negative numbers, so we
            // need to cludge things manually. First get the string.
            $r = sprintf('%.0f', (float)$r);
            // Due to floating point precision loss, $r will more than likely
            // look something like 4652999999999.9234. We grab one more digit
            // than we need to precise from $r and then use that to round
            // appropriately.
            $precise = (string)round(substr($r, 0, strlen($r) + $scale), -1);
            // Now we return it, truncating the zero that was rounded off.
            return substr($precise, 0, -1) . str_repeat('0', -$scale + 1);
        }
        return number_format((float)$r, $scale, '.', '');
    }
}

// vim: et sw=4 sts=4
