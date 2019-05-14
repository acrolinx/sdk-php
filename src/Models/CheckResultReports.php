<?php


namespace Acrolinx\SDK\Models;


class CheckResultReports
{
    private $report;
    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    /**
     * @return Report
     */
    public function getReport(): Report
    {
        return $this->report;
    }

}