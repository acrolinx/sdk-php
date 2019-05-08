<?php

namespace Acrolinx\SDK;

abstract class ReportType
{
    const termHarvesting = 'termHarvesting';
    const scorecard = 'scorecard';
    const extractedText = 'extractedText';
    const request_text = 'extractedText';
}

abstract class ContentEncoding
{
    const none = 'none';
    const base64 = 'base64';
}

abstract class CheckType
{
    const batch = 'batch';
    const interactive = 'interactive';
    const baseline = 'baseline';
    const automated = 'automated';
}

abstract class GuidanceProfileStatus
{
    const ready = 'ready';
    const loading = 'loading';
    const unavailable = 'unavailable';
}
