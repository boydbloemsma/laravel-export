<?php

namespace Spatie\Export\Jobs;

use Illuminate\Contracts\Routing\UrlGenerator;
use RuntimeException;
use Illuminate\Http\Request;
use Spatie\Export\Destination;
use Illuminate\Contracts\Http\Kernel;
use Spatie\Export\Traits\NormalizesPath;

class ExportPath
{
    use NormalizesPath;

    /** @var string */
    protected $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function handle(Kernel $kernel, Destination $destination, UrlGenerator $urlGenerator)
    {
        $response = $kernel->handle(
            Request::create($urlGenerator->to($this->path))
        );

        if ($response->status() !== 200) {
            throw new RuntimeException("Path [{$this->path}] returned status code [{$response->status()}]");
        }

        $destination->write($this->normalizePath($this->path), $response->content());
    }
}
