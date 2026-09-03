const fs = require('fs');

const content = fs.readFileSync('resources/views/surat-masuk-keluar.blade.php', 'utf8');

// extract the content between <script> and </script> at the bottom of the file
const scriptMatch = content.match(/<script>\s*(function toggleBulkMode[\s\S]*?)<\/script>\s*@endsection/);

if (!scriptMatch) {
    console.log("Could not find script block");
    process.exit(1);
}

const jsContent = scriptMatch[1];

// To avoid PHP blade syntax errors in Node like {!! json_encode(...) !!}, let's just strip them out or mock them
const safeJs = jsContent.replace(/{!!.*?!!}/g, '[]');

try {
    new Function(safeJs);
    console.log("Syntax is valid!");
} catch (e) {
    console.error("Syntax Error:", e.message);
}
