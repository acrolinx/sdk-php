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

namespace Acrolinx\SDK\Exceptions;

use Exception;

// TODO delete class if not in use
class AcrolinxServerException extends Exception
{

    protected $_status;

    public function __construct($message = "", $code = 0, Exception $previous = NULL, $status = NULL)
    {
        $this->_status = $status;
        parent::__construct($message, $code, $previous);
    }

    public function getStatus()
    {
        return $this->_status;
    }
}