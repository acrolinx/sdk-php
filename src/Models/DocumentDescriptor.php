<?php


namespace Acrolinx\SDK\Models;


class DocumentDescriptor
{
    private $id;
    private $customFields;
    private $displayInfo;

    public function __construct(string $id, CustomFieldCommon $customFieldCommon, string $displayInfo)
    {
        $this->id = $id;
        $this->customFields = $customFieldCommon;
        $this->displayInfo = $displayInfo;
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
    public function getDisplayInfo(): string
    {
        return $this->displayInfo;
    }

    /**
     * @return CustomFieldCommon
     */
    public function getCustomFields(): CustomFieldCommon
    {
        return $this->customFields;
    }
}