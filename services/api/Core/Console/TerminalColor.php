<?php

namespace CloudCastle\Core\Console;

final class TerminalColor
{
    /**
     * ANSI коды для цветов текста
     */
    private const COLORS = [
        'black' => "\033[0;30m",   // Чёрный
        'red' => "\033[0;31m",     // Красный
        'green' => "\033[0;32m",   // Зелёный
        'yellow' => "\033[0;33m",  // Желтый
        'blue' => "\033[0;34m",    // Синий
        'magenta' => "\033[0;35m", // Магента
        'cyan' => "\033[0;36m",    // Циан
        'white' => "\033[0;37m",    // Белый
        'reset' => "\033[0m",      // Сброс цвета
    ];
    
    /**
     * @param string $text
     * @return string
     */
    public static function red(string $text): string
    {
        return self::COLORS['red'] . $text . self::COLORS['reset'];
    }
    
    /**
     * @param string $text
     * @return string
     */
    public static function green(string $text): string
    {
        return self::COLORS['green'] . $text . self::COLORS['reset'];
    }
    
    /**
     * @param string $text
     * @return string
     */
    public static function yellow(string $text): string
    {
        return self::COLORS['yellow'] . $text . self::COLORS['reset'];
    }
    
    /**
     * @param string $text
     * @return string
     */
    public static function blue(string $text): string
    {
        return self::COLORS['blue'] . $text . self::COLORS['reset'];
    }
    
    /**
     * @param string $text
     * @return string
     */
    public static function magenta(string $text): string
    {
        return self::COLORS['magenta'] . $text . self::COLORS['reset'];
    }
    
    /**
     * @param string $text
     * @return string
     */
    public static function cyan(string $text): string
    {
        return self::COLORS['cyan'] . $text . self::COLORS['reset'];
    }
    
    /**
     * @param string $text
     * @return string
     */
    public static function white(string $text): string
    {
        return self::COLORS['white'] . $text . self::COLORS['reset'];
    }
}