<?php


/*
* Copyright 2019-present Acrolinx GmbH
*
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
*
*     http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*/

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