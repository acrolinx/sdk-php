

### Class: \Acrolinx\SDK\AcrolinxEndpoint

| Visibility | Function |
|:-----------|:---------|
| public | <strong>__construct(</strong><em>\Acrolinx\SDK\Models\AcrolinxEndPointProperties</em> <strong>$props</strong>, <em>\React\EventLoop\LoopInterface</em> <strong>$loop</strong>)</strong> : <em>void</em><br /><em>AcrolinxEndpoint constructor.</em> |
| public | <strong>check(</strong><em>\string</em> <strong>$authToken</strong>, <em>\Acrolinx\SDK\Models\CheckRequest</em> <strong>$request</strong>)</strong> : <em>PromiseInterface containing {@see \Acrolinx\SDK\Models\CheckResponse} or Exception</em><br /><em>Submit a check.</em> |
| public | <strong>getAcrolinxContentAnalysisDashboard(</strong><em>\string</em> <strong>$authToken</strong>, <em>\string</em> <strong>$batchId</strong>)</strong> : <em>PromiseInterface containing {@see \Acrolinx\SDK\Models\ContentAnalysisDashboardLinks} or Exception</em><br /><em>Get the link to the Acrolinx Content Analysis Dashboard for a batch check.</em> |
| public | <strong>getCapabilities(</strong><em>\string</em> <strong>$authToken</strong>)</strong> : <em>PromiseInterface containing {@see \Acrolinx\SDK\Models\PlatformCapabilities} or Exception</em><br /><em>Get current servers capabilities</em> |
| public | <strong>getCheckingCapabilities(</strong><em>\string</em> <strong>$authToken</strong>)</strong> : <em>PromiseInterface containing {@see \Acrolinx\SDK\Models\CheckCapabilities} or Exception</em><br /><em>Get supported options for check.</em> |
| public | <strong>getPlatformInformation()</strong> : <em>PromiseInterface containing {@see \Acrolinx\SDK\Models\PlatformInformation} or Exception</em><br /><em>Get server information</em> |
| public | <strong>pollforCheckResult(</strong><em>\string</em> <strong>$url</strong>, <em>\string</em> <strong>$authToken</strong>)</strong> : <em>PromiseInterface containg {@see \Acrolinx\SDK\Models\CheckResult} or Exception</em><br /><em>Poll for a check result.</em> |
| public | <strong>setClientLocale(</strong><em>mixed</em> <strong>$clientLocale</strong>)</strong> : <em>void</em><br /><em>Sets the language interface.</em> |
| public | <strong>signIn(</strong><em>\Acrolinx\SDK\Models\SsoSignInOptions</em> <strong>$options</strong>)</strong> : <em>PromiseInterface containing {@see \Acrolinx\SDK\Models\SignInSuccessData} or Exception</em><br /><em>Sign in to authenticate with the Acrolinx Core Platform.</em> |

