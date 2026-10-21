<?php

namespace MarkWilson\XmlToJson;

/**
 * XML to JSON string converter
 *
 * @package MarkWilson\XmlToJson
 * @author  Mark Wilson <mark@89allport.co.uk>
 */
class XmlToJsonConverter
{
    public function convert(\SimpleXMLElement $xml): string
    {
        return json_encode($this->asArray($xml));
    }

    public function asArray(\SimpleXMLElement $xml): array
    {
        return [$xml->getName() => $this->getData($xml)];
    }

    private function getData(\SimpleXMLElement $xml): array|string
    {
        $data = [];

        foreach ($xml->attributes() as $key => $value) {
            $data['-' . $key] = (string) $value;
        }

        if ($xml->count() > 0) {
            $children = $xml->children();

            foreach ($children as $key => $child) {
                $childData = $this->getData($child);

                if (isset($data[$key])) {
                    if (is_array($data[$key])) {
                        $data[$key][] = $childData;
                    } else {
                        $data[$key] = [$data[$key], $childData];
                    }
                } else {
                    $data[$key] = [$childData];
                }
            }

            foreach ($data as $key => $value) {
                if (is_array($value) && count($value) === 1) {
                    $data[$key] = $value[0];
                }
            }
        } else {
            $value = (string) $xml;

            if (count($data) === 0) {
                $data = $value;
            } elseif (strlen((string) $xml)) {
                $data['#text'] = (string) $xml;
            }
        }

        return $data;
    }
}
