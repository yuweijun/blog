---
layout: post
title: "php函数json_encode兼容低版本"
date: "Fri, 14 Sep 2012 15:39:20 +0800"
categories: php
---

`json_enode`是在php-5.2.0才作为标准扩展加入的，之前的版本需要自己实现这个功能。

json_encode实现版本一
-----

{% highlight php %}
<?php
if(!function_exists('json_encode')) {
    function json_encode($arg) {
        $returnValue = '';
        $c = '';
        $i = '';
        $l = '';
        $s = '';
        $v = '';
        $numeric = true;

        switch (gettype($arg)) {
        case 'array':
            foreach ($arg AS $i => $v) {
                if (!is_numeric($i)) {
                    $numeric = false;
                    break;
                }
            }

            if ($numeric) {
                foreach ($arg AS $i => $v) {
                    if (strlen($s) > 0) {
                        $s .= ',';
                    }
                    $s .= json_encode($arg[$i]);
                }

                $returnValue = '[' . $s . ']';
            } else {
                foreach ($arg AS $i => $v) {
                    if (strlen($s) > 0) {
                        $s .= ',';
                    }
                    $s .= json_encode($i) . ':' . json_encode($arg[$i]);
                }

                $returnValue = '{' . $s . '}';
            }
            break;

        case 'object':
            foreach (get_object_vars($arg) AS $i => $v) {
                $v = json_encode($v);

                if (strlen($s) > 0) {
                    $s .= ',';
                }
                $s .= json_encode($i) . ':' . $v;
            }

            $returnValue = '{' . $s . '}';
            break;

        case 'integer':
        case 'double':
            $returnValue = is_numeric($arg) ? (string) $arg : 'null';
            break;

        case 'string':
            $returnValue = '"' . strtr($arg, array(
                "\r" => '\\r',
                "\n" => '\\n',
                "\t" => '\\t',
                "\b" => '\\b',
                "\f" => '\\f',
                '\\' => '\\\\',
                '"' => '\"',
                "\x00" => '\u0000',
                "\x01" => '\u0001',
                "\x02" => '\u0002',
                "\x03" => '\u0003',
                "\x04" => '\u0004',
                "\x05" => '\u0005',
                "\x06" => '\u0006',
                "\x07" => '\u0007',
                "\x08" => '\b',
                "\x0b" => '\u000b',
                "\x0c" => '\f',
                "\x0e" => '\u000e',
                "\x0f" => '\u000f',
                "\x10" => '\u0010',
                "\x11" => '\u0011',
                "\x12" => '\u0012',
                "\x13" => '\u0013',
                "\x14" => '\u0014',
                "\x15" => '\u0015',
                "\x16" => '\u0016',
                "\x17" => '\u0017',
                "\x18" => '\u0018',
                "\x19" => '\u0019',
                "\x1a" => '\u001a',
                "\x1b" => '\u001b',
                "\x1c" => '\u001c',
                "\x1d" => '\u001d',
                "\x1e" => '\u001e',
                "\x1f" => '\u001f'
            )) . '"';
            break;

        case 'boolean':
            $returnValue = $arg?'true':'false';
            break;

        default:
            $returnValue = 'null';
        }

        return $returnValue;
    }
}
?>
{% endhighlight %}

json_endcode实现版本二
-----

{% highlight php %}
<?php
if (!function_exists('json_encode')) {
    function json_encode($data) {
        switch ($type = gettype($data)) {
            case 'NULL':
                return 'null';
            case 'boolean':
                return ($data ? 'true' : 'false');
            case 'integer':
            case 'double':
            case 'float':
                return $data;
            case 'string':
                return '"' . addslashes($data) . '"';
            case 'object':
                $data = get_object_vars($data);
            case 'array':
                $output_index_count = 0;
                $output_indexed = array();
                $output_associative = array();
                foreach ($data as $key => $value) {
                    $output_indexed[] = json_encode($value);
                    $output_associative[] = json_encode($key) . ':' . json_encode($value);
                    if ($output_index_count !== NULL && $output_index_count++ !== $key) {
                        $output_index_count = NULL;
                    }
                }
                if ($output_index_count !== NULL) {
                    return '[' . implode(',', $output_indexed) . ']';
                } else {
                    return '{' . implode(',', $output_associative) . '}';
                }
            default:
                return ''; // Not supported
        }
    }
}
?>
{% endhighlight %}

实现版本三
-----

{% highlight php %}
<?php
function __json_encode( $data ) {
    if( is_array($data) || is_object($data) ) {
        $islist = is_array($data) && ( empty($data) || array_keys($data) === range(0,count($data)-1) );

        if( $islist ) {
            $json = '[' . implode(',', array_map('__json_encode', $data) ) . ']';
        } else {
            $items = Array();
            foreach( $data as $key => $value ) {
                $items[] = __json_encode("$key") . ':' . __json_encode($value);
            }
            $json = '{' . implode(',', $items) . '}';
        }
    } elseif( is_string($data) ) {
        # Escape non-printable or Non-ASCII characters.
        # I also put the \\ character first, as suggested in comments on the 'addclashes' page.
        $string = '"' . addcslashes($data, "\\\"\n\r\t/" . chr(8) . chr(12)) . '"';
        $json    = '';
        $len    = strlen($string);
        # Convert UTF-8 to Hexadecimal Codepoints.
        for( $i = 0; $i < $len; $i++ ) {

            $char = $string[$i];
            $c1 = ord($char);

            # Single byte;
            if( $c1 <128 ) {
                $json .= ($c1 > 31) ? $char : sprintf("\\u%04x", $c1);
                continue;
            }

            # Double byte
            $c2 = ord($string[++$i]);
            if ( ($c1 & 32) === 0 ) {
                $json .= sprintf("\\u%04x", ($c1 - 192) * 64 + $c2 - 128);
                continue;
            }

            # Triple
            $c3 = ord($string[++$i]);
            if( ($c1 & 16) === 0 ) {
                $json .= sprintf("\\u%04x", (($c1 - 224) <<12) + (($c2 - 128) << 6) + ($c3 - 128));
                continue;
            }

            # Quadruple
            $c4 = ord($string[++$i]);
            if( ($c1 & 8 ) === 0 ) {
                $u = (($c1 & 15) << 2) + (($c2>>4) & 3) - 1;

                $w1 = (54<<10) + ($u<<6) + (($c2 & 15) << 2) + (($c3>>4) & 3);
                $w2 = (55<<10) + (($c3 & 15)<<6) + ($c4-128);
                $json .= sprintf("\\u%04x\\u%04x", $w1, $w2);
            }
        }
    } else {
        # int, floats, bools, null
        $json = strtolower(var_export( $data, true ));
    }
    return $json;
}
?>
{% endhighlight %}

References
-----

1. [json_encode的php实现](http://yaronspace.cn/blog/archives/1128)
2. [json_encode](http://php.net/manual/en/function.json-encode.php)

