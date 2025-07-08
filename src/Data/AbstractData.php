<?php

namespace Laraditz\MyInvois\Data;

use ReflectionClass;
use Sabre\Xml\Writer;
use ReflectionProperty;
use ReflectionParameter;
use BadMethodCallException;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Sabre\Xml\XmlSerializable;
use Laraditz\MyInvois\Enums\XMLNS;
use Laraditz\MyInvois\Contracts\WithValue;
use Laraditz\MyInvois\Attributes\Attributes;
use Laraditz\MyInvois\Contracts\WithNamespace;

abstract class AbstractData implements WithNamespace, WithValue, XmlSerializable
{
    public function toArray(): array
    {
        $body = [];
        $class = new ReflectionClass(static::class);

        $constructor = $class->getConstructor();

        foreach ($constructor->getParameters() as $property) {

            if ($property->allowsNull() === false || $this->{$property->name}) {

                $body += [$property->name => $this->{$property->name}];
            }
        }

        return $body;
    }

    // in progress
    public function toJson()
    {
        $body = [];
        $class = new ReflectionClass(static::class);

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
        $body = [];

        $class = new ReflectionClass(static::class);

        $constructor = $class->getConstructor();

        foreach ($constructor->getParameters() as $property) {

            if ($property->allowsNull() === false || $this->{$property->name}) {

                $name = $property->name;
                $value = $this->getValue($name);

                $ns = $this->ns($name);

                if ($ns && $ns instanceof XMLNS && $ns->value !== '') {
                    // $name = '{' . $ns->getNamespace() . '}' . $name;
                    $name = $ns->value . ':' . $name;
                }

                $this->buildBody($body, $name, $value);
            }
        }

        return $body;
    }

    private function buildBody(array &$body, string $tagName, mixed $value)
    {
        $name = $tagName;
        $attributes = [];
        $propertyAttributes = [];
        $classAttributes = [];
        $subdata = [];

        if (
            is_object($value)
            && Str::of(get_class($value))->startsWith('Laraditz\\MyInvois\\Data')
        ) {
            // get class attributes
            $rc = new ReflectionClass($value);
            $rcAttributes = data_get($rc->getAttributes(Attributes::class), '0');
            if ($rcAttributes) {
                $attributeClass = $rcAttributes->newInstance();
                $classAttributes = $attributeClass->attrs;
            }

            if (
                property_exists($value, 'attributes')
                && method_exists($value, 'getAttributes')
            ) {
                $propertyAttributes = $value->getAttributes();
            }

            $attributes = [...$classAttributes, ...$propertyAttributes];

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

                if (
                    property_exists($value, 'attributes')
                    && count($value->attributes) > 0
                ) {
                    $subdata['attributes'] = $value->attributes;
                }

                $body[] = $subdata;
            } else {
                $subdata = [
                    'name' => $name,
                    'value' => $value->toXmlArray(),
                ];

                if (count($attributes) > 0) {
                    $subdata['attributes'] = $attributes;
                }

                $body[] = $subdata;
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
                    // if ($name == 'cac:AdditionalDocumentReference' && $val?->ID == 'L1') {
                    //     dd($name, $value, $val?->ID);
                    // }
                    $this->buildBody($body, $name, $val);
                }
            }
        } else {
            // $body += [
            //     $name => $value
            // ];
        }
    }

    public function xmlSerialize(Writer $writer): void
    {
        $writer->write($this->toXmlArray());
    }

    public function getValue(string $name): mixed
    {
        return $this->$name;
    }

    public function add(string $name, mixed $value): static
    {
        if (property_exists(static::class, $name)) {
            // $rp = new ReflectionProperty(static::class, $name);

            // if ($rp?->getType()?->getName() === 'array') {
            //     $this->$name[] = $value;
            // } else {
            //     $this->$name = $value;
            // }
            $this->$name = $value;

        } else {
            throw new BadMethodCallException(__('Property does not exists.'));
        }

        return $this;
    }
}