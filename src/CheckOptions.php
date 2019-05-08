<?php


namespace Acrolinx\SDK;


class CheckOptions
{
    public $guidanceProfileId;
    public $reportTypes;
    public $checkType;
    public $addons;
    public $partialCheckRanges;
    public $contentFormat;
    public $languageId;
    public $batchId;
    public $disableCustomFieldValidation;


    public function getJson()
    {
        return json_encode(SDKUtils::objectToArray($this));
    }


}


