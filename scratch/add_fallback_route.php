<?php
// The issue is that the form is correctly defined in HTML
// but JavaScript may be grabbing it wrong, OR 
// the browser is ignoring the _method=DELETE field
// because the form renders as GET when JS can't find the form.
// 
// Let's look at the actual issue: 
// The form wraps the table container - this looks correct structurally.
// BUT: The form has id="bulkDeleteForm1" but IT IS NOT directly inside x-card
// It might be that the form is nested inside another element that prevents submission
// 
// Most likely root cause: The submitBulkDelete1() function runs document.getElementById('bulkDeleteForm1').submit()
// but the form HTML is fine. The 405 GET error means the browser is NOT submitting via the form's 
// action/method, but instead navigating to the URL directly.
//
// The real fix: change submitBulkDelete1 to create a proper form submission with fetch/XHR
// OR: add a GET fallback route (like other modules do for pengiriman-dokumen)

$routeFile = 'routes/web.php';
$content = file_get_contents($routeFile);

$target = "Route::delete('/kearsipan/pa-teknik/bulk-destroy', [PaTeknikController::class, 'destroyBulk'])->name('pa-teknik.destroyBulk');";
$replacement = "Route::delete('/kearsipan/pa-teknik/bulk-destroy', [PaTeknikController::class, 'destroyBulk'])->name('pa-teknik.destroyBulk');
    Route::get('/kearsipan/pa-teknik/bulk-destroy', function() { return redirect()->route('pa-teknik.index'); });";

if (strpos($content, "Route::get('/kearsipan/pa-teknik/bulk-destroy'") === false) {
    $content = str_replace($target, $replacement, $content);
    file_put_contents($routeFile, $content);
    echo "Added GET fallback route for pa-teknik bulk-destroy.\n";
} else {
    echo "Fallback route already exists.\n";
}
