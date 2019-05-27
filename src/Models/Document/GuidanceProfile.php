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


class GuidanceProfile
{
    private $id;
    private $displayName;
    private $language;
    private $goals = array();

    public function __construct($guidanceProfile)
    {
        $this->id = $guidanceProfile->id;
        $this->displayName = $guidanceProfile->displayName;
        $this->language = new Language($guidanceProfile->language);

        foreach ($guidanceProfile->goals as $goal) {
            array_push($this->goals, new Goal($goal));
        }

    }

    /**
     * @return mixed
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return Language
     */
    public function getLanguage(): Language
    {
        return $this->language;
    }

    /**
     * @return array
     */
    public function getGoals(): array
    {
        return $this->goals;
    }
}

class Goal
{
    private $id;
    private $displayName;
    private $color;

    public function __construct($goal)
    {
        $this->id = $goal->id;
        $this->displayName = $goal->displayName;
        $this->color = $goal->color;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }
}

class Language
{
    private $id;
    private $displayName;

    public function __construct($language)
    {
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }
}