<?php

function fixMenuRBAC() {
    $path = 'resources/views/layouts/app.blade.php';
    $content = file_get_contents($path);

    // Remove the @if(auth()->check() && auth()->user()->isAdmin()) and its corresponding @endif
    $content = str_replace('@if(auth()->check() && auth()->user()->isAdmin())', '', $content);
    
    // To remove the @endif, we need to be careful not to remove the wrong one.
    // In my previous diff, the @endif was right after Log Audit.
    // Let's replace the specific block.
    
    $search = <<<HTML
                        <a href="/admin/log-audit" class="flex items-center gap-3 px-3 py-2.5 {{ request()->is('admin/log-audit') ? 'bg-pkt-jingga text-white shadow-md' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium transition-colors text-sm">
                            <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            Log Audit
                        </a>
                        @endif
HTML;

    $replace = <<<HTML
                        <a href="/admin/log-audit" class="flex items-center gap-3 px-3 py-2.5 {{ request()->is('admin/log-audit') ? 'bg-pkt-jingga text-white shadow-md' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium transition-colors text-sm">
                            <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            Log Audit
                        </a>
HTML;

    $content = str_replace($search, $replace, $content);
    
    // Fallback if formatting was slightly different
    $content = preg_replace('/Log Audit\s*<\/a>\s*@endif/', "Log Audit\n                        </a>", $content);

    file_put_contents($path, $content);
    echo "Menu RBAC removed from view.\n";
}

fixMenuRBAC();
