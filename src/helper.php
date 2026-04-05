<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

use think\Collection;
use think\helper\Arr;

if (!function_exists('throw_if')) {
    /**
     * 按条件抛异常
     *
     * @template TValue
     * @template TException of \Throwable
     *
     * @param TValue                                     $condition
     * @param TException|class-string<TException>|string $exception
     * @param mixed                                      ...$parameters
     * @return TValue
     *
     * @throws TException
     */
    function throw_if($condition, $exception, ...$parameters)
    {
        if ($condition) {
            throw (is_string($exception) ? new $exception(...$parameters) : $exception);
        }

        return $condition;
    }
}

if (!function_exists('throw_unless')) {
    /**
     * 按条件抛异常
     *
     * @template TValue
     * @template TException of \Throwable
     *
     * @param TValue                                     $condition
     * @param TException|class-string<TException>|string $exception
     * @param mixed                                      ...$parameters
     * @return TValue
     *
     * @throws TException
     */
    function throw_unless($condition, $exception, ...$parameters)
    {
        if (!$condition) {
            throw (is_string($exception) ? new $exception(...$parameters) : $exception);
        }

        return $condition;
    }
}

if (!function_exists('tap')) {
    /**
     * 对一个值调用给定的闭包，然后返回该值
     *
     * @template TValue
     *
     * @param TValue                         $value
     * @param (callable(TValue): mixed)|null $callback
     * @return TValue
     */
    function tap($value, $callback = null)
    {
        if (is_null($callback)) {
            return $value;
        }

        $callback($value);

        return $value;
    }
}

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @template TValue
     *
     * @param TValue|\Closure(): TValue $value
     * @return TValue
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (!function_exists('collect')) {
    /**
     * Create a collection from the given value.
     *
     * @param mixed $value
     * @return Collection
     */
    function collect($value = null)
    {
        return new Collection($value);
    }
}

if (!function_exists('data_fill')) {
    /**
     * Fill in data where it's missing.
     *
     * @param mixed        $target
     * @param string|array $key
     * @param mixed        $value
     * @return mixed
     */
    function data_fill(&$target, $key, $value)
    {
        return data_set($target, $key, $value, false);
    }
}

if (!function_exists('data_get')) {
    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param mixed            $target
     * @param string|array|int $key
     * @param mixed            $default
     * @return mixed
     */
    function data_get($target, $key, $default = null)
    {
        if (is_null($key)) {
            return $target;
        }

        $key = is_array($key) ? $key : explode('.', $key);

        while (!is_null($segment = array_shift($key))) {
            if ('*' === $segment) {
                if ($target instanceof Collection) {
                    $target = $target->all();
                } elseif (!is_array($target)) {
                    return value($default);
                }

                $result = [];

                foreach ($target as $item) {
                    $result[] = data_get($item, $key);
                }

                return in_array('*', $key) ? Arr::collapse($result) : $result;
            }

            if (Arr::accessible($target) && Arr::exists($target, $segment)) {
                $target = $target[$segment];
            } elseif (is_object($target) && isset($target->{$segment})) {
                $target = $target->{$segment};
            } else {
                return value($default);
            }
        }

        return $target;
    }
}

if (!function_exists('data_set')) {
    /**
     * Set an item on an array or object using dot notation.
     *
     * @param mixed        $target
     * @param string|array $key
     * @param mixed        $value
     * @param bool         $overwrite
     * @return mixed
     */
    function data_set(&$target, $key, $value, $overwrite = true)
    {
        $segments = is_array($key) ? $key : explode('.', $key);

        if (($segment = array_shift($segments)) === '*') {
            if (!Arr::accessible($target)) {
                $target = [];
            }

            if ($segments) {
                foreach ($target as &$inner) {
                    data_set($inner, $segments, $value, $overwrite);
                }
            } elseif ($overwrite) {
                foreach ($target as &$inner) {
                    $inner = $value;
                }
            }
        } elseif (Arr::accessible($target)) {
            if ($segments) {
                if (!Arr::exists($target, $segment)) {
                    $target[$segment] = [];
                }

                data_set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite || !Arr::exists($target, $segment)) {
                $target[$segment] = $value;
            }
        } elseif (is_object($target)) {
            if ($segments) {
                if (!isset($target->{$segment})) {
                    $target->{$segment} = [];
                }

                data_set($target->{$segment}, $segments, $value, $overwrite);
            } elseif ($overwrite || !isset($target->{$segment})) {
                $target->{$segment} = $value;
            }
        } else {
            $target = [];

            if ($segments) {
                data_set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite) {
                $target[$segment] = $value;
            }
        }

        return $target;
    }
}

if (!function_exists('trait_uses_recursive')) {
    /**
     * 获取一个trait里所有引用到的trait
     *
     * @param string $trait Trait
     * @return array
     */
    function trait_uses_recursive(string $trait): array
    {
        $traits = class_uses($trait);
        foreach ($traits as $trait) {
            $traits += trait_uses_recursive($trait);
        }

        return $traits;
    }
}

if (!function_exists('class_basename')) {
    /**
     * 获取类名(不包含命名空间)
     *
     * @param mixed $class 类名
     * @return string
     */
    function class_basename($class): string
    {
        $class = is_object($class) ? get_class($class) : $class;
        return basename(str_replace('\\', '/', $class));
    }
}

if (!function_exists('class_uses_recursive')) {
    /**
     *获取一个类里所有用到的trait，包括父类的
     *
     * @param mixed $class 类名
     * @return array
     */
    function class_uses_recursive($class): array
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $results = [];
        $classes = array_merge([$class => $class], class_parents($class));
        foreach ($classes as $class) {
            $results += trait_uses_recursive($class);
        }

        return array_unique($results);
    }
}

if (!function_exists('array_is_list')) {
    /**
     * 判断数组是否为list
     *
     * @param array $array 数据
     * @return bool
     */    
    function array_is_list(array $array): bool
    {
        return array_values($array) === $array;
    }
}

if (!function_exists('json_validate')) {
    /**
     * 判断是否为有效json数据
     *
     * @param string $string 数据
     * @return bool
     */     
    function json_validate(string $string): bool 
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
if(!function_exists('str_contains')) {
    /**
     * 判断字符串是否包含某个子串
     *
     * @param string $haystack 被搜索的字符串
     * @param string|array $needles 需要搜索的字符串或字符串数组
     * @return bool
     */
    function str_contains(string $haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ('' !== $needle && false !== strpos($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }
}
if(!function_exists('curl')){
    /**
    * curl请求
    *
    * @param string url - url地址 require
    * @param array data [] 传递的参数
    * @param string timeout 30 超时时间
    * @param string request POST 请求类型
    * @param array header [] 头部参数
    * @param  bool curlFile false 是否curl上传文件
    * @return int http_code - http状态码
    * @return string error - 错误信息
    * @return string content - 内容
    */
    function curl($url, $data = [], $timeout = 30, $method = 'GET', $header = [], $curlFile = false) 
    {
        //初始化
        $ch = curl_init();
        $method = strtoupper($method);
        if ($method === 'GET' && !empty($data)) {
            $queryString = is_array($data) ? http_build_query($data) : $data;
            $url .= (strpos($url, '?') !== false ? '&' : '?') . $queryString;
        }
        //设置
        $options = [
            CURLOPT_URL            => $url,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HEADER         => false,
            CURLOPT_USERAGENT      => config('app.curl.user_agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36'),
            CURLOPT_REFERER        => request()->host(),
            // SSL 验证
            CURLOPT_SSL_VERIFYPEER => config('app.curl.ssl_verify', false),
            CURLOPT_SSL_VERIFYHOST => config('app.curl.ssl_verify_host', false),
        ];
        if ($method === 'POST') {
            $options[CURLOPT_POST] = true;
        } elseif ($method !== 'GET') {
            $options[CURLOPT_CUSTOMREQUEST] = $method;
        }
        if ($method !== 'GET' && !empty($data)) {
            if (is_array($data) && !$curlFile) {
                $options[CURLOPT_POSTFIELDS] = http_build_query($data);
            } else {
                $options[CURLOPT_POSTFIELDS] = $data;
            }
        }
        if (!empty($header)) {
            $options[CURLOPT_HTTPHEADER] = $header;
        }
        curl_setopt_array($ch, $options);
        $content   = curl_exec($ch);
        $error     = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        $result = ['http_code' => $http_code,'error' => $error,'content' => $content];
    
        return $result;
    }
}
