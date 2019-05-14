<?php


namespace Acrolinx\SDK\Models;


class CheckResult
{

    private $id;
    private $document;
    private $quality;
    private $reports;

    public function __construct(string $id, DocumentDescriptor $documentDescriptor,
                                DocumentQuality $documentQuality, CheckResultReports $reports)
    {
        $this->id = $id;
        $this->document = $documentDescriptor;
        $this->quality = $documentQuality;
        $this->reports = $reports;
    }

    /**
     * @return DocumentDescriptor
     */
    public function getDocument(): DocumentDescriptor
    {
        return $this->document;
    }

    /**
     * @return DocumentQuality
     */
    public function getQuality(): DocumentQuality
    {
        return $this->quality;
    }

    /**
     * @return CheckResultReports
     */
    public function getReports(): CheckResultReports
    {
        return $this->reports;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }


}