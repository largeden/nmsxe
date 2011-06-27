<?php
    /**
     * @class  asn1
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

    class asn1 {

        var $stream;
        var $header;
        var $length;
        var $tag;
        var $sub;
        var $asns;

        function asn1($stream = array(), $header = '', $length = '', $tag = '', $sub = ''){
            $this->stream = $stream;
            $this->header = $header;
            $this->length = $length;
            $this->tag = $tag;
            $this->sub = $sub;

            return $this->toDOM();
        }

        function typeName() {
            if (!$this->tag) return 'unknown';
            $tagClass = $this->tag >> 6;
            $tagConstructed = ($this->tag >> 5) & 1;
            $tagNumber = $this->tag & 0x1F;

            switch ($tagClass) {
                case 0: // universal
                    switch ($tagNumber) {
                        case 0x00: return 'EOC';
                        case 0x01: return 'BOOLEAN';
                        case 0x02: return 'INTEGER';
                        case 0x03: return 'BIT_STRING';
                        case 0x04: return 'OCTET_STRING';
                        case 0x05: return 'NULL';
                        case 0x06: return 'OBJECT_IDENTIFIER';
                        case 0x07: return 'ObjectDescriptor';
                        case 0x08: return 'EXTERNAL';
                        case 0x09: return 'REAL';
                        case 0x0A: return 'ENUMERATED';
                        case 0x0B: return 'EMBEDDED_PDV';
                        case 0x0C: return 'UTF8String';
                        case 0x10: return 'SEQUENCE';
                        case 0x11: return 'SET';
                        case 0x12: return 'NumericString';
                        case 0x13: return 'PrintableString'; // ASCII subset
                        case 0x14: return 'TeletexString'; // aka T61String
                        case 0x15: return 'VideotexString';
                        case 0x16: return 'IA5String'; // ASCII
                        case 0x17: return 'UTCTime';
                        case 0x18: return 'GeneralizedTime';
                        case 0x19: return 'GraphicString';
                        case 0x1A: return 'VisibleString'; // ASCII subset
                        case 0x1B: return 'GeneralString';
                        case 0x1C: return 'UniversalString';
                        case 0x1E: return 'BMPString';
                        default: return "Universal_" . $tagNumber;
                    }
                case 1: return "Application_" . $tagNumber;
                case 2: return "[" . $tagNumber . "]"; // Context
                case 3: return "Private_" . $tagNumber;
            }
        }

        function content() {
            if (!$this->tag) return null;
            $tagClass = $this->tag >> 6;
            if ($tagClass != 0) return ($this->sub == null) ? null : "(" . count($this->sub) . ")";
            $tagNumber = $this->tag & 0x1F;
            $content = $this->posContent();
            $len = abs($this->length);

            switch ($tagNumber) {
                case 0x01: // BOOLEAN
                    return ($this->stream->get($content) == 0) ? 'false' : 'true';
                case 0x02: // INTEGER
                    return $this->stream->parseInteger($content, $content + $len);
                case 0x03: // BIT_STRING
                    if($this->sub) {
                        $str->size = count($this->sub);
                        $str->capacity = 'elem';
                        return $str;
                    } else return $this->stream->parseBitString($content, $content + $len);
                case 0x04: // OCTET_STRING
                    if($this->sub) {
                        $str->size = count($this->sub);
                        $str->capacity = 'elem';
                        return $str;
                    } else return $this->stream->parseOctetString($content, $content + $len);
                //case 0x05: // NULL
                case 0x06: // OBJECT_IDENTIFIER
                    return $this->stream->parseOID($content, $content + $len);
                //case 0x07: // ObjectDescriptor
                //case 0x08: // EXTERNAL
                //case 0x09: // REAL
                //case 0x0A: // ENUMERATED
                //case 0x0B: // EMBEDDED_PDV
                case 0x10: // SEQUENCE
                case 0x11: // SET
                    $str->size = count($this->sub);
                    $str->capacity = 'elem';
                    return $str;
                case 0x0C: // UTF8String
                    return $this->stream->parseStringUTF($content, $content + $len);
                case 0x12: // NumericString
                case 0x13: // PrintableString
                case 0x14: // TeletexString
                case 0x15: // VideotexString
                case 0x16: // IA5String
                //case 0x19: // GraphicString
                case 0x1A: // VisibleString
                //case 0x1B: // GeneralString
                //case 0x1C: // UniversalString
                //case 0x1E: // BMPString
                    return $this->stream->parseStringISO($content, $content + $len);
                case 0x17: // UTCTime
                case 0x18: // GeneralizedTime
                    return $this->stream->parseTime($content, $content + $len);
            }
            return null;
        }

        function toDOM() {
            $asn->sub = $this->sub;
            $asn->header = preg_replace("/_/", " ", $this->typeName());
            $asn->offset = @$this->stream->pos;
            $asn->length[] = $this->header;
            $asn->length[] = $this->length;
            $asn->octet_string = $this->content();

            if ($this->tag & 0x20) $asn->build = 'constructed';
            else if ((($this->tag == 0x03) || ($this->tag == 0x04)) && ($this->sub != null)) $asn->build = 'encapsulates';

            return $asn;
        }

        function gets() {
            return $this->asns;
        }

        function posContent() {
            return $this->stream->pos + $this->header;
        }

        function decodeLength($stream) {
            $buf = $stream->get();
            $len = $buf & 0x7F;

            if ($len == $buf) return $len;
            if ($len == 0) return -1; // undefined

            $buf = 0;
            for ($i = 0; $i < $len; $i++) $buf = ($buf << 8) | $stream->get();

            return $buf;
        }

        function hasContent($tag, $len, $stream) {
            if ($tag & 0x20) return true; // constructed
            if (($tag < 0x03) || ($tag > 0x04)) return false;

            $oP = new Stream();
            $p = $oP->Stream($stream);
            if ($tag == 0x03) $p->get(); // BitString unused bits, must be in [0, 7]

            $subTag = $p->get();
            if (($subTag >> 6) & 0x01) return false; // not (universal or context)

            return (($p->pos - $stream->pos) + $subLength == $len);
        }

        function decode($stream) {
            if(!$stream->enc) {
                $oStream = new Stream();
                $stream = $oStream->Stream($stream, 0);
            }

            $oStream = new Stream();
            $streamStart = $oStream->Stream($stream);
            $tag = $stream->get();
            $len = $this->decodeLength($stream);
            $header = $stream->pos - $streamStart->pos;
            $sub = null;

            if ($this->hasContent($tag, $len, $stream)) {

                $start = $stream->pos;
                // skip BitString unused bits, must be in [0, 7]
                if ($tag == 0x03) $stream->get();

                $sub = array();
                if ($len >= 0) {
                    // definite length
                    $end = $start + $len;

                    while($stream->pos < $end) $sub[count($sub)] = $this->decode($stream);

                }

            } else $stream->pos = $stream->pos + $len;

            return $this->asn1($streamStart, $header, $len, $tag, $sub);
        }

        function hex($a) {
            if(!$Base64->decoder) {
                $b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
                $allow = "= \f\n\r\t\u00A0\u2028\u2029";
                $dec = array();

                for ($i=0; $i<strlen($b64); $i++) $dec[substr($b64, $i, 1)] = $i;
                $allow = explode("\\",$allow);

                for ($i=0; $i<count($allow); $i++) $dec[$allow[$i]] = -1;

                $Base64->decoder = $dec;
            }

            $out = array();
            $bits = 0;
            $char_count = 0;

            for ($i=0; $i<strlen($a); $i++) {
                $c = substr($a, $i, 1);

                if($c == '=') break;

                $c = $Base64->decoder[$c];

                if($c == -1) continue;

                $bits |= $c;

                if($char_count++ >= 3) {
                    $out[count($out)] = ($bits >> 16);
                    $out[count($out)] = ($bits >> 8) & 0xFF;
                    $out[count($out)] = $bits & 0xFF;
                    $bits = 0;
                    $char_count = 0;
                } else $bits <<= 6;

            }

            switch ($char_count) {
              case 1:
                break;
              case 2:
                $out[count($out)] = ($bits >> 10);
                break;
              case 3:
                $out[count($out)] = ($bits >> 16);
                $out[count($out)] = ($bits >> 8) & 0xFF;
                break;
            }

            return $out;
        }

        function procAsn1($hex) {
            $ber = $this->decode($this->hex($hex));
            ksort($ber->sub);
            return $ber;
        }

        function arr($menu, $depth = '') {
            if(!is_array($menu)) $menu = array($menu);
            $depth++;

            foreach($menu as $key => $val){
                unset($item);
                $item->header = $val->header;
                $item->length = $val->length;
                $item->octet_string = $val->octet_string;
                $item->depth = $depth;
                $item->child_count = count($val->sub);
/*
                if(isset($item->octet_string->value) && preg_match("/^((\.1|1).3.6.*[0-9])$/", $item->octet_string->value)) {
                    if(!preg_match("/^\./", $item->octet_string->value)) $item->octet_string->value = ".".$item->octet_string->value;
                    $snmptrap_message = snmpwalkoid("10.0.0.10", "public", $item->octet_string->value);
                }
*/
                // snmp trap 메시지를 severity 검사하기 위해 정보를 저장
                if(in_array($item->header, array('OBJECT IDENTIFIER','OCTET STRING','INTEGER'))) $GLOBALS['asn1_octet_value'] .= $item->octet_string->value." | ";

                $GLOBALS['asn1_items'][] = $item;

                if($val->sub) $GLOBALS['asn1_items'] = $this->arr($val->sub, $depth);
            }

            return $GLOBALS['asn1_items'];
        }

        function _serialize($val = '') {
            if(!$val) return;
            $arr = $this->arr($val);
            unset($GLOBALS['asn1_items']);
            return $arr;
        }
    }
?>