<?php

namespace App\Http\Controllers;

use App\Settings\SiteSettings;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function __invoke(SiteSettings $settings): Response
    {
        $blocked = (bool) $settings->get('block_search_indexing', true);

        $body = $blocked
            ? "User-agent: *\nDisallow: /\n"
            : "User-agent: *\nDisallow:\n";

        return response($body, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
