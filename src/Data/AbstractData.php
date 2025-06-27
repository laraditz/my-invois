<?php

namespace Laraditz\MyInvois\Data;

use Sabre\Xml\Writer;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Laraditz\MyInvois\Enums\XMLNS;
use Laraditz\MyInvois\Contracts\WithValue;
use Laraditz\MyInvois\Contracts\WithNamespace;

abstract class AbstractData implements WithNamespace, WithValue
{
    public function toArray(): array
    {
        $body = [];
        $class = new \ReflectionClass(static::class);

        $constructor = $class->getConstructor();

        foreach ($constructor->getParameters() as $property) {

            if ($property->allowsNull() === false || $this->{$property->name}) {

                $body += [$property->name => $this->{$property->name}];
            }
        }

        return $body;
    }

    public function toJson()
    {
        $body = [];
        $class = new \ReflectionClass(static::class);

        $constructor = $class->getConstructor();

        foreach ($constructor->getParameters() as $property) {

            if ($property->allowsNull() === false || $this->{$property->name}) {
                $value = $this->{$property->name};

                $body += [
                    $property->name => is_string($value) || is_numeric($value) ? [['_' => $value]] : [$value]
                ];
            }
        }

        // return $body;
        return json_encode($body);
    }

    public function toXmlArray()
    {
        $class = new \ReflectionClass(static::class);
        // $service = new \Sabre\Xml\Service();

        $body = [];
        $class = new \ReflectionClass(static::class);

        $constructor = $class->getConstructor();

        foreach ($constructor->getParameters() as $property) {

            if ($property->allowsNull() === false || $this->{$property->name}) {

                $name = $property->name;
                $value = $this->getValue($name);
                $subdata = null;

                $ns = $this->ns($name);

                if ($ns && $ns instanceof XMLNS) {
                    // $name = '{' . $ns->getNamespace() . '}' . $name;
                    $name = $ns->value . ':' . $name;
                }

                if (
                    is_object($value)
                    && Str::of(get_class($value))->startsWith('Laraditz\\MyInvois\\Data')
                ) {
                    if ($value instanceof Money) {
                        $subdata = [
                            'name' => $name,
                            'value' => $value->value,
                            'attributes' => ['currencyID' => $value->currencyID],
                        ];

                        $body[] = $subdata;

                    } elseif (property_exists($value, 'value')) {
                        $subdata = [
                            'name' => $name,
                            'value' => $value->value,
                        ];

                        if (property_exists($value, 'attributes')) {
                            $subdata['attributes'] = $value->attributes;
                        }

                        $body[] = $subdata;
                    } else {
                        $body[$name] = $value->toXmlArray();
                    }

                } elseif (is_string($value) || is_numeric($value)) {
                    $body += [
                        $name => $value
                    ];
                } elseif (is_bool($value)) {
                    $body += [
                        $name => $value === true ? 'true' : 'false'
                    ];

                } elseif (is_array($value)) {
                    if (data_get($value, 'value')) {
                        $data['name'] = $name;
                        $data['value'] = data_get($value, 'value');

                        $attributes = data_get($value, 'attributes');

                        if ($attributes && is_array($attributes) && count($attributes) > 0) {
                            $data['attributes'] = $attributes;
                        }

                        $body += [
                            $data
                        ];
                    } else {

                        foreach ($value as $key => $val) {
                            // dd($val, $val->toXmlArray());
                            $subdata = null;

                            if (
                                is_object($val)
                                && Str::of(get_class($val))->startsWith('Laraditz\\MyInvois\\Data')
                            ) {
                                if ($val instanceof Money) {
                                    $subdata = [
                                        'name' => $name,
                                        'value' => $val->value,
                                        'attributes' => ['currencyID' => $val->currencyID],
                                    ];

                                    $body[] = $subdata;
                                } elseif (property_exists($val, 'value')) {
                                    $subdata = [
                                        'name' => $name,
                                        'value' => $val->value,
                                    ];

                                    if (property_exists($val, 'attributes')) {
                                        $subdata['attributes'] = $val->attributes;
                                    }

                                    $body[] = $subdata;
                                } else {
                                    $body[] = [
                                        'name' => $name,
                                        'value' => $val->toXmlArray(),
                                    ];
                                }
                            }

                        }
                    }
                } else {
                    // $body += [
                    //     $name => $value
                    // ];
                }

            }
        }

        return $body;
    }

    public function getValue(string $name): mixed
    {
        return $this->$name;
    }
}