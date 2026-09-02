<?php
$content = file_get_contents('resources/views/layouts/app.blade.php');

$search = <<<EOD
                        <!-- Manajemen Pengguna -->
                        <a href="/admin/manajemen-pengguna" class="flex items-center gap-3 px-3 py-2.5 {{ request()->is('admin/manajemen-pengguna') ? 'bg-pkt-jingga text-white shadow-md' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium transition-colors text-sm">
                            <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Manajemen Pengguna
                        </a>

                        <a href="/admin/log-audit" class="flex items-center gap-3 px-3 py-2.5 {{ request()->is('admin/log-audit') ? 'bg-pkt-jingga text-white shadow-md' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium transition-colors text-sm">
                            <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            Log Audit
                        </a>
EOD;

$replace = <<<EOD
                        @if(auth()->check() && auth()->user()->isAdmin())
                        <!-- Manajemen Pengguna -->
                        <a href="/admin/manajemen-pengguna" class="flex items-center gap-3 px-3 py-2.5 {{ request()->is('admin/manajemen-pengguna') ? 'bg-pkt-jingga text-white shadow-md' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium transition-colors text-sm">
                            <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Manajemen Pengguna
                        </a>

                        <a href="/admin/log-audit" class="flex items-center gap-3 px-3 py-2.5 {{ request()->is('admin/log-audit') ? 'bg-pkt-jingga text-white shadow-md' : 'text-blue-100 hover:bg-white/10' }} rounded-lg font-medium transition-colors text-sm">
                            <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            Log Audit
                        </a>
                        @endif
EOD;

$content = str_replace($search, $replace, $content);
file_put_contents('resources/views/layouts/app.blade.php', $content);
echo "Sidebar updated.\n";
