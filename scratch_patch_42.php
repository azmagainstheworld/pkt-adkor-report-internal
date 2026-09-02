<?php
$path = 'resources/views/perizinan-perkantoran.blade.php';
$content = file_get_contents($path);

// The pattern to search for is:
//         </x-table>
//             </div>
//         </form>
//     </x-card>

// And we want to insert the pagination links between </form> and </x-card>
// Since we only want to do this for Tabel 3, we can find the specific block.

// Find Tabel 3 ending block
$search = <<<EOD
        </x-table>
            </div>
        </form>
    </x-card>
EOD;

$replace = <<<EOD
        </x-table>
            </div>
        </form>
        <div class="p-4 border-t border-gray-100 text-xs bg-white">
            {{ \$dataProses->links() }}
        </div>
    </x-card>
EOD;

$search = str_replace("\r\n", "\n", $search);
$replace = str_replace("\r\n", "\n", $replace);
$content = str_replace("\r\n", "\n", $content);

if (strpos($content, '{{ $dataProses->links() }}') === false) {
    // try literal replace
    $content = str_replace($search, $replace, $content);
    // If not matched, try regex because whitespace might differ
    if (strpos($content, '{{ $dataProses->links() }}') === false) {
        $content = preg_replace('/(<\/\s*form\s*>)\s*(<\/\s*x-card\s*>)/', "$1\n        <div class=\"p-4 border-t border-gray-100 text-xs bg-white\">\n            {{ \$dataProses->links() }}\n        </div>\n    $2", $content, 1);
    }
    
    file_put_contents($path, $content);
    echo "Added pagination links for Tabel 3!\n";
} else {
    echo "Pagination links already exist for Tabel 3!\n";
}
?>
