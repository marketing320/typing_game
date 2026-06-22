<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockMobile
{
    /**
     * Block phones and tablets from the challenge flow — it must be played on the
     * official PC setup. Both the on/off switch and the message are admin-configurable.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $enabled = SystemSetting::get('mobile_block_enabled', '1');

        if ($enabled === '1' && $this->isMobile((string) $request->header('User-Agent'))) {
            $message = SystemSetting::get(
                'mobile_block_message',
                'This challenge can only be played on our official PC setup. Please come to our booth and use the computer provided to take part.'
            );

            return response()->view('mobile-blocked', ['message' => $message], 403);
        }

        return $next($request);
    }

    /**
     * User-Agent match for phones and tablets. Note: iPadOS Safari often reports a
     * desktop UA, so some iPads can slip through — UA detection is best-effort.
     */
    private function isMobile(string $userAgent): bool
    {
        if ($userAgent === '') {
            return false;
        }

        return (bool) preg_match(
            '/(android|iphone|ipod|ipad|iemobile|blackberry|opera mini|opera mobi|windows phone|webos|mobile|tablet|kindle|silk|playbook)/i',
            $userAgent
        );
    }
}
