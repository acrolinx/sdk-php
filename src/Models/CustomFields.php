<?php

namespace Acrolinx\SDK\Models;

class  CustomFieldCommon {
    protected $displayName;
    protected $key;
    protected $inputType;
    protected $value;

    public function __construct(string $displayName, string $key, CustomFieldInputType $customFieldInputType, string $value)
    {

    }


}

class CustomFieldList extends CustomFieldCommon {
    protected $type = CustomFieldType::TYPE_LIST;
    protected $possibleValues;

    public function __construct(string $displayName, string $key,
                                CustomFieldInputType $customFieldInputType, string $value, array $possibleValues)
    {
        parent::__construct($displayName, $key, $customFieldInputType, $value);
        $this->possibleValues = $possibleValues;
    }
}

class CustomFieldText extends CustomFieldCommon {
    protected $type = CustomFieldType::TYPE_TEXT;

    public function __construct(string $displayName, string $key, CustomFieldInputType $customFieldInputType, string $value)
    {
        parent::__construct($displayName, $key, $customFieldInputType, $value);
    }
}

abstract class CustomFieldInputType {
    const REQUIRED = 'required';
    const EXTERNALLY_PROVIDED = 'externally_provided';
    const OPTIONAL = 'optional';
}

abstract class CustomFieldType {
    const TYPE_LIST = 'list';
    const TYPE_TEXT = 'text';
}