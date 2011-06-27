<?php
    /**
     * @class  Stream
     * @author largeden (webmaster@animeclub.net)
     * @brief  ASN.1 decoder and converting to php
     * ASN.1 JavaScript decoder
     * Copyright (c) 2008-2009 Lapo Luchini <lapo@lapo.it>
     *
     * Permission to use, copy, modify, and/or distribute this software for any
     * purpose with or without fee is hereby granted, provided that the above
     * copyright notice and this permission notice appear in all copies.
     *
     * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
     * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
     * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
     * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
     * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
     * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
     * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
     **/

    class Stream {

        var $enc;
        var $pos;

        function Stream($enc = '', $pos = 0) {
            if($enc->enc) {
                $this->enc = $enc->enc;
                $this->pos = $enc->pos;
            } else {
                $this->enc = $enc;
                $this->pos = $pos;
            }

            return $this;
        }

        function get($pos = '') {
            if (!$pos) $pos = $this->pos++;

            return $this->enc[$pos];
        }

        function hexByte($b = '') {
            $hexDigits = "0123456789ABCDEF";
            return substr($hexDigits, ($b >> 4) & 0xF, 1) . substr($hexDigits, ($b & 0xF), 1);
        }

        function parseStringISO($start = '', $end = '') {
            $str = array();
            for ($i = $start; $i < $end; $i++) $str->value .= chr($this->get($i));

            return $str;
        }

        function parseStringUTF($start = '', $end = '') {
            $str = array();
            $c = 0;
            for ($i = $start; $i < $end; ) {
                $c = $this->get($i++);

                if ($c < 128) $str->value .= chr($c);
                else if (($c > 191) && ($c < 224)) $str->value .= chr((($c & 0x1F) << 6) | ($this->get($i++) & 0x3F));
                else $str->value .= chr((($c & 0x0F) << 12) | (($this->get($i++) & 0x3F) << 6) | ($this->get($i++) & 0x3F));

                //TODO: this doesn't check properly 'end', some char could begin before and end after
            }

            return $str;
        }

        function parseTime($start = '', $end = '') {
            $reTime = "/^((?:1[89]|2\d)?\d\d)(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])([01]\d|2[0-3])(?:([0-5]\d)(?:([0-5]\d)(?:[.,](\d{1,3}))?)?)?(Z|[-+](?:[0]\d|1[0-2])([0-5]\d)?)?$/";

            $s = $this->parseStringISO($start, $end);

            preg_match($reTime, $s, $m);

            if (!preg_match($reTime, $s)) return "Unrecognized time: " . $s;

            $s = $m[1] . "-" . $m[2] . "-" . $m[3] . " " . $m[4];
            if ($m[5]) {
                $s .= ":" . $m[5];
                if ($m[6]) {
                    $s .= ":" . $m[6];
                    if ($m[7]) $s .= "." . $m[7];
                }
            }
            if ($m[8]) {
                $s .= " UTC";
                if ($m[8] != "Z") {
                    $s .= $m[8];
                    if ($m[9]) $s .= ":" . $m[9];
                }
            }
            return $s;
        }

        function parseInteger($start = '', $end = '') {
            //TODO support negative numbers
            $len = $end - $start;
            if ($len > 4) {
                $len <<= 3;
                $s = $this->get($start);
                if ($s == 0) $len -= 8;
                else
                    while ($s < 128) {
                        $s <<= 1;
                        $len--;
                    }

                $str->size = $len;
                $str->capacity = 'bit';

                return $str;
            }

            $str->value = 0;
            for ($i = $start; $i < $end; $i++) $str->value = ($str->value << 8) | $this->get($i);

            return $str;
        }

        function parseBitString($start = '', $end = '') {
            $unusedBit = $this->get($start);
            $lenBit = (($end - $start - 1) << 3) - $unusedBit;

            $str->size = $lenBit;
            $str->capacity = 'bit';

            if ($lenBit <= 20) {
                $skip = $unusedBit;
                $str->value .= " ";
                for ($i = $end - 1; $i > $start; $i--) {
                    $b = $this->get($i);
                    for ($j = $skip; $j < 8; $j++) $str->value .= ($b >> $j) & 1 ? '1' : '0';
                    $skip = 0;
                }
            }

            return $str;
        }

        function parseOctetString($start = '', $end = '') {
            $len = $end - $start;

            $str->size = $len;
            $str->capacity = 'byte';

            //if ($len > 20)
            //    $end = $start + 20;
            for ($i = $start; $i < $end; $i++) $str->value .= pack("H*", $this->hexByte($this->get($i)));

            //if ($len > 20) $str->value .= chr(8230); // ellipsis

            return $str;
        }

        function parseOID($start = '', $end = '') {
            $str->value = 0;
            $n = 0;
            $bits = 0;

            for ($i = $start; $i < $end; $i++) {
                $v = $this->get($i);
                $n = ($n << 7) | ($v & 0x7F);
                $bits += 7;
                if (!($v & 0x80)) { // finished
                    if (!$str->value) {
                        $s1 = $n / 40;
                        $s2 = $n % 40;
                        $str->value = intval($s1) . "." . intval($s2);
                    } else $str->value .= "." . (($bits >= 31) ? 'bigint' : $n);

                    $n = $bits = 0;
                }
                // $s .= chr('string');
            }

            return $str;
        }
    }
?>