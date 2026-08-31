<?php
$file = 'resources/views/layouts/app.blade.php';
$content = file_get_contents($file);

$script = '
    @if(request(\'search\'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const searchTerm = @json(request(\'search\'));
            if (!searchTerm || searchTerm.trim() === \'\') return;
            
            const walker = document.createTreeWalker(
                document.body,
                NodeFilter.SHOW_TEXT,
                {
                    acceptNode: function(node) {
                        const parent = node.parentNode;
                        if (parent.tagName === \'SCRIPT\' || parent.tagName === \'STYLE\' || parent.tagName === \'NOSCRIPT\' || parent.tagName === \'MARK\') {
                            return NodeFilter.FILTER_REJECT;
                        }
                        if (node.nodeValue.toLowerCase().includes(searchTerm.toLowerCase())) {
                            return NodeFilter.FILTER_ACCEPT;
                        }
                        return NodeFilter.FILTER_SKIP;
                    }
                }
            );

            const nodesToReplace = [];
            let node;
            while (node = walker.nextNode()) {
                nodesToReplace.push(node);
            }

            const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\\\]/g, \'\\\\$&\')})`, \'gi\');
            
            nodesToReplace.forEach(node => {
                const parent = node.parentNode;
                const text = node.nodeValue;
                const html = text.replace(regex, \'<mark class="bg-yellow-300 rounded px-1 text-black font-bold">\$1</mark>\');
                
                const wrapper = document.createElement(\'span\');
                wrapper.innerHTML = html;
                
                while (wrapper.firstChild) {
                    parent.insertBefore(wrapper.firstChild, node);
                }
                parent.removeChild(node);
            });
            
            const firstMark = document.querySelector(\'mark\');
            if(firstMark) {
                firstMark.scrollIntoView({behavior: "smooth", block: "center"});
            }
        });
    </script>
    @endif
</body>
';

$content = str_replace('</body>', $script, $content);
file_put_contents($file, $content);
echo "Highlight script added.\n";
