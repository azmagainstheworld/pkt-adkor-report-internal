const { JSDOM } = require('jsdom');
const dom = new JSDOM(`
<table>
  <tr class="pelaporan-row">
    <td>This is a test of eksternal reports</td>
  </tr>
</table>
`);
const document = dom.window.document;
const NodeFilter = dom.window.NodeFilter;

function pelaporanSearch(term) {
    term = term.toLowerCase().trim();
    const rows = document.querySelectorAll('.pelaporan-row');
    rows.forEach(function(row) {
        row.querySelectorAll('mark.pelaporan-highlight').forEach(function(mark) {
            const parent = mark.parentNode;
            parent.replaceChild(document.createTextNode(mark.textContent), mark);
            parent.normalize();
        });

        if (term === '') return;

        let found = false;
        if (row.textContent.toLowerCase().includes(term)) {
            found = true;
            row.querySelectorAll('td').forEach(function(td) {
                const walker = document.createTreeWalker(td, NodeFilter.SHOW_TEXT, null, false);
                const textNodes = [];
                let node;
                while ((node = walker.nextNode())) { textNodes.push(node); }

                textNodes.forEach(function(textNode) {
                    const content = textNode.nodeValue;
                    if (!content.toLowerCase().includes(term)) return;

                    const escaped = term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                    const regex = new RegExp(escaped, 'gi');
                    const fragment = document.createDocumentFragment();
                    let lastIndex = 0;
                    let match;

                    regex.lastIndex = 0;
                    while ((match = regex.exec(content)) !== null) {
                        if (match.index > lastIndex) {
                            fragment.appendChild(document.createTextNode(content.slice(lastIndex, match.index)));
                        }
                        const mark = document.createElement('mark');
                        mark.className = 'pelaporan-highlight';
                        mark.style.backgroundColor = '#fef08a';
                        mark.textContent = match[0];
                        fragment.appendChild(mark);
                        lastIndex = match.index + match[0].length;
                    }
                    if (lastIndex < content.length) {
                        fragment.appendChild(document.createTextNode(content.slice(lastIndex)));
                    }
                    textNode.parentNode.replaceChild(fragment, textNode);
                });
            });
        }
    });
}
pelaporanSearch('eksternal');
console.log(document.body.innerHTML);
