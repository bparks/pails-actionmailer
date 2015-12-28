<?php

namespace Pails\ActionMailer;

class Mailer
{
    private $view;
    protected $layout;

    public static $from;

    private static $transport_name;
    private static $transport_options;
    private static $mailer_view_dirs = array('views');
    private static $default_layout = null;

    public static function __callStatic($name, $args)
    {
        $mailer = new static();
        $mailer->view = self::directoryName(get_class($mailer)).'/'.$name;
        $mailer->layout = self::$default_layout;
        return call_user_func_array([$mailer, $name], $args);
    }

    private static function directoryName($classname)
    {
        return strtolower(preg_replace('/(.)([A-Z])/e', "'$1_$2'", $classname));
    }

    public static function setTransport($classname, $options)
    {
        self::$transport_name = $classname;
        self::$transport_options = $options;
    }

    public static function useForMailerViews($path)
    {
        self::$mailer_view_dirs[] = $path;
    }

    public static function pathFor($filename)
    {
        $path = null;
        $i = 0;
        do {
            if ($i >= count(self::$mailer_view_dirs))
                return null;
            $path = self::$mailer_view_dirs[$i] . '/' . $filename;
            $i++;
        } while (!file_exists($path));
        return $path;
    }

    public static function setDefaultLayout($layout)
    {
        self::$default_layout = $layout;
    }

    protected function mail($to, $subject, $view = null, $layout = null, $model = null)
    {
        if ($view != null && !is_string($view) && $layout == null) {
            $model = $view;
            $view = null;
        }
        if ($layout != null && !is_string($layout) && $model == null) {
            $model = $layout;
            $layout = null;
        }
        if ($view == null || trim($view) == '')
            $view = $this->view;
        if ($layout == null || trim($layout) == '')
            $layout = $this->layout;
        if (self::$transport_name == null)
        {
            self::$transport_name = "\\Pails\\ActionMailer\\Transports\\NativeTransport";
            self::$transport_options = array();
        }
        return new Message($to, self::$from, $subject, $view, $layout, $model, new self::$transport_name(self::$transport_options));
    }
}
