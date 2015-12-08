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

namespace DmitryMamontov\PhoneNormalizer;

use DmitryMamontov\PhoneNormalizer\Object\PhoneObject;

/**
 * PhoneNormalizer - Normalizer phone numbers.
 *
 * @author    Dmitry Mamontov <d.slonyara@gmail.com>
 * @copyright 2015 Dmitry Mamontov <d.slonyara@gmail.com>
 * @license   http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @version   Release: 1.0.1
 * @link      https://github.com/dmamontov/phone-normalizer/
 * @since     Class available since Release 1.0.1
 */
class PhoneNormalizer
{
    /**
     * The list of possible codes of countries and cities.
     * 
     * @var array
     * @access protected
     */
    protected $codes = array();

    /**
     * Flag converting a literal character to numbers.
     * 
     * @var boolean
     * @access protected
     */
    protected $convert = true;

    /**
     * Sets a flag converting a literal character to numbers.
     * 
     * @return PhoneNormalizer
     * @access public
     */
    public function enableConvert()
    {
        $this->convert = true;

        return $this;
    }

    /**
     * Removes the flag converting a literal character to numbers.
     *
     * @return PhoneNormalizer
     * @access public
     */
    public function disableConvert()
    {
        $this->convert = false;

        return $this;
    }

    /**
     * Checks to see if the flag converting a literal character to numbers.
     *
     * @return boolean
     * @access public
     */
    public function isEnabledConvert()
    {
        return $this->convert;
    }

    /**
     * Checks if flag is removed converting a literal character to numbers.
     *
     * @return boolean
     * @access public
     */
    public function isDisabledConvert()
    {
        return !$this->convert;
    }

    /**
     * Gets a list of possible codes of countries and cities.
     *
     * @return array
     * @access public
     */
    public function getCodes()
    {
        return $this->codes;
    }

    /**
     * Loading the list of possible codes of countries and cities from different sources.
     * Possible values are:
     * - Array
     * - A string in the format json
     * - URL of the file containing the json
     * - The local path of the file containing the json
     *
     * @param mixed $codes
     * @return PhoneNormalizer
     * @throws InvalidArgumentException
     * @access public
     */
    public function loadCodes($codes)
    {
        if (empty($codes) || is_null($codes)) {
            throw new \InvalidArgumentException('Parameter `codes` must contains a data');
        } else if (is_array($codes)) {
            $this->loadCodesByArray($codes);
        } else if ($this->isJson($codes)) {
            $this->loadCodesByJsonString($codes);
        } else if (preg_match_all('/((?:http|https)(?::\\/{2}[\\w]+)(?:[\\/|\\.]?)(?:[^\\s"]*))/is', $codes)) {
            $this->loadCodesByRemoteJsonFile($codes);
        } else if (preg_match_all('/((?:\\/[\\w\\.\\-]+)+)/is', $codes)) {
            $this->loadCodesByJsonFile($codes);
        }

        return $this;
    }

    /**
     * Loading the list of possible codes of countries and cities from the array.
     *
     * @param array $codes
     * @return PhoneNormalizer
     * @throws InvalidArgumentException
     * @access public
     */
    public function loadCodesByArray($codes)
    {
        if (!sizeof($codes)) {
            throw new \InvalidArgumentException('Parameter `codes` must contains a data');
        }

        $this->codes = $codes;

        return $this;
    }

    /**
     * Loading the list of possible codes of countries and cities from the file containing the json.
     *
     * @param string $codesFile
     * @return PhoneNormalizer
     * @throws InvalidArgumentException
     * @access public
     */
    public function loadCodesByJsonFile($codesFile)
    {
        if (empty($codesFile) || is_null($codesFile)) {
            throw new \InvalidArgumentException('Parameter `codesFile` must contains a data');
        } else if (!file_exists($codesFile)) {
            throw new \InvalidArgumentException('File not found');
        } else if (!is_file($codesFile) && is_dir($codesFile)) {
            throw new \InvalidArgumentException('File can not be a directory');
        } else if (!is_readable($codesFile)) {
            throw new \InvalidArgumentException('Can not read the file');
        }

        $this->loadCodesByJsonString(file_get_contents($codesFile));

        return $this;
    }

    /**
     * Loading the list of possible codes of countries and cities from the file containing the json on a remote server.
     *
     * @param string $codesRemote
     * @return PhoneNormalizer
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @access public
     */
    public function loadCodesByRemoteJsonFile($codesRemote)
    {
        if (empty($codesRemote) || is_null($codesRemote)) {
            throw new \InvalidArgumentException('Parameter `codesRemote` must contains a data');
        }

        $codesString = file_get_contents($codesRemote);
        if (!$codesString) {
            throw new \RuntimeException('Failed to get a file on a remote server');
        }

        $this->loadCodesByJsonString($codesString);
        unset($codesString);

        return $this;
    }

    /**
     * Loading the list of possible codes of countries and cities from the string in the format json.
     *
     * @param string $codesString
     * @return PhoneNormalizer
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @access public
     */
    public function loadCodesByJsonString($codesString)
    {
        if (empty($codesString) || is_null($codesString)) {
            throw new \InvalidArgumentException('Parameter `codesString` must contains a data');
        } else if (!$this->isJson($codesString)) {
            throw new \RuntimeException('Not Valid json');
        }

        $this->loadCodesByArray(json_decode($codesString, true));

        return $this;
    }

    /**
     * It normalizes the list of phone numbers.
     *
     * @param array $phones
     * @return array
     * @throws InvalidArgumentException
     * @access public
     */
    public function normalizeAll($phones)
    {
        if (!sizeof($phones)) {
            throw new \InvalidArgumentException('Parameter `phones` must contains a data');
        }

        return array_map(array($this, 'normalize'), $phones);
    }

    /**
     * It normalizes the phone number.
     *
     * @param string $phone
     * @return PhoneObject
     * @access public
     */
    public function normalize($phone)
    {
        if (empty($phone) || is_null($phone)) {
            return new PhoneObject();
        }

        $phone = $this->prepare(trim((string) $phone));

        if (strlen($phone) > 7) {
            foreach ($this->getCodes() as $countryCode => $data) {
                if ($phoneObject = $this->parse($data, $countryCode, $phone)) {
                    return $phoneObject;
                }
            }
        }

        return new PhoneObject(null, null, null, $phone);
    }

    /**
     * Pre processing phone number, removing all unnecessary characters.
     *
     * @param string $phone
     * @return string
     * @access private
     */
    private function prepare($phone)
    {
        $phone = preg_replace('/[^0-9A-Za-z]/', '', $phone);

        if ($this->isEnabledConvert() && !is_numeric($phone)) {
            $phone = $this->converting($phone);
        } else {
            $phone = preg_replace('/[^0-9]/', '', $phone);
        }

        if (substr($phone, 0, 2) == '00') {
            $phone = substr($phone, 2, strlen($phone) - 2);
        }

        return $phone;
    }

    /**
     * Parse a phone number on the components.
     *
     * @param array $data
     * @param integer $countryCode
     * @param string $phone
     * @return mixed
     * @access private
     */
    private function parse($data, $countryCode, $phone)
    {
        $zero = false;
        $codeLength = strlen($countryCode);

        if (substr($phone, 0, $codeLength) != $countryCode) {
            return false;
        } elseif ($countryCode == 8) {
            $phone[0] = $countryCode = '7';
        }

        $phone = substr($phone, $codeLength, strlen($phone) - $codeLength);

        if (isset($data['zero']) && $data['zero'] && $phone[0] == '0') {
            $zero = true;
            $phone = substr($phone, 1, strlen($phone) - 1);
        }

        $code = null;

        if (isset($data['exceptions'])) {
            for (
                $cityCodeLength = strlen((string) max($data['exceptions']));
                $cityCodeLength >= strlen((string) min($data['exceptions']));
                $cityCodeLength--
            ) {
                if (in_array(substr($phone, 0, $cityCodeLength), $data['exceptions'])) {
                    $code = sprintf('%s%d', $zero ? '0' : '', substr($phone, 0, $cityCodeLength));
                    $phone = substr($phone, $cityCodeLength, strlen($phone) - $cityCodeLength);
                    break;
                }
            }
        }

        if (is_null($code)) {
            if (!isset($data['code_length'])) {
                $data['code_length'] = 0;
            }

            $code = substr($phone, 0, $data['code_length']);
            $phone = substr($phone, $data['code_length'], strlen($phone) - $data['code_length']);
        }

        return new PhoneObject($countryCode, $data['name'], $code, $phone);
    }

    /**
     * Convert a literal character in digital.
     *
     * @param string $phone
     * @return string
     * @access private
     */
    private function converting($phone)
    {
        $replace = array(
            '2' => array('a','b','c'),
            '3' => array('d','e','f'),
            '4' => array('g','h','i'),
            '5' => array('j','k','l'),
            '6' => array('m','n','o'),
            '7' => array('p','q','r','s'),
            '8' => array('t','u','v'),
            '9' => array('w','x','y','z')
        );

        foreach($replace as $digit => $letters) {
            $phone = str_ireplace($letters, $digit, $phone);
        }

        return $phone;
    }

    /**
     * Checks if a string json.
     *
     * @param string $phone
     * @return boolean
     * @access private
     */
    private function isJson($string)
    {
        if (is_string($string) == false) {
            return false;
        }

        json_decode($string);

        return (json_last_error() == JSON_ERROR_NONE);
    }
}
