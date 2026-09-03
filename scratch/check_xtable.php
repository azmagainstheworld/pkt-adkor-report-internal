<?php
// The structure is correct: form wraps everything, hidden inputs are inside form.
// The problem must be something else. Let me look at what happens more carefully.
// 
// Issue: The bulkDeleteForm1 contains the tableContainerBulk1 which has the x-table component.
// x-table is a Blade component that renders a <table>. 
// HTML DOES NOT ALLOW forms inside tables, OR tables that have checkboxes 
// that submit to a parent form outside the table.
// 
// More specifically: a <form> element wrapping <table> is valid HTML5.
// The checkboxes INSIDE <tr>/<td> ARE part of the parent form if that form wraps the table.
// 
// HOWEVER: the issue might be that `x-table` component itself renders a <form> or something
// that breaks nesting. Let's check.
$xTable = file_get_contents('resources/views/components/table.blade.php');
echo $xTable . "\n";
