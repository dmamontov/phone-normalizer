<?php
/**
 * PhoneNormalizer
 *
 * Copyright (c) 2015, Dmitry Mamontov <d.slonyara@gmail.com>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Dmitry Mamontov nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package   phone-normalizer
 * @author    Dmitry Mamontov <d.slonyara@gmail.com>
 * @copyright 2015 Dmitry Mamontov <d.slonyara@gmail.com>
 * @license   http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @since     File available since Release 1.0.1
 */
 
namespace DmitryMamontov\PhoneNormalizer\Object;

/**
 * PhoneObject - The object of the phone number.
 *
 * @author    Dmitry Mamontov <d.slonyara@gmail.com>
 * @copyright 2015 Dmitry Mamontov <d.slonyara@gmail.com>
 * @license   http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @version   Release: 1.0.1
 * @link      https://github.com/dmamontov/phone-normalizer/
 * @since     Class available since Release 1.0.1
 */
class PhoneObject
{
    /**
     * Data on the country.
     *
     * @var array
     * @access protected
     */
    protected $country = array('code' => null, 'name' => null);

    /**
     * Region code, city or mobile operator.
     *
     * @var string
     * @access protected
     */
    protected $code;

    /**
     * Phone without any codes.
     *
     * @var string
     * @access protected
     */
    protected $number;

    /**
     * Create an object of a phone number.
     *
     * @param integer $countryCode
     * @param string $countryName
     * @param string $code
     * @param string $number
     * @access public
     */
    public function __construct($countryCode = null, $countryName = null, $code = null, $number = null)
    {
        if (!is_null($countryCode)) {
            $this->setCountryCode($countryCode);
        }

        if (!is_null($countryName)) {
            $this->setCountryName($countryName);
        }

        if (!is_null($code)) {
            $this->setCode($code);
        }

        if (!is_null($number)) {
            $this->setNumber($number);
        }
    }

    /**
     * Get the code for the country.
     *
     * @return integer
     * @access public
     */
    public function getCountryCode()
    {
        return $this->country['code'];
    }

    /**
     * Set the country code.
     *
     * @param integer $countryCode
     * @return PhoneObject
     * @access public
     */
    public function setCountryCode($countryCode)
    {
        $this->country['code'] = $countryCode;

        return $this;
    }

    /**
     * Get the name of the country.
     *
     * @return string
     * @access public
     */
    public function getCountryName()
    {
        return $this->country['name'];
    }

    /**
     * Set the name of the country.
     *
     * @param string $countryName
     * @return PhoneObject
     * @access public
     */
    public function setCountryName($countryName)
    {
        $this->country['name'] = $countryName;

        return $this;
    }

    /**
     * Get the code for the region, city or mobile operator.
     *
     * @return string
     * @access public
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set the area code, city or mobile operator.
     *
     * @param string $code
     * @return PhoneObject
     * @access public
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get the phone number without any codes.
     *
     * @return string
     * @access public
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set the phone number without any codes.
     *
     * @param string $number
     * @return PhoneObject
     * @access public
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Format phone number in a given format.
     * The variables available in the format:
     * - # indicates one telephone number. It does not take into account the country code and area code.
     * - #CC# сode of the country.
     * - #CN# the name of the country.
     * - #c# area code, city or mobile operator.
     *
     * @param string $format
     * @return string
     * @access public
     */
    public function format($format = '+#CC#(#c#)###-##-##')
    {
        if (strpos($format, '#c#') !== false) {
            $format = str_replace('#c#', $this->getCode(), $format);
        }

        if (strpos($format, '#CC#') !== false) {
            $format = str_replace('#CC#', $this->getCountryCode(), $format);
        }

        if (strpos($format, '#CN#') !== false) {
            $format = str_replace('#CN#', $this->getCountryName(), $format);
        }

        $number = $this->getNumber();
        for ($length = 0, $position = 0; $length < strlen($format); $length++) {
            if ($position < strlen($number) && $format[$length] == '#') {
                $format[$length] = $number[$position++];
            } else if ($format[$length] == '#') {
                $format[$length] = ' ';
            }
        }

        $format = preg_replace('/[\s|\n|\t]+/', ' ', $format);
        $format = str_replace(array('()', '[]'), '', $format);
        $format = str_replace(array('- '), ' ', $format);

        return trim($format);
    }
}
