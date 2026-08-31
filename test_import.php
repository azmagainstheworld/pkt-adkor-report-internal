require __DIR__.'/vendor/autoload.php';  
 = require_once __DIR__.'/bootstrap/app.php';  
 = - 
- 
try { Maatwebsite\Excel\Facades\Excel::import(new App\Imports\PerizinanTerbitImport, 'referensi/Template_perizinan-terbit (1).xlsx'); echo 'Import succeeded. Total: ' . App\Models\PerizinanTerbit::count(); } catch (\Exception ) { echo 'Error: ' . -; }  
