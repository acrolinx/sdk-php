<?php

namespace Acrolinx\SDK;

// ToDo: I have currently kept access specifiers for data members as public for converting objects to JSON.
// ToDo: We need to write a recursive function to convert objects to JSON which converts private members.

class DocumentDescriptorRequest
{
    public $reference;

    public function __construct(string $reference)
    {
        $this->reference = $reference;
    }
}

class CheckRange
{
    public $begin;
    public $end;

    public function __construct(int $begin, int $end)
    {
        $this->end = $end;
        $this->begin = $begin;
    }

}

class CheckRequest
{
    public $content;
    public $contentEncoding;
    public $checkOptions;
    public $document;

    public function __construct($content)
    {
        $this->content = $content;
    }

    public function getJson()
    {
        return json_encode(SDKUtils::objectToArray($this));
    }

}