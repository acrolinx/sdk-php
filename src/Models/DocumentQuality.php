<?php


namespace Acrolinx\SDK\Models;


class DocumentQuality
{

    private $score;
    private $status;

    public function __construct(int $score, DocumentQualityStatus $qualityStatus)
    {
        $this->score = $score;
        $this->status = $qualityStatus;
    }

    /**
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * @return DocumentQualityStatus
     */
    public function getStatus(): DocumentQualityStatus
    {
        return $this->status;
    }

}