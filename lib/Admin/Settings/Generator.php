<?php

namespace SGI\STL\Admin\Settings;

trait Generator
{

    public static function select(array $options, string $value, string $name, bool $enabled, string $label, string $description, string $class)
    {

        printf(
            '<select name="%s" class="%s" %s>',
            $name,
            $class,
            (!$enabled) ? 'readonly' : ''
        );

        foreach ($options as $opt_value => $opt_label) :

            printf(
                '<option value="%s" %s>%s</option>',
                $opt_value,
                selected($value, $opt_value, false),
                $opt_label
            );

        endforeach;

        printf(
            '</select>
            <p class="description">%s</p>',
            $description
        );

    }

    public static function checkbox(bool $value, string $name, bool $enabled, string $label, string $description)
    {

        printf(
            '<label for="%s">
                <input type="checkbox" name="%s" %s %s> %s
            </label>
            <p class="description">%s</p>',
            $name,
            $name,
            checked($value, true, false),
            (!$enabled) ? 'readonly' : '',
            $label,
            $description
        );

    }

    public static function input(string $value, string $name, bool $enabled, string $label, string $description)
    {

        printf(
            '<input type="text" name="%s" value="%s" %s>
            <p class="description">%s</p>',
            $name,
            $value,
            (!$enabled) ? 'readonly' : '',
            $description
        );

    }

}